<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id', // Boleh tetap ada sebagai shortcut
        'lesson_plan_id',
        'date',
        'material_link',
        'description', // Atau 'notes' sesuai migration kamu
        'image_proof',  // Tambahkan ini jika di migration ada foto bukti
        'status',       // Misalnya: Draft/Published
        'is_verified',  // Kunci untuk Payroll
    ];

    /**
     * Relasi ke Pengajar
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Bidang
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relasi ke Rencana Materi (Planner)
     */
    public function lessonPlan()
    {
        return $this->belongsTo(LessonPlan::class);
    }

    /**
     * RELASI PALING PENTING: Ke daftar Absensi & Nilai Siswa
     * Dengan ini kita bisa panggil: $journal->attendances
     */
    public function attendances()
    {
        return $this->hasMany(Attendances::class);
    }
}