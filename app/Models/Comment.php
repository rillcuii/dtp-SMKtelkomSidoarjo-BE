<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'showcase_id',
        'body',
        'parent_id',
        'is_official_review'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showcase()
    {
        return $this->belongsTo(Showcase::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}
