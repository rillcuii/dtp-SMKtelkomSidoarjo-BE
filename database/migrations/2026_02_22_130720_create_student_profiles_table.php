<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Data tambahan khusus siswa
            $table->string('kelas')->nullable();   // Contoh: "XI RPL 1"
            $table->string('jurusan')->nullable(); // Contoh: "Rekayasa Perangkat Lunak"
            $table->text('bio')->nullable();       // Deskripsi diri
            $table->string('skills')->nullable();  // Contoh: "React, Laravel, MySQL"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
