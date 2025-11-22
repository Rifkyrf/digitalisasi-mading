<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambah kolom role_id
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('role');
            $table->foreign('role_id')->references('id')->on('hakguna');
        });

        // Isi role_id berdasarkan role lama
        DB::table('users')->where('role', 'admin')->update(['role_id' => DB::table('hakguna')->where('name', 'admin')->first()->id]);
        DB::table('users')->where('role', 'guru')->update(['role_id' => DB::table('hakguna')->where('name', 'guru')->first()->id]);
        DB::table('users')->where('role', 'siswa')->update(['role_id' => DB::table('hakguna')->where('name', 'siswa')->first()->id]);
        DB::table('users')->where('role', 'guest')->update(['role_id' => DB::table('hakguna')->where('name', 'guest')->first()->id]);

        // Hapus kolom role lama
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // Ganti nama role_id menjadi role
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_id', 'role');
        });
    }

    public function down()
    {
        // Kembalikan ke enum
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role']);
            $table->dropColumn('role');
            $table->enum('role', ['guest', 'siswa', 'guru', 'admin'])->default('guest');
        });
    }
};