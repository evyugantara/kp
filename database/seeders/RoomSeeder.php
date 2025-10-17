<?php
// ======================================================================
// FILE: database/seeders/RoomSeeder.php   (NEW)
// ======================================================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        if (Room::count() === 0) {
            Room::factory()->count(8)->create();
        }
    }
}
