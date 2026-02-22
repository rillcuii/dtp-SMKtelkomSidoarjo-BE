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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete(); // Asumsi siswa ada di tabel users
            $table->foreignId('lesson_plan_id')->constrained()->cascadeOnDelete();
            $table->integer('score'); // Skala 1-5

            // Fitur Otomatisasi Mapping dari PRD (Generated Column)
            $table->string('category')->storedAs("
        CASE 
            WHEN score = 5 THEN 'Fast'
            WHEN score >= 3 THEN 'Middle'
            ELSE 'Slow'
        END
    ");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};
