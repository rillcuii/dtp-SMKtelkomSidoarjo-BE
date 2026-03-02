<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');

            // Status Kehadiran
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alfa'])->default('Hadir');

            // Penilaian Capaian Kompetensi (Opsional)
            $table->enum('competence_status', ['Tercapai', 'Proses', 'Pendampingan'])->nullable();

            // Penilaian Showcase/Portofolio (Opsional)
            $table->enum('showcase_status', ['Belum', 'Proses', 'Selesai'])->nullable();

            // Intervensi & Mapping (Opsional)
            $table->enum('mapping_status', ['None', 'Kandidat Lomba', 'Recovery'])->default('None');

            // Nilai Angka (Opsional)
            $table->integer('score')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
