<?php

namespace App\Services;

use App\Models\AssistantDocument;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIServicesasli
{
    /**
     * Proses seluruh dokumen dengan ekstraksi dan analisis AI
     * Strategi baru: pecah LAPORAN UTAMA per bagian, lalu tiap bagian dicek ke dokumen pendukung tertentu
     */
    public function prosesDokumen(AssistantDocument $document)
    {
        Log::info('Starting section-based document analysis', ['document_id' => $document->id]);

        $laporanUtamaItem = $document->documentItems()
            ->where('kategori', 'LIKE', '%laporan_utama%')
            ->first();

        if (!$laporanUtamaItem) {
            $document->update(['kesimpulan' => 'Error: Laporan Utama tidak ditemukan.']);
            return;
        }
        $pathLaporan = storage_path('app/public/' . $laporanUtamaItem->path_file);
        $teksLaporanUtama = $this->extractTextFromPdfSmart($pathLaporan);
        if (trim($teksLaporanUtama) === '') {
            $document->update(['kesimpulan' => 'Error: Gagal mengekstrak teks dari Laporan Utama atau Laporan utama harus PDF.']);
            return;
        }

        $teksLaporanUtama = $this->normalizeText($teksLaporanUtama);

        $laporanUtamaItem->update([
            'hasil_ai' => 'Dokumen acuan utama - divalidasi per bagian.',
            'status_verifikasi' => 'ditemukan'
        ]);

        $dokumenPendukung = [];
        foreach ($document->documentItems as $item) {
            if ($item->id == $laporanUtamaItem->id) {
                continue;
            }

            $path = storage_path('app/public/' . $item->path_file);
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $kategori = strtolower(trim((string) $item->kategori));
            $teksItem = '';

            if ($ext === 'pdf') {
                $teksItem = $this->extractTextFromPdfSmart($path);
            } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                if (!file_exists($path)) {
                    $item->update([
                        'hasil_ai' => 'Error: File gambar tidak ditemukan.',
                        'status_verifikasi' => 'tidak_ditemukan',
                    ]);
                    continue;
                }

                $mimeType = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                $teksItem = $this->extractTextFromImage(
                    base64_encode(file_get_contents($path)),
                    $mimeType
                );
            }

            if (trim($teksItem) === '') {
                $item->update([
                    'hasil_ai' => 'Dokumen pendukung tidak memiliki teks yang bisa dianalisis.',
                    'status_verifikasi' => 'tidak_ditemukan',
                ]);
                continue;
            }

            $dokumenPendukung[$kategori] = $teksItem;
            $item->update([
                'hasil_ai' => 'Dokumen pendukung siap untuk validasi per bagian.',
                'status_verifikasi' => 'pending',
            ]);
        }

        $sections = config('document_rules.laporan_sections', []);
        $onlySections = config('document_rules.only_sections', []);

        if (is_string($onlySections) && trim($onlySections) !== '') {
            $onlySections = array_map('trim', explode(',', $onlySections));
        }

        if (is_array($onlySections) && !empty($onlySections)) {
            $onlySections = array_values(array_filter(array_map(
                fn($s) => trim((string) $s),
                $onlySections
            )));

            $sections = array_filter(
                $sections,
                fn($_, $sectionName) => in_array((string) $sectionName, $onlySections, true),
                ARRAY_FILTER_USE_BOTH
            );
        }

        $hasilPerSection = [];
        $totalValid = 0;
        $totalSection = count($sections);

        foreach ($sections as $sectionName => $sectionConfig) {
            $keywords = $sectionConfig['keywords'] ?? [];
            $checkAgainst = $sectionConfig['check_against'] ?? [];
            $instruction = $sectionConfig['instruction'] ?? '';
            $mode = strtolower((string) ($sectionConfig['mode'] ?? 'compare_documents'));
            $isAiOnly = $mode === 'ai_only';

            $sectionSnippet = $this->extractSectionFromLaporan($sectionName, $teksLaporanUtama, $keywords);
            $laporanEvidence = $this->extractEvidenceSnippet($sectionSnippet['snippet'] ?? '', $keywords, 260);

            $relevantDocs = '';
            $availableDocs = [];
            $docExcerpts = [];
            foreach ($checkAgainst as $kategoriTarget) {
                if (isset($dokumenPendukung[$kategoriTarget])) {
                    $relevantDocs .= "\n\n[{$kategoriTarget}]:\n" . mb_substr($dokumenPendukung[$kategoriTarget], 0, 2000);
                    $availableDocs[] = $kategoriTarget;
                    $docExcerpts[$kategoriTarget] = $this->extractEvidenceSnippet($dokumenPendukung[$kategoriTarget], $keywords, 260);
                }
            }

            if (!$isAiOnly && $relevantDocs === '') {
                $hasilPerSection[$sectionName] = [
                    'mode' => $mode,
                    'status' => 'tidak_ditemukan',
                    'hasil' => "[SKIP] Dokumen pendukung tidak tersedia untuk bagian '{$sectionName}'.",
                    'checked_against' => [],
                    'snippet_found' => false,
                    'laporan_excerpt' => $laporanEvidence,
                    'doc_excerpts' => [],
                ];
                continue;
            }

            $prompt = $isAiOnly
                ? $this->buildSectionPromptAiOnly($sectionName, $sectionSnippet, $instruction)
                : $this->buildSectionPrompt($sectionName, $sectionSnippet, $relevantDocs, $instruction, $availableDocs);
            $hasilValidasi = $this->analisisAI($prompt, '', false);
            $status = $this->parseValidationStatus($hasilValidasi);

            if ($status === 'ditemukan') {
                $totalValid++;
            }

            $hasilPerSection[$sectionName] = [
                'mode' => $mode,
                'status' => $status,
                'hasil' => $hasilValidasi,
                'checked_against' => $availableDocs,
                'snippet_found' => !empty($sectionSnippet['snippet']),
                'laporan_excerpt' => $laporanEvidence,
                'doc_excerpts' => $docExcerpts,
            ];

            sleep(1);
        }

        $laporanUtamaItem->update([
            'hasil_ai' => $this->buildLaporanUtamaInsightMarkdown($hasilPerSection),
        ]);

        $kesimpulanMarkdown = $this->buildFinalConclusion($hasilPerSection);
        $skor = $totalSection > 0 ? (int) round(($totalValid / $totalSection) * 100) : 0;
        $status = ($skor == 100) ? 'cocok' : 'tidak_cocok';

        foreach ($document->documentItems as $item) {
            if ($item->id == $laporanUtamaItem->id) {
                continue;
            }

            $kategori = strtolower(trim((string) $item->kategori));
            $persamaan = [];
            $perbedaan = [];

            foreach ($hasilPerSection as $sectionName => $result) {
                if (in_array($kategori, $result['checked_against'] ?? [])) {
                    $sectionLabel = ucwords(str_replace('_', ' ', (string) $sectionName));
                    $ringkas = trim((string) ($result['hasil'] ?? '-'));
                    $ringkas = mb_substr(preg_replace('/\s+/', ' ', $ringkas), 0, 280);
                    $laporanBanding = $this->shortText($result['laporan_excerpt'] ?? '-', 220);
                    $dokumenBanding = $this->shortText($result['doc_excerpts'][$kategori] ?? '-', 220);

                    if (($result['status'] ?? 'tidak_ditemukan') === 'ditemukan') {
                        $persamaan[] = [
                            'section' => $sectionLabel,
                            'note' => $ringkas,
                            'laporan' => $laporanBanding,
                            'dokumen' => $dokumenBanding,
                        ];
                    } else {
                        $perbedaan[] = [
                            'section' => $sectionLabel,
                            'note' => $ringkas,
                            'laporan' => $laporanBanding,
                            'dokumen' => $dokumenBanding,
                        ];
                    }
                }
            }

            if (!empty($persamaan) || !empty($perbedaan)) {
                $finalItemStatus = empty($perbedaan) ? 'ditemukan' : 'tidak_ditemukan';
                $detailItem = $this->buildItemDetailMarkdown($item->kategori, $persamaan, $perbedaan);

                $item->update([
                    'hasil_ai' => $detailItem,
                    'status_verifikasi' => $finalItemStatus,
                ]);
            } else {
                $item->update([
                    'hasil_ai' => "Dokumen tidak termasuk daftar 'check_against' pada konfigurasi section.",
                    'status_verifikasi' => 'pending',
                ]);
            }
        }

        $document->update([
            'kesimpulan' => $kesimpulanMarkdown,
            'skor'       => $skor,
            'status'     => $status,
        ]);

        Log::info('Section-based analysis completed', [
            'document_id' => $document->id,
            'score' => $skor,
            'total_sections' => $totalSection,
            'valid_sections' => $totalValid,
        ]);
    }

    private function normalizeText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = $this->removeIgnoredLines($text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim((string) $text);
    }

    private function removeIgnoredLines(string $text): string
    {
        $containsRules = config('document_rules.ignore_lines_if_contains', []);
        $regexRules = config('document_rules.ignore_lines_if_regex', []);

        if (empty($containsRules) && empty($regexRules)) {
            return $text;
        }

        $lines = preg_split('/\R/u', $text) ?: [];
        $filtered = [];

        foreach ($lines as $line) {
            $line = trim((string) $line);

            if ($line === '') {
                $filtered[] = $line;
                continue;
            }

            if ($this->shouldIgnoreLine($line, $containsRules, $regexRules)) {
                continue;
            }

            $filtered[] = $line;
        }

        return implode("\n", $filtered);
    }

    private function shouldIgnoreLine(string $line, array $containsRules, array $regexRules): bool
    {
        foreach ($containsRules as $needle) {
            $needle = trim((string) $needle);
            if ($needle !== '' && mb_stripos($line, $needle) !== false) {
                return true;
            }
        }

        foreach ($regexRules as $pattern) {
            $pattern = trim((string) $pattern);
            if ($pattern === '') {
                continue;
            }

            if (@preg_match($pattern, $line) === 1) {
                return true;
            }
        }

        return false;
    }

    private function extractSectionFromLaporan(string $sectionName, string $laporanText, array $keywords): array
    {
        $maxChars = (int) config('document_rules.max_snippet_chars', 3000);
        $fallbackParagraphs = (int) config('document_rules.fallback_paragraphs', 3);

        if (empty($keywords) || $laporanText === '') {
            return [
                'snippet' => mb_substr($laporanText, 0, $maxChars),
                'fallback' => true,
                'matched_keyword' => null,
            ];
        }

        $startPos = null;
        $matchedKeyword = null;

        foreach ($keywords as $keyword) {
            $pos = $this->findKeywordPosition($laporanText, (string) $keyword);
            if ($pos !== false && ($startPos === null || $pos < $startPos)) {
                $startPos = $pos;
                $matchedKeyword = $keyword;
            }
        }

        if ($startPos === null) {
            $paragraphs = preg_split('/\n\s*\n/u', $laporanText);
            $fallbackText = implode("\n\n", array_slice($paragraphs, 0, $fallbackParagraphs));
            return [
                'snippet' => mb_substr($fallbackText, 0, $maxChars),
                'fallback' => true,
                'matched_keyword' => null,
            ];
        }

        $allKeywords = $this->getAllSectionKeywords();
        $endPos = null;

        foreach ($allKeywords as $nextKeyword) {
            $nextPos = $this->findKeywordPosition($laporanText, (string) $nextKeyword, $startPos + 20);
            if ($nextPos !== false && ($endPos === null || $nextPos < $endPos)) {
                $endPos = $nextPos;
            }
        }

        if ($endPos === null || $endPos <= $startPos) {
            $snippet = mb_substr($laporanText, $startPos, $maxChars);
        } else {
            $snippet = mb_substr($laporanText, $startPos, min($endPos - $startPos, $maxChars));
        }

        return [
            'snippet' => trim($snippet),
            'fallback' => false,
            'matched_keyword' => $matchedKeyword,
        ];
    }

    private function getAllSectionKeywords(): array
    {
        $sections = config('document_rules.laporan_sections', []);
        $allKeywords = [];

        foreach ($sections as $sectionConfig) {
            if (!empty($sectionConfig['keywords']) && is_array($sectionConfig['keywords'])) {
                $allKeywords = array_merge($allKeywords, $sectionConfig['keywords']);
            }
        }

        $allKeywords = array_values(array_unique(array_filter($allKeywords)));
        usort($allKeywords, fn($a, $b) => strlen($b) <=> strlen($a));

        return $allKeywords;
    }

    private function buildSectionPrompt(string $sectionName, array $sectionData, string $dokumenPendukung, string $instruction, array $availableDocs): string
    {
        $sectionText = $sectionData['snippet'] ?? '';
        $fallbackInfo = !empty($sectionData['fallback'])
            ? "\nCATATAN: Keyword bagian '{$sectionName}' tidak ditemukan, menggunakan fallback paragraf awal.\n"
            : '';

        $docsStr = implode(', ', $availableDocs);

        return "
Peran: Auditor Dokumen Senior.
Tugas: Validasi bagian '{$sectionName}' dari LAPORAN UTAMA terhadap dokumen pendukung: {$docsStr}.

[INSTRUKSI]:
{$instruction}
{$fallbackInfo}

[BAGIAN '{$sectionName}' DARI LAPORAN UTAMA]:
{$sectionText}

[DOKUMEN PENDUKUNG]:
{$dokumenPendukung}

OUTPUT WAJIB:
1. Awali dengan [VALID] jika data sesuai, atau [INVALID] jika tidak sesuai/tidak ditemukan.
2. Sebutkan dokumen mana yang mendukung validasi (jika valid).
3. Maksimal 4 kalimat, ringkas dan jelas.
";
    }

    private function buildSectionPromptAiOnly(string $sectionName, array $sectionData, string $instruction): string
    {
        $sectionText = $sectionData['snippet'] ?? '';
        $fallbackInfo = !empty($sectionData['fallback'])
            ? "\nCATATAN: Keyword bagian '{$sectionName}' tidak ditemukan, menggunakan fallback paragraf awal.\n"
            : '';

        return "
Peran: Auditor Dokumen Senior.
Tugas: Analisis bagian '{$sectionName}' dari LAPORAN UTAMA tanpa pembanding dokumen lain.

[INSTRUKSI]:
{$instruction}
{$fallbackInfo}

[BAGIAN '{$sectionName}' DARI LAPORAN UTAMA]:
{$sectionText}

OUTPUT WAJIB:
1. Awali dengan [VALID] jika informasi inti tersedia dan konsisten dalam bagian ini, atau [INVALID] jika tidak memadai/bertentangan.
2. Sebutkan poin penting yang ditemukan dari bagian ini.
3. Maksimal 4 kalimat, ringkas dan jelas.
";
    }

    private function buildFinalConclusion(array $hasilPerSection): string
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

                if (!empty($result['laporan_excerpt']) && ($result['laporan_excerpt'] ?? '-') !== '-') {
                    $markdown .= "Cuplikan Laporan: " . $result['laporan_excerpt'] . "\n\n";
                }

                $markdown .= ($result['hasil'] ?? '-') . "\n\n";
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

                if (!empty($result['checked_against'])) {
                    $markdown .= "Dicek terhadap: " . implode(', ', $result['checked_against']) . "\n\n";
                }

                $markdown .= ($result['hasil'] ?? '-') . "\n\n";
                $markdown .= "---\n\n";
            }
        }

        if (empty($aiOnlyResults) && empty($compareResults)) {
            $markdown .= "Tidak ada hasil section yang dapat ditampilkan.\n\n";
        }

        return $markdown;
    }

    private function buildLaporanUtamaInsightMarkdown(array $hasilPerSection): string
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
            $hasil = trim((string) ($result['hasil'] ?? '-'));
            $excerpt = trim((string) ($result['laporan_excerpt'] ?? '-'));

            $lines[] = '#### ' . $sectionLabel . ' (' . $statusLabel . ')';
            if ($excerpt !== '' && $excerpt !== '-') {
                $lines[] = '- Cuplikan: ' . $this->shortText($excerpt, 260);
            }
            $lines[] = '- Insight: ' . ($hasil === '' ? '-' : $this->shortText($hasil, 500));
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    private function buildItemDetailMarkdown(string $kategori, array $persamaan, array $perbedaan): string
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

    private function parseValidationStatus(string $hasil): string
    {
        if (
            stripos($hasil, '[INVALID]') === 0 ||
            stripos($hasil, 'INVALID') === 0 ||
            stripos($hasil, '[TIDAK') !== false ||
            stripos($hasil, 'TIDAK SESUAI') !== false ||
            stripos($hasil, 'TIDAK COCOK') !== false
        ) {
            return 'tidak_ditemukan';
        }

        if (
            stripos($hasil, '[VALID]') !== false ||
            stripos($hasil, 'VALID') !== false ||
            stripos($hasil, 'SESUAI') !== false ||
            stripos($hasil, 'COCOK') !== false
        ) {
            return 'ditemukan';
        }

        return 'tidak_ditemukan';
    }

    private function extractTextFromImage(string $imageBase64, string $mimeType): string
    {
        $prompt = "Lakukan OCR pada gambar dokumen ini. Keluarkan teks mentah seakurat mungkin tanpa ringkasan.";
        $result = $this->analisisAI($prompt, $imageBase64, true, $mimeType);
        return $this->normalizeText($result);
    }

    private function extractTextFromPdfSmart(string $pdfPath): string
    {
        $text = $this->normalizeText((string) $this->ekstrakTeksPDF($pdfPath));
        if (!$this->isPdfTextInsufficient($text)) {
            return $text;
        }

        Log::info('PDF text is insufficient, trying vision OCR fallback', ['path' => $pdfPath]);
        $visionText = $this->extractTextFromPdfVision($pdfPath);
        return $this->normalizeText($visionText);
    }

    private function isPdfTextInsufficient(string $text): bool
    {
        $text = trim($text);
        if ($text === '') {
            return true;
        }

        if (stripos($text, 'Gagal mengekstrak teks PDF:') === 0) {
            return true;
        }

        $minChars = (int) env('PDF_TEXT_MIN_CHARS', 120);
        return mb_strlen($text) < $minChars;
    }

    private function extractTextFromPdfVision(string $pdfPath): string
    {
        $images = $this->convertPdfToImages($pdfPath);
        if (empty($images)) {
            return '';
        }

        $maxPages = (int) env('PDF_VISION_MAX_PAGES', 3);
        $ocrChunks = [];
        $page = 0;

        foreach ($images as $imagePath) {
            if ($page >= $maxPages) {
                break;
            }

            $page++;
            if (!is_file($imagePath)) {
                continue;
            }

            $base64 = base64_encode((string) file_get_contents($imagePath));
            $ocr = $this->extractTextFromImage($base64, 'image/jpeg');
            if (trim($ocr) !== '') {
                $ocrChunks[] = "[Halaman {$page}]\n" . $ocr;
            }
        }

        return implode("\n\n", $ocrChunks);
    }

    private function convertPdfToImages(string $pdfPath): array
    {
        $images = $this->convertPdfToImagesWithImagick($pdfPath);
        if (!empty($images)) {
            return $images;
        }

        return $this->convertPdfToImagesWithPoppler($pdfPath);
    }

    private function convertPdfToImagesWithImagick(string $pdfPath): array
    {
        if (!extension_loaded('imagick') || !class_exists('Imagick')) {
            return [];
        }

        $maxPages = (int) env('PDF_VISION_MAX_PAGES', 3);
        $dpi = (int) env('PDF_VISION_DPI', 150);
        $outputDir = storage_path('app/tmp/pdf_vision_' . uniqid());

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $result = [];
        try {
            $imagick = new \Imagick();
            $imagick->setResolution($dpi, $dpi);
            $imagick->readImage($pdfPath);

            $index = 0;
            foreach ($imagick as $page) {
                if ($index >= $maxPages) {
                    break;
                }

                $index++;
                $page->setImageFormat('jpeg');
                $page->setImageCompressionQuality(85);
                $out = $outputDir . DIRECTORY_SEPARATOR . 'page-' . $index . '.jpg';
                $page->writeImage($out);
                $result[] = $out;
            }

            $imagick->clear();
            $imagick->destroy();
        } catch (\Exception $e) {
            Log::warning('Imagick PDF to image failed', ['error' => $e->getMessage()]);
            return [];
        }

        return $result;
    }

    private function convertPdfToImagesWithPoppler(string $pdfPath): array
    {
        if (!$this->isCommandAvailable('pdftoppm')) {
            return [];
        }

        $dpi = (int) env('PDF_VISION_DPI', 150);
        $outputDir = storage_path('app/tmp/pdf_vision_' . uniqid());
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $prefix = $outputDir . DIRECTORY_SEPARATOR . 'page';
        $cmd = 'pdftoppm -jpeg -r ' . (int) $dpi . ' ' . $this->escapeShellArg($pdfPath) . ' ' . $this->escapeShellArg($prefix);

        $output = [];
        $exitCode = 0;
        exec($cmd, $output, $exitCode);
        if ($exitCode !== 0) {
            Log::warning('Poppler PDF to image failed', ['exit_code' => $exitCode]);
            return [];
        }

        $files = glob($prefix . '-*.jpg') ?: [];
        sort($files);
        return $files;
    }

    private function isCommandAvailable(string $command): bool
    {
        $probe = strtoupper(PHP_OS_FAMILY) === 'WINDOWS'
            ? 'where ' . $command . ' 2>NUL'
            : 'command -v ' . $command . ' 2>/dev/null';

        $result = shell_exec($probe);
        return is_string($result) && trim($result) !== '';
    }

    private function escapeShellArg(string $value): string
    {
        if (strtoupper(PHP_OS_FAMILY) === 'WINDOWS') {
            return '"' . str_replace('"', '""', $value) . '"';
        }

        return escapeshellarg($value);
    }

    private function extractEvidenceSnippet(string $text, array $keywords, int $maxLen = 240): string
    {
        $clean = $this->normalizeText($text);
        if ($clean === '') {
            return '-';
        }

        foreach ($keywords as $keyword) {
            $keyword = trim((string) $keyword);
            if ($keyword === '') {
                continue;
            }

            $pos = $this->findKeywordPosition($clean, $keyword);
            if ($pos !== false) {
                $start = max(0, $pos - 40);
                return $this->shortText(mb_substr($clean, $start, $maxLen), $maxLen);
            }
        }

        return $this->shortText($clean, $maxLen);
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

    private function findKeywordPosition(string $text, string $keyword, int $fromCharOffset = 0): int|false
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return false;
        }

        $slice = $fromCharOffset > 0 ? mb_substr($text, $fromCharOffset) : $text;
        if ($slice === '') {
            return false;
        }

        $parts = preg_split('/\s+/u', $keyword) ?: [];
        $parts = array_values(array_filter(array_map(static fn($part) => trim((string) $part), $parts)));
        if (empty($parts)) {
            return false;
        }

        $pattern = '/(?<![\p{L}\p{N}])' . implode('\\s+', array_map(static fn($part) => preg_quote($part, '/'), $parts)) . '(?![\p{L}\p{N}])/iu';
        if (!preg_match($pattern, $slice, $matches, PREG_OFFSET_CAPTURE)) {
            return false;
        }

        $bytePos = $matches[0][1] ?? null;
        if (!is_int($bytePos) || $bytePos < 0) {
            return false;
        }

        $charPosInSlice = mb_strlen(substr($slice, 0, $bytePos), 'UTF-8');
        return $fromCharOffset + $charPosInSlice;
    }

