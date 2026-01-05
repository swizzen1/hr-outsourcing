<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
<header class="bg-slate-900 text-white">
    <nav class="max-w-6xl mx-auto flex flex-wrap items-center gap-3 px-6 py-4">
        <a class="font-semibold" href="{{ route('dashboard.redirect') }}">Home</a>
        @auth
            @if (auth()->user()->hasRole(['Admin', 'HR']))
                <a class="text-slate-200 hover:text-white" href="{{ route('dashboard.admin') }}">Dashboard</a>
            @elseif (auth()->user()->hasRole('Company Admin'))
                <a class="text-slate-200 hover:text-white" href="{{ route('dashboard.company') }}">Dashboard</a>
                @if (auth()->user()->company)
                    <a class="text-slate-200 hover:text-white" href="{{ route('company.vacancies.index', auth()->user()->company) }}">Vacancies</a>
                    <a class="text-slate-200 hover:text-white" href="{{ route('company.leave_requests.index', auth()->user()->company) }}">Leave Requests</a>
                    <a class="text-slate-200 hover:text-white" href="{{ route('company.absences.index', auth()->user()->company) }}">Absences</a>
                @endif
            @else
                <a class="text-slate-200 hover:text-white" href="{{ route('dashboard.employee') }}">Dashboard</a>
                <a class="text-slate-200 hover:text-white" href="{{ route('me.attendance.show') }}">Attendance</a>
                <a class="text-slate-200 hover:text-white" href="{{ route('me.leave_requests.index') }}">Leave Requests</a>
            @endif
            <span class="ml-auto text-sm text-slate-300">Role: {{ auth()->user()->getRoleNames()->implode(', ') }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg bg-slate-700 px-3 py-1.5 text-sm font-semibold hover:bg-slate-600" type="submit">Logout</button>
            </form>
        @endauth
        @guest
            <a class="text-slate-200 hover:text-white" href="{{ route('login') }}">Login</a>
        @endguest
    </nav>
</header>

<main class="max-w-6xl mx-auto px-6 py-6">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
            <ul class="list-disc space-y-1 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
