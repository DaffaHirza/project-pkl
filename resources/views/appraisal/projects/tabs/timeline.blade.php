{{-- Timeline Tab --}}
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Timeline Proyek</h3>

        <div class="relative">
            {{-- Timeline Line --}}
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

            <div class="space-y-6">
                @forelse($project->activities ?? [] as $activity)
                <div class="relative pl-10">
                    {{-- Timeline Dot --}}
                    <div class="absolute left-0 w-8 h-8 rounded-full flex items-center justify-center
                        @if($activity->type === 'stage_change') bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                        @elseif($activity->type === 'document_upload') bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400
                        @elseif($activity->type === 'inspection') bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400
                        @elseif($activity->type === 'payment') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400
                        @elseif($activity->type === 'approval') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400
                        @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                        @endif
                    ">
                        @if($activity->type === 'stage_change')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        @elseif($activity->type === 'document_upload')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        @elseif($activity->type === 'inspection')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        </svg>
                        @elseif($activity->type === 'payment')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @elseif($activity->type === 'approval')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @endif
                    </div>

                    {{-- Activity Content --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $activity->description }}</p>
                                @if($activity->metadata)
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    @if($activity->type === 'stage_change')
                                    <span class="inline-flex items-center gap-1">
                                        <span class="px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-xs">{{ $activity->metadata['from'] ?? '-' }}</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                        <span class="px-2 py-0.5 rounded bg-blue-200 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs">{{ $activity->metadata['to'] ?? '-' }}</span>
                                    </span>
                                    @elseif($activity->type === 'payment')
                                    <span class="text-green-600 dark:text-green-400 font-medium">
                                        + Rp {{ number_format($activity->metadata['amount'] ?? 0, 0, ',', '.') }}
                                    </span>
                                    @else
                                    {{ json_encode($activity->metadata) }}
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $activity->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        @if($activity->user)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ substr($activity->user->name ?? '', 0, 1) }}</span>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $activity->user->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                {{-- Default Timeline for new project --}}
                <div class="relative pl-10">
                    <div class="absolute left-0 w-8 h-8 rounded-full flex items-center justify-center bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Proyek dibuat</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Proyek {{ $project->project_name }} telah dibuat</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $project->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        @if($project->createdBy)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ substr($project->createdBy->name ?? '', 0, 1) }}</span>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $project->createdBy->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforelse

                {{-- Stage History --}}
                @if($project->stageHistory && $project->stageHistory->count() > 0)
                @foreach($project->stageHistory->sortByDesc('created_at') as $history)
                <div class="relative pl-10">
                    <div class="absolute left-0 w-8 h-8 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Perubahan Stage</p>
                                <div class="mt-2 text-sm">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-xs">{{ $history->from_stage ?? 'Lead' }}</span>
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                        <span class="px-2 py-0.5 rounded bg-blue-200 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs">{{ $history->to_stage }}</span>
                                    </span>
                                </div>
                                @if($history->notes)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $history->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $history->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $history->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        @if($history->changedBy)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ substr($history->changedBy->name ?? '', 0, 1) }}</span>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $history->changedBy->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
