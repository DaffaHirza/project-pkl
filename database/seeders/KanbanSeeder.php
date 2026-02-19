<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClientKanban;
use App\Models\ProjectKanban;
use App\Models\ProjectAssetKanban;
use App\Models\AssetDocumentKanban;
use App\Models\AssetNoteKanban;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KanbanSeeder extends Seeder
{
    /**
     * Seed data for simplified Kanban assessment system.
     * 
     * Structure:
     * - ClientKanban (5 clients)
     * - ProjectKanban (8 projects)
     * - ProjectAssetKanban (15 assets across stages 1-13)
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

        // Create sample clients
        $clients = collect([
            ['name' => 'Budi Santoso', 'company_name' => 'PT Maju Bersama', 'email' => 'budi@majubersama.co.id', 'phone' => '081234567890'],
            ['name' => 'Siti Rahayu', 'company_name' => 'CV Sejahtera', 'email' => 'siti@sejahtera.com', 'phone' => '082345678901'],
            ['name' => 'Ahmad Hidayat', 'company_name' => 'PT Karya Mandiri', 'email' => 'ahmad@karyamandiri.id', 'phone' => '083456789012'],
            ['name' => 'Dewi Lestari', 'company_name' => 'PT Bank Central', 'email' => 'dewi@bankcentral.co.id', 'phone' => '084567890123'],
            ['name' => 'Rudi Hartono', 'company_name' => 'PT Properti Jaya', 'email' => 'rudi@propertijaya.com', 'phone' => '085678901234'],
        ])->map(fn($data) => ClientKanban::create($data));

        // Create sample projects with different statuses
        $projects = [
            // Active projects
            ['client_idx' => 0, 'name' => 'Penilaian Gudang Kawasan Industri', 'status' => 'active', 'due_date' => now()->addDays(30)],
            ['client_idx' => 1, 'name' => 'Penilaian Ruko Jalan Sudirman', 'status' => 'active', 'due_date' => now()->addDays(14)],
            ['client_idx' => 2, 'name' => 'Penilaian Rumah Tinggal Kemang', 'status' => 'active', 'due_date' => now()->addDays(7)],
            ['client_idx' => 3, 'name' => 'Penilaian Mesin Pabrik Tekstil', 'status' => 'active', 'due_date' => now()->subDays(3)], // Overdue
            ['client_idx' => 0, 'name' => 'Penilaian Tanah Kavling BSD', 'status' => 'active', 'due_date' => now()->addDays(45)],
            // Completed projects
            ['client_idx' => 4, 'name' => 'Penilaian Gedung Perkantoran', 'status' => 'completed', 'due_date' => now()->subDays(10)],
            // Cancelled
            ['client_idx' => 1, 'name' => 'Penilaian Tanah Pertanian', 'status' => 'cancelled', 'due_date' => null],
        ];

        $createdProjects = collect();
        foreach ($projects as $p) {
            $createdProjects->push(ProjectKanban::create([
                'client_id' => $clients[$p['client_idx']]->id,
                'name' => $p['name'],
                'status' => $p['status'],
                'due_date' => $p['due_date'],
                'description' => 'Project penilaian aset untuk keperluan agunan bank.',
            ]));
        }

        // Create assets at various stages
        $assets = [
            // Project 1: Gudang (2 assets spread across stages)
            ['project_idx' => 0, 'name' => 'Gudang A - Blok 1', 'type' => 'bangunan', 'stage' => 4, 'priority' => 'normal'],
            ['project_idx' => 0, 'name' => 'Gudang B - Blok 2', 'type' => 'bangunan', 'stage' => 3, 'priority' => 'normal'],
            
            // Project 2: Ruko (1 asset in review)
            ['project_idx' => 1, 'name' => 'Ruko 3 Lantai No. 15', 'type' => 'tanah_bangunan', 'stage' => 6, 'priority' => 'warning'],
            
            // Project 3: Rumah (1 asset near completion)
            ['project_idx' => 2, 'name' => 'Rumah Tinggal Jl. Kemang Raya', 'type' => 'tanah_bangunan', 'stage' => 10, 'priority' => 'normal'],
            
            // Project 4: Mesin Pabrik (3 assets, overdue project - critical)
            ['project_idx' => 3, 'name' => 'Mesin Tenun Rapier T-500', 'type' => 'mesin', 'stage' => 5, 'priority' => 'critical'],
            ['project_idx' => 3, 'name' => 'Mesin Dyeing JT-2000', 'type' => 'mesin', 'stage' => 4, 'priority' => 'critical'],
            ['project_idx' => 3, 'name' => 'Forklift Toyota 7FBR-18', 'type' => 'kendaraan', 'stage' => 3, 'priority' => 'warning'],
            
            // Project 5: Tanah (2 assets early stages)
            ['project_idx' => 4, 'name' => 'Tanah Kavling A-01', 'type' => 'tanah', 'stage' => 2, 'priority' => 'normal'],
            ['project_idx' => 4, 'name' => 'Tanah Kavling A-02', 'type' => 'tanah', 'stage' => 1, 'priority' => 'normal'],
            
            // Project 6: Completed (all assets archived)
            ['project_idx' => 5, 'name' => 'Gedung 8 Lantai Jl. Gatot Subroto', 'type' => 'tanah_bangunan', 'stage' => 13, 'priority' => 'normal'],
            ['project_idx' => 5, 'name' => 'Basement Parkir', 'type' => 'bangunan', 'stage' => 13, 'priority' => 'normal'],
        ];

        $createdAssets = collect();
        foreach ($assets as $idx => $a) {
            $asset = ProjectAssetKanban::create([
                'project_id' => $createdProjects[$a['project_idx']]->id,
                'name' => $a['name'],
                'asset_type' => $a['type'],
                'current_stage' => $a['stage'],
                'priority' => $a['priority'],
                'position' => $idx,
                'location' => 'Jakarta',
                'description' => 'Objek penilaian untuk keperluan agunan.',
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
                    'content' => 'Pindah ke tahap ' . ProjectAssetKanban::STAGES[$s],
                ]);
            }
        }

        // Add some sample notes
        $sampleNotes = [
            ['asset_idx' => 0, 'stage' => 4, 'content' => 'Survei lapangan selesai, data lengkap.'],
            ['asset_idx' => 2, 'stage' => 6, 'content' => 'Menunggu approval dari reviewer senior.'],
            ['asset_idx' => 3, 'stage' => 10, 'content' => 'Draft laporan sudah disetujui klien, sedang finalisasi.'],
            ['asset_idx' => 4, 'stage' => 5, 'content' => 'Data mesin perlu verifikasi ulang dengan pabrik.'],
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

        $this->command->info('Kanban seeder completed: 5 clients, 7 projects, 11 assets');
    }
}
