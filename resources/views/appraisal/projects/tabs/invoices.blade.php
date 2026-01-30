{{-- Invoices Tab --}}
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Invoice & Pembayaran</h3>
            <a href="{{ route('appraisal.invoices.create', ['project' => $project->id]) }}" class="btn btn-primary btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Invoice
            </a>
        </div>

        {{-- Invoice Summary --}}
        @php
            $totalInvoiced = $project->invoices?->sum('amount') ?? 0;
            $totalPaid = $project->invoices?->where('status', 'paid')->sum('amount') ?? 0;
            $totalPending = $totalInvoiced - $totalPaid;
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Invoice</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalInvoiced, 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                <p class="text-xs text-green-600 dark:text-green-400 mb-1">Sudah Dibayar</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
            </div>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                <p class="text-xs text-yellow-600 dark:text-yellow-400 mb-1">Belum Dibayar</p>
                <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">Rp {{ number_format($totalPending, 0, ',', '.') }}</p>
            </div>
        </div>

        @if($project->invoices && $project->invoices->count() > 0)
        {{-- Invoice List --}}
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Jatuh Tempo</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->invoices as $invoice)
                    <tr>
                        <td>
                            <a href="{{ route('appraisal.invoices.show', $invoice) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td>{{ $invoice->invoice_date?->format('d M Y') ?? '-' }}</td>
                        <td>
                            <span class="{{ $invoice->due_date?->isPast() && $invoice->status !== 'paid' ? 'text-red-600 dark:text-red-400 font-medium' : '' }}">
                                {{ $invoice->due_date?->format('d M Y') ?? '-' }}
                            </span>
                            @if($invoice->due_date?->isPast() && $invoice->status !== 'paid')
                            <span class="text-xs text-red-500">(Overdue)</span>
                            @endif
                        </td>
                        <td class="text-right font-medium">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($invoice->status === 'paid')
                            <span class="badge badge-success badge-sm">Lunas</span>
                            @elseif($invoice->status === 'partial')
                            <span class="badge badge-warning badge-sm">Sebagian</span>
                            @elseif($invoice->status === 'overdue')
                            <span class="badge badge-error badge-sm">Overdue</span>
                            @elseif($invoice->status === 'cancelled')
                            <span class="badge badge-ghost badge-sm">Batal</span>
                            @else
                            <span class="badge badge-info badge-sm">Pending</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </label>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48">
                                    <li><a href="{{ route('appraisal.invoices.show', $invoice) }}">Lihat Detail</a></li>
                                    <li><a href="{{ route('appraisal.invoices.download', $invoice) }}">Download PDF</a></li>
                                    @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                                    <li><a href="{{ route('appraisal.invoices.edit', $invoice) }}">Edit</a></li>
                                    <li>
                                        <button onclick="recordPayment({{ $invoice->id }})">Catat Pembayaran</button>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Payment History --}}
        @if($project->payments && $project->payments->count() > 0)
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Riwayat Pembayaran</h4>
            <div class="space-y-3">
                @foreach($project->payments->sortByDesc('payment_date') as $payment)
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $payment->invoice?->invoice_number ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->payment_date?->format('d M Y') }} • {{ $payment->payment_method ?? '-' }}</p>
                        </div>
                    </div>
                    <p class="font-semibold text-green-600 dark:text-green-400">+ Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @else
        {{-- No Invoices --}}
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
            </svg>
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum Ada Invoice</h4>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Invoice proyek akan ditampilkan di sini setelah dibuat.</p>
            <a href="{{ route('appraisal.invoices.create', ['project' => $project->id]) }}" class="btn btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Invoice
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Payment Modal --}}
<div id="paymentModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/50" onclick="closePaymentModal()"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Catat Pembayaran</h3>
            <form id="paymentForm" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah Pembayaran</label>
                        <input type="number" name="amount" class="input input-bordered w-full" placeholder="0" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Pembayaran</label>
                        <input type="date" name="payment_date" class="input input-bordered w-full" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metode Pembayaran</label>
                        <select name="payment_method" class="select select-bordered w-full" required>
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Tunai</option>
                            <option value="check">Cek/Giro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan</label>
                        <textarea name="notes" rows="2" class="textarea textarea-bordered w-full" placeholder="Catatan pembayaran..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closePaymentModal()" class="btn btn-ghost">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function recordPayment(invoiceId) {
    const modal = document.getElementById('paymentModal');
    const form = document.getElementById('paymentForm');
    form.action = `/appraisal/invoices/${invoiceId}/payments`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
