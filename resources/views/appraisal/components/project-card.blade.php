{{-- Project Card Component --}}
<div class="group bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-all duration-200"
     data-project-id="{{ $project->id }}"
     onclick="window.location.href='{{ route('appraisal.projects.show', $project) }}'">
    
    {{-- Priority Indicator --}}
    @if($project->priority_status !== 'normal')
    <div class="mb-3">
        @if($project->priority_status === 'critical')
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

    {{-- Project Code --}}
    <div class="mb-2">
        <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $project->project_code }}</span>
    </div>

    {{-- Project Name --}}
    <h4 class="font-semibold text-gray-900 dark:text-white text-sm leading-tight mb-2 line-clamp-2">
        {{ $project->name }}
    </h4>

    {{-- Client --}}
    @if($project->client)
    <div class="flex items-center gap-2 mb-3">
        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                {{ substr($project->client->name, 0, 1) }}
            </span>
        </div>
        <span class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $project->client->name }}</span>
    </div>
    @endif

    {{-- Location --}}
    @if($project->location)
    <div class="space-y-1.5 mb-3">
        <div class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
            <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="line-clamp-1">{{ $project->location }}</span>
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800">
        {{-- Due Date --}}
        @if($project->due_date)
        <div class="flex items-center gap-1.5 text-xs 
            {{ $project->due_date->isPast() ? 'text-red-600 dark:text-red-400' : ($project->due_date->diffInDays(now()) <= 3 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-500 dark:text-gray-400') }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>{{ $project->due_date->format('d M Y') }}</span>
        </div>
        @else
        <span></span>
        @endif

        {{-- Quick Actions (shown on hover) --}}
        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button onclick="event.stopPropagation();" 
                    class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                </svg>
            </button>
        </div>
    </div>
</div>