//     private function normalizeText(string $text): string
//     {
//         $text = str_replace(["\r\n", "\r"], "\n", $text);
//         $text = preg_replace('/[ \t]+/', ' ', $text);
//         $text = preg_replace('/\n{3,}/', "\n\n", $text);
//         return trim($text);
//     }

//     private function extractSectionFromLaporan(string $sectionName, string $laporanText, array $keywords): array
//     {
//         $maxChars = config('document_rules.max_snippet_chars', 3000);
//         $fallbackParagraphs = config('document_rules.fallback_paragraphs', 3);

//         if (empty($keywords) || empty($laporanText)) {
//             return [
//                 'snippet' => mb_substr($laporanText, 0, $maxChars),
//                 'fallback' => true,
//                 'matched_keyword' => null
//             ];
//         }

//         $lowerText = mb_strtolower($laporanText);
//         $startPos = null;
//         $matchedKeyword = null;

//         // Cari keyword pertama yang cocok
//         foreach ($keywords as $keyword) {
//             $pos = mb_stripos($lowerText, mb_strtolower($keyword));
//             if ($pos !== false && ($startPos === null || $pos < $startPos)) {
//                 $startPos = $pos;
//                 $matchedKeyword = $keyword;
//             }
//         }

//         if ($startPos === null) {
//             // Fallback: ambil paragraf awal
//             $paragraphs = preg_split('/\n\s*\n/u', $laporanText);
//             $fallbackText = implode("\n\n", array_slice($paragraphs, 0, $fallbackParagraphs));

