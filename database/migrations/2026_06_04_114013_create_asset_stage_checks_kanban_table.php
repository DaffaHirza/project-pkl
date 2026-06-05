<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_stage_checks_kanban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')
                ->constrained('assets_kanban')
                ->onDelete('cascade');

            $table->unsignedTinyInteger('stage');
            $table->boolean('is_checked')->default(false);
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['asset_id', 'stage']);
            $table->index(['asset_id', 'is_checked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_stage_checks_kanban');
    }
};
