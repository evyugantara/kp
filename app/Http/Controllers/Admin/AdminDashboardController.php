<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Announcement;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalRooms      = Room::count();
        $availableRooms  = Room::where('tersedia', true)->count();
        $occupiedRooms   = max(0, $totalRooms - $availableRooms);

        // Aman untuk skema berbeda: jika kolom 'status' ada, hitung yang aktif; jika tidak, fallback = kamar terisi.
        $activeTenants = Schema::hasColumn('tenants', 'status')
            ? Tenant::whereIn('status', ['aktif', 'active', 'valid'])->count()
            : $occupiedRooms;

        $activeAnnouncements = Announcement::active()->count();
        $allAnnouncements    = Announcement::count();

        return view('admin.dashboard', [
            'totalRooms'          => $totalRooms,
            'availableRooms'      => $availableRooms,
            'occupiedRooms'       => $occupiedRooms,
            'activeTenants'       => $activeTenants,
            'activeAnnouncements' => $activeAnnouncements,
            'allAnnouncements'    => $allAnnouncements,
        ]);
    }
}
