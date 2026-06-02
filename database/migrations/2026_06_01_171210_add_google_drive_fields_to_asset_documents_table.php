<?php

use App\Models\AssetDocumentKanban;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function documentTableName(): string
    {
        $modelTable = (new AssetDocumentKanban())->getTable();

        $candidates = array_unique([
            $modelTable,
            'asset_documents',
            'asset_document_kanbans',
            'asset_documents_kanban',
            'asset_kanban_documents',
        ]);

        foreach ($candidates as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        throw new \RuntimeException(
            'Tabel dokumen asset tidak ditemukan. Cek nama tabel pada migration create_asset_documents_table.php'
        );
    }

    public function up(): void
    {
        $tableName = $this->documentTableName();

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'storage_disk')) {
                $table->string('storage_disk')->default('google_drive');
            }

            if (!Schema::hasColumn($tableName, 'drive_file_id')) {
                $table->string('drive_file_id')->nullable();
            }

            if (!Schema::hasColumn($tableName, 'drive_web_view_link')) {
                $table->text('drive_web_view_link')->nullable();
            }

            if (!Schema::hasColumn($tableName, 'drive_web_content_link')) {
                $table->text('drive_web_content_link')->nullable();
            }
        });
    }

    public function down(): void
    {
        $tableName = $this->documentTableName();

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (Schema::hasColumn($tableName, 'drive_web_content_link')) {
                $table->dropColumn('drive_web_content_link');
            }

            if (Schema::hasColumn($tableName, 'drive_web_view_link')) {
                $table->dropColumn('drive_web_view_link');
            }

            if (Schema::hasColumn($tableName, 'drive_file_id')) {
                $table->dropColumn('drive_file_id');
            }

            if (Schema::hasColumn($tableName, 'storage_disk')) {
                $table->dropColumn('storage_disk');
            }
        });
    }
};