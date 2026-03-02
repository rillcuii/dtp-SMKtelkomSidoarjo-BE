<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('showcase_tags', function (Blueprint $table) {
            $table->id();
            // Sambungkan ke tabel showcases (Cascade delete)
            $table->foreignId('showcase_id')->constrained('showcases')->onDelete('cascade');

            $table->string('name');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('showcase_tags');
    }
};
