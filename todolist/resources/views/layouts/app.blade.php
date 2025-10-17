<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-50 via-rose-50 to-emerald-50 text-slate-800">
    <div class="min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="mb-10 rounded-3xl bg-white/80 p-6 shadow-lg ring-1 ring-white/60 backdrop-blur">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-500">Welcome back</p>
                        <h1 class="mt-2 text-3xl font-bold text-slate-900">My To-Do List</h1>
                        <p class="mt-1 max-w-xl text-sm text-slate-500">Plan your day with a splash of color and stay motivated with a vibrant overview of everything on your list.</p>
                    </div>
                    <div class="flex flex-col items-end gap-4 sm:flex-row sm:items-center">
                        <span class="inline-flex items-center rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 px-4 py-1.5 text-sm font-semibold text-white shadow-lg">{{ now()->format('l, F j, Y') }}</span>
                        <nav class="flex gap-2 rounded-full bg-slate-100/70 p-1 text-sm font-semibold text-slate-500 shadow-inner">
                            <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-2 rounded-full px-4 py-2 transition {{ request()->routeIs('tasks.index') ? 'bg-white text-indigo-600 shadow' : 'hover:text-indigo-500' }}">
                                <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
                                Tasks
                            </a>
                            <a href="{{ route('tasks.calendar') }}" class="inline-flex items-center gap-2 rounded-full px-4 py-2 transition {{ request()->routeIs('tasks.calendar') ? 'bg-white text-emerald-600 shadow' : 'hover:text-emerald-500' }}">
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                Calendar
                            </a>
                        </nav>
                    </div>
                </div>
            </header>

            @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200/80 bg-emerald-50/80 px-4 py-3 text-sm font-medium text-emerald-700 shadow-sm">
                {{ session('status') }}
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>

</html>