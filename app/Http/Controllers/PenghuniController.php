<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PenghuniController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Jika punya middleware role, bisa aktifkan:
        // $this->middleware('role:pengelola');
    }

    public function index(Request $request)
    {
        $q = Tenant::with(['user','room'])
            ->when($request->filled('s'), function($qry) use ($request){
                $s = '%'.$request->s.'%';
                $qry->whereHas('user', fn($u)=>$u->where('name','like',$s)->orWhere('email','like',$s))
                    ->orWhereHas('room', fn($r)=>$r->where('nama','like',$s)->orWhere('kode','like',$s))
                    ->orWhere('phone','like',$s)->orWhere('nik','like',$s);
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('penghuni.index', compact('q'));
    }

    public function create()
    {
        $rooms = Room::orderBy('nama')->get();
        return view('penghuni.form', ['rooms' => $rooms]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // data user
            'name'   => ['required','string','max:150'],
            'email'  => ['required','email','max:190','unique:users,email'],
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

        $plain = Str::password(10); // password acak
        DB::transaction(function() use (&$data, $plain) {
            $user = User::create([
                'name' => $data['name'],
                'email'=> $data['email'],
                'password' => Hash::make($plain),
                'role' => 'penghuni',
            ]);
            $data['user_id'] = $user->id;
            unset($data['name'],$data['email']);
            Tenant::create($data);
        });

        return redirect()->route('admin.penghuni.index')
            ->with('ok', 'Penghuni dibuat. Password sementara: '.$plain);
    }

    public function edit(Tenant $penghuni)
    {
        $rooms = Room::orderBy('nama')->get();
        $penghuni->load(['user','room']);
        return view('penghuni.form', ['rooms'=>$rooms, 'tenant'=>$penghuni]);
    }

    public function update(Request $request, Tenant $penghuni)
    {
        $data = $request->validate([
            // data user
            'name'   => ['required','string','max:150'],
            'email'  => ['required','email','max:190', Rule::unique('users','email')->ignore($penghuni->user_id)],
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

        DB::transaction(function() use (&$data, $penghuni) {
            $penghuni->user->update([
                'name' => $data['name'],
                'email'=> $data['email'],
            ]);
            unset($data['name'],$data['email']);
            $penghuni->update($data);
        });

        return redirect()->route('admin.penghuni.index')->with('ok','Data penghuni diperbarui.');
    }

    public function destroy(Tenant $penghuni)
    {
        DB::transaction(function() use ($penghuni) {
            // hapus user juga (cascade dari FK bisa, tapi aman double-check)
            $user = $penghuni->user;
            $penghuni->delete();
            if ($user) $user->delete();
        });

        return back()->with('ok','Penghuni dihapus.');
    }

    public function resetPassword(Tenant $penghuni)
    {
        $plain = Str::password(10);
        $penghuni->user->update(['password' => Hash::make($plain)]);
        return back()->with('ok','Password baru: '.$plain);
    }
}
