<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'lesson_plan_id',
        'date',
        'material_link',
        'description',
        'status',
        'is_verified',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function lessonPlan()
    {
        return $this->belongsTo(LessonPlan::class);
    }   
}
