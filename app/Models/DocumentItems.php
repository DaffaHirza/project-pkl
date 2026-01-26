<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentItems extends Model
{
    protected $fillable = [
        'document_id',
        'nama_file',
        'kategori',
        'path_file',
        'hasil_ai',
        'status_verifikasi',
    ];

    protected $guarded = ['id'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
