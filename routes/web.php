<?php

use Illuminate\Support\Facades\Route;

// === Controller publik & admin yang sudah ada ===
use App\Http\Controllers\RoomController;
use App\Http\Controllers\Admin\PenghuniController as AdminPenghuniController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\RoomMaintenanceController;
use App\Http\Controllers\Admin\AdminDashboardController;

// === Controller penghuni ===
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\ProfileController as TenantProfileController;
use App\Http\Controllers\Tenant\AnnouncementController as TenantAnnouncementController;
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController; // <— TAMBAH

/*
|--------------------------------------------------------------------------
| Rute Publik (Halaman Informasi Kos)
|--------------------------------------------------------------------------
*/
Route::get('/', [RoomController::class,'publicIndex'])->name('home');
Route::get('/kamar/{room:kode}', [RoomController::class,'publicShow'])->name('rooms.public.show');

/*
|--------------------------------------------------------------------------
| Redirect /dashboard sesuai role
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/dashboard', function () {
        $role = strtolower(auth()->user()->role ?? '');
        if ($role === 'pengelola') return redirect()->route('admin.dashboard');
        if ($role === 'penghuni')  return redirect()->route('tenant.dashboard');
        return view('dashboard'); // fallback (kalau ada role lain)
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Rute Admin / Pengelola
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','pengelola'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard pakai controller statistik
    Route::get('/dashboard', [AdminDashboardController::class,'index'])->name('dashboard');

    // CRUD Kamar (admin.rooms.*)
    Route::resource('rooms', RoomController::class)->except(['show']);

    // Hapus 1 foto galeri kamar (penting untuk upload multi-foto)
    Route::delete('/rooms/{room}/images/{image}', [RoomController::class,'deleteImage'])
         ->name('rooms.images.destroy');

    // Sinkron ketersediaan kamar TANPA artisan
    Route::post('rooms/sync-availability', [RoomMaintenanceController::class, 'syncAvailability'])
         ->name('rooms.sync');

    // Modul Penghuni
    Route::resource('penghuni', AdminPenghuniController::class)
         ->parameters(['penghuni' => 'penghuni']);
    Route::post('penghuni/{penghuni}/reset-password', [AdminPenghuniController::class, 'resetPassword'])
         ->name('penghuni.reset');

    // Modul Tagihan (placeholder index yang sudah ada)
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');

    // PENGUMUMAN (CRUD lengkap) — admin.pengumuman.*
    Route::resource('pengumuman', PengumumanController::class)
         ->parameters(['pengumuman' => 'pengumuman']);
});

/*
|--------------------------------------------------------------------------
| Rute Penghuni
|--------------------------------------------------------------------------
*/
Route::middleware(['auth']) // kalau punya middleware role, ganti ke ['auth','penghuni']
    ->prefix('penghuni')->name('tenant.')->group(function () {
        Route::get('/dashboard', [TenantDashboardController::class,'index'])->name('dashboard');
        Route::get('/profil',    [TenantProfileController::class,'edit'])->name('profile.edit');
        Route::put('/profil',    [TenantProfileController::class,'update'])->name('profile.update');

        // Pengumuman (read-only untuk penghuni)
        Route::get('/pengumuman', [TenantAnnouncementController::class,'index'])
             ->name('announcements.index');

        // Pembayaran (QRIS Midtrans)
        Route::get('/pembayaran',         [TenantPaymentController::class, 'index'])->name('payments.index');
        Route::get('/pembayaran/success', [TenantPaymentController::class, 'success'])->name('payments.success');
        Route::get('/pembayaran/finish',  [TenantPaymentController::class, 'finish'])->name('payments.finish');
    });

    //route debug
    Route::get('/midtrans-debug', function () {
    return [
        'server_key_starts' => substr(config('midtrans.server_key'), 0, 10), // harus 'SB-Mid-serv'
        'client_key_starts' => substr(config('midtrans.client_key'), 0, 10), // harus 'SB-Mid-clie'
        'is_production'     => config('midtrans.is_production'),            // harus false
    ];
});


require __DIR__.'/auth.php';
