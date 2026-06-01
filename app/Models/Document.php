<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'skor',
        'kesimpulan',
        'status',
        'token_input',
        'token_output',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the user who created this document
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Get total tokens for this document
     */
    public function getTotalTokenAttribute(): int
    {
        return $this->token_input + $this->token_output;
    }
}
