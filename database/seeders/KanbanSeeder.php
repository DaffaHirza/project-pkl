<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClientKanban;
use App\Models\AssetKanban;
use App\Models\AssetNoteKanban;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KanbanSeeder extends Seeder
{
    /**
     * Seed data for simplified Kanban assessment system.
     * 
     * Structure:
     * - ClientKanban (bank → debitur, pt_cv → pt_anak)
     * - AssetKanban (assets linked directly to clients)
     * - AssetDocumentKanban (sample documents)
     * - AssetNoteKanban (sample notes)
     */
    public function run(): void
    {
        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@appraisal.test'],
            [
                'name' => 'Admin Appraisal',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create bank client with debiturs
        $bank = ClientKanban::create([
            'name' => 'Dewi Lestari',
            'company_name' => 'PT Bank Central',
            'type' => 'bank',
        ]);

        $debitur1 = ClientKanban::create([
            'name' => 'Budi Santoso',
            'company_name' => 'PT Maju Bersama',
            'type' => 'debitur',
            'parent_id' => $bank->id,
        ]);

        $debitur2 = ClientKanban::create([
            'name' => 'Siti Rahayu',
            'company_name' => 'CV Sejahtera',
            'type' => 'debitur',
            'parent_id' => $bank->id,
        ]);

        // Create PT/CV clients with child companies
        $ptInduk = ClientKanban::create([
            'name' => 'Ahmad Hidayat',
            'company_name' => 'PT Karya Mandiri Group',
            'type' => 'pt_cv',
        ]);

        $ptAnak1 = ClientKanban::create([
            'name' => 'Rudi Hartono',
            'company_name' => 'PT Karya Properti',
            'type' => 'pt_cv',
            'parent_id' => $ptInduk->id,
        ]);

        $ptAnak2 = ClientKanban::create([
            'name' => 'Andi Wijaya',
            'company_name' => 'PT Karya Industri',
            'type' => 'pt_cv',
            'parent_id' => $ptInduk->id,
        ]);

        // Standalone PT/CV (no parent, no children)
        $ptStandalone = ClientKanban::create([
            'name' => 'Maya Putri',
            'company_name' => 'CV Mandiri Jaya',
            'type' => 'pt_cv',
        ]);

        // Create assets directly linked to clients
        $assets = [
            // Debitur 1 assets (via bank)
            ['client' => $debitur1, 'name' => 'Gudang A - Blok 1', 'type' => 'bangunan', 'stage' => 4],
            ['client' => $debitur1, 'name' => 'Gudang B - Blok 2', 'type' => 'bangunan', 'stage' => 3],
            
            // Debitur 2 assets (via bank)
            ['client' => $debitur2, 'name' => 'Ruko 3 Lantai No. 15', 'type' => 'tanah_bangunan', 'stage' => 6],
            
            // PT Anak 1 assets (via PT Group)
            ['client' => $ptAnak1, 'name' => 'Rumah Tinggal Jl. Kemang Raya', 'type' => 'tanah_bangunan', 'stage' => 10],
            ['client' => $ptAnak1, 'name' => 'Tanah Kavling A-01', 'type' => 'tanah', 'stage' => 2],
            
            // PT Anak 2 assets (via PT Group)
            ['client' => $ptAnak2, 'name' => 'Mesin Tenun Rapier T-500', 'type' => 'mesin', 'stage' => 5],
            ['client' => $ptAnak2, 'name' => 'Mesin Dyeing JT-2000', 'type' => 'mesin', 'stage' => 4],
            ['client' => $ptAnak2, 'name' => 'Forklift Toyota 7FBR-18', 'type' => 'kendaraan', 'stage' => 3],
            
            // PT Induk direct assets
            ['client' => $ptInduk, 'name' => 'Gedung Kantor Pusat', 'type' => 'tanah_bangunan', 'stage' => 13],
            
            // Standalone PT assets
            ['client' => $ptStandalone, 'name' => 'Tanah Kavling BSD', 'type' => 'tanah', 'stage' => 1],
            ['client' => $ptStandalone, 'name' => 'Gedung 3 Lantai', 'type' => 'tanah_bangunan', 'stage' => 8],
        ];

        $createdAssets = collect();
        foreach ($assets as $idx => $a) {
            $asset = AssetKanban::create([
                'client_id' => $a['client']->id,
                'name' => $a['name'],
                'asset_type' => $a['type'],
                'current_stage' => $a['stage'],
                'position' => $idx,
                'location' => 'Jakarta',
            ]);
            $createdAssets->push($asset);

            // Add initial stage note
            AssetNoteKanban::create([
                'asset_id' => $asset->id,
                'user_id' => $admin->id,
                'stage' => 1,
                'type' => 'stage_change',
                'content' => 'Asset didaftarkan dan memulai tahap Inisiasi.',
            ]);

            // Add notes for stage changes
            for ($s = 2; $s <= $a['stage']; $s++) {
                AssetNoteKanban::create([
                    'asset_id' => $asset->id,
                    'user_id' => $admin->id,
                    'stage' => $s,
                    'type' => 'stage_change',
                    'content' => 'Pindah ke tahap ' . AssetKanban::STAGES[$s],
                ]);
            }
        }

        // Add some sample notes
        $sampleNotes = [
            ['asset_idx' => 0, 'stage' => 4, 'content' => 'Survei lapangan selesai, data lengkap.'],
            ['asset_idx' => 2, 'stage' => 6, 'content' => 'Menunggu approval dari reviewer senior.'],
            ['asset_idx' => 3, 'stage' => 10, 'content' => 'Draft laporan sudah disetujui klien, sedang finalisasi.'],
            ['asset_idx' => 5, 'stage' => 5, 'content' => 'Data mesin perlu verifikasi ulang dengan pabrik.'],
        ];

        foreach ($sampleNotes as $note) {
            AssetNoteKanban::create([
                'asset_id' => $createdAssets[$note['asset_idx']]->id,
                'user_id' => $admin->id,
                'stage' => $note['stage'],
                'type' => 'note',
                'content' => $note['content'],
            ]);
        }

        $this->command->info('Kanban seeder completed: 7 clients (1 bank, 2 debiturs, 3 PT/CV, 1 standalone), 11 assets');
    }
}
