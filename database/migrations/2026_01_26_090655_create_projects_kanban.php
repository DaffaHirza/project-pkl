<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('kanban_clients')->onDelete('cascade');
            
            // Informasi Dasar Proyek
            $table->string('project_code')->unique(); // Kode Unik (misal: PRJ-2024-001)
            $table->string('name'); // Nama Penilaian (misal: Rumah Tinggal Bpk Budi)
            $table->string('location'); // Lokasi Aset
            
            // LOGIKA KANBAN
            // Stage: 'lead', 'proposal', 'contract', 'inspection', 'analysis', 
            //        'review', 'client_approval', 'final_report', 'invoicing', 'done'
            $table->string('current_stage')->default('lead')->index(); 
            
            // Status Warna: 'normal', 'warning' (ada halangan), 'critical' (telat/reject)
            $table->string('priority_status')->default('normal');

            // Global status project
            // Values: ongoing, completed, on_hold, cancelled
            $table->string('global_status')->default('ongoing');
            
            // Jumlah objek dalam project
            $table->integer('total_assets')->default(0);
            
            // Deadline global proyek (opsional)
            $table->date('due_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Agar data tidak hilang permanen jika dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_kanban');
    }
};
