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
        Schema::create('invoices_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_kanban')->onDelete('cascade');
            $table->string('invoice_number');
            $table->string('status')->default('unpaid'); // unpaid, paid, cancelled
            $table->date('payment_due_date');
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_kanban');
    }
};
