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
        // 1. Bikin Akun Admin, Mentor, Guru, dan Siswa
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

        // 2. Bikin Mata Pelajaran
        $subjectWeb = Subject::create([
            'name' => 'Web Development',
            'description' => 'Belajar bikin web dari nol sampai hosting.'
        ]);

        $subjectUIUX = Subject::create([
            'name' => 'UI/UX Design',
            'description' => 'Figma, Wireframing, dan Prototyping.'
        ]);

        // 3. Assign Mentor Budi ke Pelajaran Web Dev (Tabel Pivot)
        $mentor->subjects()->attach($subjectWeb->id);

        // 4. Bikin Rencana Pembelajaran (Lesson Plan) buat Web Dev
        LessonPlan::create([
            'subject_id' => $subjectWeb->id,
            'title' => 'Pengenalan HTML & CSS',
            'learning_objective' => 'Siswa mampu membuat struktur web dasar dan styling.',
            'scheduled_month' => 'Januari 2026',
        ]);

        LessonPlan::create([
            'subject_id' => $subjectWeb->id,
            'title' => 'Laravel Basic',
            'learning_objective' => 'Siswa memahami MVC dan Routing di Laravel.',
            'scheduled_month' => 'Februari 2026',
        ]);
    }
}
