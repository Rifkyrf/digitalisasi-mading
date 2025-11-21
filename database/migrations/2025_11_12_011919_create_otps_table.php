<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('phone'); // Nomor HP penerima
            $table->string('otp_code'); // Kode OTP
            $table->timestamp('expires_at'); // Waktu kadaluarsa
            $table->boolean('used')->default(false); // Sudah digunakan atau belum
            $table->timestamps();
        });

        // Index untuk pencarian cepat berdasarkan nomor HP
        Schema::table('otps', function (Blueprint $table) {
            $table->index('phone');
        });
    }

    public function down()
    {
        Schema::dropIfExists('otps');
    }
}