<?php

namespace App\Services\AI;

class ValidationResultFormatter
{
    public function buildFinalConclusion(array $hasilPerSection): string
    {
        $validSections = [];
        $invalidSections = [];

        foreach ($hasilPerSection as $sectionName => $result) {
            $sectionLabel = ucwords(str_replace('_', ' ', (string) $sectionName));
            if (($result['status'] ?? 'tidak_ditemukan') === 'ditemukan') {
                $validSections[] = $sectionLabel;
            } else {
                $invalidSections[] = $sectionLabel;
            }
        }

        $markdown = "KESIMPULAN VALIDASI DOKUMEN\n\n";

        if (empty($invalidSections)) {
            $markdown .= "Semua bagian telah tervalidasi dengan baik. Tidak ada bagian yang invalid.\n\n";
        } else {
            $markdown .= "Bagian yang Valid:\n";
            foreach ($validSections as $section) {
                $markdown .= "- " . $section . "\n";
            }
            $markdown .= "\nBagian yang Invalid:\n";
            foreach ($invalidSections as $section) {
                $markdown .= "- " . $section . "\n";
            }
            $markdown .= "\nBagian yang invalid perlu ditinjau dan diperbaiki kembali.\n\n";
        }

        $markdown .= "Terima kasih atas perhatian Anda.\n";

        return $markdown;
    }

    public function buildLaporanUtamaInsightMarkdown(array $hasilPerSection): string
    {
        $aiOnlyResults = [];

        foreach ($hasilPerSection as $sectionName => $result) {
            if (($result['mode'] ?? 'compare_documents') === 'ai_only') {
                $aiOnlyResults[$sectionName] = $result;
            }
        }

        if (empty($aiOnlyResults)) {
            return 'Dokumen acuan utama - divalidasi per bagian.';
        }

        $lines = [];
        $lines[] = '### Insight AI Only - Laporan Utama';
        $lines[] = '';
        $lines[] = 'Ringkasan berikut diambil langsung dari isi Laporan Utama tanpa pembanding dokumen lain.';
        $lines[] = '';

        foreach ($aiOnlyResults as $sectionName => $result) {
            $sectionLabel = ucwords(str_replace('_', ' ', (string) $sectionName));
            $statusLabel = ($result['status'] ?? 'tidak_ditemukan') === 'ditemukan' ? 'VALID' : 'INVALID';
            $parsed = is_array($result['parsed_ai'] ?? null) ? $result['parsed_ai'] : [];
            $hasil = trim((string) ($parsed['catatan'] ?? ($result['hasil'] ?? '-')));

            $lines[] = '#### ' . $sectionLabel . ' (' . $statusLabel . ')';
            if (!empty($parsed)) {
                $lines[] = '- Status AI: ' . ($parsed['status'] ?? '-');
            }
            $lines[] = '- Insight: ' . ($hasil === '' ? '-' : $this->shortText($hasil, 500));

            $anomali = $parsed['anomali_ditemukan'] ?? [];
            if (is_array($anomali) && !empty($anomali)) {
                $lines[] = '- Anomali: ' . implode(' | ', $anomali);
            }

            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    public function buildItemDetailMarkdown(string $kategori, array $persamaan, array $perbedaan): string
    {
        $lines = [];
        $lines[] = '### Ringkasan Validasi Dokumen: ' . strtoupper($kategori);
        $lines[] = '';

        $lines[] = '#### Persamaan';
        if (empty($persamaan)) {
            $lines[] = '- Tidak ada bagian yang terverifikasi cocok.';
        } else {
            foreach ($persamaan as $row) {
                $lines[] = '- **' . $row['section'] . '**';
                $lines[] = '  - Status: Sama/VALID';
                $lines[] = '  - Laporan Utama: ' . ($row['laporan'] ?? '-');
                $lines[] = '  - Dokumen Pendukung: ' . ($row['dokumen'] ?? '-');
                $lines[] = '  - Catatan AI: ' . $row['note'];
            }
        }

        $lines[] = '';
        $lines[] = '#### Perbedaan';
        if (empty($perbedaan)) {
            $lines[] = '- Tidak ditemukan perbedaan pada section yang dicek.';
        } else {
            foreach ($perbedaan as $row) {
                $lines[] = '- **' . $row['section'] . '**';
                $lines[] = '  - Status: Berbeda/INVALID';
                $lines[] = '  - Laporan Utama: ' . ($row['laporan'] ?? '-');
                $lines[] = '  - Dokumen Pendukung: ' . ($row['dokumen'] ?? '-');
                $lines[] = '  - Catatan AI (yang bikin invalid): ' . $row['note'];
            }
        }

        return implode("\n", $lines);
    }

    private function shortText(string $text, int $maxLen = 220): string
    {
        $text = preg_replace('/\s+/', ' ', trim($text));
        if ($text === '') {
            return '-';
        }

        return mb_strlen($text) > $maxLen
            ? mb_substr($text, 0, $maxLen) . '...'
            : $text;
    }
}
