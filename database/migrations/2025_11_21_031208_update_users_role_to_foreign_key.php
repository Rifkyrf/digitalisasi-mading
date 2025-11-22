<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Cek apakah kolom `role` adalah enum atau foreign key
        // Jika enum, maka ubah ke foreign key. Jika sudah foreign key, lewati.
        $columnInfo = DB::select("
            SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, IS_NULLABLE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'role' AND TABLE_SCHEMA = DATABASE()
        ");

        $roleColumn = $columnInfo[0] ?? null;

        if ($roleColumn && strpos($roleColumn->COLUMN_TYPE, 'enum') !== false) {
            // Kolom `role` adalah enum, maka kita ubah

            // Buat kolom `role_id` sebagai pengganti
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('nis');
            });

            // Isi `role_id` berdasarkan `role` enum
            DB::table('users')->where('role', 'admin')->update(['role_id' => DB::table('hakgunas')->where('name', 'admin')->first()->id]);
            DB::table('users')->where('role', 'guru')->update(['role_id' => DB::table('hakgunas')->where('name', 'guru')->first()->id]);
            DB::table('users')->where('role', 'siswa')->update(['role_id' => DB::table('hakgunas')->where('name', 'siswa')->first()->id]);
            DB::table('users')->where('role', 'guest')->update(['role_id' => DB::table('hakgunas')->where('name', 'guest')->first()->id]);

            // Tambahkan foreign key
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')->references('id')->on('hakgunas');
            });

            // Hapus kolom `role` yang lama
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });

            // Ganti nama `role_id` menjadi `role`
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role_id', 'role');
            });
        }
    }

    public function down()
    {
        // Kembalikan ke enum
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key jika ada
            if (Schema::hasColumn('users', 'role')) {
                $table->dropForeign(['role']);
            }
            $table->dropColumn('role');
        });

        // Tambahkan kembali kolom enum `role`
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['guest', 'siswa', 'guru', 'admin'])->default('guest');
        });
    }
};