<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KanbanClient;
use App\Models\ProjectKanban;
use App\Models\ProposalKanban;
use App\Models\ContractKanban;
use App\Models\InspectionKanban;
use App\Models\WorkingPaperKanban;
use App\Models\ReportKanban;
use App\Models\ApprovalKanban;
use App\Models\InvoiceKanban;
use App\Models\DocumentKanban;
use App\Models\ActivityKanban;
use Illuminate\Database\Seeder;

class AppraisalKanbanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users (tanpa role, semua setara)
        $users = $this->createUsers();

        // Create clients
        $clients = $this->createClients();

        // Create projects at different stages
        $projects = $this->createProjects($clients, $users);

        $this->command->info('✅ Appraisal Kanban Seeder completed!');
        $this->command->info("   - {$users->count()} Users");
        $this->command->info("   - {$clients->count()} Clients");
        $this->command->info("   - {$projects->count()} Projects");
    }

    /**
     * Create test users
     */
    private function createUsers()
    {
        $usersData = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@appraisal.com'],
            ['name' => 'Budi Santoso', 'email' => 'budi@appraisal.com'],
            ['name' => 'Citra Dewi', 'email' => 'citra@appraisal.com'],
            ['name' => 'Dian Permata', 'email' => 'dian@appraisal.com'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@appraisal.com'],
        ];

        $users = collect();
        foreach ($usersData as $userData) {
            $users->push(User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password123'),
                ]
            ));
        }

        return $users;
    }

    /**
     * Create test clients
     */
    private function createClients()
    {
        $clientsData = [
            [
                'name' => 'Ir. Bambang Wijaya',
                'company_name' => 'PT Bank Mandiri (Persero) Tbk',
                'email' => 'bambang.wijaya@bankmandiri.co.id',
                'phone' => '021-5245678',
                'address' => 'Jl. Jend. Gatot Subroto Kav. 36-38, Jakarta Selatan',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'company_name' => 'PT Bank Rakyat Indonesia',
                'email' => 'siti.nurhaliza@bri.co.id',
                'phone' => '021-2510244',
                'address' => 'Jl. Jend. Sudirman Kav. 44-46, Jakarta Pusat',
            ],
            [
                'name' => 'Hendra Gunawan',
                'company_name' => 'PT Astra International Tbk',
                'email' => 'hendra.gunawan@astra.co.id',
                'phone' => '021-5087000',
                'address' => 'Jl. Gaya Motor Raya No. 8, Jakarta Utara',
            ],
            [
                'name' => 'Dewi Kartika',
                'company_name' => null, // Personal client
                'email' => 'dewi.kartika@gmail.com',
                'phone' => '0812-3456-7890',
                'address' => 'Jl. Kemang Raya No. 45, Jakarta Selatan',
            ],
            [
                'name' => 'Rudi Hermawan',
                'company_name' => 'PT Telkom Indonesia',
                'email' => 'rudi.hermawan@telkom.co.id',
                'phone' => '022-4521234',
                'address' => 'Jl. Japati No. 1, Bandung',
            ],
            [
                'name' => 'Maya Sari',
                'company_name' => 'PT Pertamina (Persero)',
                'email' => 'maya.sari@pertamina.com',
                'phone' => '021-3815111',
                'address' => 'Jl. Medan Merdeka Timur No. 1A, Jakarta Pusat',
            ],
        ];

        $clients = collect();
        foreach ($clientsData as $clientData) {
            $clients->push(KanbanClient::firstOrCreate(
                ['email' => $clientData['email']],
                $clientData
            ));
        }

        return $clients;
    }

    /**
     * Create projects at different stages
     */
    private function createProjects($clients, $users)
    {
        $projects = collect();

        // Project 1: Lead stage (baru masuk)
        $projects->push($this->createLeadProject($clients[0], $users));

        // Project 2: Proposal stage
        $projects->push($this->createProposalProject($clients[1], $users));

        // Project 3: Contract stage
        $projects->push($this->createContractProject($clients[2], $users));

        // Project 4: Inspection stage
        $projects->push($this->createInspectionProject($clients[3], $users));

        // Project 5: Analysis stage
        $projects->push($this->createAnalysisProject($clients[4], $users));

        // Project 6: Review stage
        $projects->push($this->createReviewProject($clients[5], $users));

        // Project 7: Client Approval stage
        $projects->push($this->createClientApprovalProject($clients[0], $users));

        // Project 8: Final Report stage
        $projects->push($this->createFinalReportProject($clients[1], $users));

        // Project 9: Invoicing stage
        $projects->push($this->createInvoicingProject($clients[2], $users));

        // Project 10: Done (completed)
        $projects->push($this->createDoneProject($clients[3], $users));

        return $projects;
    }

    /**
     * Create Lead stage project
     */
    private function createLeadProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0001',
            'name' => 'Penilaian Rumah Tinggal - Jl. Sudirman',
            'location' => 'Jl. Jend. Sudirman No. 100, Jakarta Selatan',
            'current_stage' => 'lead',
            'priority_status' => 'normal',
            'due_date' => now()->addDays(30),
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[0]->id,
            'activity_type' => 'comment',
            'stage_context' => 'lead',
            'description' => 'Permintaan penilaian masuk dari klien Bank Mandiri untuk agunan kredit.',
        ]);

        return $project;
    }

    /**
     * Create Proposal stage project
     */
    private function createProposalProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0002',
            'name' => 'Penilaian Ruko 3 Lantai - Mangga Dua',
            'location' => 'Jl. Mangga Dua Raya No. 55, Jakarta Utara',
            'current_stage' => 'proposal',
            'priority_status' => 'normal',
            'due_date' => now()->addDays(25),
        ]);

        ProposalKanban::create([
            'project_id' => $project->id,
            'proposal_number' => 'PRP-202601-0001',
            'date_sent' => now()->subDays(2),
            'status' => 'sent',
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[1]->id,
            'activity_type' => 'stage_move',
            'stage_context' => 'proposal',
            'description' => 'Memindahkan project dari lead ke proposal',
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[1]->id,
            'activity_type' => 'upload',
            'stage_context' => 'proposal',
            'description' => 'Mengupload file: Proposal_Penilaian_RukoMangga2.pdf',
        ]);

        return $project;
    }

    /**
     * Create Contract stage project
     */
    private function createContractProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0003',
            'name' => 'Penilaian Gudang Industri - Cikarang',
            'location' => 'Kawasan Industri Jababeka, Cikarang, Bekasi',
            'current_stage' => 'contract',
            'priority_status' => 'normal',
            'due_date' => now()->addDays(20),
        ]);

        ProposalKanban::create([
            'project_id' => $project->id,
            'proposal_number' => 'PRP-202601-0002',
            'date_sent' => now()->subDays(7),
            'status' => 'approved',
        ]);

        ContractKanban::create([
            'project_id' => $project->id,
            'spk_number' => 'SPK-2026-0001',
            'signed_date' => now()->subDays(1),
            'file_path' => null,
        ]);

        DocumentKanban::create([
            'project_id' => $project->id,
            'uploader_id' => $users[2]->id,
            'category' => 'contract',
            'file_name' => 'SPK_Gudang_Cikarang.pdf',
            'file_path' => 'documents/contracts/SPK_Gudang_Cikarang.pdf',
            'file_type' => 'pdf',
            'file_size' => 524288,
            'description' => 'Surat Perintah Kerja untuk penilaian gudang Cikarang',
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[2]->id,
            'activity_type' => 'stage_move',
            'stage_context' => 'contract',
            'description' => 'SPK sudah ditandatangani, siap untuk inspeksi lapangan',
        ]);

        return $project;
    }

    /**
     * Create Inspection stage project
     */
    private function createInspectionProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0004',
            'name' => 'Penilaian Apartemen Kemang Village',
            'location' => 'Kemang Village Tower Infinity, Jakarta Selatan',
            'current_stage' => 'inspection',
            'priority_status' => 'warning',
            'due_date' => now()->addDays(15),
        ]);

        ProposalKanban::create([
            'project_id' => $project->id,
            'proposal_number' => 'PRP-202601-0003',
            'date_sent' => now()->subDays(10),
            'status' => 'approved',
        ]);

        ContractKanban::create([
            'project_id' => $project->id,
            'spk_number' => 'SPK-2026-0002',
            'signed_date' => now()->subDays(5),
            'file_path' => 'documents/contracts/SPK_Apartemen_Kemang.pdf',
        ]);

        InspectionKanban::create([
            'project_id' => $project->id,
            'surveyor_id' => $users[3]->id,
            'inspection_date' => now(),
            'notes' => 'Inspeksi dijadwalkan hari ini. Unit apartemen lantai 15.',
            'latitude' => '-6.2615',
            'longitude' => '106.8106',
        ]);

        DocumentKanban::create([
            'project_id' => $project->id,
            'uploader_id' => $users[3]->id,
            'category' => 'legal_doc',
            'file_name' => 'Sertifikat_HGB_KemangVillage.pdf',
            'file_path' => 'documents/legal/Sertifikat_HGB_KemangVillage.pdf',
            'file_type' => 'pdf',
            'file_size' => 1048576,
            'description' => 'Sertifikat HGB unit apartemen',
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[3]->id,
            'activity_type' => 'obstacle',
            'stage_context' => 'inspection',
            'description' => 'Akses unit terbatas, perlu koordinasi dengan pengelola gedung. Inspeksi dipending 1 hari.',
        ]);

        return $project;
    }

    /**
     * Create Analysis stage project
     */
    private function createAnalysisProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0005',
            'name' => 'Penilaian Tanah Kavling - BSD City',
            'location' => 'BSD City Sektor XIV, Tangerang Selatan',
            'current_stage' => 'analysis',
            'priority_status' => 'normal',
            'due_date' => now()->addDays(12),
        ]);

        ProposalKanban::create([
            'project_id' => $project->id,
            'proposal_number' => 'PRP-202601-0004',
            'date_sent' => now()->subDays(15),
            'status' => 'approved',
        ]);

        ContractKanban::create([
            'project_id' => $project->id,
            'spk_number' => 'SPK-2026-0003',
            'signed_date' => now()->subDays(10),
            'file_path' => 'documents/contracts/SPK_Tanah_BSD.pdf',
        ]);

        InspectionKanban::create([
            'project_id' => $project->id,
            'surveyor_id' => $users[4]->id,
            'inspection_date' => now()->subDays(3),
            'notes' => 'Tanah kavling seluas 500m2. Akses jalan bagus, listrik & air tersedia.',
            'latitude' => '-6.3024',
            'longitude' => '106.6528',
        ]);

        WorkingPaperKanban::create([
            'project_id' => $project->id,
            'analyst_id' => $users[0]->id,
        ]);

        // Field photos
        foreach (['Foto_Depan.jpg', 'Foto_Samping.jpg', 'Foto_Belakang.jpg'] as $i => $fileName) {
            DocumentKanban::create([
                'project_id' => $project->id,
                'uploader_id' => $users[4]->id,
                'category' => 'field_photo',
                'file_name' => $fileName,
                'file_path' => "documents/photos/{$project->id}/{$fileName}",
                'file_type' => 'jpg',
                'file_size' => 2097152,
                'description' => "Foto dokumentasi inspeksi lapangan",
            ]);
        }

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[0]->id,
            'activity_type' => 'stage_move',
            'stage_context' => 'analysis',
            'description' => 'Inspeksi selesai. Memulai analisis dan perhitungan nilai.',
        ]);

        return $project;
    }

    /**
     * Create Review stage project
     */
    private function createReviewProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0006',
            'name' => 'Penilaian Pabrik Pengolahan - Karawang',
            'location' => 'Kawasan Industri KIIC, Karawang',
            'current_stage' => 'review',
            'priority_status' => 'normal',
            'due_date' => now()->addDays(10),
        ]);

        ProposalKanban::create([
            'project_id' => $project->id,
            'proposal_number' => 'PRP-202601-0005',
            'date_sent' => now()->subDays(20),
            'status' => 'approved',
        ]);

        ContractKanban::create([
            'project_id' => $project->id,
            'spk_number' => 'SPK-2026-0004',
            'signed_date' => now()->subDays(15),
            'file_path' => 'documents/contracts/SPK_Pabrik_Karawang.pdf',
        ]);

        InspectionKanban::create([
            'project_id' => $project->id,
            'surveyor_id' => $users[3]->id,
            'inspection_date' => now()->subDays(10),
            'notes' => 'Pabrik pengolahan makanan. Luas bangunan 2000m2, tanah 5000m2.',
            'latitude' => '-6.3615',
            'longitude' => '107.2891',
        ]);

        WorkingPaperKanban::create([
            'project_id' => $project->id,
            'analyst_id' => $users[1]->id,
        ]);

        ReportKanban::create([
            'project_id' => $project->id,
            'type' => 'draft_report',
            'file_path' => 'documents/reports/Draft_Pabrik_Karawang_v1.pdf',
            'version' => 1,
            'is_approved' => false,
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[1]->id,
            'activity_type' => 'stage_move',
            'stage_context' => 'review',
            'description' => 'Draft laporan selesai. Menunggu review internal.',
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[2]->id,
            'activity_type' => 'comment',
            'stage_context' => 'review',
            'description' => 'Sedang mereview draft laporan. Akan selesai dalam 2 hari.',
        ]);

        return $project;
    }

    /**
     * Create Client Approval stage project
     */
    private function createClientApprovalProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0007',
            'name' => 'Penilaian Hotel Bintang 3 - Yogyakarta',
            'location' => 'Jl. Malioboro No. 50, Yogyakarta',
            'current_stage' => 'client_approval',
            'priority_status' => 'normal',
            'due_date' => now()->addDays(7),
        ]);

        ProposalKanban::create([
            'project_id' => $project->id,
            'proposal_number' => 'PRP-202601-0006',
            'date_sent' => now()->subDays(25),
            'status' => 'approved',
        ]);

        ContractKanban::create([
            'project_id' => $project->id,
            'spk_number' => 'SPK-2026-0005',
            'signed_date' => now()->subDays(20),
            'file_path' => 'documents/contracts/SPK_Hotel_Yogya.pdf',
        ]);

        InspectionKanban::create([
            'project_id' => $project->id,
            'surveyor_id' => $users[4]->id,
            'inspection_date' => now()->subDays(15),
            'notes' => 'Hotel 50 kamar, fasilitas lengkap. Lokasi strategis di Malioboro.',
            'latitude' => '-7.7928',
            'longitude' => '110.3656',
        ]);

        WorkingPaperKanban::create([
            'project_id' => $project->id,
            'analyst_id' => $users[0]->id,
        ]);

        ReportKanban::create([
            'project_id' => $project->id,
            'type' => 'draft_report',
            'file_path' => 'documents/reports/Draft_Hotel_Yogya_v1.pdf',
            'version' => 1,
            'is_approved' => true,
        ]);

        ApprovalKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[2]->id,
            'stage' => 'internal_review',
            'status' => 'approved',
            'comments' => 'Draft laporan sudah sesuai standar. Disetujui untuk dikirim ke klien.',
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[2]->id,
            'activity_type' => 'approval',
            'stage_context' => 'client_approval',
            'description' => 'Draft disetujui internal. Menunggu approval dari klien Bank Mandiri.',
        ]);

        return $project;
    }

    /**
     * Create Final Report stage project
     */
    private function createFinalReportProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0008',
            'name' => 'Penilaian Mall - Surabaya',
            'location' => 'Jl. Basuki Rahmat No. 8, Surabaya',
            'current_stage' => 'final_report',
            'priority_status' => 'normal',
            'due_date' => now()->addDays(5),
        ]);

        ProposalKanban::create([
            'project_id' => $project->id,
            'proposal_number' => 'PRP-202601-0007',
            'date_sent' => now()->subDays(30),
            'status' => 'approved',
        ]);

        ContractKanban::create([
            'project_id' => $project->id,
            'spk_number' => 'SPK-2026-0006',
            'signed_date' => now()->subDays(25),
            'file_path' => 'documents/contracts/SPK_Mall_Surabaya.pdf',
        ]);

        InspectionKanban::create([
            'project_id' => $project->id,
            'surveyor_id' => $users[3]->id,
            'inspection_date' => now()->subDays(20),
            'notes' => 'Mall 5 lantai, luas bangunan 15000m2. Tingkat okupansi 85%.',
            'latitude' => '-7.2575',
            'longitude' => '112.7521',
        ]);

        WorkingPaperKanban::create([
            'project_id' => $project->id,
            'analyst_id' => $users[1]->id,
        ]);

        ReportKanban::create([
            'project_id' => $project->id,
            'type' => 'draft_report',
            'file_path' => 'documents/reports/Draft_Mall_Surabaya_v2.pdf',
            'version' => 2,
            'is_approved' => true,
        ]);

        ApprovalKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[2]->id,
            'stage' => 'internal_review',
            'status' => 'approved',
            'comments' => 'Revisi minor sudah diperbaiki. Approved.',
        ]);

        ApprovalKanban::create([
            'project_id' => $project->id,
            'user_id' => null, // Client approval
            'stage' => 'client_approval',
            'status' => 'approved',
            'comments' => 'Klien menyetujui draft laporan. Silakan cetak final.',
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[0]->id,
            'activity_type' => 'stage_move',
            'stage_context' => 'final_report',
            'description' => 'Approval klien diterima. Menyiapkan laporan final untuk dicetak.',
        ]);

        return $project;
    }

    /**
     * Create Invoicing stage project
     */
    private function createInvoicingProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0009',
            'name' => 'Penilaian Gedung Perkantoran - SCBD',
            'location' => 'SCBD Lot 9, Jakarta Selatan',
            'current_stage' => 'invoicing',
            'priority_status' => 'warning',
            'due_date' => now()->addDays(3),
        ]);

        ProposalKanban::create([
            'project_id' => $project->id,
            'proposal_number' => 'PRP-202601-0008',
            'date_sent' => now()->subDays(35),
            'status' => 'approved',
        ]);

        ContractKanban::create([
            'project_id' => $project->id,
            'spk_number' => 'SPK-2026-0007',
            'signed_date' => now()->subDays(30),
            'file_path' => 'documents/contracts/SPK_Gedung_SCBD.pdf',
        ]);

        InspectionKanban::create([
            'project_id' => $project->id,
            'surveyor_id' => $users[4]->id,
            'inspection_date' => now()->subDays(25),
            'notes' => 'Gedung 20 lantai, grade A office. Okupansi 90%.',
            'latitude' => '-6.2244',
            'longitude' => '106.8094',
        ]);

        WorkingPaperKanban::create([
            'project_id' => $project->id,
            'analyst_id' => $users[0]->id,
        ]);

        ReportKanban::create([
            'project_id' => $project->id,
            'type' => 'final_report',
            'file_path' => 'documents/reports/Final_Gedung_SCBD.pdf',
            'version' => 1,
            'is_approved' => true,
        ]);

        ApprovalKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[2]->id,
            'stage' => 'internal_review',
            'status' => 'approved',
            'comments' => 'Laporan final sudah sesuai.',
        ]);

        ApprovalKanban::create([
            'project_id' => $project->id,
            'user_id' => null,
            'stage' => 'client_approval',
            'status' => 'approved',
            'comments' => 'Klien setuju. Silakan kirim laporan dan invoice.',
        ]);

        InvoiceKanban::create([
            'project_id' => $project->id,
            'invoice_number' => 'INV-202601-0001',
            'status' => 'unpaid',
            'payment_due_date' => now()->addDays(14),
            'paid_at' => null,
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[1]->id,
            'activity_type' => 'stage_move',
            'stage_context' => 'invoicing',
            'description' => 'Laporan final sudah dikirim. Invoice terbit, menunggu pembayaran.',
        ]);

        return $project;
    }

    /**
     * Create Done (completed) project
     */
    private function createDoneProject($client, $users)
    {
        $project = ProjectKanban::create([
            'client_id' => $client->id,
            'project_code' => 'PRJ-2026-0010',
            'name' => 'Penilaian Rumah Mewah - Pondok Indah',
            'location' => 'Jl. Metro Pondok Indah, Jakarta Selatan',
            'current_stage' => 'done',
            'priority_status' => 'normal',
            'due_date' => now()->subDays(5),
        ]);

        ProposalKanban::create([
            'project_id' => $project->id,
            'proposal_number' => 'PRP-202601-0009',
            'date_sent' => now()->subDays(40),
            'status' => 'approved',
        ]);

        ContractKanban::create([
            'project_id' => $project->id,
            'spk_number' => 'SPK-2026-0008',
            'signed_date' => now()->subDays(35),
            'file_path' => 'documents/contracts/SPK_Rumah_PI.pdf',
        ]);

        InspectionKanban::create([
            'project_id' => $project->id,
            'surveyor_id' => $users[3]->id,
            'inspection_date' => now()->subDays(30),
            'notes' => 'Rumah 2 lantai, LT 800m2, LB 500m2. Kondisi sangat baik.',
            'latitude' => '-6.2726',
            'longitude' => '106.7842',
        ]);

        WorkingPaperKanban::create([
            'project_id' => $project->id,
            'analyst_id' => $users[1]->id,
        ]);

        ReportKanban::create([
            'project_id' => $project->id,
            'type' => 'final_report',
            'file_path' => 'documents/reports/Final_Rumah_PI.pdf',
            'version' => 1,
            'is_approved' => true,
        ]);

        ApprovalKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[2]->id,
            'stage' => 'internal_review',
            'status' => 'approved',
            'comments' => 'OK',
        ]);

        ApprovalKanban::create([
            'project_id' => $project->id,
            'user_id' => null,
            'stage' => 'client_approval',
            'status' => 'approved',
            'comments' => 'Approved',
        ]);

        InvoiceKanban::create([
            'project_id' => $project->id,
            'invoice_number' => 'INV-202601-0002',
            'status' => 'paid',
            'payment_due_date' => now()->subDays(10),
            'paid_at' => now()->subDays(7),
        ]);

        // Final archived document
        DocumentKanban::create([
            'project_id' => $project->id,
            'uploader_id' => $users[0]->id,
            'category' => 'report_file',
            'file_name' => 'Laporan_Final_Rumah_PI_Arsip.pdf',
            'file_path' => 'documents/archive/Laporan_Final_Rumah_PI.pdf',
            'file_type' => 'pdf',
            'file_size' => 5242880,
            'description' => 'Laporan final yang sudah diarsipkan',
        ]);

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $users[0]->id,
            'activity_type' => 'stage_move',
            'stage_context' => 'done',
            'description' => 'Proyek selesai. Pembayaran lunas, laporan diarsipkan.',
        ]);

        return $project;
    }
}
