@extends('layouts.app')

@php
use App\Models\ProjectKanban;
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="{{ route('appraisal.dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Appraisal</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('appraisal.projects.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Proyek</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('appraisal.projects.show', $project) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $project->project_code }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-700 dark:text-gray-300">Edit</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Proyek</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $project->project_code }}</p>
    </div>

    {{-- Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <form action="{{ route('appraisal.projects.update', $project) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Client --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Klien <span class="text-red-500">*</span></span>
                </label>
                <select name="client_id" class="select select-bordered w-full @error('client_id') select-error @enderror" required>
                    <option value="">Pilih Klien</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                        @if($client->company_name) - {{ $client->company_name }} @endif
                    </option>
                    @endforeach
                </select>
                @error('client_id')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
                @enderror
            </div>

            {{-- Project Name --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Nama Proyek <span class="text-red-500">*</span></span>
                </label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" 
                       class="input input-bordered w-full @error('name') input-error @enderror" 
                       placeholder="Contoh: Penilaian Tanah dan Bangunan di Jl. Sudirman"
                       required>
                @error('name')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
                @enderror
            </div>

            {{-- Location --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Lokasi</span>
                </label>
                <input type="text" name="location" value="{{ old('location', $project->location) }}" 
                       class="input input-bordered w-full @error('location') input-error @enderror" 
                       placeholder="Contoh: Jl. Sudirman No. 123, Jakarta Selatan">
                @error('location')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
                @enderror
            </div>

            {{-- Due Date --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Target Selesai</span>
                </label>
                <input type="date" name="due_date" value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}" 
                       class="input input-bordered w-full @error('due_date') input-error @enderror">
                @error('due_date')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
                @enderror
            </div>

            {{-- Current Stage --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Stage</span>
                </label>
                <select name="current_stage" class="select select-bordered w-full @error('current_stage') select-error @enderror">
                    @foreach(ProjectKanban::STAGES as $key => $label)
                    <option value="{{ $key }}" {{ old('current_stage', $project->current_stage) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                @error('current_stage')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
                @enderror
            </div>

            {{-- Priority Status --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Status Prioritas</span>
                </label>
                <select name="priority_status" class="select select-bordered w-full @error('priority_status') select-error @enderror">
                    <option value="normal" {{ old('priority_status', $project->priority_status) == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="warning" {{ old('priority_status', $project->priority_status) == 'warning' ? 'selected' : '' }}>⚠️ Perhatian</option>
                    <option value="critical" {{ old('priority_status', $project->priority_status) == 'critical' ? 'selected' : '' }}>🔴 Kritis</option>
                </select>
                @error('priority_status')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('appraisal.projects.show', $project) }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
