<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenant = Tenant::with('room')
            ->where('user_id', $request->user()->id)
            ->first();

        $nextDue  = $tenant?->nextDueDate();
        $daysLeft = $nextDue ? $nextDue->diffInDays(now(), false) * -1 : null;

        return view('tenant.dashboard', [
            'tenant'   => $tenant,
            'nextDue'  => $nextDue,
            'daysLeft' => $daysLeft,
            'title'    => 'Dashboard Penghuni',
        ]);
    }
}