//             return [
//                 'snippet' => mb_substr($fallbackText, 0, $maxChars),
//                 'fallback' => true,
//                 'matched_keyword' => null
//             ];
//         }

//         // Cari end position (keyword section berikutnya)
//         $allKeywords = $this->getAllSectionKeywords();
//         $endPos = null;

//         foreach ($allKeywords as $nextKeyword) {
//             $nextPos = mb_stripos($lowerText, mb_strtolower($nextKeyword), $startPos + 20);
//             if ($nextPos !== false && ($endPos === null || $nextPos < $endPos)) {
//                 $endPos = $nextPos;
//             }
//         }

//         if ($endPos === null || $endPos <= $startPos) {
//             $snippet = mb_substr($laporanText, $startPos, $maxChars);
//         } else {
//             $snippet = mb_substr($laporanText, $startPos, min($endPos - $startPos, $maxChars));
//         }

//         return [
//             'snippet' => trim($snippet),
//             'fallback' => false,
//             'matched_keyword' => $matchedKeyword
//         ];
//     }

//     private function getAllSectionKeywords(): array
//     {
//         $sections = config('document_rules.laporan_sections', []);
//         $allKeywords = [];

//         foreach ($sections as $sectionConfig) {
//             if (!empty($sectionConfig['keywords']) && is_array($sectionConfig['keywords'])) {
//                 $allKeywords = array_merge($allKeywords, $sectionConfig['keywords']);
//             }
//         }

