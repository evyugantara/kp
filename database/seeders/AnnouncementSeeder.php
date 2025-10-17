<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Carbon;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        // Cari pengelola; kalau tidak ada pakai user pertama; kalau tetap null pakai ID 1
        $admin    = User::where('role', 'pengelola')->first() ?? User::first();
        $authorId = $admin->id ?? 1;

        // Contoh 1: aktif sekarang tanpa batas waktu
        Announcement::updateOrCreate(
            ['judul' => 'Pengumuman Uji Aktif'],
            [
                'isi'          => "Ini pengumuman contoh dan sudah aktif.\nSilakan abaikan.",
                'starts_at'    => null,
                'ends_at'      => null,
                'is_published' => true,
                'created_by'   => $authorId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]
        );

        // Contoh 2: aktif dalam rentang 7 hari
        Announcement::updateOrCreate(
            ['judul' => 'Perawatan Air – Besok Pagi'],
            [
                'isi'          => "Akan ada perawatan tandon air besok pukul 09:00–10:00.\nMohon maaf atas ketidaknyamanannya.",
                'starts_at'    => Carbon::now()->subMinutes(5),
                'ends_at'      => Carbon::now()->addDays(7),
                'is_published' => true,
                'created_by'   => $authorId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]
        );
    }
}
