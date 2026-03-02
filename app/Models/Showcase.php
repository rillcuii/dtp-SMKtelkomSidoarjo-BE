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
        'tags',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function urls()
    {
        return $this->hasMany(ShowcaseUrl::class);
    }
    // Relasi ke ShowcaseTag (Satu Showcase punya banyak Tag)
    public function tags()
    {
        return $this->hasMany(ShowcaseTag::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class); // Pastikan kamu udah punya Model 'Like'
    }

    // Relasi ke Comments
    public function comments()
    {
        return $this->hasMany(Comment::class); // Pastikan kamu udah punya Model 'Comment'
    }
}
