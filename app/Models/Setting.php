<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'settings';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'key',
        'value',
    ];
}
