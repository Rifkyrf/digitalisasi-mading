<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Agar membaca baris pertama sebagai header

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'nis' => $row['nis'],
            'role' => $row['role'],
            'password' => Hash::make('password_default'), // Atau gunakan kolom password dari Excel jika ada
        ]);
    }
}