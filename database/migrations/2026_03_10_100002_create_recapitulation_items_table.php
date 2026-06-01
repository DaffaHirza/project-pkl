<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Items table for tracking individual asset progress within a recapitulation period.
     */
    public function up(): void
    {
        Schema::create('recapitulation_items_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recapitulation_id')->constrained('recapitulations_kanban')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets_kanban')->onDelete('cascade');
            
            // Progress tracking
            $table->unsignedTinyInteger('stage_start'); // Stage at period start
            $table->unsignedTinyInteger('stage_end');   // Stage at period end
            
            // Work status for this period
            $table->enum('work_status', [
                'not_started',   // Belum dikerjakan
                'in_progress',   // Sedang dikerjakan
                'completed',     // Selesai dalam periode ini
                'blocked',       // Terhambat
                'pending_review' // Menunggu review
            ])->default('in_progress');
            
            $table->text('activities')->nullable();     // What was done during this period
            $table->text('notes')->nullable();          // Additional notes/blockers
            $table->text('next_actions')->nullable();   // Planned next actions
            
            $table->timestamps();

            $table->unique(['recapitulation_id', 'asset_id']);
            $table->index('work_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recapitulation_items_kanban');
    }
};