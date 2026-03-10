<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_plans', function (Blueprint $table) {
            // Menambah kolom untuk Step 3 di UI
            $table->string('media_type')->nullable()->after('learning_objective'); // isinya: 'video', 'document', atau 'link'
            $table->string('media_url')->nullable()->after('media_type');          // isinya: link url-nya
        });
    }

    public function down(): void
    {
        Schema::table('lesson_plans', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'media_url']);
        });
    }
};
