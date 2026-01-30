@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('appraisal.projects.show', $project) }}" class="btn btn-ghost btn-sm btn-square">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Jadwalkan Inspeksi</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $project->project_code }} - {{ $project->name }}</p>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="alert alert-error mb-6">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <h3 class="font-bold">Terdapat kesalahan:</h3>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Project Info Card --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-4 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-medium text-blue-900 dark:text-blue-100">Informasi Proyek</p>
                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                    <strong>Klien:</strong> {{ $project->client->name ?? '-' }}<br>
                    <strong>Lokasi:</strong> {{ $project->location ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('appraisal.inspections.store', $project) }}" method="POST">
        @csrf

        {{-- Inspection Details --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Inspeksi</h2>
            
            <div class="space-y-4">
                {{-- Surveyor --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Surveyor <span class="text-red-500">*</span></span>
                    </label>
                    <select name="surveyor_id" class="select select-bordered w-full @error('surveyor_id') select-error @enderror" required>
                        <option value="">Pilih Surveyor</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('surveyor_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('surveyor_id')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                {{-- Inspection Date --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Tanggal Inspeksi <span class="text-red-500">*</span></span>
                    </label>
                    <input type="date" name="inspection_date" value="{{ old('inspection_date') }}" 
                           class="input input-bordered w-full @error('inspection_date') input-error @enderror" required>
                    @error('inspection_date')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Catatan</span>
                    </label>
                    <textarea name="notes" rows="3" 
                              class="textarea textarea-bordered w-full @error('notes') textarea-error @enderror"
                              placeholder="Catatan atau instruksi khusus untuk surveyor...">{{ old('notes') }}</textarea>
                    @error('notes')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Location Coordinates (Optional) --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Koordinat Lokasi (Opsional)</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Koordinat dapat diisi saat inspeksi berlangsung melalui aplikasi mobile.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Latitude</span>
                    </label>
                    <input type="text" name="latitude" value="{{ old('latitude') }}" 
                           class="input input-bordered w-full @error('latitude') input-error @enderror"
                           placeholder="-6.123456">
                    @error('latitude')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Longitude</span>
                    </label>
                    <input type="text" name="longitude" value="{{ old('longitude') }}" 
                           class="input input-bordered w-full @error('longitude') input-error @enderror"
                           placeholder="106.789012">
                    @error('longitude')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('appraisal.projects.show', $project) }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Jadwalkan Inspeksi
            </button>
        </div>
    </form>
</div>
@endsection
