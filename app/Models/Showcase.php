<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showcase extends Model
{
    protected $fillable = [
        'student_id',
        'title',
        'media_url',
        'media_type',
        'description',
        'likes_count',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }       
}
