<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('osis_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role'); // Ketua Umum, Sekbid 1, dll
            $table->string('photo')->nullable(); // path file di storage
            $table->enum('type', ['inti', 'sekbid']); // 7 inti vs sekbid 1-10
            $table->integer('order')->default(0); // urutan tampil
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('osis_members');
    }
};