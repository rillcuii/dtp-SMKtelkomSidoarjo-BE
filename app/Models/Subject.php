<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini jika pakai Seeder nanti
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    // Tambahkan 'hourly_rate' agar Admin bisa input honor mentor
    protected $fillable = ['name', 'slug', 'description', 'hourly_rate'];

    public function users()
    {
        // Sebutkan nama tabel pivot 'subject_user' secara eksplisit
        return $this->belongsToMany(User::class, 'subject_user')->withTimestamps();
    }

    public function lessonPlans()
    {
        return $this->hasMany(LessonPlan::class);
    }
}
