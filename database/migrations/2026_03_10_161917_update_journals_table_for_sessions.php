<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('journals', function (Blueprint $table) {
            // Tambah bidang, tanggal, dan tipe sesi
            $table->foreignId('subject_id')->after('user_id')->constrained('subjects')->onDelete('cascade');
            $table->date('date')->after('subject_id');
            $table->enum('session_type', ['Reguler', 'Penilaian'])->after('date')->default('Reguler');
        });
    }

    public function down()
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['subject_id', 'date', 'session_type']);
        });
    }
};
