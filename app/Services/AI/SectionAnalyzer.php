<?php

namespace App\Services\AI;

class SectionAnalyzer
{
    public function parseAiResponse(string $raw): array
    {
        $clean = trim($raw);

        // Bersihkan markdown fence seperti ```json ... ``` jika ada.
        if (preg_match('/^\s*```(?:json)?\s*(.*?)\s*```\s*$/is', $clean, $matches)) {
            $clean = trim((string) ($matches[1] ?? ''));
        }

        // Fallback jika model mengirim awalan "json" tanpa fence.
        $clean = preg_replace('/^json\s*/i', '', $clean) ?? $clean;

        $decoded = json_decode($clean, true);
        if (is_array($decoded)) {
            $statusRaw = strtoupper(trim((string) ($decoded['status'] ?? '')));
            $status = in_array($statusRaw, ['VALID', 'TIDAK VALID'], true) ? $statusRaw : 'TIDAK VALID';

            $catatan = trim((string) ($decoded['catatan'] ?? ''));
            if ($catatan === '') {
                $catatan = '-';
            }

            $anomali = $decoded['anomali_ditemukan'] ?? [];
            if (is_string($anomali)) {
                $anomali = [trim($anomali)];
            }

            if (!is_array($anomali)) {
                $anomali = [];
            }

            $anomali = array_values(array_filter(array_map(
                static fn($item) => trim((string) $item),
                $anomali
            )));

            return [
                'status' => $status,
                'catatan' => $catatan,
                'anomali_ditemukan' => $anomali,
                'data_ekstraksi' => is_array($decoded['data_ekstraksi'] ?? null) ? $decoded['data_ekstraksi'] : [],
                'raw_cleaned' => $clean,
            ];
        }

        $status = 'TIDAK VALID';
        if (preg_match('/\bTIDAK\s+VALID\b/i', $clean)) {
            $status = 'TIDAK VALID';
        } elseif (preg_match('/\bVALID\b/i', $clean)) {
            $status = 'VALID';
        }

        return [
            'status' => $status,
            'catatan' => $clean === '' ? '-' : $this->shortText($clean, 800),
            'anomali_ditemukan' => [],
            'data_ekstraksi' => [],
            'raw_cleaned' => $clean,
        ];
    }

    public function extractSectionFromLaporan(string $sectionName, string $laporanText, array $keywords): array
    {
        $maxChars = (int) config('document_rules.max_snippet_chars', 3000);
        $fallbackParagraphs = (int) config('document_rules.fallback_paragraphs', 3);
        $minCharsAfterTitle = (int) config('document_rules.min_section_chars_after_title', 800);
        if ($minCharsAfterTitle <= 0) {
            $minCharsAfterTitle = 800;
        }

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
        $currentSectionKeywords = array_fill_keys(
            array_values(array_filter(array_map(
                static fn($keyword) => mb_strtolower(trim((string) $keyword)),
                $keywords
            ))),
            true
        );
        $endPos = null;

        foreach ($allKeywords as $nextKeyword) {
            $nextKeywordNorm = mb_strtolower(trim((string) $nextKeyword));
            if ($nextKeywordNorm !== '' && isset($currentSectionKeywords[$nextKeywordNorm])) {
                continue;
            }

            $nextPos = $this->findKeywordPosition($laporanText, (string) $nextKeyword, $startPos + 20);
            if ($nextPos !== false && $nextPos <= ($startPos + $minCharsAfterTitle)) {
                continue;
            }

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

    public function buildSectionPrompt(string $sectionName, array $sectionData, string $dokumenPendukung, string $instruction, array $availableDocs): string
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

    public function buildSectionPromptAiOnly(string $sectionName, array $sectionData, string $instruction): string
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

    public function parseValidationStatus(string $hasil): string
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

    public function extractEvidenceSnippet(string $text, array $keywords, int $maxLen = 240): string
    {
        $clean = preg_replace('/\s+/', ' ', trim($text));
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

    public function shortText(string $text, int $maxLen = 220): string
    {
        $text = preg_replace('/\s+/', ' ', trim($text));
        if ($text === '') {
            return '-';
        }

        return mb_strlen($text) > $maxLen
            ? mb_substr($text, 0, $maxLen) . '...'
            : $text;
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
}
