<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('osis_members', function (Blueprint $table) {
            // Tambah kolom angkatan (wajib diisi)
            $table->string('angkatan')->after('type');

            // Tambah kolom nama_sekbid (opsional, hanya untuk type = 'sekbid')
            $table->string('nama_sekbid')->nullable()->after('type');

            // Ubah kolom role menjadi enum: ketua, sekretaris, anggota
            DB::statement("ALTER TABLE `osis_members` MODIFY `role` ENUM('ketua','sekretaris','anggota') NOT NULL");
        });
    }

    public function down()
    {
        Schema::table('osis_members', function (Blueprint $table) {
            $table->dropColumn(['angkatan', 'nama_sekbid']);

            // Kembalikan role ke tipe varchar(255)
            DB::statement("ALTER TABLE `osis_members` MODIFY `role` VARCHAR(255) NOT NULL");
        });
    }
};