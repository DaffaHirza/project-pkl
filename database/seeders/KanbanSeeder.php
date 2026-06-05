<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClientKanban;
use App\Models\AssetKanban;
use App\Models\AssetNoteKanban;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KanbanSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            /*
            |--------------------------------------------------------------------------
            | Optional Clean Test Data
            |--------------------------------------------------------------------------
            | Aktifkan bagian ini kalau database memang khusus testing.
            | Jangan aktifkan di production.
            */
            // DB::table('asset_stage_checks_kanban')->delete();
            // DB::table('asset_notes_kanban')->delete();
            // DB::table('asset_documents_kanban')->delete();
            // AssetKanban::withTrashed()->forceDelete();
            // ClientKanban::query()->delete();

            $admin = User::firstOrCreate(
                ['email' => 'admin@appraisal.test'],
                [
                    'name' => 'Admin Appraisal',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $staff = User::firstOrCreate(
                ['email' => 'staff@appraisal.test'],
                [
                    'name' => 'Staff Penilai',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Clients
            |--------------------------------------------------------------------------
            */

            $bankMandiri = $this->createClient('Bank Mandiri KJPP', 'PT Bank Mandiri Tbk', 'bank', null, 'SPK/BM/001/2026');
            $bankBca = $this->createClient('Bank BCA KJPP', 'PT Bank Central Asia Tbk', 'bank', null, 'SPK/BCA/002/2026');
            $bankBri = $this->createClient('Bank BRI KJPP', 'PT Bank Rakyat Indonesia Tbk', 'bank', null, 'SPK/BRI/003/2026');

            $debiturA = $this->createClient('Budi Santoso', 'PT Maju Bersama Properti', 'debitur', $bankMandiri->id);
            $debiturB = $this->createClient('Siti Rahayu', 'CV Sejahtera Abadi', 'debitur', $bankMandiri->id);
            $debiturC = $this->createClient('Rudi Hartono', 'PT Sumber Makmur Industri', 'debitur', $bankBca->id);
            $debiturD = $this->createClient('Maya Putri', 'CV Karya Sentosa', 'debitur', $bankBca->id);
            $debiturE = $this->createClient('Andi Wijaya', 'PT Agro Nusantara', 'debitur', $bankBri->id);
            $debiturF = $this->createClient('Dewi Lestari', 'PT Logistik Prima', 'debitur', $bankBri->id);

            $ptIndukA = $this->createClient('Direksi Karya Mandiri Group', 'PT Karya Mandiri Group', 'pt_cv', null, 'SPK/PTKMG/004/2026');
            $ptIndukB = $this->createClient('Direksi Sentra Niaga', 'PT Sentra Niaga Indonesia', 'pt_cv', null, 'SPK/PTSN/005/2026');
            $ptIndukC = $this->createClient('Direksi Global Teknologi', 'PT Global Teknologi Persada', 'pt_cv', null, 'SPK/PTGT/006/2026');

            $ptAnakA1 = $this->createClient('Manajemen Karya Properti', 'PT Karya Properti', 'pt_cv', $ptIndukA->id);
            $ptAnakA2 = $this->createClient('Manajemen Karya Industri', 'PT Karya Industri', 'pt_cv', $ptIndukA->id);
            $ptAnakA3 = $this->createClient('Manajemen Karya Logistik', 'PT Karya Logistik', 'pt_cv', $ptIndukA->id);

            $ptAnakB1 = $this->createClient('Manajemen Sentra Retail', 'PT Sentra Retail', 'pt_cv', $ptIndukB->id);
            $ptAnakB2 = $this->createClient('Manajemen Sentra Food', 'CV Sentra Food', 'pt_cv', $ptIndukB->id);

            $ptAnakC1 = $this->createClient('Manajemen Global Data Center', 'PT Global Data Center', 'pt_cv', $ptIndukC->id);
            $ptAnakC2 = $this->createClient('Manajemen Global Software', 'PT Global Software House', 'pt_cv', $ptIndukC->id);

            $ptStandaloneA = $this->createClient('Pemilik CV Mandiri Jaya', 'CV Mandiri Jaya', 'pt_cv');
            $ptStandaloneB = $this->createClient('Pemilik PT Prima Valuasi', 'PT Prima Valuasi Properti', 'pt_cv');
            $ptStandaloneC = $this->createClient('Pemilik CV Berkah Mesin', 'CV Berkah Mesin', 'pt_cv');

            /*
            |--------------------------------------------------------------------------
            | Assets
            |--------------------------------------------------------------------------
            */

            $assets = [
                // Stage 1 - Inisiasi
                [$ptStandaloneA, 'Tanah Kavling BSD Blok A-01', 'tanah', 'Tangerang Selatan', 1],
                [$debiturA, 'Rumah Tinggal Cluster Anggrek', 'tanah_bangunan', 'Bekasi', 1],

                // Stage 2 - Penawaran
                [$ptAnakA1, 'Tanah Kavling Komersial A-12', 'tanah', 'Depok', 2],
                [$debiturB, 'Ruko 2 Lantai Pasar Modern', 'bangunan', 'Jakarta Timur', 2],

                // Stage 3 - Kesepakatan
                [$debiturA, 'Gudang B Blok 2 Kawasan Industri', 'bangunan', 'Cikarang', 3],
                [$ptStandaloneB, 'Kantor Operasional Lantai 5', 'bangunan', 'Jakarta Selatan', 3],

                // Stage 4 - Eksekusi Lapangan
                [$debiturC, 'Pabrik Tekstil Unit Produksi', 'tanah_bangunan', 'Bandung', 4],
                [$ptAnakA2, 'Forklift Toyota 7FBR-18', 'kendaraan', 'Karawang', 4],
                [$debiturD, 'Kendaraan Operasional Pajero Sport', 'kendaraan', 'Semarang', 4],

                // Stage 5 - Analisis
                [$ptAnakA2, 'Mesin Tenun Rapier T-500', 'mesin', 'Karawang', 5],
                [$ptStandaloneC, 'Mesin CNC Milling Haas VF-2', 'mesin', 'Tangerang', 5],

                // Stage 6 - Review 1
                [$debiturB, 'Ruko 3 Lantai No. 15', 'tanah_bangunan', 'Jakarta Barat', 6],
                [$ptAnakB1, 'Gerai Retail Plaza Sentra', 'bisnis', 'Surabaya', 6],

                // Stage 7 - Draft Resume
                [$ptAnakA3, 'Gudang Logistik Pendingin', 'tanah_bangunan', 'Bekasi', 7],
                [$debiturE, 'Lahan Perkebunan Sawit Blok 7', 'tanah', 'Kalimantan Barat', 7],

                // Stage 8 - Approval Klien
                [$ptStandaloneA, 'Gedung 3 Lantai Mandiri Jaya', 'tanah_bangunan', 'Bogor', 8],
                [$ptAnakB2, 'Brand Value Sentra Food', 'aset_tak_berwujud', 'Jakarta Pusat', 8],

                // Stage 9 - Draft Laporan
                [$ptIndukA, 'Gedung Kantor Pusat KMG', 'tanah_bangunan', 'Jakarta Selatan', 9],
                [$ptAnakC2, 'Aplikasi ERP Internal', 'aset_tak_berwujud', 'Jakarta Selatan', 9],

                // Stage 10 - Review 2
                [$ptAnakA1, 'Rumah Tinggal Jl. Kemang Raya', 'tanah_bangunan', 'Jakarta Selatan', 10],
                [$debiturF, 'Armada Truk Box 10 Unit', 'kendaraan', 'Tangerang', 10],

                // Stage 11 - Finalisasi
                [$ptAnakC1, 'Server Data Center Rack A', 'personal_property', 'Jakarta Pusat', 11],
                [$debiturC, 'Inventaris Kantor Cabang Bandung', 'inventaris', 'Bandung', 11],

                // Stage 12 - Delivery & Payment
                [$ptIndukB, 'Portfolio Bisnis Sentra Niaga', 'bisnis', 'Jakarta Utara', 12],
                [$ptStandaloneB, 'Tanah dan Bangunan Gudang Prima', 'tanah_bangunan', 'Cileungsi', 12],

                // Stage 13 - Arsip
                [$ptIndukC, 'Gedung Head Office Global Teknologi', 'tanah_bangunan', 'BSD City', 13],
                [$debiturE, 'Cold Storage Agro Nusantara', 'bangunan', 'Medan', 13],

                // Extra untuk filter tipe asset
                [$ptAnakC1, 'UPS Data Center APC 40KVA', 'personal_property', 'Jakarta Pusat', 5],
                [$ptAnakB1, 'Peralatan Display Retail', 'inventaris', 'Surabaya', 3],
                [$debiturF, 'Tanah Parkir Pool Logistik', 'tanah', 'Tangerang', 2],
                [$bankMandiri, 'Agunan Korporasi Bank Mandiri', 'lainnya', 'Jakarta Pusat', 4],
                [$bankBca, 'Objek Penilaian Khusus BCA', 'lainnya', 'Jakarta Barat', 6],
            ];

            $createdAssets = collect();

            foreach ($assets as $index => [$client, $name, $type, $location, $stage]) {
                $asset = AssetKanban::create([
                    'client_id' => $client->id,
                    'name' => $name,
                    'asset_type' => $type,
                    'location' => $location,
                    'current_stage' => $stage,
                    'position' => $index,
                ]);

                $createdAssets->push($asset);

                $this->createStageHistory($asset, $admin, $stage);
                $this->createChecklist($asset, $admin, $staff, $stage);
            }

            /*
            |--------------------------------------------------------------------------
            | Notes: Normal, Rejection, Blocked
            |--------------------------------------------------------------------------
            */

            $normalNotes = [
                [0, 1, 'note', 'Dokumen awal sudah diterima dari klien.'],
                [2, 2, 'note', 'Penawaran sudah dikirim dan menunggu konfirmasi.'],
                [6, 4, 'note', 'Survei lapangan selesai, dokumentasi foto sudah lengkap.'],
                [9, 5, 'note', 'Analisis pendekatan biaya sedang disusun.'],
                [12, 6, 'note', 'Reviewer meminta penajaman asumsi pasar.'],
                [16, 8, 'note', 'Klien sudah menerima draft resume dan sedang melakukan review internal.'],
                [20, 10, 'note', 'Review kedua sedang dilakukan oleh reviewer senior.'],
                [24, 12, 'note', 'Laporan sudah dikirim, menunggu konfirmasi pembayaran.'],
                [26, 13, 'note', 'Dokumen sudah masuk arsip final.'],
            ];

            $warningNotes = [
                [5, 3, 'blocked', 'Terhambat karena dokumen legal belum lengkap dari pihak klien.'],
                [10, 5, 'rejection', 'Hasil verifikasi mesin tidak sesuai dengan data awal, perlu inspeksi ulang.'],
                [12, 6, 'blocked', 'Review 1 tertunda karena reviewer meminta data pembanding tambahan.'],
                [15, 8, 'rejection', 'Klien menolak asumsi nilai pasar awal, perlu revisi pendekatan.'],
                [20, 10, 'blocked', 'Review 2 belum dapat diselesaikan karena lampiran laporan belum final.'],
                [30, 4, 'blocked', 'Objek khusus perlu klarifikasi dokumen pendukung dari bank.'],
            ];

            foreach (array_merge($normalNotes, $warningNotes) as [$assetIndex, $stage, $type, $content]) {
                if (!isset($createdAssets[$assetIndex])) {
                    continue;
                }

                AssetNoteKanban::create([
                    'asset_id' => $createdAssets[$assetIndex]->id,
                    'user_id' => $type === 'note' ? $staff->id : $admin->id,
                    'stage' => $stage,
                    'type' => $type,
                    'content' => $content,
                    'created_at' => now()->subDays(rand(1, 12)),
                    'updated_at' => now()->subDays(rand(0, 5)),
                ]);
            }

            $this->command->info('KanbanSeeder selesai.');
            $this->command->info('Users: admin@appraisal.test / staff@appraisal.test, password: password');
            $this->command->info('Clients: bank, debitur, PT/CV induk, PT/CV anak, dan PT/CV standalone.');
            $this->command->info('Assets: ' . $createdAssets->count() . ' data tersebar di stage 1-13.');
        });
    }

    private function createClient(
        string $name,
        ?string $companyName,
        string $type,
        ?int $parentId = null,
        ?string $spkNumber = null
    ): ClientKanban {
        return ClientKanban::create([
            'name' => $name,
            'company_name' => $companyName,
            'spk_number' => $spkNumber,
            'type' => $type,
            'parent_id' => $parentId,
        ]);
    }

    private function createStageHistory(AssetKanban $asset, User $user, int $currentStage): void
    {
        AssetNoteKanban::create([
            'asset_id' => $asset->id,
            'user_id' => $user->id,
            'stage' => 1,
            'type' => 'stage_change',
            'content' => 'Asset didaftarkan dan memulai tahap Inisiasi.',
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
        ]);

        for ($stage = 2; $stage <= $currentStage; $stage++) {
            AssetNoteKanban::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'stage' => $stage,
                'type' => 'stage_change',
                'content' => 'Pindah ke tahap ' . (AssetKanban::STAGES[$stage] ?? "Stage {$stage}"),
                'created_at' => now()->subDays(max(1, 20 - $stage)),
                'updated_at' => now()->subDays(max(1, 20 - $stage)),
            ]);
        }
    }

    private function createChecklist(AssetKanban $asset, User $admin, User $staff, int $currentStage): void
    {
        $stageCheckModel = 'App\\Models\\AssetStageCheckKanban';

        if (!class_exists($stageCheckModel)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Checklist sengaja tidak selalu sequential.
        | Ini untuk test case: Review 2 bisa sudah dicek walau Review 1 belum.
        |--------------------------------------------------------------------------
        */

        $checkedStages = [];

        if ($currentStage <= 3) {
            $checkedStages = [1];
        } elseif ($currentStage <= 6) {
            $checkedStages = [1, 2, 4];
        } elseif ($currentStage <= 9) {
            $checkedStages = [1, 2, 3, 5, 7];
        } elseif ($currentStage <= 12) {
            $checkedStages = [1, 2, 4, 7, 10];
        } else {
            $checkedStages = [1, 2, 3, 4, 5, 7, 10, 12, 13];
        }

        // Beberapa asset dibuat non-sequential ekstrem untuk test.
        if ($asset->id % 5 === 0) {
            $checkedStages = [1, 4, 10];
        }

        if ($asset->id % 7 === 0) {
            $checkedStages = [2, 6, 11];
        }

        foreach (array_unique($checkedStages) as $stage) {
            $stageCheckModel::create([
                'asset_id' => $asset->id,
                'stage' => $stage,
                'is_checked' => true,
                'checked_by' => $stage % 2 === 0 ? $staff->id : $admin->id,
                'checked_at' => now()->subDays(rand(0, 14)),
                'note' => 'Checklist stage ' . (AssetKanban::STAGES[$stage] ?? "Stage {$stage}") . ' untuk kebutuhan testing.',
            ]);
        }
    }
}