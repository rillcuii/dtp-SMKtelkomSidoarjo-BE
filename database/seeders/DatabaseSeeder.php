<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;
use App\Models\LessonPlan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Master
        $admin = User::create([
            'name' => 'Admin DTP',
            'email' => 'admin@smktelkom.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $mentor = User::create([
            'name' => 'Budi (Mentor Industri)',
            'email' => 'mentor@smktelkom.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'mentor',
        ]);

        $siswa = User::create([
            'name' => 'Andi (Siswa)',
            'email' => 'siswa@smktelkom.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
        ]);

        // 2. Buat Bidang (Subjects) + Harga Per Jam (Hourly Rate)
        $subjectWeb = Subject::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Belajar bikin web dari nol sampai hosting.',
            'hourly_rate' => 150000 // Contoh honor per sesi/jam
        ]);

        $subjectUIUX = Subject::create([
            'name' => 'UI/UX Design',
            'slug' => 'ui-ux-design',
            'description' => 'Figma, Wireframing, dan Prototyping.',
            'hourly_rate' => 125000
        ]);

        // 3. Hubungkan User ke Bidang (Tabel Pivot)
        // Mentor Budi mengajar Web Dev
        $mentor->subjects()->attach($subjectWeb->id);

        // Siswa Andi mengambil kelas Web Dev
        $siswa->subjects()->attach($subjectWeb->id);

        // 4. Buat Rencana Pembelajaran (Lesson Plan)
        // Sekarang wajib ada 'user_id' (siapa pengajarnya) dan 'scheduled_date'
        LessonPlan::create([
            'user_id' => $mentor->id,
            'subject_id' => $subjectWeb->id,
            'title' => 'Pengenalan HTML & CSS',
            'learning_objective' => 'Siswa mampu membuat struktur web dasar dan styling.',
            'scheduled_date' => now()->format('Y-m-d'), // Set tanggal hari ini agar muncul di jadwal
            'scheduled_month' => 'Maret 2026',
        ]);

        LessonPlan::create([
            'user_id' => $mentor->id,
            'subject_id' => $subjectWeb->id,
            'title' => 'Laravel Basic',
            'learning_objective' => 'Siswa memahami MVC dan Routing di Laravel.',
            'scheduled_date' => now()->addDays(7)->format('Y-m-d'), // Jadwal minggu depan
            'scheduled_month' => 'Maret 2026',
        ]);
    }
}
