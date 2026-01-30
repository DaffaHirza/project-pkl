<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all boards created by this user
     */
    public function boards()
    {
        return $this->hasMany(Board::class, 'created_by');
    }

    /**
     * Get all cards assigned to this user
     */
    public function assignedCards()
    {
        return $this->belongsToMany(Card::class, 'card_assignments');
    }

    /**
     * Get all card assignments for this user
     */
    public function cardAssignments()
    {
        return $this->hasMany(CardAssignment::class);
    }

    // ==========================================
    // KANBAN PROJECT RELATIONSHIPS
    // ==========================================

    /**
     * Get all inspections assigned to this user (as surveyor)
     */
    public function inspections()
    {
        return $this->hasMany(InspectionKanban::class, 'surveyor_id');
    }

    /**
     * Get all working papers assigned to this user (as analyst)
     */
    public function workingPapers()
    {
        return $this->hasMany(WorkingPaperKanban::class, 'analyst_id');
    }

    /**
     * Get all approvals made by this user
     */
    public function approvals()
    {
        return $this->hasMany(ApprovalKanban::class, 'user_id');
    }

    /**
     * Get all documents uploaded by this user
     */
    public function uploadedDocuments()
    {
        return $this->hasMany(DocumentKanban::class, 'uploader_id');
    }

    /**
     * Get all activities by this user
     */
    public function kanbanActivities()
    {
        return $this->hasMany(ActivityKanban::class, 'user_id');
    }

    /**
     * Get overdue assigned cards count
     */
    public function getOverdueCardsCountAttribute(): int
    {
        return $this->assignedCards()
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();
    }

    /**
     * Get high priority assigned cards count
     */
    public function getHighPriorityCardsCountAttribute(): int
    {
        return $this->assignedCards()
            ->where('priority', 'high')
            ->count();
    }
}
