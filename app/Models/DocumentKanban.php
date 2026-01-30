<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentKanban extends Model
{
    use HasFactory;

    protected $table = 'documents_kanban';

    protected $fillable = [
        'project_id',
        'project_asset_id',
        'uploader_id',
        'category',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
    ];

    /**
     * Document category options
     */
    public const CATEGORIES = [
        'contract' => 'Kontrak',
        'field_photo' => 'Foto Lapangan',
        'legal_doc' => 'Dokumen Legal',
        'report_file' => 'File Laporan',
        'asset_photo' => 'Foto Objek',
        'supporting_doc' => 'Dokumen Pendukung',
    ];

    /**
     * Get the project for this document
     */
    public function project()
    {
        return $this->belongsTo(ProjectKanban::class, 'project_id');
    }

    /**
     * Get the asset for this document (if asset specific)
     */
    public function asset()
    {
        return $this->belongsTo(ProjectAsset::class, 'project_asset_id');
    }

    /**
     * Get the uploader (user) for this document
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Get human readable file size
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for project level documents (no asset)
     */
    public function scopeProjectLevel($query)
    {
        return $query->whereNull('project_asset_id');
    }

    /**
     * Scope for asset specific documents
     */
    public function scopeAssetLevel($query)
    {
        return $query->whereNotNull('project_asset_id');
    }

    /**
     * Check if this is an asset level document
     */
    public function isAssetDocument(): bool
    {
        return $this->project_asset_id !== null;
    }
}
