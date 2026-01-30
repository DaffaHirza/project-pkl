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
        Schema::create('approvals_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            $table->unsignedBigInteger('project_asset_id')->nullable(); // FK ditambahkan nanti
            $table->foreignId('user_id')->nullable(); // Null jika approval dari Klien eksternal
            $table->string('approval_level')->default('asset'); // 'project' atau 'asset'
            $table->string('stage'); // 'internal_review', 'client_approval'
            $table->string('status'); // 'approved', 'rejected/revision'
            $table->text('comments')->nullable(); // Catatan revisi
            
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
        Schema::dropIfExists('approvals_kanban');
    }
};
