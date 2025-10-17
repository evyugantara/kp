<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            // AnnouncementSeeder::class, // aktifkan kalau kamu pakai seeder pengumuman juga
        ]);
    }
}
