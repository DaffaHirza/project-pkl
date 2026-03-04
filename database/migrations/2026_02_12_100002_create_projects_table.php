<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Projects - Menyimpan data project penilaian
     * 1 Project milik 1 Client, bisa punya banyak Asset
     */
    public function up(): void
    {
        Schema::create('projects_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients_kanban')->onDelete('cascade');
            $table->string('name');                         // Nama project
            $table->string('status', 20)->default('active'); // active, completed, cancelled
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects_kanban');
    }
};
