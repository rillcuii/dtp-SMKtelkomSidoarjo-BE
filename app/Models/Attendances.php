<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendances extends Model
{
    use HasFactory;

    // Pakai protected $table jika nama tabel di DB kamu 'attendances' 
    // tapi nama Model-nya pakai akhiran 's' (Attendances)
    protected $table = 'attendances';

    protected $fillable = [
        'journal_id',
        'student_id',
        'status',                   // Hadir, Alfa, dll
        'competence_status',        // Capaian Kompetensi
        'showcase_status',          // Status Portofolio
        'is_competition_candidate', // Kandidat Lomba (Boolean)
        'is_recovery_needed',       // Perlu Recovery (Boolean)
        'score'                     // Nilai angka (jika ada)
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
