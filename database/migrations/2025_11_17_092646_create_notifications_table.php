<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('custom_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID user yang menerima notifikasi
            $table->string('title'); // Judul notifikasi
            $table->text('message'); // Isi notifikasi
            $table->string('type')->nullable(); // Jenis notifikasi (draft_submitted, work_published, dll)
            $table->string('url')->nullable(); // URL untuk mengarahkan saat klik notifikasi
            $table->boolean('read')->default(false); // Status apakah sudah dibaca
            $table->timestamp('read_at')->nullable(); // Waktu dibaca
            $table->timestamps(); // created_at, updated_at

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_notifications');
    }
};