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
        Schema::create('documents_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            $table->unsignedBigInteger('project_asset_id')->nullable(); // FK ditambahkan nanti
            $table->foreignId('uploader_id')->constrained('users');
            
            // Kategori: 'contract', 'field_photo', 'legal_doc', 'report_file'
            $table->string('category'); 
            
            $table->string('file_name');
            $table->string('file_path'); // Path penyimpanan di server/S3
            $table->string('file_type'); // pdf, jpg, png
            $table->bigInteger('file_size');
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
        Schema::dropIfExists('documents_kanban');
    }
};
