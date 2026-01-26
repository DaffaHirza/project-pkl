<?php

namespace App\Services;

use App\Models\Document;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;

class GeminiServices
{
    /**
     * Proses seluruh dokumen dengan ekstraksi dan analisis AI
     */
    public function prosesDokumen(Document $document)
    {
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

        $laporanUtamaItem->update([
            'hasil_ai' => 'Ini adalah dokumen acuan utama.',
            'status_verifikasi' => 'ditemukan'
        ]);

        $kesimpulanItem = '';

        foreach ($document->documentItems as $item) {
            if ($item->id == $laporanUtamaItem->id) continue;

            $path = storage_path('app/public/' . $item->path_file);
            $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            $teksItem = '';
            $isImage   = false;
            $mimeType  = "";

            if ($ext == 'pdf') {
                $teksItem = $this->ekstrakTeksPDF($path);
            } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                if (file_exists($path)) {
                    $teksItem = base64_encode(file_get_contents($path));
                    $isImage   = true;
                    $mimeType = ($ext == 'png') ? 'image/png' : 'image/jpeg';
                } else {
                    $item->update(['hasil_ai' => 'Error: File gambar tidak ditemukan.']);
                    continue;
                }
            } else {
                continue;
            }

            $promptPerItem = "
            Peran: Auditor Dokumen.
            Tugas: Bandingkan Laporan Utama vs Dokumen Pendukung ini. 
            
            [LAPORAN UTAMA]: " . $teksLaporanUtama . "
            
            INSTRUKSI:
            1. Analisis apakah data di Laporan Utama ini SESUAI dengan dokumen pendukung.
            2. Berikan output berupa PARAGRAF PENDEK (maksimal 3 kalimat).
            3. Jika Sesuai, awali kalimat dengan kata [VALID].
            4. Jika Tidak Sesuai/Tidak Ada, awali dengan kata [INVALID].
            ";

            if (!$isImage) {
                $promptPerItem .= "\n\n[DOKUMEN PENDUKUNG]:\n" . $teksItem;
            } else {
                $promptPerItem .= "\n\n(Lihat Gambar Lampiran)";
            }
            // if ($isImage) {
            //     $promptPerItem = "Peran: Auditor. Tugas: Lihat gambar ini. Apakah data/nama/info di gambar ini VALID dan SESUAI dengan Teks Laporan Utama berikut?\n\n[TEKS LAPORAN]:\n" . substr($teksLaporanUtama, 0, 3000);
            // } else {
            //     $promptPerItem = "
            //     Peran: Senior Quality Assurance & Compliance Auditor.
            //     Tugas: Lakukan CROSS-CHECK VALIDATION. Bandingkan 'LAPORAN_UTAMA'\n$teksLaporanUtama\n sebagai acuan kebenaran (Source of Truth) dengan dokumen pendukung lainnya (PROPOSAL, RESUME, SERTIFIKAT, KERTAS_KERJA) \n$teksItem\n.
            //     Apakah informasi, data, dan detail dalam APORAN_UTAMA? tersebut SESUAI dan VALID berdasarkan dokumen pendukung?
            //     Berikan analisis mendetail. Jika ada ketidaksesuaian, tandai sebagai 'tidak_ditemukan'.
            //     Output: Berikan analisis dalam format Markdown rapi.
            //     ";
            // }

            $hasilTeks = $this->analisisAI($promptPerItem, $isImage ? $teksItem : "", $isImage, $mimeType);
            // Pastikan respons AI bisa dibaca$statusDB = 'tidak_ditemukan';
            if (stripos($hasilTeks, 'VALID') !== false || stripos($hasilTeks, 'SESUAI') !== false || stripos($hasilTeks, 'COCOK') !== false) {
                $statusDB = 'ditemukan';
            }

            $item->update([
                'hasil_ai' => $hasilTeks,
                'status_verifikasi' => $statusDB
            ]);

            $kesimpulanItem = "- Dokumen {$item->kategori}: {$hasilTeks} (Status: {$statusDB})\n";
            sleep(2);
        }

        $promptFinal = "
        Peran: Kepala Auditor.
        Tugas: Buat Kesimpulan Akhir & Skor Audit.
        
        DATA TEMUAN AUDIT:
        $kesimpulanItem
        
        \n[INSTRUKSI WAJIB]:
        1. Buat kesimpulan akhir audit dalam format MARKDOWN RAPI.
        2. Berikan SKOR AKHIR dari 0-100 berdasarkan tingkat kesesuaian dokumen pendukung dengan laporan utama.
        3. Buat daftar TEMUAN AUDIT terpisah untuk setiap kategori dokumen (PROPOSAL, KERTAS_KERJA, RESUME, SERTIFIKAT) jika ada ketidaksesuaian atau masalah.
        ";

        $kesimpulanFinal = $this->analisisAI($promptFinal, "", false);
        $skor = 0;
        if (preg_match('/(\d{1,3})\/100/', $kesimpulanFinal, $matches)) {
            $skor = (int)$matches[1];
        } elseif (preg_match('/Skor:\s*(\d{1,3})/', $kesimpulanFinal, $matches)) {
            $skor = (int)$matches[1];
        }

        $document->update([
            'kesimpulan' => $kesimpulanFinal,
            'skor'       => $skor,
            'status'     => 'selesai'
        ]);
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
    private function analisisAI($promptText, $imageData = "", $isImage = false, $mimeType = '')
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured');
        }
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash:generateContent?key={$apiKey}";


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
}
