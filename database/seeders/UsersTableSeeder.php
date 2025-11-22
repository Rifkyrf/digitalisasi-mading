<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => '1',
                'nis' => '00001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Guru Satu',
                'email' => 'guru@example.com',
                'password' => Hash::make('password'),
                'role' => '2',
                'nis' => '11111',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siswa Satu',
                'email' => 'siswa@example.com',
                'password' => Hash::make('password'),
                'role' => '2',
                'nis' => '22222',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Guest User',
                'email' => 'guest@example.com',
                'password' => Hash::make('password'),
                'role' => '2',
                'nis' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}