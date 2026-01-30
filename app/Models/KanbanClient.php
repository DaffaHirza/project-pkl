<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanClient extends Model
{
    use HasFactory;

    protected $table = 'kanban_clients';

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
    ];

    /**
     * Get all projects for this client
     */
    public function projects()
    {
        return $this->hasMany(ProjectKanban::class, 'client_id');
    }
}
