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
        Schema::table('attendances', function (Blueprint $table) {
            // 1. Ubah kolom status & penilaian agar nullable (biar sesi reguler gak error)
            $table->string('status')->nullable()->change(); // hadir, alfa, dll
            $table->string('competence_status')->nullable()->change(); // tercapai, proses, pendampingan
            $table->string('showcase_status')->nullable()->change(); // belum, proses, selesai

            // 2. Hapus mapping_status yang lama (ENUM) karena di UI itu bentuknya Checkbox (bisa pilih dua-duanya)
            $table->dropColumn('mapping_status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            // 3. Tambah kolom baru pengganti mapping_status dalam bentuk boolean
            $table->boolean('is_competition_candidate')->default(false)->after('showcase_status');
            $table->boolean('is_recovery_needed')->default(false)->after('is_competition_candidate');
        });
    }
};
