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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Mentor/Guru yang input
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date'); // Sesuai report_date di dokumen
            $table->string('material_link')->nullable(); // Link materi yang diunggah (Sesuai dokumen)
            $table->text('description'); // Sesuai notes di dokumen
            $table->enum('status', ['Merah', 'Kuning', 'Hijau']); // Untuk Quick Jurnal
            $table->boolean('is_verified')->default(false); // Syarat Payroll otomatis
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
