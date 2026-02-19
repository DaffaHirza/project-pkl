<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\AssetDocumentKanban;
use App\Models\AssetNoteKanban;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // ==========================================
    // ROLE CONSTANTS
    // ==========================================
    
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPERUSER = 'superuser';

    public const ROLES = [
        self::ROLE_USER => 'User',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_SUPERUSER => 'Superuser (Developer)',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // ==========================================
    // ROLE METHODS
    // ==========================================

    /**
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is superuser (developer)
     */
    public function isSuperuser(): bool
    {
        return $this->role === self::ROLE_SUPERUSER;
    }

    /**
     * Check if user has admin-level access (admin or superuser)
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPERUSER]);
    }

    /**
     * Get role display name
     */
    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Unknown';
    }

    /**
     * Check if user can perform action
     * Superuser can do everything, admin can do most things, user is limited
     */
    public function can($ability, $arguments = []): bool
    {
        // Superuser bypass
        if ($this->isSuperuser()) {
            return true;
        }

        return parent::can($ability, $arguments);
    }

    /**
     * Scope: Active users only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Admins only
     */
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', [self::ROLE_ADMIN, self::ROLE_SUPERUSER]);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    // ==========================================
    // KANBAN RELATIONSHIPS
    // ==========================================

    /**
     * Get all documents uploaded by this user
     */
    public function uploadedDocuments()
    {
        return $this->hasMany(AssetDocumentKanban::class, 'uploaded_by');
    }

    /**
     * Get all notes created by this user
     */
    public function assetNotes()
    {
        return $this->hasMany(AssetNoteKanban::class, 'user_id');
    }
}
