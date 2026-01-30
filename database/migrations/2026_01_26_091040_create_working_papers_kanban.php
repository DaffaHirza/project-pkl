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
        Schema::create('working_papers_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            $table->unsignedBigInteger('project_asset_id')->nullable(); // FK ditambahkan nanti
            $table->foreignId('analyst_id')->constrained('users'); // Siapa yang menghitung
            
            // Kolom tambahan untuk analisa
            // Values: market_approach, cost_approach, income_approach
            $table->string('methodology')->nullable();
            $table->decimal('assessed_value', 15, 2)->nullable();
            // Values: draft, review, approved
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            
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
        Schema::dropIfExists('working_papers_kanban');
    }
};
