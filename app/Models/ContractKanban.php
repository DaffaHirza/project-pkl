<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractKanban extends Model
{
    use HasFactory;

    protected $table = 'contracts_kanban';

    protected $fillable = [
        'project_id',
        'spk_number',
        'signed_date',
        'file_path',
    ];

    protected $casts = [
        'signed_date' => 'date',
    ];

    /**
     * Get the project for this contract
     */
    public function project()
    {
        return $this->belongsTo(ProjectKanban::class, 'project_id');
    }

    /**
     * Generate SPK number
     */
    public static function generateSpkNumber(): string
    {
        $year = date('Y');
        $lastContract = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastContract ? (int) substr($lastContract->spk_number, -4) + 1 : 1;
        
        return sprintf('SPK-%s-%04d', $year, $sequence);
    }
}
