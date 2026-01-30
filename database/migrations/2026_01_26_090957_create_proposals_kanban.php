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
        Schema::create('proposals_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            $table->string('proposal_number')->unique();
            $table->date('date_sent');
            $table->string('status')->default('draft'); // draft, sent, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals_kanban');
    }
};
