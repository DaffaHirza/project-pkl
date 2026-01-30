{{-- Analysis/Working Paper Tab --}}
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kertas Kerja Penilaian</h3>
            @if(!$project->workingPaper)
            <a href="{{ route('appraisal.working-papers.create', ['project' => $project->id]) }}" class="btn btn-primary btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Kertas Kerja
            </a>
            @else
            <div class="flex items-center gap-2">
                <a href="{{ route('appraisal.working-papers.edit', $project->workingPaper) }}" class="btn btn-outline btn-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('appraisal.working-papers.export', $project->workingPaper) }}" class="btn btn-outline btn-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </a>
            </div>
            @endif
        </div>

        @if($project->workingPaper)
        {{-- Working Paper Status --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                <p class="font-semibold">
                    @if($project->workingPaper->status === 'approved')
                    <span class="text-green-600 dark:text-green-400">✓ Disetujui</span>
                    @elseif($project->workingPaper->status === 'review')
                    <span class="text-yellow-600 dark:text-yellow-400">⏳ Dalam Review</span>
                    @else
                    <span class="text-gray-600 dark:text-gray-400">Draft</span>
                    @endif
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pendekatan Penilaian</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $project->workingPaper->valuation_approach ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal Penilaian</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $project->workingPaper->valuation_date?->format('d M Y') ?? '-' }}</p>
            </div>
        </div>

        {{-- Valuation Summary --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Nilai</h4>
            
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Komponen</th>
                            <th class="text-right">Nilai</th>
                            <th class="text-center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nilai Tanah</td>
                            <td class="text-right font-medium">Rp {{ number_format($project->workingPaper->land_value ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center text-sm text-gray-500">{{ $project->workingPaper->land_value_method ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Nilai Bangunan</td>
                            <td class="text-right font-medium">Rp {{ number_format($project->workingPaper->building_value ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center text-sm text-gray-500">{{ $project->workingPaper->building_value_method ?? '-' }}</td>
                        </tr>
                        @if($project->workingPaper->other_assets_value)
                        <tr>
                            <td>Aset Lainnya</td>
                            <td class="text-right font-medium">Rp {{ number_format($project->workingPaper->other_assets_value ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center text-sm text-gray-500">{{ $project->workingPaper->other_assets_notes ?? '-' }}</td>
                        </tr>
                        @endif
                        <tr class="border-t-2 border-gray-300 dark:border-gray-600 font-bold">
                            <td>Total Nilai Pasar</td>
                            <td class="text-right text-lg text-blue-600 dark:text-blue-400">Rp {{ number_format($project->workingPaper->total_market_value ?? 0, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                        @if($project->workingPaper->liquidation_value)
                        <tr>
                            <td>Nilai Likuidasi</td>
                            <td class="text-right">Rp {{ number_format($project->workingPaper->liquidation_value ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center text-sm text-gray-500">{{ $project->workingPaper->liquidation_percentage ?? '-' }}% dari Nilai Pasar</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Data Pembanding (if any) --}}
        @if($project->workingPaper->comparables && count($project->workingPaper->comparables) > 0)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Data Pembanding</h4>
            <div class="overflow-x-auto">
                <table class="table w-full text-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Lokasi</th>
                            <th>Luas Tanah</th>
                            <th>Harga</th>
                            <th>Harga/m²</th>
                            <th>Sumber</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->workingPaper->comparables as $index => $comp)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $comp['location'] ?? '-' }}</td>
                            <td>{{ $comp['land_area'] ?? '-' }} m²</td>
                            <td>Rp {{ number_format($comp['price'] ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($comp['price_per_sqm'] ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $comp['source'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Assumptions & Limiting Conditions --}}
        @if($project->workingPaper->assumptions || $project->workingPaper->limiting_conditions)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($project->workingPaper->assumptions)
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Asumsi</h4>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $project->workingPaper->assumptions }}</p>
                    </div>
                </div>
                @endif
                @if($project->workingPaper->limiting_conditions)
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Kondisi Pembatas</h4>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $project->workingPaper->limiting_conditions }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        @else
        {{-- No Working Paper --}}
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum Ada Kertas Kerja</h4>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Kertas kerja analisis penilaian akan ditampilkan di sini.</p>
            <a href="{{ route('appraisal.working-papers.create', ['project' => $project->id]) }}" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Kertas Kerja
            </a>
        </div>
        @endif
    </div>
</div>
