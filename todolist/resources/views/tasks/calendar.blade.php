@extends('layouts.app')

@section('content')
@php
    $priorityStyles = [
        'low' => [
            'badge' => 'bg-emerald-100 text-emerald-700',
        ],
        'medium' => [
            'badge' => 'bg-amber-100 text-amber-700',
        ],
        'high' => [
            'badge' => 'bg-rose-100 text-rose-700',
        ],
    ];
@endphp

<div class="space-y-10">
    <section class="rounded-3xl bg-white/80 p-8 shadow-xl ring-1 ring-white/60 backdrop-blur">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-500">Calendar overview</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ $calendar['monthLabel'] }}</h2>
                <p class="mt-1 max-w-xl text-sm text-slate-500">Navigate through your schedule and discover which days are the busiest at a glance.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm font-semibold">
                <a href="{{ route('tasks.calendar', ['month' => $calendar['previousMonth']]) }}" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-slate-600 transition hover:bg-slate-200">
                    <span class="text-lg">⟵</span>
                    <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $calendar['previousMonth'])->format('M Y') }}</span>
                </a>
                <a href="{{ route('tasks.calendar') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 px-4 py-2 text-white shadow-lg transition hover:from-indigo-600 hover:to-purple-600">Back to current month</a>
                <a href="{{ route('tasks.calendar', ['month' => $calendar['nextMonth']]) }}" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-slate-600 transition hover:bg-slate-200">
                    <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $calendar['nextMonth'])->format('M Y') }}</span>
                    <span class="text-lg">⟶</span>
                </a>
            </div>
        </div>

        <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200/80 bg-gradient-to-br from-white via-indigo-50 to-emerald-50 p-6">
            @php
                $weeks = $calendar['days']->chunk(7);
            @endphp
            <div class="grid grid-cols-7 gap-3 text-center text-xs font-semibold uppercase tracking-widest text-indigo-400">
                <span>Mon</span>
                <span>Tue</span>
                <span>Wed</span>
                <span>Thu</span>
                <span>Fri</span>
                <span>Sat</span>
                <span>Sun</span>
            </div>
            <div class="mt-4 space-y-3">
                @foreach ($weeks as $week)
                    <div class="grid grid-cols-7 gap-3">
                        @foreach ($week as $day)
                            @php
                                $dateKey = $day->toDateString();
                                $isCurrentMonth = $day->isSameMonth($calendar['reference']);
                                $isToday = $day->isSameDay($today);
                                $dayTasks = $calendar['tasksByDate']->get($dateKey, collect());
                                $taskCount = $dayTasks->count();
                            @endphp
                            <div class="min-h-[160px] rounded-2xl border-2 px-3 py-3 text-left transition {{ $isToday ? 'border-indigo-500 bg-white shadow-lg' : ($isCurrentMonth ? 'border-transparent bg-white/80 hover:border-indigo-300 hover:shadow-md' : 'border-dashed border-slate-200 bg-white/40 text-slate-300') }}">
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-sm {{ $isCurrentMonth ? 'text-slate-700' : 'text-slate-300' }}">{{ $day->format('j') }}</span>
                                    @if ($isToday)
                                        <span class="rounded-full bg-indigo-500 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white">Today</span>
                                    @elseif ($taskCount > 0)
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-600">{{ $taskCount }} tasks</span>
                                    @endif
                                </div>
                                <div class="mt-3 space-y-2">
                                    @forelse ($dayTasks as $calendarTask)
                                        @php
                                            $priorityStyle = $priorityStyles[$calendarTask->priority];
                                        @endphp
                                        <button
                                            type="button"
                                            class="w-full rounded-xl px-3 py-2 text-left text-xs font-semibold shadow-sm transition hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $priorityStyle['badge'] }}"
                                            data-calendar-task
                                            data-title="{{ $calendarTask->title }}"
                                            data-status="{{ $calendarTask->is_done ? 'Completed' : 'In progress' }}"
                                            data-priority="{{ ucfirst($calendarTask->priority) }}"
                                            data-date="{{ optional($calendarTask->due_date)->format('l, F j, Y') }}"
                                            data-notes="{{ e($calendarTask->notes ?? '') }}"
                                        >
                                            <div class="truncate">{{ $calendarTask->title }}</div>
                                            <p class="mt-1 text-[11px] font-medium text-slate-500">{{ $calendarTask->is_done ? 'Completed' : 'Due' }}</p>
                                        </button>
                                    @empty
                                        <p class="text-[11px] font-medium text-slate-300">No tasks scheduled</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <div class="rounded-3xl bg-white/80 p-6 shadow-lg ring-1 ring-white/60">
            <h3 class="text-lg font-semibold text-slate-900">Monthly highlights</h3>
            <p class="mt-1 text-sm text-slate-500">A quick glance at how you're progressing this month.</p>
            <dl class="mt-6 grid grid-cols-3 gap-4 text-center text-sm font-semibold">
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 px-4 py-5 text-indigo-600 shadow-sm">
                    <dt class="text-xs font-medium uppercase tracking-wide text-indigo-500">Total tasks</dt>
                    <dd class="mt-2 text-2xl">{{ $stats['total'] }}</dd>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 px-4 py-5 text-emerald-600 shadow-sm">
                    <dt class="text-xs font-medium uppercase tracking-wide text-emerald-500">Completed</dt>
                    <dd class="mt-2 text-2xl">{{ $stats['completed'] }}</dd>
                </div>
                <div class="rounded-2xl border border-rose-100 bg-rose-50/70 px-4 py-5 text-rose-600 shadow-sm">
                    <dt class="text-xs font-medium uppercase tracking-wide text-rose-500">Active</dt>
                    <dd class="mt-2 text-2xl">{{ $stats['active'] }}</dd>
                </div>
            </dl>
            <div class="mt-8 space-y-3">
                @foreach ($priorityBreakdown as $priority => $count)
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-widest text-slate-500">
                            <span>{{ ucfirst($priority) }} priority</span>
                            <span>{{ $count }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-200">
                            @php
                                $percentage = $stats['total'] > 0 ? round(($count / max(1, $stats['total'])) * 100) : 0;
                                $barColors = [
                                    'high' => 'from-rose-500 to-rose-400',
                                    'medium' => 'from-amber-500 to-amber-400',
                                    'low' => 'from-emerald-500 to-emerald-400',
                                ];
                            @endphp
                            <div class="h-2 rounded-full bg-gradient-to-r {{ $barColors[$priority] ?? 'from-slate-400 to-slate-300' }}" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl bg-white/80 p-6 shadow-lg ring-1 ring-white/60">
            <h3 class="text-lg font-semibold text-slate-900">Weekly breakdown</h3>
            <p class="mt-1 text-sm text-slate-500">See when your workload peaks.</p>
            <div class="mt-6 space-y-4">
                @forelse ($tasksByWeek as $weekLabel => $weeklyTasks)
                    <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-sm">
                        <div class="flex items-center justify-between text-sm font-semibold text-indigo-500">
                            <span>Week of {{ $weekLabel }}</span>
                            <span>{{ $weeklyTasks->count() }} tasks</span>
                        </div>
                        <div class="mt-3 space-y-2 text-sm">
                            @foreach ($weeklyTasks as $task)
                                <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-2">
                                    <span class="mt-1 inline-flex h-2 w-2 flex-none rounded-full {{ match ($task->priority) {
                                        'high' => 'bg-rose-500',
                                        'medium' => 'bg-amber-400',
                                        default => 'bg-emerald-400',
                                    } }}"></span>
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-700">{{ $task->title }}</p>
                                        <p class="text-xs text-slate-500">Due {{ optional($task->due_date)->format('D, M j') }}</p>
                                    </div>
                                    @if ($task->is_done)
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-600">Done</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No tasks scheduled for this month yet.</p>
                @endforelse
            </div>
            <div class="mt-6 flex justify-end">
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-4 py-2 text-sm font-semibold text-white shadow-lg transition hover:from-emerald-600 hover:to-teal-600">Back to tasks</a>
            </div>
        </div>
    </section>
