<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Clients - Menyimpan data klien
     * 
     * Type:
     * - bank: Client perbankan yang memiliki debitur
     * - pt_cv: Client PT/CV yang langsung memiliki project atau memiliki PT anak
     * - debitur: Debitur dari bank (child dari client bank)
     * 
     * Parent ID digunakan untuk:
     * - Debitur: parent_id = ID bank
     * - PT Anak: parent_id = ID PT induk
     * 
     * Alur:
     * - Bank → Debitur (child) → Projects → Assets
     * - PT/CV → Projects → Assets
     * - PT/CV → PT Anak (child) → Projects → Assets
     */
    public function up(): void
    {
        Schema::create('clients_kanban', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // Nama kontak person / debitur
            $table->string('company_name')->nullable(); // Nama perusahaan/instansi
            $table->string('spk_number')->nullable();   // Nomor SPK untuk bank
            $table->string('type', 20)->default('bank'); // bank / pt_cv / debitur
            $table->foreignId('parent_id')              // Self-referential: debitur->bank, pt_anak->pt_induk
                ->nullable()
                ->constrained('clients_kanban')
                ->onDelete('cascade');
            $table->timestamps();
            
            $table->index('type');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients_kanban');
    }
};
