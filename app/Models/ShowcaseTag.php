<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowcaseTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'showcase_id',
        'name'
    ];

    // Relasi balik ke Showcase
    public function showcase()
    {
        return $this->belongsTo(Showcase::class);
    }
}