</div>
<div id="calendar-task-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 px-4 py-8">
    <div class="relative w-full max-w-md rounded-3xl bg-white/95 p-6 shadow-2xl ring-1 ring-white/70">
        <button type="button" class="absolute right-4 top-4 text-slate-400 transition hover:text-slate-600" data-modal-dismiss aria-label="Close details">
            ✕
        </button>
        <div class="space-y-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-500">Task</p>
                <h3 id="modal-task-title" class="mt-1 text-2xl font-bold text-slate-900"></h3>
            </div>
            <dl class="space-y-3 text-sm text-slate-600">
                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                    <dt class="font-medium">Status</dt>
                    <dd id="modal-task-status" class="font-semibold text-slate-800"></dd>
                </div>
                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                    <dt class="font-medium">Priority</dt>
                    <dd id="modal-task-priority" class="font-semibold text-slate-800"></dd>
                </div>
                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                    <dt class="font-medium">Due date</dt>
                    <dd id="modal-task-date" class="font-semibold text-slate-800"></dd>
                </div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                    <dt class="font-medium text-slate-700">Notes</dt>
                    <dd id="modal-task-notes" class="mt-2 whitespace-pre-line text-slate-600"></dd>
                </div>
            </dl>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('calendar-task-modal');
            if (!modal) {
                return;
            }

            const titleEl = modal.querySelector('#modal-task-title');
            const statusEl = modal.querySelector('#modal-task-status');
            const priorityEl = modal.querySelector('#modal-task-priority');
            const dateEl = modal.querySelector('#modal-task-date');
            const notesEl = modal.querySelector('#modal-task-notes');
            const closeButtons = modal.querySelectorAll('[data-modal-dismiss]');

            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            document.querySelectorAll('[data-calendar-task]').forEach((taskButton) => {
                taskButton.addEventListener('click', () => {
                    titleEl.textContent = taskButton.dataset.title || '';
                    statusEl.textContent = taskButton.dataset.status || 'In progress';
                    priorityEl.textContent = taskButton.dataset.priority || '';
                    dateEl.textContent = taskButton.dataset.date || 'No due date';
                    notesEl.textContent = taskButton.dataset.notes || 'No notes provided.';

                    openModal();
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>
@endpush
@endsection
