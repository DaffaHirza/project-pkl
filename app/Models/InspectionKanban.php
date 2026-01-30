<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionKanban extends Model
{
    use HasFactory;

    protected $table = 'inspections_kanban';

    protected $fillable = [
        'project_asset_id',
        'project_id', // Legacy, will be removed
        'surveyor_id',
        'inspection_date',
        'notes',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    /**
     * Get the asset for this inspection (NEW - Primary Relationship)
     */
    public function asset()
    {
        return $this->belongsTo(ProjectAsset::class, 'project_asset_id');
    }

    /**
     * Get the project through asset
     */
    public function project()
    {
        return $this->hasOneThrough(
            ProjectKanban::class,
            ProjectAsset::class,
            'id', // Foreign key on project_assets
            'id', // Foreign key on projects_kanban
            'project_asset_id', // Local key on inspections_kanban
            'project_id' // Local key on project_assets
        );
    }

    /**
     * Get the surveyor (user) for this inspection
     */
    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    /**
     * Get coordinates as array
     */
    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
            ];
        }
        return null;
    }
}
