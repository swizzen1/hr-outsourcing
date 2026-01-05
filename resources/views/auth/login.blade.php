@extends('layouts.app')

@section('content')
<div class="mx-auto mt-10 max-w-md rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-xl font-semibold">Login</h2>
    <form method="POST" action="{{ route('login.submit') }}" class="mt-4 space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">
        </div>
        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">
        </div>
        <button class="w-full rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-700" type="submit">Login</button>
    </form>
</div>
@endsection
