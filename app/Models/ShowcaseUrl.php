<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowcaseUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'showcase_id',
        'name',
        'url'
    ];

    // Relasi balik ke Showcase (Satu URL milik satu Showcase)
    public function showcase()
    {
        return $this->belongsTo(Showcase::class);
    }
}
