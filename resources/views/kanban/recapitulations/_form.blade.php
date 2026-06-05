<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
            Informasi Rekapitulasi
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Lengkapi periode laporan dan ringkasan evaluasi pekerjaan.
        </p>
    </div>

    <div class="p-6 space-y-6">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Judul Rekapitulasi <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" id="title"
                value="{{ old('title', $recapitulation->title ?? ($suggestedTitle ?? '')) }}"
                placeholder="Contoh: Rekapitulasi Minggu 1 Juni 2026"
                class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:border-brand-500 focus:ring-brand-500"
                required>
            @error('title')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="period_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tanggal Mulai <span class="text-red-500">*</span>
                </label>
                <input type="date" name="period_start" id="period_start"
                    value="{{ old('period_start', isset($recapitulation) ? $recapitulation->period_start->format('Y-m-d') : $suggestedPeriod['start']->format('Y-m-d')) }}"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:border-brand-500 focus:ring-brand-500"
                    required>
                @error('period_start')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="period_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tanggal Akhir <span class="text-red-500">*</span>
                </label>
                <input type="date" name="period_end" id="period_end"
                    value="{{ old('period_end', isset($recapitulation) ? $recapitulation->period_end->format('Y-m-d') : $suggestedPeriod['end']->format('Y-m-d')) }}"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:border-brand-500 focus:ring-brand-500"
                    required>
                @error('period_end')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="summary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Ringkasan / Catatan Evaluasi
            </label>
            <textarea name="summary" id="summary" rows="5"
                placeholder="Tuliskan ringkasan umum progres, kendala, atau catatan evaluasi pada periode ini."
                class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:border-brand-500 focus:ring-brand-500 resize-none">{{ old('summary', $recapitulation->summary ?? '') }}</textarea>
            @error('summary')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        @if (($mode ?? 'create') === 'create')
            <label
                class="flex items-start gap-3 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/70 border border-gray-200 dark:border-gray-700 cursor-pointer">
                <input type="checkbox" name="auto_generate" value="1"
                    {{ old('auto_generate', true) ? 'checked' : '' }}
                    class="mt-1 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <span>
                    <span class="block text-sm font-medium text-gray-900 dark:text-white">
                        Auto-generate item rekapitulasi
                    </span>
                    <span class="block mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Sistem akan mengambil asset yang memiliki aktivitas, catatan, atau pembaruan dalam periode yang
                        dipilih.
                    </span>
                </span>
            </label>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startInput = document.getElementById('period_start');
            const endInput = document.getElementById('period_end');
            const titleInput = document.getElementById('title');

            function updateTitle() {
                if (!startInput.value || titleInput.dataset.edited === '1') return;

                const date = new Date(startInput.value + 'T00:00:00');
                const weekNum = Math.ceil(date.getDate() / 7);
                const months = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];

                titleInput.value =
                    `Rekapitulasi Minggu ${weekNum} ${months[date.getMonth()]} ${date.getFullYear()}`;
            }

            titleInput.addEventListener('input', function() {
                titleInput.dataset.edited = '1';
            });

            startInput.addEventListener('change', updateTitle);

            endInput.addEventListener('change', function() {
                if (startInput.value && endInput.value && endInput.value < startInput.value) {
                    endInput.value = startInput.value;
                }
            });
        });
    </script>
@endpush
