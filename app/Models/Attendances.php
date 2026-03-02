<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendances extends Model
{
    use HasFactory;

    // Nama tabel jika kamu membuat migration dengan nama 'attendances' (jamak) 
    // tapi modelnya 'Attendance' (tunggal), Laravel biasanya otomatis. 
    // Tapi jika modelmu namanya 'Attendances' (pakai s), sebaiknya ubah jadi 'Attendance' agar standar.

    protected $fillable = [
        'journal_id', 
        'student_id', 
        'status', 
        'competence_status', 
        'showcase_status', 
        'mapping_status', 
        'score'
    ];

    /**
     * Relasi ke Jurnal (Induknya)
     */
    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Relasi ke Siswa (User)
     * Kita arahkan student_id ke model User
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}