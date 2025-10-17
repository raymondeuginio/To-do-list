@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
        <div class="border-b border-slate-200/60 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-900">Filter tasks</h2>
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
        <div class="border-b border-slate-200/60 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-900">Add a task</h2>
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
        <div class="border-b border-slate-200/60 px-6 py-5 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Your tasks</h2>
            @if ($tasks->total())
                <p class="text-sm text-slate-500">{{ $tasks->total() }} total</p>
            @endif
        </div>
        <div class="px-6 py-5">
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-600">
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
                <form id="bulk-form" method="POST" action="{{ route('tasks.bulk') }}" class="space-y-4">
                    @csrf
                    @foreach (request()->query() as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <span class="font-medium text-slate-700">Bulk actions</span>
                        <div class="flex gap-2">
                            <button type="submit" name="action" value="toggle" class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">Toggle done</button>
                            <button type="submit" name="action" value="delete" class="inline-flex items-center rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500" onclick="return confirm('Delete selected tasks?')">Delete</button>
                        </div>
                        <span class="text-slate-400">Select tasks below to apply</span>
                    </div>
                </form>

                <div class="mt-4 space-y-4">
                    @foreach ($tasks as $task)
                        @php
                            $priorityColors = [
                                'low' => 'bg-emerald-100 text-emerald-700',
                                'medium' => 'bg-amber-100 text-amber-700',
                                'high' => 'bg-rose-100 text-rose-700',
                            ];
                            $isOverdue = ! $task->is_done && $task->due_date && $task->due_date->isPast();
                        @endphp
                        <div class="rounded-xl border border-slate-200 px-4 py-4 shadow-sm transition hover:border-slate-300">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div class="flex flex-1 items-start gap-4">
                                    <div class="pt-1">
                                        <input type="checkbox" form="bulk-form" name="task_ids[]" value="{{ $task->id }}" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    </div>
                                    <div class="flex-1 space-y-3">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                @foreach (request()->query() as $key => $value)
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endforeach
                                                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-800">
                                                    <input type="checkbox" onchange="this.form.submit()" {{ $task->is_done ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                    <span class="{{ $task->is_done ? 'line-through text-slate-400' : 'text-slate-800' }}">{{ $task->title }}</span>
                                                </label>
                                            </form>
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $priorityColors[$task->priority] }}">{{ ucfirst($task->priority) }}</span>
                                            @if ($task->due_date)
                                                <span class="text-xs font-medium {{ $isOverdue ? 'text-rose-600' : 'text-slate-500' }}">
                                                    Due {{ $task->due_date->format('M j, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($task->notes)
                                            <p class="text-sm text-slate-600 whitespace-pre-line">{{ $task->notes }}</p>
                                        @endif
                                        <details class="group">
                                            <summary class="cursor-pointer text-xs font-medium text-indigo-600 transition group-open:text-indigo-500">Edit task</summary>
                                            <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                                                <form method="POST" action="{{ route('tasks.update', $task) }}" class="grid gap-3 md:grid-cols-2">
                                                    @csrf
                                                    @method('PUT')
                                                    @foreach (request()->query() as $key => $value)
                                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                    @endforeach
                                                    <div class="md:col-span-2">
                                                        <label class="block text-xs font-medium text-slate-600">Title</label>
                                                        <input type="text" name="title" value="{{ old('title', $task->title) }}" required maxlength="150" class="mt-1 w-full rounded-lg border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-300">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-slate-600">Due date</label>
                                                        <input type="date" name="due_date" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-300">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-slate-600">Priority</label>
                                                        <select name="priority" class="mt-1 w-full rounded-lg border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-300">
                                                            <option value="low" @selected(old('priority', $task->priority) === 'low')>Low</option>
                                                            <option value="medium" @selected(old('priority', $task->priority) === 'medium')>Medium</option>
                                                            <option value="high" @selected(old('priority', $task->priority) === 'high')>High</option>
                                                        </select>
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="block text-xs font-medium text-slate-600">Notes</label>
                                                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border-slate-200 bg-white focus:border-indigo-400 focus:ring-indigo-300">{{ old('notes', $task->notes) }}</textarea>
                                                    </div>
                                                    <div class="md:col-span-2 flex items-center justify-between">
                                                        <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
                                                            <input type="checkbox" name="is_done" value="1" {{ old('is_done', $task->is_done) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Mark as done
                                                        </label>
                                                        <div class="flex gap-2">
                                                            <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Save</button>
                                                            <button type="submit" form="delete-task-{{ $task->id }}" class="inline-flex items-center rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500" onclick="return confirm('Delete this task?')">Delete</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </details>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form id="delete-task-{{ $task->id }}" method="POST" action="{{ route('tasks.destroy', $task) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                            @foreach (request()->query() as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        </form>
                    @endforeach
                </div>

                <div class="pt-6">
                    {{ $tasks->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