//         $allKeywords = array_values(array_unique(array_filter($allKeywords)));
//         usort($allKeywords, fn($a, $b) => strlen($b) <=> strlen($a));

//         return $allKeywords;
//     }

//     private function buildSectionPrompt(string $sectionName, array $sectionData, string $dokumenPendukung, string $instruction, array $availableDocs): string
//     {
//         $sectionText = $sectionData['snippet'];
//         $fallbackInfo = $sectionData['fallback']
//             ? "\n⚠️ CATATAN: Keyword bagian '{$sectionName}' tidak ditemukan, menggunakan fallback paragraf awal.\n"
//             : "";

//         $docsStr = implode(', ', $availableDocs);

//         return "
// Peran: Auditor Dokumen Senior.
// Tugas: Validasi bagian '{$sectionName}' dari LAPORAN UTAMA terhadap dokumen pendukung: {$docsStr}.

// [INSTRUKSI]:
// {$instruction}
// {$fallbackInfo}

// [BAGIAN '{$sectionName}' DARI LAPORAN UTAMA]:
// {$sectionText}

// [DOKUMEN PENDUKUNG]:
// {$dokumenPendukung}

// OUTPUT WAJIB:
// 1. Awali dengan [VALID] jika data sesuai, atau [INVALID] jika tidak sesuai/tidak ditemukan.
// 2. Sebutkan dokumen mana yang mendukung validasi (jika valid).
// 3. Maksimal 3 kalimat, ringkas dan jelas.
// ";
//     }

