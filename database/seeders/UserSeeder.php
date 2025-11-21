<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin BN666',
            'email' => 'admin@bn666.sch.id',
            'nis' => '00000',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Pak Budi, Guru',
            'email' => 'guru@bn666.sch.id',
            'nis' => '99999',
            'password' => bcrypt('guru123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Siswa 1',
            'email' => 'siswa@bn666.sch.id',
            'nis' => '12345',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);
    }
}