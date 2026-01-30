<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalKanban extends Model
{
    use HasFactory;

    protected $table = 'proposals_kanban';

    protected $fillable = [
        'project_id',
        'proposal_number',
        'date_sent',
        'status',
    ];

    protected $casts = [
        'date_sent' => 'date',
    ];

    /**
     * Proposal status options
     */
    public const STATUS = [
        'draft' => 'Draft',
        'sent' => 'Terkirim',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ];

    /**
     * Get the project for this proposal
     */
    public function project()
    {
        return $this->belongsTo(ProjectKanban::class, 'project_id');
    }

    /**
     * Generate proposal number
     */
    public static function generateProposalNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastProposal = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastProposal ? (int) substr($lastProposal->proposal_number, -4) + 1 : 1;
        
        return sprintf('PRP-%s%s-%04d', $year, $month, $sequence);
    }
}
