<?php

namespace App\Services;

use App\Models\AssistantDocument;
use App\Services\AI\AIClientService;
use App\Services\AI\DocumentTextExtractor;
use App\Services\AI\SectionAnalyzer;
use App\Services\AI\ValidationResultFormatter;
use Illuminate\Support\Facades\Log;

class AIServices
{
    public function __construct(
        private AIClientService $aiClient,
        private DocumentTextExtractor $textExtractor,
        private SectionAnalyzer $sectionAnalyzer,
        private ValidationResultFormatter $resultFormatter
    ) {}

    /**
     * Proses seluruh dokumen dengan ekstraksi dan analisis AI.
     * Strategi: pecah Laporan Utama per bagian, lalu tiap bagian divalidasi sesuai rules.
     */
    public function prosesDokumen(AssistantDocument $document): void
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
        $teksLaporanUtama = $this->textExtractor->extractTextFromPdfSmart($pathLaporan);
        if (trim($teksLaporanUtama) === '') {
            $document->update(['kesimpulan' => 'Error: Gagal mengekstrak teks dari Laporan Utama atau Laporan utama harus PDF.']);
            return;
        }

        $teksLaporanUtama = $this->textExtractor->normalizeText($teksLaporanUtama);

        $laporanUtamaItem->update([
            'hasil_ai' => 'Dokumen acuan utama - divalidasi per bagian.',
            'status_verifikasi' => 'ditemukan',
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
                $teksItem = $this->textExtractor->extractTextFromPdfSmart($path);
            } elseif (in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                if (!file_exists($path)) {
                    $item->update([
                        'hasil_ai' => 'Error: File gambar tidak ditemukan.',
                        'status_verifikasi' => 'tidak_ditemukan',
                    ]);
                    continue;
                }

                $mimeType = $ext === 'png' ? 'image/png' : 'image/jpeg';
                $teksItem = $this->textExtractor->extractTextFromImage(
                    base64_encode((string) file_get_contents($path)),
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

            $sectionSnippet = $this->sectionAnalyzer->extractSectionFromLaporan($sectionName, $teksLaporanUtama, $keywords);
            $laporanEvidence = $this->sectionAnalyzer->extractEvidenceSnippet($sectionSnippet['snippet'] ?? '', $keywords, 260);

            $relevantDocs = '';
            $availableDocs = [];
            $docExcerpts = [];
            foreach ($checkAgainst as $kategoriTarget) {
                if (isset($dokumenPendukung[$kategoriTarget])) {
                    $relevantDocs .= "\n\n[{$kategoriTarget}]:\n" . mb_substr($dokumenPendukung[$kategoriTarget], 0, 2000);
                    $availableDocs[] = $kategoriTarget;
                    $docExcerpts[$kategoriTarget] = $this->sectionAnalyzer->extractEvidenceSnippet($dokumenPendukung[$kategoriTarget], $keywords, 260);
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
                ? $this->sectionAnalyzer->buildSectionPromptAiOnly($sectionName, $sectionSnippet, $instruction)
                : $this->sectionAnalyzer->buildSectionPrompt($sectionName, $sectionSnippet, $relevantDocs, $instruction, $availableDocs);

            $hasilValidasi = $this->aiClient->analyze($prompt);
            $status = $this->sectionAnalyzer->parseValidationStatus($hasilValidasi);

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
            'hasil_ai' => $this->resultFormatter->buildLaporanUtamaInsightMarkdown($hasilPerSection),
        ]);

        $kesimpulanMarkdown = $this->resultFormatter->buildFinalConclusion($hasilPerSection);
        $skor = $totalSection > 0 ? (int) round(($totalValid / $totalSection) * 100) : 0;
        $status = $skor === 100 ? 'cocok' : 'tidak_cocok';

        foreach ($document->documentItems as $item) {
            if ($item->id == $laporanUtamaItem->id) {
                continue;
            }

            $kategori = strtolower(trim((string) $item->kategori));
            $persamaan = [];
            $perbedaan = [];

            foreach ($hasilPerSection as $sectionName => $result) {
                if (in_array($kategori, $result['checked_against'] ?? [], true)) {
                    $sectionLabel = ucwords(str_replace('_', ' ', (string) $sectionName));
                    $ringkas = trim((string) ($result['hasil'] ?? '-'));
                    $ringkas = mb_substr((string) preg_replace('/\s+/', ' ', $ringkas), 0, 280);
                    $laporanBanding = $this->sectionAnalyzer->shortText($result['laporan_excerpt'] ?? '-', 220);
                    $dokumenBanding = $this->sectionAnalyzer->shortText($result['doc_excerpts'][$kategori] ?? '-', 220);

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
                $detailItem = $this->resultFormatter->buildItemDetailMarkdown($item->kategori, $persamaan, $perbedaan);

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
            'skor' => $skor,
            'status' => $status,
        ]);

        Log::info('Section-based analysis completed', [
            'document_id' => $document->id,
            'score' => $skor,
            'total_sections' => $totalSection,
            'valid_sections' => $totalValid,
        ]);
    }
}
