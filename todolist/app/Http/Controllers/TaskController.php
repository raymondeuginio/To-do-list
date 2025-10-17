<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->applyFilters(Task::query(), $request);

        $sort = $request->get('sort', 'latest');
        $this->applySorting($query, $sort);

        $tasks = $query->paginate(10)->appends($request->query());

        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $weeklyTasks = Task::query()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$startOfWeek, $endOfWeek])
            ->get();

        $monthlyTasks = Task::query()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->get();

        $calendarStart = $startOfMonth->copy()->startOfWeek();
        $calendarEnd = $endOfMonth->copy()->endOfWeek();

        $calendarDays = collect(CarbonPeriod::create($calendarStart, $calendarEnd))->map(
            static fn (Carbon $day) => $day->copy(),
        );

        $calendarTasksByDate = Task::query()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$calendarStart, $calendarEnd])
            ->orderBy('due_date')
            ->get()
            ->groupBy(fn (Task $task) => $task->due_date?->toDateString());

        $summary = [
            'totals' => [
                'total' => Task::query()->count(),
                'completed' => Task::query()->where('is_done', true)->count(),
                'active' => Task::query()->where('is_done', false)->count(),
                'overdue' => Task::query()
                    ->where('is_done', false)
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $now->toDateString())
                    ->count(),
            ],
            'week' => [
                'total' => $weeklyTasks->count(),
                'completed' => $weeklyTasks->where('is_done', true)->count(),
                'remaining' => $weeklyTasks->where('is_done', false)->count(),
            ],
            'month' => [
                'total' => $monthlyTasks->count(),
                'completed' => $monthlyTasks->where('is_done', true)->count(),
                'remaining' => $monthlyTasks->where('is_done', false)->count(),
            ],
        ];

        return view('tasks.index', [
            'tasks' => $tasks,
            'filters' => [
                'q' => $request->string('q')->toString(),
                'status' => $request->get('status', 'all'),
                'sort' => $sort,
            ],
            'summary' => $summary,
            'calendar' => [
                'monthLabel' => $now->format('F Y'),
                'days' => $calendarDays,
                'tasksByDate' => $calendarTasksByDate,
            ],
            'today' => $now,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateTask($request);

        Task::create($data);

        return redirect()->route('tasks.index')->with('status', 'Task created.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $data = $this->validateTask($request);

        $task->update($data);

        return redirect()->route('tasks.index', $request->query())->with('status', 'Task updated.');
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index', $request->query())->with('status', 'Task deleted.');
    }

    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $task->update(['is_done' => ! $task->is_done]);

        return redirect()->route('tasks.index', $request->query())->with('status', 'Task updated.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'task_ids' => ['required', 'array'],
            'task_ids.*' => ['integer', 'exists:tasks,id'],
            'action' => ['required', 'in:delete,toggle'],
        ]);

        $tasks = Task::whereIn('id', $data['task_ids'])->get();

        if ($data['action'] === 'delete') {
            Task::whereIn('id', $tasks->pluck('id'))->delete();
            $message = 'Selected tasks deleted.';
        } else {
            foreach ($tasks as $task) {
                $task->update(['is_done' => ! $task->is_done]);
            }
            $message = 'Selected tasks toggled.';
        }

        return redirect()->route('tasks.index', $request->query())->with('status', $message);
    }

    public function export(Request $request): Response
    {
        $query = $this->applyFilters(Task::query(), $request);
        $this->applySorting($query, $request->get('sort', 'latest'));

        $tasks = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tasks.csv"',
        ];

        $callback = static function () use ($tasks): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Title', 'Notes', 'Due Date', 'Priority', 'Is Done', 'Created At', 'Updated At']);

            foreach ($tasks as $task) {
                fputcsv($handle, [
                    $task->title,
                    $task->notes,
                    optional($task->due_date)?->format('Y-m-d'),
                    $task->priority,
                    $task->is_done ? 'Yes' : 'No',
                    $task->created_at?->format('Y-m-d H:i:s'),
                    $task->updated_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return ResponseFactory::stream($callback, 200, $headers);
    }

    protected function applyFilters(Builder $query, Request $request): Builder
    {
        if ($search = $request->string('q')->toString()) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $status = $request->get('status', 'all');
        if ($status === 'todo') {
            $query->where('is_done', false);
        } elseif ($status === 'done') {
            $query->where('is_done', true);
        }

        return $query;
    }

    protected function applySorting(Builder $query, string $sort): void
    {
        if ($sort === 'due') {
            $query->orderByRaw('due_date IS NULL')
                ->orderBy('due_date')
                ->orderBy('created_at', 'desc');
        } elseif ($sort === 'priority') {
            $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    protected function validateTask(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
            'is_done' => ['nullable', 'boolean'],
        ]);

        $validated['is_done'] = $request->boolean('is_done');

        return $validated;
    }
}
