<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'title',              // Di UI ini jadi "Topik Pembelajaran"
        'learning_objective', // Di UI ini jadi "Tujuan Pembelajaran"
        'media_type',         // BARU: Dari UI Step 3 (Tipe Media)
        'media_url',          // BARU: Dari UI Step 3 (Link/URL)
        'scheduled_date',
        'scheduled_month'
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
