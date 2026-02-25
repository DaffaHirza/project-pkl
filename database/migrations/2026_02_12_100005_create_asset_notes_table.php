<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Asset Notes - Catatan per stage
     */
    public function up(): void
    {
        Schema::create('asset_notes_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('project_assets_kanban')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedTinyInteger('stage');           // Stage saat note dibuat (1-13)
            $table->string('type', 20)->default('note');    // note, stage_change, approval, rejection
            $table->text('content');                        // Isi catatan
            $table->timestamps();
            
            $table->index(['asset_id', 'stage']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_notes_kanban');
    }
};
