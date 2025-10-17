<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;

class RoomMaintenanceController extends Controller
{
    public function syncAvailability()
    {
        // Reset semua kamar -> tersedia = true
        Room::query()->update(['tersedia' => true]);

        // Set tidak tersedia untuk kamar yang punya tenant status AKTIF
        Room::whereHas('tenants', function ($q) {
            $q->whereRaw("LOWER(TRIM(status)) = 'aktif'");
        })->update(['tersedia' => false]);

        return back()->with('ok','Ketersediaan kamar berhasil disinkron.');
    }
}
