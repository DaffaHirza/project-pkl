{{-- Reports Tab --}}
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Laporan Penilaian</h3>
            <a href="{{ route('appraisal.reports.create', ['project' => $project->id]) }}" class="btn btn-primary btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Laporan
            </a>
        </div>

        @if($project->reports && $project->reports->count() > 0)
        <div class="space-y-4">
            @foreach($project->reports as $report)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-blue-500 dark:hover:border-blue-500 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $report->title ?? 'Laporan Penilaian' }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $report->report_number ?? '-' }}</p>
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span>Versi {{ $report->version ?? '1.0' }}</span>
                                <span>•</span>
                                <span>{{ $report->created_at->format('d M Y') }}</span>
                                @if($report->approver)
                                <span>•</span>
                                <span>Approved by {{ $report->approver->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($report->status === 'approved')
                        <span class="badge badge-success badge-sm">Approved</span>
                        @elseif($report->status === 'review')
                        <span class="badge badge-warning badge-sm">Review</span>
                        @elseif($report->status === 'revision')
                        <span class="badge badge-error badge-sm">Revisi</span>
                        @else
                        <span class="badge badge-ghost badge-sm">Draft</span>
                        @endif
                    </div>
                </div>

                {{-- Report Actions --}}
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    <a href="{{ route('appraisal.reports.show', $report) }}" class="btn btn-ghost btn-sm gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview
                    </a>
                    <a href="{{ route('appraisal.reports.download', $report) }}" class="btn btn-ghost btn-sm gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download PDF
                    </a>
                    @if($report->status !== 'approved')
                    <a href="{{ route('appraisal.reports.edit', $report) }}" class="btn btn-ghost btn-sm gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    @endif
                    @if($report->status === 'draft' || $report->status === 'revision')
                    <form action="{{ route('appraisal.reports.submit-review', $report) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-primary btn-sm gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Submit Review
                        </button>
                    </form>
                    @endif
                </div>

                {{-- Review Comments (if any) --}}
                @if($report->reviews && $report->reviews->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Review Terbaru</p>
                    @foreach($report->reviews->take(2) as $review)
                    <div class="flex items-start gap-2 text-sm mb-2">
                        <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs">
                            {{ substr($review->reviewer->name ?? '', 0, 1) }}
                        </div>
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $review->reviewer->name ?? '-' }}</span>
                            <span class="text-gray-500 dark:text-gray-400">: {{ Str::limit($review->comment ?? '', 100) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @else
        {{-- No Reports --}}
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum Ada Laporan</h4>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Laporan penilaian akan ditampilkan di sini setelah dibuat.</p>
            <a href="{{ route('appraisal.reports.create', ['project' => $project->id]) }}" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Laporan
            </a>
        </div>
        @endif
    </div>

    {{-- Resume Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Resume Penilaian</h3>
            <a href="{{ route('appraisal.resumes.create', ['project' => $project->id]) }}" class="btn btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Resume
            </a>
        </div>

        @if($project->resumes && $project->resumes->count() > 0)
        <div class="space-y-3">
            @foreach($project->resumes as $resume)
            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $resume->title ?? 'Resume Penilaian' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $resume->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('appraisal.resumes.download', $resume) }}" class="btn btn-ghost btn-sm btn-square">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center text-gray-500 dark:text-gray-400 py-4">Belum ada resume</p>
        @endif
    </div>
</div>
