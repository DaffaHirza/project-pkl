{{-- Inspection Tab --}}
<div class="space-y-6">
    {{-- Inspection Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Data Inspeksi</h3>
            @if(!$project->inspection)
            <a href="{{ route('appraisal.inspections.create', ['project' => $project->id]) }}" class="btn btn-primary btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Inspeksi
            </a>
            @else
            <a href="{{ route('appraisal.inspections.edit', $project->inspection) }}" class="btn btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Inspeksi
            </a>
            @endif
        </div>

        @if($project->inspection)
        {{-- Inspection Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal Inspeksi</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $project->inspection->inspection_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Inspektor</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $project->inspection->inspector?->name ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                <p class="font-semibold text-gray-900 dark:text-white">
                    @if($project->inspection->status === 'completed')
                    <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Selesai
                    </span>
                    @elseif($project->inspection->status === 'in_progress')
                    <span class="inline-flex items-center gap-1 text-yellow-600 dark:text-yellow-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Dalam Proses
                    </span>
                    @else
                    <span class="text-gray-500 dark:text-gray-400">Dijadwalkan</span>
                    @endif
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Foto</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $project->inspection->photos_count ?? 0 }} foto</p>
            </div>
        </div>

        {{-- Property Details from Inspection --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Detail Properti</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Luas Tanah</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->inspection->land_area ?? '-' }} m²</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Luas Bangunan</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->inspection->building_area ?? '-' }} m²</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Tahun Dibangun</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->inspection->year_built ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Kondisi Fisik</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->inspection->physical_condition ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Jenis Sertifikat</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->inspection->certificate_type ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">No. Sertifikat</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->inspection->certificate_number ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Zona/Peruntukan</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->inspection->zoning ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Aksesibilitas</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->inspection->accessibility ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Inspection Photos --}}
        @if($project->inspection->photos && count($project->inspection->photos) > 0)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Dokumentasi Foto</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($project->inspection->photos as $photo)
                <a href="{{ $photo->url }}" target="_blank" class="aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 hover:opacity-80 transition-opacity">
                    <img src="{{ $photo->thumbnail_url ?? $photo->url }}" alt="{{ $photo->caption ?? 'Foto Inspeksi' }}" class="w-full h-full object-cover">
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Notes --}}
        @if($project->inspection->notes)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Catatan Inspeksi</h4>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $project->inspection->notes }}</p>
            </div>
        </div>
        @endif

        @else
        {{-- No Inspection --}}
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum Ada Data Inspeksi</h4>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Data inspeksi lapangan akan ditampilkan di sini setelah inspeksi dilakukan.</p>
            <a href="{{ route('appraisal.inspections.create', ['project' => $project->id]) }}" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Inspeksi
            </a>
        </div>
        @endif
    </div>
</div>
