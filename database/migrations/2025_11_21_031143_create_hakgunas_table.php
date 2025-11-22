<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hakgunas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'admin', 'guru', 'siswa', 'guest'
            $table->timestamps();
        });

        // Isi data awal
        DB::table('hakgunas')->insert([
            ['name' => 'admin'],
            ['name' => 'guru'],
            ['name' => 'siswa'],
            ['name' => 'guest'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('hakgunas');
    }
};