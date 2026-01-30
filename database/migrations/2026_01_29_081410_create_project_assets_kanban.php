<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat tabel project_assets_kanban dan menambahkan FK constraints
     * ke tabel-tabel yang sudah memiliki kolom project_asset_id
     */
    public function up(): void
    {
        // 1. Buat tabel project_assets_kanban
        Schema::create('project_assets_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            
            // Identifikasi Objek
            $table->string('asset_code', 50)->unique()->nullable(); // Auto: PRJ001-A01
            $table->string('name'); // Nama objek (misal: "Tanah & Bangunan Kantor Pusat")
            $table->text('description')->nullable();
            
            // Jenis Aset
            $table->string('asset_type')->default('lainnya');
            // Values: tanah, bangunan, tanah_bangunan, mesin, kendaraan, inventaris, aset_tak_berwujud, lainnya
            
            // Lokasi Objek
            $table->text('location_address')->nullable();
            $table->string('location_coordinates', 100)->nullable(); // GPS: lat,lng
            
            // Technical Workflow Stage (per-objek)
            $table->string('current_stage')->default('pending')->index();
            // Values: pending, inspection, analysis, review, client_approval, final_report, done
            
            // Priority
            $table->string('priority_status')->default('normal');
            // Values: normal, warning, critical
            
            // Kanban positioning
            $table->integer('position')->default(0);
            
            // Deadline per objek
            $table->date('target_completion_date')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk query kanban
            $table->index(['project_id', 'current_stage']);
        });

        // 2. Tambahkan FK constraints ke tabel-tabel yang sudah ada
        Schema::table('inspections_kanban', function (Blueprint $table) {
            $table->foreign('project_asset_id')
                  ->references('id')
                  ->on('project_assets_kanban')
                  ->onDelete('cascade');
        });

        Schema::table('working_papers_kanban', function (Blueprint $table) {
            $table->foreign('project_asset_id')
                  ->references('id')
                  ->on('project_assets_kanban')
                  ->onDelete('cascade');
        });

        Schema::table('reports_kanban', function (Blueprint $table) {
            $table->foreign('project_asset_id')
                  ->references('id')
                  ->on('project_assets_kanban')
                  ->onDelete('cascade');
        });

        Schema::table('approvals_kanban', function (Blueprint $table) {
            $table->foreign('project_asset_id')
                  ->references('id')
                  ->on('project_assets_kanban')
                  ->onDelete('cascade');
        });

        Schema::table('documents_kanban', function (Blueprint $table) {
            $table->foreign('project_asset_id')
                  ->references('id')
                  ->on('project_assets_kanban')
                  ->onDelete('cascade');
        });

        Schema::table('activities_kanban', function (Blueprint $table) {
            $table->foreign('project_asset_id')
                  ->references('id')
                  ->on('project_assets_kanban')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop FK constraints dulu (urutan terbalik)
        Schema::table('activities_kanban', function (Blueprint $table) {
            $table->dropForeign(['project_asset_id']);
        });

        Schema::table('documents_kanban', function (Blueprint $table) {
            $table->dropForeign(['project_asset_id']);
        });

        Schema::table('approvals_kanban', function (Blueprint $table) {
            $table->dropForeign(['project_asset_id']);
        });

        Schema::table('reports_kanban', function (Blueprint $table) {
            $table->dropForeign(['project_asset_id']);
        });

        Schema::table('working_papers_kanban', function (Blueprint $table) {
            $table->dropForeign(['project_asset_id']);
        });

        Schema::table('inspections_kanban', function (Blueprint $table) {
            $table->dropForeign(['project_asset_id']);
        });

        // Drop tabel project_assets_kanban
        Schema::dropIfExists('project_assets_kanban');
    }
};
