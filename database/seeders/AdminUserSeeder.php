<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // <-- Impor model User
use Illuminate\Support\Facades\Hash; // <-- Impor Hash untuk enkripsi password

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Cari berdasarkan email
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'), // Password dienkripsi
                'role' => 'admin',
                'email_verified_at' => now() // Langsung verifikasi email
            ]
        );
    }
}