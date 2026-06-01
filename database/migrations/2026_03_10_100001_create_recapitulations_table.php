<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Table for weekly recapitulation of asset progress.
     * Used for weekly evaluation meetings.
     */
    public function up(): void
    {
        Schema::create('recapitulations_kanban', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255); // e.g., "Rekapitulasi Minggu 1 Maret 2026"
            $table->date('period_start');
            $table->date('period_end');
            $table->text('summary')->nullable(); // Overall summary/notes
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['period_start', 'period_end']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recapitulations_kanban');
    }
};
