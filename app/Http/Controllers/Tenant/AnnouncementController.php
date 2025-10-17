<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->query('mode', 'aktif'); // 'aktif' | 'all'
        $now  = now();

        if ($mode === 'all') {
            // Semua yang dipublish. Urutkan: Aktif (0) → Terjadwal (1) → Selesai (2)
            $items = Announcement::query()
                ->where('is_published', true)
                ->orderByRaw(
                    "(CASE
                        WHEN ((starts_at IS NULL OR starts_at <= ?) AND (ends_at IS NULL OR ends_at >= ?)) THEN 0
                        WHEN (starts_at IS NOT NULL AND starts_at > ?) THEN 1
                        ELSE 2
                      END)", [$now, $now, $now]
                )
                ->orderByDesc('starts_at')
                ->orderByDesc('created_at')
                ->paginate(10)
                ->appends(['mode' => 'all']);
        } else {
            // Hanya yang aktif (default)
            $items = Announcement::active()
                ->orderByDesc('starts_at')
                ->orderByDesc('created_at')
                ->paginate(10)
                ->appends(['mode' => 'aktif']);
        }

        return view('tenant.announcements.index', [
            'title' => 'Pengumuman',
            'items' => $items,
            'mode'  => $mode,
        ]);
    }
}
