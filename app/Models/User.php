<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ... kodingan yang sudah kamu buat ...

    public function subjects()
    {
        // Tambahkan nama tabel pivot secara eksplisit agar aman
        return $this->belongsToMany(Subject::class, 'subject_user')->withTimestamps();
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    // --- TAMBAHAN HELPER ROLE ---
    public function isAdmin() { return $this->role === 'admin'; }
    public function isGuru() { return $this->role === 'guru'; }
    public function isMentor() { return $this->role === 'mentor'; }
    public function isSiswa() { return $this->role === 'siswa'; }
    
    // Khusus untuk absen: Mempermudah akses data absen siswa
    public function attendances()
    {
        return $this->hasMany(Attendances::class, 'student_id');
    }

}
