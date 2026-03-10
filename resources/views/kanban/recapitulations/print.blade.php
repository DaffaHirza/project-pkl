<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $recapitulation->title }} - Cetak</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1f2937;
            background: white;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #1f2937;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header .period {
            font-size: 14px;
            color: #4b5563;
        }
        
        .header .meta {
            font-size: 11px;
            color: #6b7280;
            margin-top: 10px;
        }
        
        /* Summary */
        .summary {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .summary h2 {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }
        
        .summary p {
            color: #4b5563;
        }
        
        /* Stats */
        .stats {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }
        
        .stat-card .number {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .stat-card .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        
        /* Table */
        .section-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        
        th {
            background: #f9fafb;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        td {
            font-size: 11px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 500;
        }
        
        .status-completed {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-in_progress {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-pending_review {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-blocked {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-not_started {
            background: #f3f4f6;
            color: #4b5563;
        }
        
        .stage-info {
            font-size: 10px;
        }
        
        .stage-label {
            display: inline-block;
            padding: 1px 6px;
            background: #e5e7eb;
            border-radius: 3px;
            margin-right: 3px;
        }
        
        .stage-arrow {
            color: #9ca3af;
            margin: 0 3px;
        }
        
        .activities-list {
            margin: 0;
            padding-left: 15px;
        }
        
        .activities-list li {
            margin-bottom: 2px;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #6b7280;
        }
        
        /* Print specific */
        @media print {
            body {
                font-size: 11px;
            }
            
            .container {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
        
        /* Print button */
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Cetak
    </button>
    
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ $recapitulation->title }}</h1>
            <div class="period">{{ $recapitulation->period_label }}</div>
            <div class="meta">
                Dibuat oleh: {{ $recapitulation->creator->name ?? 'System' }} |
                Dicetak: {{ now()->format('d M Y H:i') }}
            </div>
        </div>
        
        {{-- Summary --}}
        @if($recapitulation->summary)
        <div class="summary">
            <h2>Ringkasan</h2>
            <p>{{ $recapitulation->summary }}</p>
        </div>
        @endif
        
        {{-- Stats --}}
        @php
            $total = $recapitulation->items->count();
            $completed = $recapitulation->items->where('work_status', 'completed')->count();
            $inProgress = $recapitulation->items->where('work_status', 'in_progress')->count();
            $pendingReview = $recapitulation->items->where('work_status', 'pending_review')->count();
            $blocked = $recapitulation->items->where('work_status', 'blocked')->count();
        @endphp
        
        <div class="stats">
            <div class="stat-card">
                <div class="number">{{ $total }}</div>
                <div class="label">Total Aset</div>
            </div>
            <div class="stat-card">
                <div class="number">{{ $completed }}</div>
                <div class="label">Selesai</div>
            </div>
            <div class="stat-card">
                <div class="number">{{ $inProgress }}</div>
                <div class="label">Dalam Proses</div>
            </div>
            <div class="stat-card">
                <div class="number">{{ $pendingReview }}</div>
                <div class="label">Menunggu Review</div>
            </div>
            <div class="stat-card">
                <div class="number">{{ $blocked }}</div>
                <div class="label">Terhambat</div>
            </div>
        </div>
        
        {{-- Items Table --}}
        <h2 class="section-title">Detail Pekerjaan Aset</h2>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 20%">Aset</th>
                    <th style="width: 12%">Status</th>
                    <th style="width: 15%">Stage</th>
                    <th style="width: 23%">Aktivitas</th>
                    <th style="width: 25%">Catatan / Langkah Selanjutnya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recapitulation->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->asset->name ?? 'N/A' }}</strong>
                        @if($item->asset && $item->asset->client)
                        <br><span style="color: #6b7280;">{{ $item->asset->client->name }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ $item->work_status }}">
                            {{ \App\Models\RecapitulationItemKanban::WORK_STATUSES[$item->work_status] ?? $item->work_status }}
                        </span>
                    </td>
                    <td>
                        <div class="stage-info">
                            @if($item->stage_start)
                            <span class="stage-label">{{ $item->stage_start }}</span>
                            @endif
                            @if($item->stage_start && $item->stage_end && $item->stage_start !== $item->stage_end)
                            <span class="stage-arrow">→</span>
                            <span class="stage-label">{{ $item->stage_end }}</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($item->activities)
                            @php $activities = is_array($item->activities) ? $item->activities : json_decode($item->activities, true); @endphp
                            @if(!empty($activities))
                            <ul class="activities-list">
                                @foreach(array_slice($activities, 0, 5) as $activity)
                                <li>{{ $activity }}</li>
                                @endforeach
                                @if(count($activities) > 5)
                                <li><em>+{{ count($activities) - 5 }} lainnya</em></li>
                                @endif
                            </ul>
                            @else
                            <span style="color: #9ca3af;">-</span>
                            @endif
                        @else
                        <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($item->notes)
                        <p>{{ Str::limit($item->notes, 100) }}</p>
                        @endif
                        @if($item->next_actions)
                        <p style="margin-top: 5px; color: #4b5563;">
                            <strong>Next:</strong> {{ Str::limit($item->next_actions, 80) }}
                        </p>
                        @endif
                        @if(!$item->notes && !$item->next_actions)
                        <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #9ca3af; padding: 20px;">
                        Belum ada item dalam rekapitulasi ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- Footer --}}
        <div class="footer">
            <div>
                Status: {{ $recapitulation->status === 'published' ? 'Dipublikasikan' : 'Draft' }}
                @if($recapitulation->published_at)
                ({{ $recapitulation->published_at->format('d M Y H:i') }})
                @endif
            </div>
            <div>
                Periode {{ $recapitulation->duration_days }} hari
            </div>
        </div>
    </div>
</body>
</html>
