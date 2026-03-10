<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',      // Bidang (UI/UX, dll)
        'lesson_plan_id',  // Link ke materi planner
        'date',            // Tanggal mengajar
        'session_type',    // 'Reguler' atau 'Penilaian'
        'notes',           // Ringkasan aktivitas & kendala
        'image_proof',     // Foto bukti
        'is_verified',     // Status untuk Payroll
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

    public function attendances()
    {
        return $this->hasMany(Attendances::class, 'journal_id');
    }
}
