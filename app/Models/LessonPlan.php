<?php

namespace App\Models;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Model;

class LessonPlan extends Model
{
    protected $fillable = ['subject_id', 'title', 'learning_objective', 'scheduled_month'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }   
}
