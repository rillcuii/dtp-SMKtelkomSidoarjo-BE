<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonPlan extends Model
{
    use HasFactory;

    // Pastikan fillable sesuai dengan migration terbaru kita
    protected $fillable = [
        'user_id',
        'subject_id',
        'title',
        'learning_objective', // atau 'description' sesuai migration kamu
        'scheduled_date',     // Tanggal spesifik (Penting untuk filter jadwal hari ini)
        'scheduled_month'     // Untuk grouping bulanan
    ];

    /**
     * Relasi ke Bidang (Subject)
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relasi ke Pengajar (User)
     * Penting: Supaya kita tahu siapa yang membuat planner ini
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Jurnal (Realisasi dari Planner)
     */
    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    /**
     * Scope untuk memudahkan memanggil jadwal hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', now()->toDateString());
    }
}
