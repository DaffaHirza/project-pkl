<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\User;
use App\Models\DocumentItems;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'skor',
        'kesimpulan',
        'status',
    ];
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function documentItems()
    {
        return $this->hasMany(DocumentItems::class);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => ucwords(str_replace('_', ' ', $this->status)),
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->status) {
                    'draft'       => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-300',
                    'proses'      => 'bg-brand-100 text-brand-800 dark:bg-brand-900 dark:text-brand-300',
                    'cocok'       => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
                    'tidak_cocok' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                };
            }
        );
    }
}
