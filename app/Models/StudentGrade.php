<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    protected $fillable = ['student_id', 'lesson_plan_id', 'score'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lessonPlan()
    {
        return $this->belongsTo(LessonPlan::class);
    }
}
