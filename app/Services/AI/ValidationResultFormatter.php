<?php

namespace App\Services\AI;

class ValidationResultFormatter
{
    public function buildFinalConclusion(array $hasilPerSection): string
    {
        $markdown = "# Hasil Validasi Laporan Utama\n\n";
        $markdown .= "Ringkasan dibagi menjadi dua bagian: insight AI-only dari Laporan Utama dan validasi silang dengan dokumen pendukung.\n\n";

        $aiOnlyResults = [];
        $compareResults = [];

        foreach ($hasilPerSection as $sectionName => $result) {
            if (($result['mode'] ?? 'compare_documents') === 'ai_only') {
                $aiOnlyResults[$sectionName] = $result;
                continue;
            }

            $compareResults[$sectionName] = $result;
        }

        if (!empty($aiOnlyResults)) {
            $markdown .= "## Insight AI Only (Laporan Utama)\n\n";
            $markdown .= "Bagian ini dianalisis langsung dari isi Laporan Utama tanpa pembanding dokumen lain.\n\n";
            $markdown .= "---\n\n";

            foreach ($aiOnlyResults as $sectionName => $result) {
                $statusBadge = ($result['status'] ?? 'tidak_ditemukan') === 'ditemukan' ? 'VALID' : 'INVALID';
                $markdown .= "### {$statusBadge} - " . ucwords(str_replace('_', ' ', (string) $sectionName)) . "\n\n";
                $markdown .= "Sumber: Laporan Utama (AI-only).\n\n";

                $parsed = is_array($result['parsed_ai'] ?? null) ? $result['parsed_ai'] : [];
                if (!empty($parsed)) {
                    $markdown .= 'Status AI: ' . ($parsed['status'] ?? '-') . "\n\n";
                    $markdown .= 'Catatan: ' . ($parsed['catatan'] ?? '-') . "\n\n";

                    $anomali = $parsed['anomali_ditemukan'] ?? [];
                    if (is_array($anomali) && !empty($anomali)) {
                        $markdown .= "Anomali Ditemukan:\n";
                        foreach ($anomali as $item) {
                            $markdown .= '- ' . $item . "\n";
                        }
                        $markdown .= "\n";
                    }
                }

                if (!empty($result['laporan_excerpt']) && ($result['laporan_excerpt'] ?? '-') !== '-') {
                    $markdown .= 'Cuplikan Laporan: ' . $result['laporan_excerpt'] . "\n\n";
                }

                if (empty($parsed)) {
                    $markdown .= ($result['hasil'] ?? '-') . "\n\n";
                }
                $markdown .= "---\n\n";
            }
        }

        if (!empty($compareResults)) {
            $markdown .= "## Validasi Dengan Dokumen Pendukung\n\n";
            $markdown .= "Bagian ini membandingkan isi Laporan Utama dengan dokumen pembanding yang tersedia.\n\n";
            $markdown .= "---\n\n";

            foreach ($compareResults as $sectionName => $result) {
                $statusBadge = ($result['status'] ?? 'tidak_ditemukan') === 'ditemukan' ? 'VALID' : 'INVALID';
                $markdown .= "### {$statusBadge} - " . ucwords(str_replace('_', ' ', (string) $sectionName)) . "\n\n";

                $parsed = is_array($result['parsed_ai'] ?? null) ? $result['parsed_ai'] : [];
                if (!empty($parsed)) {
                    $markdown .= 'Status AI: ' . ($parsed['status'] ?? '-') . "\n\n";
                    $markdown .= 'Catatan: ' . ($parsed['catatan'] ?? '-') . "\n\n";

                    $anomali = $parsed['anomali_ditemukan'] ?? [];
                    if (is_array($anomali) && !empty($anomali)) {
                        $markdown .= "Anomali Ditemukan:\n";
                        foreach ($anomali as $item) {
                            $markdown .= '- ' . $item . "\n";
                        }
                        $markdown .= "\n";
                    }
                }

                if (!empty($result['checked_against'])) {
                    $markdown .= 'Dicek terhadap: ' . implode(', ', $result['checked_against']) . "\n\n";
                }

                if (empty($parsed)) {
                    $markdown .= ($result['hasil'] ?? '-') . "\n\n";
                }
                $markdown .= "---\n\n";
            }
        }

        if (empty($aiOnlyResults) && empty($compareResults)) {
            $markdown .= "Tidak ada hasil section yang dapat ditampilkan.\n\n";
        }

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
