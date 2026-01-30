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
        Schema::create('activities_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            $table->unsignedBigInteger('project_asset_id')->nullable(); // FK ditambahkan nanti
            $table->foreignId('user_id')->constrained(); // Siapa yang melakukan aksi
            
            // Tipe Aksi: 
            // 'stage_move' (pindah tahap kanban), 
            // 'comment' (diskusi biasa), 
            // 'approval' (approve draft), 
            // 'rejection' (tolak hasil), 
            // 'obstacle' (laporan halangan/macet),
            // 'upload' (upload file)
            $table->string('activity_type');
            
            // Konteks tahap saat ini
            $table->string('stage_context')->nullable(); 
            
            // Isi pesan / alasan reject / detail halangan
            $table->text('description')->nullable(); 
            
            $table->timestamps();
            
            // Index
            $table->index('project_asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities_kanban');
    }
};
