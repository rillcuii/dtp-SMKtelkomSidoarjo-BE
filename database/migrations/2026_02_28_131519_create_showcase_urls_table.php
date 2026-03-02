<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('showcase_urls', function (Blueprint $table) {
            $table->id();
            // Sambungkan ke tabel showcases, kalau showcase dihapus, URL-nya ikut terhapus (cascade)
            $table->foreignId('showcase_id')->constrained('showcases')->onDelete('cascade');

            $table->string('name'); // Contoh isi: "GitHub", "Live Demo", "Figma"
            $table->string('url');  // Contoh isi: "https://github.com/bos/karya"

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('showcase_urls');
    }
};
