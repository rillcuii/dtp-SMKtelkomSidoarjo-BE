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
        Schema::create('portfolios', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->text('description');
        $table->string('tags')->nullable(); // Untuk React, Figma, IoT dll
        $table->string('github_url')->nullable(); // Opsional
        $table->string('figma_url')->nullable(); // Opsional
        $table->string('demo_url')->nullable(); // Opsional
        $table->string('file_path'); // Untuk Thumbnail
        $table->boolean('is_public')->default(true); // Default true karena tombolnya "Upload & Publish"
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
