<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectKanban;

class ClientKanban extends Model
{
    use HasFactory;

    protected $table = 'clients_kanban';

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function projects()
    {
        return $this->hasMany(ProjectKanban::class, 'client_id');
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

    public function getProjectsCountAttribute(): int
    {
        return $this->projects()->count();
    }

    public function getActiveProjectsCountAttribute(): int
    {
        return $this->projects()->where('status', 'active')->count();
    }
}
