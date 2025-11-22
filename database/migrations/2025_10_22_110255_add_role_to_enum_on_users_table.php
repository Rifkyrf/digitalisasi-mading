<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Jika kolom `role` tidak ada, tambahkan sebagai enum
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['guest', 'siswa', 'guru', 'admin'])
                      ->default('guest'); // Atau 'siswa' jika Anda lebih suka
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};