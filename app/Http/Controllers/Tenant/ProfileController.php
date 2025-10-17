<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user   = $request->user();
        $tenant = Tenant::where('user_id', $user->id)->first();

        return view('tenant.profile', [
            'title'  => 'Profil Penghuni',
            'user'   => $user,
            'tenant' => $tenant,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $tenant = Tenant::where('user_id', $user->id)->first();

        $data = $request->validate([
            'name'   => ['required','string','max:150'],
            'email'  => ['required','email','max:190', Rule::unique('users','email')->ignore($user->id)],
            'phone'  => ['nullable','string','max:30'],
            'nik'    => ['nullable','string','max:30'],
            'alamat' => ['nullable','string','max:255'],

            'password' => ['nullable','string','min:6','confirmed'],
        ]);

        // update user
        $user->name  = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        // update identitas tenant (jika sudah ada)
        if ($tenant) {
            $tenant->phone  = $data['phone']  ?? $tenant->phone;
            $tenant->nik    = $data['nik']    ?? $tenant->nik;
            $tenant->alamat = $data['alamat'] ?? $tenant->alamat;
            $tenant->save();
        }

        return back()->with('ok','Profil berhasil disimpan.');
    }
}
