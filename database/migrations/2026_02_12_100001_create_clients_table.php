<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Clients - Menyimpan data klien
     * 1 Client bisa punya banyak Project
     */
    public function up(): void
    {
        Schema::create('clients_kanban', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // Nama kontak person
            $table->string('company_name')->nullable(); // Nama perusahaan/instansi
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients_kanban');
    }
};
