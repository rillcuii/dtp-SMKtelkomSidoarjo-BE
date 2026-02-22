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
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200); // Dibatasi 200 karakter sesuai SQL di dokumen
            $table->text('learning_objective'); // Tujuan pembelajaran (Sesuai dokumen)
            $table->string('scheduled_month', 20); // Sesuai dokumen
            $table->timestamps(); // Standar bawaan Laravel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_plans');
    }
};
