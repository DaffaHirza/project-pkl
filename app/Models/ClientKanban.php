<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssetKanban;

class ClientKanban extends Model
{
    use HasFactory;

    protected $table = 'clients_kanban';

    protected $fillable = [
        'name',
        'company_name',
        'type',
        'parent_id',
    ];

    // ==========================================
    // CONSTANTS
    // ==========================================

    public const TYPES = [
        'bank' => 'Bank/Perbankan',
        'pt_cv' => 'PT/CV',
        'debitur' => 'Debitur',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Assets milik client ini
     */
    public function assets()
    {
        return $this->hasMany(AssetKanban::class, 'client_id');
    }

    /**
     * Parent client (bank untuk debitur, PT induk untuk PT anak)
     */
    public function parent()
    {
        return $this->belongsTo(ClientKanban::class, 'parent_id');
    }

    /**
     * Child clients (debitur untuk bank, PT anak untuk PT induk)
     */
    public function children()
    {
        return $this->hasMany(ClientKanban::class, 'parent_id');
    }

    /**
     * Alias: Debitur dari bank ini
     */
    public function debiturs()
    {
        return $this->children()->where('type', 'debitur');
    }

    /**
     * Alias: PT anak dari PT ini
     */
    public function childCompanies()
    {
        return $this->children()->where('type', 'pt_cv');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getDisplayNameAttribute(): string
    {
        return $this->company_name 
            ? "{$this->name} ({$this->company_name})"
            : $this->name;
    }

    public function getAssetsCountAttribute(): int
    {
        return $this->assets()->count();
    }

    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getIsBankAttribute(): bool
    {
        return $this->type === 'bank';
    }

    public function getIsPtCvAttribute(): bool
    {
        return $this->type === 'pt_cv';
    }

    public function getIsDebiturAttribute(): bool
    {
        return $this->type === 'debitur';
    }

    public function getHasParentAttribute(): bool
    {
        return $this->parent_id !== null;
    }

    public function getChildrenCountAttribute(): int
    {
        return $this->children()->count();
    }

    /**
     * Mendapatkan bank pemilik debitur ini
     */
    public function getBankAttribute()
    {
        if ($this->type === 'debitur') {
            return $this->parent;
        }
        return null;
    }

    /**
     * Mendapatkan semua assets dari children (untuk bank/pt induk)
     */
    public function getAllChildrenAssetsAttribute()
    {
        $childIds = $this->children()->pluck('id');
        return AssetKanban::whereIn('client_id', $childIds)->get();
    }

    /**
     * Hitung total assets termasuk dari children
     */
    public function getTotalAssetsCountAttribute(): int
    {
        $own = $this->assets()->count();
        $childIds = $this->children()->pluck('id');
        $childAssets = AssetKanban::whereIn('client_id', $childIds)->count();
        return $own + $childAssets;
    }
}
