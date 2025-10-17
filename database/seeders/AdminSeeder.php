<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // nilai dasar
        $email = 'admin@pondokhasanah.test';

        // atribut wajib yang hampir pasti ada
        $attrs = [
            'name'              => 'Pengelola',
            'email'             => $email,
            'password'          => Hash::make('admin12345'), // <— PERBAIKI: "password" TANPA SPASI
            'email_verified_at' => now(),
        ];

        // tambahkan kolom opsional hanya jika ada di tabel users
        if (Schema::hasColumn('users', 'role'))  $attrs['role']  = 'pengelola';
        if (Schema::hasColumn('users', 'phone')) $attrs['phone'] = '6281234567890';
        if (Schema::hasColumn('users', 'remember_token')) $attrs['remember_token'] = str()->random(10);

        // buat / update tanpa duplikasi
        User::updateOrCreate(['email' => $email], $attrs);
    }
}
