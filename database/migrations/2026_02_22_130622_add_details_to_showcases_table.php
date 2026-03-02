<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Di dalam file YYYY_MM_DD__add_details_to_showcases_table.php

    public function up(): void
    {
        Schema::table('showcases', function (Blueprint $table) {
            // Menambah kolom baru sesuai form upload di demo
            $table->string('tags')->nullable()->after('description'); // Contoh: "React, Figma, IoT"
            $table->string('github_url')->nullable()->after('tags');
            $table->string('figma_url')->nullable()->after('github_url');
            $table->string('demo_url')->nullable()->after('figma_url');
        });
    }

    public function down(): void
    {
        Schema::table('showcases', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn(['tags', 'github_url', 'figma_url', 'demo_url']);
        });
    }
};
