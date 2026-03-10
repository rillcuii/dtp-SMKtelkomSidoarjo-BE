<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    // 'hourly_rate' tetap ada kalau sewaktu-waktu menu gajinya dipakai
    protected $fillable = ['name', 'slug', 'description', 'hourly_rate'];

    // --- BAGIAN TAMBAHAN UNTUK INISIAL ---
    // Ini yang akan dipanggil di API sebagai "teacher_initials"
    public function getTeacherInitialsAttribute()
    {
    // Ambil user yang rolenya mentor/guru sahaja untuk buat inisial
    return $this->users->whereIn('role', ['mentor', 'guru'])->map(function ($user) {
        // Buang simbol () dan isinya, ambil huruf depan setiap kata
        $cleanName = preg_replace('/\s*\(.*?\)\s*/', '', $user->name);
        return collect(explode(' ', trim($cleanName)))
            ->filter()
            ->map(fn($n) => strtoupper(substr($n, 0, 1)))
            ->implode('');
    })->values()->toArray();
    }

    // Kita fokus pakai relasi ini karena satu bidang bisa banyak pengajar (Many to Many)
    public function users()
    {
        return $this->belongsToMany(User::class, 'subject_user')->withTimestamps();
    }

    public function lessonPlans()
    {
        return $this->hasMany(LessonPlan::class);
    }
 // public function teachers()
    // {
    //     return $this->hasMany(User::class, 'subject_id'); // Sesuaikan dengan struktur tabelmu
    // }
}