<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\ProjectAssetKanban;
use App\Models\User;

class AssetDocumentKanban extends Model
{
    use HasFactory;

    protected $table = 'asset_documents_kanban';

    protected $fillable = [
        'asset_id',
        'uploaded_by',
        'stage',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
    ];

    protected $casts = [
        'stage' => 'integer',
        'file_size' => 'integer',
    ];

    // ==========================================
    // CONSTANTS
    // ==========================================

    // Max 20MB
    public const MAX_FILE_SIZE = 20971520;

    public const ALLOWED_TYPES = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'zip', 'rar', '7z',
        'txt', 'csv',
    ];

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function asset()
    {
        return $this->belongsTo(ProjectAssetKanban::class, 'asset_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getStageLabelAttribute(): string
    {
        return ProjectAssetKanban::STAGES[$this->stage] ?? 'Unknown';
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    // ==========================================
    // METHODS
    // ==========================================

    public function isImage(): bool
    {
        return in_array(strtolower($this->file_type), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function isPdf(): bool
    {
        return strtolower($this->file_type) === 'pdf';
    }

    public function deleteFile(): bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->delete($this->file_path);
        }
        return false;
    }

    public function getDownloadResponse()
    {
        $filePath = Storage::disk('public')->path($this->file_path);
        return response()->download($filePath, $this->file_name);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeAtStage($query, int $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeImages($query)
    {
        return $query->whereIn('file_type', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function scopeDocuments($query)
    {
        return $query->whereNotIn('file_type', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }
}
