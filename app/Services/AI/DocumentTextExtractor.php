<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class DocumentTextExtractor
{
    public function __construct(private AIClientService $aiClient) {}

    public function normalizeText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = str_replace('"5', '"S', $text);
        $text = preg_replace('/([\"\'“”])5(?=[A-Za-z])/u', '$1S', (string) $text);
        $text = $this->removeIgnoredLines($text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim((string) $text);
    }

    public function extractTextFromImage(string $imageBase64, string $mimeType): string
    {
        $prompt = 'Lakukan OCR pada gambar dokumen ini. Keluarkan teks mentah seakurat mungkin tanpa ringkasan.';
        $result = $this->aiClient->analyze($prompt, $imageBase64, true, $mimeType);

        return $this->normalizeText($result);
    }

    public function extractTextFromPdfSmart(string $pdfPath): string
    {
        $text = $this->normalizeText((string) $this->extractPdfText($pdfPath));
        if (!$this->isPdfTextInsufficient($text)) {
            return $text;
        }

        Log::info('PDF text is insufficient, trying vision OCR fallback', ['path' => $pdfPath]);
        $visionText = $this->extractTextFromPdfVision($pdfPath);

        return $this->normalizeText($visionText);
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

    private function extractPdfText(string $path): string
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($path);

            return $pdf->getText();
        } catch (\Exception $e) {
            return 'Gagal mengekstrak teks PDF: ' . $e->getMessage();
        }
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

        try {
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
        } finally {
            $this->cleanupGeneratedImages($images);
        }
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

            $this->deleteDirectoryRecursive($outputDir);

            return [];
        }

        if (empty($result)) {
            $this->deleteDirectoryRecursive($outputDir);
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

            $this->deleteDirectoryRecursive($outputDir);

            return [];
        }

        $files = glob($prefix . '-*.jpg') ?: [];
        sort($files);

        if (empty($files)) {
            $this->deleteDirectoryRecursive($outputDir);
        }

        return $files;
    }

    private function cleanupGeneratedImages(array $images): void
    {
        if (empty($images)) {
            return;
        }

        $tempRoot = storage_path('app/tmp');
        $dirs = [];

        foreach ($images as $imagePath) {
            if (is_file($imagePath)) {
                @unlink($imagePath);
            }

            $dir = dirname((string) $imagePath);
            if ($dir === '' || isset($dirs[$dir])) {
                continue;
            }

            // Safety guard: only remove directories under storage/app/tmp.
            if (str_starts_with($dir, $tempRoot . DIRECTORY_SEPARATOR)) {
                $dirs[$dir] = true;
            }
        }

        foreach (array_keys($dirs) as $dir) {
            $this->deleteDirectoryRecursive($dir);
        }
    }

    private function deleteDirectoryRecursive(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectoryRecursive($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
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
}
