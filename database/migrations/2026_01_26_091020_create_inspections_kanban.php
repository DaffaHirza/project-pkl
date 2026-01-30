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
        Schema::create('inspections_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            $table->unsignedBigInteger('project_asset_id')->nullable(); // FK ditambahkan nanti
            $table->foreignId('surveyor_id')->constrained('users'); // Siapa yang survei
            $table->date('inspection_date');
            $table->text('notes')->nullable();

            // Koordinat GPS (Untuk validasi lokasi via HP) kalau mau
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            
            $table->timestamps();
            
            // Index untuk query
            $table->index('project_asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections_kanban');
    }
};
