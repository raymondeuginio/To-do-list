@extends('layouts.app')

@section('content')
@php
    $priorityStyles = [
        'low' => [
            'badge' => 'bg-emerald-100 text-emerald-700',
            'indicator' => 'border-emerald-400',
            'status' => 'text-emerald-600',
        ],
        'medium' => [
            'badge' => 'bg-amber-100 text-amber-700',
            'indicator' => 'border-amber-400',
            'status' => 'text-amber-600',
        ],
        'high' => [
            'badge' => 'bg-rose-100 text-rose-700',
            'indicator' => 'border-rose-500',
            'status' => 'text-rose-600',
        ],
    ];

    $weekCompletion = $summary['week']['total'] > 0
        ? round(($summary['week']['completed'] / max(1, $summary['week']['total'])) * 100)
        : 0;
    $monthCompletion = $summary['month']['total'] > 0
        ? round(($summary['month']['completed'] / max(1, $summary['month']['total'])) * 100)
        : 0;
@endphp

<div class="grid gap-6 xl:grid-cols-[minmax(0,2.1fr)_minmax(260px,1fr)]">
    <div class="space-y-6">
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
            <div class="border-b border-slate-200/60 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Filter tasks</h2>
            </div>
            <form method="GET" action="{{ route('tasks.index') }}" class="px-6 py-5 grid gap-4 sm:grid-cols-4">
                <div class="sm:col-span-2">
                    <label for="q" class="block text-sm font-medium text-slate-600">Search</label>
                    <input type="text" id="q" name="q" value="{{ $filters['q'] }}" placeholder="Search title or notes" class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 focus:border-indigo-400 focus:ring-indigo-300" />
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-600">Status</label>
                    <select id="status" name="status" class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 focus:border-indigo-400 focus:ring-indigo-300">
                        <option value="all" @selected($filters['status'] === 'all')>All</option>
                        <option value="todo" @selected($filters['status'] === 'todo')>To do</option>
                        <option value="done" @selected($filters['status'] === 'done')>Done</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="block text-sm font-medium text-slate-600">Sort by</label>
                    <select id="sort" name="sort" class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 focus:border-indigo-400 focus:ring-indigo-300">
                        <option value="latest" @selected($filters['sort'] === 'latest')>Latest</option>
                        <option value="due" @selected($filters['sort'] === 'due')>Due date</option>
                        <option value="priority" @selected($filters['sort'] === 'priority')>Priority</option>
                    </select>
                </div>
                <div class="sm:col-span-4 flex flex-wrap items-center justify-between gap-3 pt-2">
                    <div class="flex gap-3">
                        <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Apply filters</button>
                        <a href="{{ route('tasks.index') }}" class="inline-flex items-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:border-slate-300">Reset</a>
                    </div>
                    <a href="{{ route('tasks.export', request()->query()) }}" class="inline-flex items-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:border-slate-300">Export CSV</a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
            <div class="border-b border-slate-200/60 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Add a task</h2>
            </div>
            <form method="POST" action="{{ route('tasks.store') }}" class="px-6 py-5 grid gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label for="title" class="block text-sm font-medium text-slate-600">Title<span class="text-rose-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="150" class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 focus:border-indigo-400 focus:ring-indigo-300" />
                </div>
                <div>
                    <label for="due_date" class="block text-sm font-medium text-slate-600">Due date</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 focus:border-indigo-400 focus:ring-indigo-300" />
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-slate-600">Priority</label>
                    <select id="priority" name="priority" class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 focus:border-indigo-400 focus:ring-indigo-300">
                        <option value="low" @selected(old('priority') === 'low')>Low</option>
                        <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                        <option value="high" @selected(old('priority') === 'high')>High</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-slate-600">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 focus:border-indigo-400 focus:ring-indigo-300" placeholder="Add any details">{{ old('notes') }}</textarea>
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Create task</button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
            <div class="flex items-center justify-between border-b border-slate-200/60 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Your tasks</h2>
                    <p class="text-xs text-slate-500">Stay on top of your commitments with a compact overview.</p>
                </div>
                @if ($tasks->total())
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-700">{{ $tasks->total() }}</p>
                        <p class="text-xs text-slate-400">tasks</p>
                    </div>
                @endif
            </div>
            <div class="px-6 py-5 space-y-5">
                @if ($errors->any())
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-600">
                        <ul class="space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($tasks->count() === 0)
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center text-slate-500">
                        You’re all caught up ✨
                    </div>
                @else
                    <form id="bulk-form" method="POST" action="{{ route('tasks.bulk') }}" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        @csrf
                        @foreach (request()->query() as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-medium text-slate-700">Bulk actions</span>
                            <div class="flex gap-2">
                                <button type="submit" name="action" value="toggle" class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">Toggle done</button>
                                <button type="submit" name="action" value="delete" class="inline-flex items-center rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500" onclick="return confirm('Delete selected tasks?')">Delete</button>
                            </div>
                            <span class="text-slate-400">Select tasks below to apply</span>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left font-medium">Select</th>
                                    <th scope="col" class="px-4 py-3 text-left font-medium">Task</th>
                                    <th scope="col" class="px-4 py-3 text-left font-medium">Due</th>
                                    <th scope="col" class="px-4 py-3 text-left font-medium">Priority</th>
                                    <th scope="col" class="px-4 py-3 text-left font-medium">Status</th>
                                    <th scope="col" class="px-4 py-3 text-right font-medium">Manage</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($tasks as $task)
                                    @php
                                        $isOverdue = ! $task->is_done && $task->due_date && $task->due_date->isPast();
                                        $priorityStyle = $priorityStyles[$task->priority];
                                    @endphp
                                    <tr class="align-top border-l-4 bg-white transition hover:bg-slate-50 {{ $priorityStyle['indicator'] }}">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" form="bulk-form" name="task_ids[]" value="{{ $task->id }}" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="space-y-2">
                                                <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    @foreach (request()->query() as $key => $value)
                                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                    @endforeach
                                                    <label class="flex items-start gap-2 text-sm font-medium text-slate-800">
                                                        <input type="checkbox" onchange="this.form.submit()" {{ $task->is_done ? 'checked' : '' }} class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                        <span class="leading-5 {{ $task->is_done ? 'line-through text-slate-400' : 'text-slate-800' }}">{{ $task->title }}</span>
                                                    </label>
                                                </form>
                                                @if ($task->notes)
                                                    <p class="text-xs leading-relaxed text-slate-500 whitespace-pre-line">{{ $task->notes }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if ($task->due_date)
                                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium {{ $isOverdue ? 'text-rose-600' : 'text-slate-700' }}">
                                                    {{ $task->due_date->format('M j, Y') }}
                                                    @if ($isOverdue)
                                                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-rose-600">Overdue</span>
                                                    @elseif ($task->due_date->isSameDay($today))
                                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-600">Today</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400">No due date</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $priorityStyle['badge'] }}">{{ ucfirst($task->priority) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold {{ $task->is_done ? 'text-emerald-600' : $priorityStyle['status'] }}">
                                                {{ $task->is_done ? 'Completed' : 'In progress' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <details class="group inline-block text-left">
                                                <summary class="cursor-pointer text-xs font-medium text-indigo-600 transition hover:text-indigo-500">Quick edit</summary>
                                                <div class="mt-3 w-72 space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-3 shadow-lg">
                                                    <form method="POST" action="{{ route('tasks.update', $task) }}" class="grid gap-3 text-left text-xs">
                                                        @csrf
                                                        @method('PUT')
                                                        @foreach (request()->query() as $key => $value)
                                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                        @endforeach
                                                        <div>
                                                            <label class="block font-medium text-slate-600">Title</label>
                                                            <input type="text" name="title" value="{{ old('title', $task->title) }}" required maxlength="150" class="mt-1 w-full rounded-lg border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-300">
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-3">
                                                            <div>
                                                                <label class="block font-medium text-slate-600">Due date</label>
                                                                <input type="date" name="due_date" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-300">
                                                            </div>
                                                            <div>
                                                                <label class="block font-medium text-slate-600">Priority</label>
                                                                <select name="priority" class="mt-1 w-full rounded-lg border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-300">
                                                                    <option value="low" @selected(old('priority', $task->priority) === 'low')>Low</option>
                                                                    <option value="medium" @selected(old('priority', $task->priority) === 'medium')>Medium</option>
                                                                    <option value="high" @selected(old('priority', $task->priority) === 'high')>High</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block font-medium text-slate-600">Notes</label>
                                                            <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-300">{{ old('notes', $task->notes) }}</textarea>
                                                        </div>
                                                        <label class="inline-flex items-center gap-2 font-medium text-slate-600">
                                                            <input type="checkbox" name="is_done" value="1" {{ old('is_done', $task->is_done) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Mark as done
                                                        </label>
                                                        <div class="flex items-center justify-between pt-1">
                                                            <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Save</button>
                                                            <button type="submit" form="delete-task-{{ $task->id }}" class="inline-flex items-center rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500" onclick="return confirm('Delete this task?')">Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </details>
                                        </td>
                                    </tr>

                                    <form id="delete-task-{{ $task->id }}" method="POST" action="{{ route('tasks.destroy', $task) }}" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                        @foreach (request()->query() as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach
                                    </form>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-6">
                        {{ $tasks->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <aside class="space-y-6">
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
            <div class="border-b border-slate-200/60 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">Recap</h2>
                <p class="text-xs text-slate-500">Overview of your workload.</p>
            </div>
            <div class="px-5 py-5 space-y-5 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Active</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $summary['totals']['active'] }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Completed</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ $summary['totals']['completed'] }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Overdue</p>
                        <p class="mt-1 text-2xl font-semibold text-rose-600">{{ $summary['totals']['overdue'] }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">All tasks</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $summary['totals']['total'] }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-xs font-medium text-slate-600">
                            <span>This week</span>
                            <span>{{ $summary['week']['completed'] }} / {{ $summary['week']['total'] }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-200">
                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $weekCompletion }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $summary['week']['remaining'] }} tasks remaining this week.</p>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs font-medium text-slate-600">
                            <span>This month</span>
                            <span>{{ $summary['month']['completed'] }} / {{ $summary['month']['total'] }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-200">
                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $monthCompletion }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $summary['month']['remaining'] }} tasks left this month.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
            <div class="border-b border-slate-200/60 px-5 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-slate-900">Calendar</h2>
                    <span class="text-xs font-medium text-slate-500">{{ $calendar['monthLabel'] }}</span>
                </div>
            </div>
            <div class="px-5 py-5">
                @php
                    $weeks = $calendar['days']->chunk(7);
                @endphp
                <div class="grid grid-cols-7 gap-2 text-[11px] font-medium uppercase tracking-wide text-slate-400">
                    <span>Mon</span>
                    <span>Tue</span>
                    <span>Wed</span>
                    <span>Thu</span>
                    <span>Fri</span>
                    <span>Sat</span>
                    <span>Sun</span>
                </div>
                <div class="mt-3 space-y-2">
                    @foreach ($weeks as $week)
                        <div class="grid grid-cols-7 gap-2">
                            @foreach ($week as $day)
                                @php
                                    $dateKey = $day->toDateString();
                                    $isCurrentMonth = $day->isSameMonth($today);
                                    $isToday = $day->isSameDay($today);
                                    $dayTasks = $calendar['tasksByDate']->get($dateKey, collect());
                                @endphp
                                <div class="min-h-[92px] rounded-xl border px-2.5 py-2 text-xs {{ $isToday ? 'border-indigo-500 bg-indigo-50' : ($isCurrentMonth ? 'border-slate-200 bg-slate-50' : 'border-slate-100 bg-white opacity-70') }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-semibold {{ $isCurrentMonth ? 'text-slate-700' : 'text-slate-300' }}">{{ $day->format('j') }}</span>
                                        @if ($isToday)
                                            <span class="rounded-full bg-indigo-500 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white">Today</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 space-y-1">
                                        @foreach ($dayTasks as $calendarTask)
                                            @php
                                                $priorityStyle = $priorityStyles[$calendarTask->priority];
                                            @endphp
                                            <span class="block truncate rounded-lg px-2 py-1 text-[11px] font-medium {{ $priorityStyle['badge'] }}">{{ $calendarTask->title }}</span>
                                        @endforeach
                                        @if ($dayTasks->isEmpty())
                                            <span class="block text-[10px] text-slate-300">-</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </aside>
</div>
@endsection
