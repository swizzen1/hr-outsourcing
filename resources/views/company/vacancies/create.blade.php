@extends('layouts.app')

@section('content')
<div class="max-w-2xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Create Vacancy - {{ $company->name }}</h2>
        <a class="rounded-lg bg-slate-600 px-3 py-1.5 text-sm font-semibold text-white" href="{{ route('company.vacancies.index', $company) }}">Back</a>
    </div>
    <form method="POST" action="{{ route('company.vacancies.store', $company) }}" class="mt-4 space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Description</label>
            <textarea name="description" rows="4" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="text-sm font-medium">Location</label>
            <input type="text" name="location" value="{{ old('location') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Employment Type</label>
            <input type="text" name="employment_type" value="{{ old('employment_type') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Status</label>
            <select name="status" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                <option value="published" @selected(old('status') === 'published')>Published</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Published At (optional)</label>
            <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Expiration Date (optional)</label>
            <input type="date" name="expiration_date" value="{{ old('expiration_date') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" type="submit">Save</button>
    </form>
</div>
@endsection
