<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\TenantCredentialsMail;
use App\Mail\TenantPasswordResetMail;

class PenghuniController extends Controller
{
    public function index(Request $request)
    {
        $tenants = Tenant::with(['user:id,name,email,phone', 'room:id,kode,nama'])
            ->when($request->filled('s'), function ($qry) use ($request) {
                $s = '%'.$request->s.'%';
                $qry->whereHas('user', function ($u) use ($s) {
                        $u->where('name', 'like', $s)
                          ->orWhere('email', 'like', $s);
                    })
                    ->orWhereHas('room', function ($r) use ($s) {
                        $r->where('nama', 'like', $s)
                          ->orWhere('kode', 'like', $s);
                    })
                    ->orWhere('phone', 'like', $s)
                    ->orWhere('nik', 'like', $s);
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        // gunakan view admin/penghuni/index.blade.php
        return view('admin.penghuni.index', compact('tenants'));
    }

    public function create()
    {
        $rooms = Room::orderBy('nama')->get();
        return view('admin.penghuni.form', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // data user
            'name'     => ['required','string','max:150'],
            'email'    => ['required','email','max:190','unique:users,email'],
            'password' => ['nullable','string','min:6','confirmed'],

            // data tenant
            'room_id'        => ['required','exists:rooms,id'],
            'phone'          => ['nullable','string','max:30'],
            'nik'            => ['nullable','string','max:30'],
            'alamat'         => ['nullable','string','max:255'],
            'tanggal_masuk'  => ['nullable','date'],
            'tanggal_keluar' => ['nullable','date','after_or_equal:tanggal_masuk'],
            'status'         => ['required', Rule::in(['Aktif','Booking','Selesai'])],
            'catatan'        => ['nullable','string'],
        ]);

        // Batasi 1 penghuni AKTIF per kamar
        if ($data['status'] === 'Aktif') {
            $exists = Tenant::where('room_id', $data['room_id'])
                ->where('status', 'Aktif')->exists();
            if ($exists) {
                return back()->withInput()->with('error', 'Kamar sudah memiliki penghuni AKTIF.');
            }
        }

        // Password: pakai input jika ada; kalau tidak, auto-generate
        $plain = $request->filled('password')
            ? $request->input('password')
            : Str::password(10);

        $tenant = null; $user = null;
        DB::transaction(function () use (&$data, $plain, &$tenant, &$user) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($plain),
                'role'     => 'penghuni',
            ]);

            $data['user_id'] = $user->id;
            unset($data['name'], $data['email'], $data['password'], $data['password_confirmation']);

            $tenant = Tenant::create($data);
        });

        // Kirim kredensial via email (opsional, jika mail terkonfigurasi)
        try {
            Mail::to($user->email)->send(new TenantCredentialsMail(
                userName: $user->name,
                email: $user->email,
                plainPassword: $plain,
                roomName: $tenant->room?->nama,
                roomCode: $tenant->room?->kode
            ));
        } catch (\Throwable $e) {
            // diamkan saja agar tidak memblokir alur
        }

        $msg = $request->filled('password')
            ? 'Penghuni dibuat.'
            : 'Penghuni dibuat. Password sementara: '.$plain;

        return redirect()->route('admin.penghuni.index')->with('ok', $msg);
    }

    public function edit(Tenant $penghuni)
    {
        $rooms = Room::orderBy('nama')->get();
        $penghuni->load(['user','room']);

        // gunakan view admin/penghuni/form.blade.php
        return view('admin.penghuni.form', [
            'rooms'  => $rooms,
            'tenant' => $penghuni,
        ]);
    }

    public function update(Request $request, Tenant $penghuni)
    {
        $data = $request->validate([
            'name'            => ['required','string','max:150'],
            'email'           => ['required','email','max:190', Rule::unique('users','email')->ignore($penghuni->user_id)],
            'room_id'         => ['required','exists:rooms,id'],
            'phone'           => ['nullable','string','max:30'],
            'nik'             => ['nullable','string','max:30'],
            'alamat'          => ['nullable','string','max:255'],
            'tanggal_masuk'   => ['nullable','date'],
            'tanggal_keluar'  => ['nullable','date','after_or_equal:tanggal_masuk'],
            'status'          => ['required', Rule::in(['Aktif','Booking','Selesai'])],
            'catatan'         => ['nullable','string'],
        ]);

        if ($data['status'] === 'Aktif') {
            $exists = Tenant::where('room_id', $data['room_id'])
                ->where('status', 'Aktif')
                ->where('id', '!=', $penghuni->id)
                ->exists();
            if ($exists) {
                return back()->withInput()->with('error', 'Kamar sudah memiliki penghuni AKTIF lain.');
            }
        }

        DB::transaction(function () use (&$data, $penghuni) {
            $penghuni->user->update([
                'name'  => $data['name'],
                'email' => $data['email'],
            ]);
            unset($data['name'], $data['email']);
            $penghuni->update($data);
        });

        return redirect()->route('admin.penghuni.index')->with('ok', 'Data penghuni diperbarui.');
    }

    public function destroy(Tenant $penghuni)
    {
        DB::transaction(function () use ($penghuni) {
            $user = $penghuni->user;
            $penghuni->delete();
            if ($user) {
                $user->delete();
            }
        });

        return back()->with('ok', 'Penghuni dihapus.');
    }

    public function resetPassword(Tenant $penghuni)
    {
        $plain = Str::password(10);
        $penghuni->user->update(['password' => Hash::make($plain)]);

        try {
            Mail::to($penghuni->user->email)->send(new TenantPasswordResetMail(
                userName: $penghuni->user->name,
                email: $penghuni->user->email,
                plainPassword: $plain
            ));
        } catch (\Throwable $e) {}

        return back()->with('ok', 'Password baru: '.$plain);
    }
}
