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

<div class="space-y-10">
    <div class="space-y-6">
        <div class="rounded-3xl bg-white/80 shadow-lg ring-1 ring-white/60 backdrop-blur">
            <div class="border-b border-white/60 bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-rose-500/10 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Filter tasks</h2>
            </div>
            <form method="GET" action="{{ route('tasks.index') }}" class="grid gap-4 px-6 py-5 sm:grid-cols-4">
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

        <div class="rounded-3xl bg-white/80 shadow-lg ring-1 ring-white/60 backdrop-blur">
            <div class="border-b border-white/60 bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-blue-500/10 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Add a task</h2>
            </div>
            <form method="POST" action="{{ route('tasks.store') }}" class="grid gap-4 px-6 py-5 sm:grid-cols-2">
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

        <div class="rounded-3xl bg-white/80 shadow-xl ring-1 ring-white/60 backdrop-blur">
            <div class="flex items-center justify-between border-b border-white/60 bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-blue-500/10 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Your tasks</h2>
                    <p class="text-xs text-slate-500">Stay on top of your commitments with a compact overview.</p>
                </div>
                @if ($tasks->total())
                    <div class="rounded-2xl bg-white/90 px-4 py-2 text-right shadow-sm">
                        <p class="text-sm font-semibold text-indigo-600">{{ $tasks->total() }}</p>
                        <p class="text-xs text-slate-400">tasks</p>
                    </div>
                @endif
            </div>
            <div class="space-y-5 px-6 py-5">
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
        </div>

        <div class="rounded-3xl bg-white/80 shadow-lg ring-1 ring-white/60 backdrop-blur">
            <div class="border-b border-white/60 bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-emerald-500/10 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Recap</h2>
                <p class="text-xs text-slate-500">Overview of your workload.</p>
            </div>
            <div class="space-y-5 px-6 py-5 text-sm">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50/70 p-3 shadow-sm">
                        <p class="text-xs text-indigo-500">Active</p>
                        <p class="mt-1 text-2xl font-semibold text-indigo-700">{{ $summary['totals']['active'] }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-3 shadow-sm">
                        <p class="text-xs text-emerald-500">Completed</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ $summary['totals']['completed'] }}</p>
                    </div>
                    <div class="rounded-xl border border-rose-100 bg-rose-50/70 p-3 shadow-sm">
                        <p class="text-xs text-rose-500">Overdue</p>
                        <p class="mt-1 text-2xl font-semibold text-rose-600">{{ $summary['totals']['overdue'] }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                        <p class="text-xs text-slate-500">All tasks</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $summary['totals']['total'] }}</p>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <div class="flex items-center justify-between text-xs font-medium text-slate-600">
                            <span>This week</span>
                            <span>{{ $summary['week']['completed'] }} / {{ $summary['week']['total'] }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-200">
                            <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $weekCompletion }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-xs font-medium text-slate-600">
                                <span>This week</span>
                                <span>{{ $summary['week']['completed'] }} / {{ $summary['week']['total'] }}</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-200">
                                <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $weekCompletion }}%"></div>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">{{ $summary['week']['remaining'] }} tasks remaining this week.</p>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-200">
                            <div class="h-2 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500" style="width: {{ $monthCompletion }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