//     private function buildFinalConclusion(array $hasilPerSection): string
//     {
//         $markdown = "# Hasil Validasi Laporan Utama\n\n";
//         $markdown .= "Validasi dilakukan **per bagian** laporan utama terhadap dokumen pendukung yang relevan.\n\n";
//         $markdown .= "---\n\n";

//         foreach ($hasilPerSection as $sectionName => $result) {
//             $statusBadge = $result['status'] === 'ditemukan' ? '✅ VALID' : '❌ INVALID';
//             $markdown .= "## {$statusBadge} - " . ucwords(str_replace('_', ' ', $sectionName)) . "\n\n";

//             if (!empty($result['checked_against'])) {
//                 $markdown .= "**Dicek terhadap:** " . implode(', ', $result['checked_against']) . "\n\n";
//             }

//             $markdown .= $result['hasil'] . "\n\n";
//             $markdown .= "---\n\n";
//         }

//         return $markdown;
//     }

    /**
     * Ekstrak teks dari file (PDF atau text)
     */
    private function ekstrakTeksPDF($path)
    {
        try {
            $parser = new Parser();
            $pdf    = $parser->parseFile($path);
            return $pdf->getText();
        } catch (\Exception $e) {
            return "Gagal mengekstrak teks PDF: " . $e->getMessage();
        }
    }

    /**
     * Analisis teks menggunakan Gemini AI
     */
    private function geminiAI($promptText, $imageData = "", $isImage = false, $mimeType = '')
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured');
        }
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/Gemini 2.5 Flash:generateContent?key={$apiKey}";


        $parts = [];
        if ($isImage) {
            $parts = [
                ["text" => $promptText],
                ["inline_data" => ["mime_type" => $mimeType, "data" => $imageData]]
            ];
        } else {
            $parts = [["text" => $promptText]];
        }

        try {
            $response = Http::timeout(120)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($apiUrl, [
                    "contents" => [["parts" => $parts]],
                    "generationConfig" => [
                        "temperature" => 0.3,
                        "maxOutputTokens" => 2000
                        // HAPUS responseMimeType JSON agar dia bebas ngomong
                    ]
                ]);

            if ($response->failed()) return "Error API: " . $response->body();

            // Langsung ambil teksnya, gak perlu json_decode aneh-aneh
            return $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada respon.';
        } catch (\Exception $e) {
            return "Exception: " . $e->getMessage();
        }
    }
    private function analisisAI($promptFinal, $imageData = "", $isImage = false, $mimeType = '')
    {
        //return $this->analisisAILMStudio($promptFinal, $imageData, $isImage, $mimeType);
        //return $this->geminiAI($promptFinal, $imageData, $isImage, $mimeType);
        return $this->analisisOpen($promptFinal, $imageData, $isImage, $mimeType);
    }

    private function analisisAILMStudio($promptFinal, $imageData = "", $isImage = false, $mimeType = '')
    {
        $apiKey = env('LM_STUDIO_API_KEY', 'lm-studio');
        $baseUrl = rtrim(env('LM_STUDIO_BASE_URL', 'http://127.0.0.1:1234'), '/');
        $apiUrl = $baseUrl . '/v1/chat/completions';
        $timeoutSeconds = (int) env('LM_STUDIO_TIMEOUT', 120);

        $messages = [];
        if ($isImage) {
            $mime = $mimeType ?: 'image/jpeg';
            $messages = [[
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $promptFinal,
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => "data:{$mime};base64,{$imageData}",
                        ],
                    ],
                ],
            ]];
        } else {
            $messages = [
                ['role' => 'user', 'content' => $promptFinal],
            ];
        }

        try {
            $response = Http::connectTimeout(15)
                ->timeout($timeoutSeconds)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post($apiUrl, [
                    'model' => env('LM_STUDIO_MODEL', 'qwen/qwen2.5-vl-7b'),
                    'messages' => $messages,
                    'temperature' => 0.1,
                    'max_tokens' => 2000,
                ]);

            if ($response->failed()) {
                $error = 'Error LM Studio: ' . $response->status() . ' - ' . $response->body();
                Log::error('LM Studio API Error', ['error' => $error]);
                return $error;
            }

            return $response['choices'][0]['message']['content'] ?? 'Tidak ada respon.';
        } catch (\Exception $e) {
            $error = 'Exception LM Studio: ' . $e->getMessage();
            Log::error('LM Studio Exception', ['error' => $error, 'trace' => $e->getTraceAsString()]);
            return $error;
        }
    }
    private function analisisOpen($promptFinal, $imageData = "", $isImage = false, $mimeType = '')
    {
        $apiKey = env('OPENROUTER_API_KEY');
        $apiKey = "lm-studio";

        $apiUrl = "http://127.0.0.1:1234";

        if (!$apiKey) {
            throw new \Exception('OpenRouter API key not configured');
        }
        // $apiUrl = "https://openrouter.ai/api/v1/chat/completions";

        $messages = [];
        if ($isImage) {
            $messages = [
                ["role" => "user", "content" => $promptFinal],
                ["role" => "user", "content" => [
                    "type" => "image_base64",
                    "data" => $imageData,
                    "mime_type" => $mimeType
                ]]
            ];
        } else {
            $messages = [
                ["role" => "user", "content" => $promptFinal]
            ];
        }

        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey
                ])
                ->post($apiUrl, [
                    "model" => "arcee-ai/trinity-large-preview:free",
                    "messages" => $messages,
                    "temperature" => 0.2,
                    "max_tokens" => 5000
                ]);

            if ($response->failed()) {
                $error = "Error API: " . $response->status() . " - " . $response->body();
                Log::error('OpenRouter API Error', ['error' => $error]);
                return $error;
            }

            $content = $response['choices'][0]['message']['content'] ?? 'Tidak ada respon.';
            return $content;
        } catch (\Exception $e) {
            $error = "Exception: " . $e->getMessage();
            Log::error('OpenRouter Exception', ['error' => $error, 'trace' => $e->getTraceAsString()]);
            return $error;
        }
    }
}
