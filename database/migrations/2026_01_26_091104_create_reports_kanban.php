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
        Schema::create('reports_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            $table->unsignedBigInteger('project_asset_id')->nullable(); // FK ditambahkan nanti
            $table->string('type'); // 'working_paper' (kertas kerja), 'draft_report', 'final_report'
            $table->string('file_path');
            $table->integer('version')->default(1);
            $table->boolean('is_approved')->default(false);
            
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
        Schema::dropIfExists('reports_kanban');
    }
};
