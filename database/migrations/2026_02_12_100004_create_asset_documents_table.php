<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Asset Documents - File upload per stage (max 100MB)
     */
    public function up(): void
    {
        Schema::create('asset_documents_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('project_assets_kanban')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->unsignedTinyInteger('stage');           // Stage saat upload (1-13)
            $table->string('file_name');                    // Nama file asli
            $table->string('file_path');                    // Path di storage
            $table->string('file_type', 10)->nullable();    // pdf, jpg, docx, dll
            $table->unsignedBigInteger('file_size');        // Ukuran dalam bytes (max 100MB)
            $table->text('description')->nullable();        // Keterangan file
            $table->timestamps();
            
            $table->index(['asset_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_documents_kanban');
    }
};
