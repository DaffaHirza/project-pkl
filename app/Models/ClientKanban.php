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
        'spk_number',
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

    /**
     * Total assets including children's assets (optimized single query)
     */
    public function getTotalAssetsCountAttribute(): int
    {
        $clientIds = collect([$this->id])
            ->merge($this->children()->pluck('id'));
        
        return AssetKanban::whereIn('client_id', $clientIds)->count();
    }

    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getChildrenCountAttribute(): int
    {
        return $this->children()->count();
    }
}
