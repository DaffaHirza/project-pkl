<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Project Assets - Objek penilaian dengan workflow stage 1-13
     * 
     * Stage Workflow:
     * 1. Inisiasi          - Masuknya permintaan penilaian
     * 2. Penawaran         - Pembuatan proposal/surat penawaran
     * 3. Kesepakatan       - Kontrak/SPK ditandatangani
     * 4. Eksekusi Lapangan - Tim melakukan inspeksi
     * 5. Analisis          - Pembuatan kertas kerja
     * 6. Review 1          - Review kertas kerja
     * 7. Draft Resume      - Kirim draft ke klien
     * 8. Approval Klien    - Klien menyetujui draft
     * 9. Draft Laporan     - Pembuatan draft laporan
     * 10. Review 2         - Review oleh reviewer
     * 11. Finalisasi       - Cetak laporan final
     * 12. Delivery & Payment - Pengiriman dan pelunasan
     * 13. Arsip            - Dokumentasi selesai
     */
    public function up(): void
    {
        Schema::create('project_assets_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            $table->string('asset_code', 20)->unique()->nullable(); // AST-2026-0001
            $table->string('name');                         // Nama objek
            $table->text('description')->nullable();
            $table->string('asset_type', 30)->default('lainnya'); 
            // tanah, bangunan, tanah_bangunan, mesin, kendaraan, inventaris, aset_tak_berwujud, lainnya
            
            $table->text('location')->nullable();           // Alamat lokasi objek
            
            // Workflow Stage (1-13)
            $table->unsignedTinyInteger('current_stage')->default(1);
            
            // Priority untuk visual kanban
            $table->string('priority', 10)->default('normal'); // normal, warning, critical
            
            // Posisi dalam kanban column
            $table->unsignedInteger('position')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['project_id', 'current_stage']);
            $table->index(['project_id', 'position']); // For ordering in board
            $table->index('current_stage');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_assets_kanban');
    }
};
