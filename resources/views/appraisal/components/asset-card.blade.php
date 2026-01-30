{{-- Asset Card Component --}}
<div class="group bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-all duration-200"
     data-asset-id="{{ $asset->id }}"
     onclick="window.location.href='{{ route('appraisal.assets.show', $asset) }}'">
    
    {{-- Priority Indicator --}}
    @if($asset->priority_status !== 'normal')
    <div class="mb-3">
        @if($asset->priority_status === 'critical')
        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            Kritis
        </span>
        @else
        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            Perhatian
        </span>
        @endif
    </div>
    @endif

    {{-- Asset Code & Type Badge --}}
    <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $asset->asset_code }}</span>
        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
            @if($asset->asset_type === 'tanah') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
            @elseif($asset->asset_type === 'bangunan') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
            @elseif($asset->asset_type === 'tanah_bangunan') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400
            @elseif($asset->asset_type === 'mesin') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
            @elseif($asset->asset_type === 'kendaraan') bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400
            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
            @endif">
            {{ $assetTypes[$asset->asset_type] ?? $asset->asset_type }}
        </span>
    </div>

    {{-- Asset Name --}}
    <h4 class="font-semibold text-gray-900 dark:text-white text-sm leading-tight mb-2 line-clamp-2">
        {{ $asset->name }}
    </h4>

    {{-- Project Info --}}
    @if($asset->project)
    <div class="flex items-center gap-2 mb-2">
        <div class="flex-shrink-0 w-5 h-5 rounded bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center">
            <svg class="w-3 h-3 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <span class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $asset->project->name }}</span>
    </div>
    @endif

    {{-- Client --}}
    @if($asset->project && $asset->project->client)
    <div class="flex items-center gap-2 mb-3">
        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                {{ substr($asset->project->client->name, 0, 1) }}
            </span>
        </div>
        <span class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $asset->project->client->name }}</span>
    </div>
    @endif

    {{-- Location --}}
    @if($asset->location_address)
    <div class="space-y-1.5 mb-3">
        <div class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
            <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="line-clamp-1">{{ $asset->location_address }}</span>
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800">
        {{-- Target Date --}}
        @if($asset->target_completion_date)
        <div class="flex items-center gap-1.5 text-xs 
            {{ $asset->target_completion_date->isPast() ? 'text-red-600 dark:text-red-400' : ($asset->target_completion_date->diffInDays(now()) <= 3 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-500 dark:text-gray-400') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>{{ $asset->target_completion_date->format('d M Y') }}</span>
        </div>
        @else
        <span class="text-xs text-gray-400">No deadline</span>
        @endif

        {{-- Progress --}}
        <div class="flex items-center gap-2">
            <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-brand-500 rounded-full" style="width: {{ $asset->progress_percentage }}%"></div>
            </div>
            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $asset->progress_percentage }}%</span>
        </div>
    </div>
</div>
