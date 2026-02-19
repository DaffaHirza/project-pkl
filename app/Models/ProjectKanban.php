<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ClientKanban;
use App\Models\ProjectAssetKanban;

class ProjectKanban extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects_kanban';

    protected $fillable = [
        'client_id',
        'project_code',
        'name',
        'description',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const STATUS = [
        'active' => 'Aktif',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->project_code)) {
                $year = date('Y');
                $count = self::whereYear('created_at', $year)->withTrashed()->count();
                $project->project_code = sprintf('PRJ-%s-%03d', $year, $count + 1);
            }
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function client()
    {
        return $this->belongsTo(ClientKanban::class, 'client_id');
    }

    public function assets()
    {
        return $this->hasMany(ProjectAssetKanban::class, 'project_id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getAssetsCountAttribute(): int
    {
        return $this->assets()->count();
    }

    public function getProgressAttribute(): int
    {
        $assets = $this->assets;
        if ($assets->isEmpty()) return 0;
        
        $totalProgress = $assets->sum(fn($a) => ($a->current_stage / 13) * 100);
        return (int) round($totalProgress / $assets->count());
    }

    public function getAssetsByStageAttribute(): array
    {
        $assets = $this->assets()->orderBy('position')->get();
        $result = [];
        
        for ($stage = 1; $stage <= 13; $stage++) {
            $result[$stage] = $assets->where('current_stage', $stage)->values();
        }
        
        return $result;
    }

    // ==========================================
    // METHODS
    // ==========================================

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        return $this->save();
    }

    // ==========================================
    // SCOPES (for efficient queries)
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    public function scopeWithMinimalClient($query)
    {
        return $query->with('client:id,name,company_name');
    }

    public function scopeForKanban($query)
    {
        return $query->select('id', 'client_id', 'name', 'project_code', 'status', 'due_date')
            ->with('client:id,name,company_name');
    }
}
