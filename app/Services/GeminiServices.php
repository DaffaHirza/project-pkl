<?php

namespace App\Services;

use App\Models\Document;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiServices
{
    /**
     * Proses seluruh dokumen dengan ekstraksi dan analisis AI
     * Strategi baru: pecah LAPORAN UTAMA per bagian, lalu tiap bagian dicek ke dokumen pendukung tertentu
     */
    public function prosesDokumen(Document $document)
    {
        Log::info('Starting section-based document analysis', ['document_id' => $document->id]);

        // 1. Extract Laporan Utama
        $laporanUtamaItem = $document->documentItems()
            ->where('kategori', 'LIKE', '%laporan_utama%')
            ->first();

        if (!$laporanUtamaItem) {
            $document->update(['kesimpulan' => 'Error: Laporan Utama tidak ditemukan.']);
            return;
        }

        $pathLaporan = storage_path('app/public/' . $laporanUtamaItem->path_file);
        $teksLaporanUtama = $this->ekstrakTeksPDF($pathLaporan);
        
        if (!$teksLaporanUtama) {
            $document->update(['kesimpulan' => 'Error: Gagal mengekstrak teks dari Laporan Utama atau Laporan utama harus PDF.']);
            return;
        }

        $teksLaporanUtama = $this->normalizeText($teksLaporanUtama);

        // 2. Extract semua dokumen pendukung ke map: kategori => teks
        $dokumenPendukung = [];
        foreach ($document->documentItems as $item) {
            if ($item->id == $laporanUtamaItem->id) continue;

            $path = storage_path('app/public/' . $item->path_file);
            $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $kategori = strtolower(trim($item->kategori));

            if ($ext == 'pdf') {
                $dokumenPendukung[$kategori] = $this->normalizeText($this->ekstrakTeksPDF($path));
            } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                // Untuk gambar, simpan metadata saja untuk saat ini
                $dokumenPendukung[$kategori] = "[GAMBAR: {$item->nama_file}]";
            }

            // Update status dokumen pendukung
            $item->update([
                'hasil_ai' => 'Dokumen pendukung berhasil diekstrak.',
                'status_verifikasi' => 'pending'
            ]);
        }

        // 3. Loop per section dari laporan utama
        $sections = config('document_rules.laporan_sections', []);
        $hasilPerSection = [];
        $totalValid = 0;
        $totalSection = count($sections);

        foreach ($sections as $sectionName => $sectionConfig) {
            $keywords = $sectionConfig['keywords'] ?? [];
            $checkAgainst = $sectionConfig['check_against'] ?? [];
            $instruction = $sectionConfig['instruction'] ?? '';

            // 3a. Extract bagian dari laporan utama
            $sectionSnippet = $this->extractSectionFromLaporan($sectionName, $teksLaporanUtama, $keywords);

            // 3b. Ambil teks dokumen pendukung yang relevan
            $relevantDocs = '';
            $availableDocs = [];
            foreach ($checkAgainst as $kategoriTarget) {
                if (isset($dokumenPendukung[$kategoriTarget])) {
                    $relevantDocs .= "\n\n[{$kategoriTarget}]:\n" . mb_substr($dokumenPendukung[$kategoriTarget], 0, 2000);
                    $availableDocs[] = $kategoriTarget;
                }
            }

            if (empty($relevantDocs)) {
                $hasilPerSection[$sectionName] = [
                    'status' => 'tidak_ditemukan',
                    'hasil' => "[SKIP] Dokumen pendukung tidak tersedia untuk bagian '{$sectionName}'.",
                    'found_in' => []
                ];
                continue;
            }

            // 3c. Validasi section laporan vs dokumen pendukung
            $prompt = $this->buildSectionPrompt($sectionName, $sectionSnippet, $relevantDocs, $instruction, $availableDocs);
            $hasilValidasi = $this->analisisAI($prompt, "", false);

            // Parse status
            $status = 'tidak_ditemukan';
            if (stripos($hasilValidasi, '[VALID]') !== false || stripos($hasilValidasi, 'SESUAI') !== false) {
                $status = 'ditemukan';
                $totalValid++;
            }

            $hasilPerSection[$sectionName] = [
                'status' => $status,
                'hasil' => $hasilValidasi,
                'checked_against' => $availableDocs,
                'snippet_found' => !empty($sectionSnippet['snippet'])
            ];

            Log::info("Section validated", [
                'section' => $sectionName,
                'status' => $status,
                'docs' => $availableDocs
            ]);

            sleep(2); // Rate limiting
        }

        // 4. Buat kesimpulan final
        $kesimpulanMarkdown = $this->buildFinalConclusion($hasilPerSection);

        // Hitung skor berdasarkan jumlah section yang valid
        $skor = $totalSection > 0 ? round(($totalValid / $totalSection) * 100) : 0;
        $status = ($skor == 100) ? 'cocok' : 'tidak_cocok';

        // Update status dokumen pendukung berdasarkan kontribusinya
        foreach ($document->documentItems as $item) {
            if ($item->id == $laporanUtamaItem->id) continue;
            
            $kategori = strtolower(trim($item->kategori));
            $kontribusi = [];
            
            foreach ($hasilPerSection as $sectionName => $result) {
                if (in_array($kategori, $result['checked_against'] ?? [])) {
                    $kontribusi[] = "{$sectionName} ({$result['status']})";
                }
            }

            if (!empty($kontribusi)) {
                $item->update([
                    'hasil_ai' => 'Digunakan untuk validasi: ' . implode(', ', $kontribusi),
                    'status_verifikasi' => 'ditemukan'
                ]);
            }
        }

        // Update laporan utama
        $laporanUtamaItem->update([
            'hasil_ai' => 'Dokumen acuan utama - divalidasi per bagian.',
            'status_verifikasi' => 'ditemukan'
        ]);

        $document->update([
            'kesimpulan' => $kesimpulanMarkdown,
            'skor'       => $skor,
            'status'     => $status
        ]);

        Log::info('Section-based analysis completed', [
            'document_id' => $document->id,
            'score' => $skor,
            'total_sections' => $totalSection,
            'valid_sections' => $totalValid
        ]);
    }

    private function normalizeText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }

    private function extractSectionFromLaporan(string $sectionName, string $laporanText, array $keywords): array
    {
        $maxChars = config('document_rules.max_snippet_chars', 3000);
        $fallbackParagraphs = config('document_rules.fallback_paragraphs', 3);

        if (empty($keywords) || empty($laporanText)) {
            return [
                'snippet' => mb_substr($laporanText, 0, $maxChars),
                'fallback' => true,
                'matched_keyword' => null
            ];
        }

        $lowerText = mb_strtolower($laporanText);
        $startPos = null;
        $matchedKeyword = null;

        // Cari keyword pertama yang cocok
        foreach ($keywords as $keyword) {
            $pos = mb_stripos($lowerText, mb_strtolower($keyword));
            if ($pos !== false && ($startPos === null || $pos < $startPos)) {
                $startPos = $pos;
                $matchedKeyword = $keyword;
            }
        }

        if ($startPos === null) {
            // Fallback: ambil paragraf awal
            $paragraphs = preg_split('/\n\s*\n/u', $laporanText);
            $fallbackText = implode("\n\n", array_slice($paragraphs, 0, $fallbackParagraphs));

            return [
                'snippet' => mb_substr($fallbackText, 0, $maxChars),
                'fallback' => true,
                'matched_keyword' => null
            ];
        }

        // Cari end position (keyword section berikutnya)
        $allKeywords = $this->getAllSectionKeywords();
        $endPos = null;

        foreach ($allKeywords as $nextKeyword) {
            $nextPos = mb_stripos($lowerText, mb_strtolower($nextKeyword), $startPos + 20);
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
            'matched_keyword' => $matchedKeyword
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
        $sectionText = $sectionData['snippet'];
        $fallbackInfo = $sectionData['fallback'] 
            ? "\n⚠️ CATATAN: Keyword bagian '{$sectionName}' tidak ditemukan, menggunakan fallback paragraf awal.\n" 
            : "";

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
3. Maksimal 3 kalimat, ringkas dan jelas.
";
    }

    private function buildFinalConclusion(array $hasilPerSection): string
    {
        $markdown = "# Hasil Validasi Laporan Utama\n\n";
        $markdown .= "Validasi dilakukan **per bagian** laporan utama terhadap dokumen pendukung yang relevan.\n\n";
        $markdown .= "---\n\n";

        foreach ($hasilPerSection as $sectionName => $result) {
            $statusBadge = $result['status'] === 'ditemukan' ? '✅ VALID' : '❌ INVALID';
            $markdown .= "## {$statusBadge} - " . ucwords(str_replace('_', ' ', $sectionName)) . "\n\n";
            
            if (!empty($result['checked_against'])) {
                $markdown .= "**Dicek terhadap:** " . implode(', ', $result['checked_against']) . "\n\n";
            }

            $markdown .= $result['hasil'] . "\n\n";
            $markdown .= "---\n\n";
        }

        return $markdown;
    }

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
    // private function analisisAI($promptText, $imageData = "", $isImage = false, $mimeType = '')
    // {
    //     $apiKey = env('GEMINI_API_KEY');
    //     if (!$apiKey) {
    //         throw new \Exception('Gemini API key not configured');
    //     }
    //     $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";


    //     $parts = [];
    //     if ($isImage) {
    //         $parts = [
    //             ["text" => $promptText],
    //             ["inline_data" => ["mime_type" => $mimeType, "data" => $imageData]]
    //         ];
    //     } else {
    //         $parts = [["text" => $promptText]];
    //     }

    //     try {
    //         $response = Http::timeout(120)
    //             ->withHeaders(['Content-Type' => 'application/json'])
    //             ->post($apiUrl, [
    //                 "contents" => [["parts" => $parts]],
    //                 "generationConfig" => [
    //                     "temperature" => 0.3,
    //                     "maxOutputTokens" => 2000
    //                     // HAPUS responseMimeType JSON agar dia bebas ngomong
    //                 ]
    //             ]);

    //         if ($response->failed()) return "Error API: " . $response->body();

    //         // Langsung ambil teksnya, gak perlu json_decode aneh-aneh
    //         return $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada respon.';
    //     } catch (\Exception $e) {
    //         return "Exception: " . $e->getMessage();
    //     }
    // }
    private function analisisAI($promptFinal, $imageData = "", $isImage = false, $mimeType = '')
    {
        $apiKey = env('OPENROUTER_API_KEY');
        if (!$apiKey) {
            throw new \Exception('OpenRouter API key not configured');
        }
        $apiUrl = "https://openrouter.ai/api/v1/chat/completions";

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
                    "model" => "meta-llama/llama-3.3-70b-instruct:free",
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
