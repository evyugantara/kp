@extends('layouts.tenant')

@section('content')
  <h2 style="margin:.2rem 0">Profil Penghuni</h2>
  <p class="muted" style="margin:0">Perbarui identitas dan kata sandi Anda.</p>

  <form method="POST" action="{{ route('tenant.profile.update') }}" style="margin-top:14px">
    @csrf @method('PUT')

    <div class="panel" style="margin-bottom:14px">
      <h3 style="margin:.2rem 0">Akun</h3>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:8px">
        <label>
          <div class="muted" style="margin-bottom:6px">Nama</div>
          <input name="name" value="{{ old('name',$user->name) }}" required
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>
        <label>
          <div class="muted" style="margin-bottom:6px">Email</div>
          <input type="email" name="email" value="{{ old('email',$user->email) }}" required
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:8px">
        <label>
          <div class="muted" style="margin-bottom:6px">Password Baru (opsional)</div>
          <input type="password" name="password"
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>
        <label>
          <div class="muted" style="margin-bottom:6px">Konfirmasi Password</div>
          <input type="password" name="password_confirmation"
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>
      </div>
    </div>

    <div class="panel">
      <h3 style="margin:.2rem 0">Identitas</h3>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:8px">
        <label>
          <div class="muted" style="margin-bottom:6px">No. HP</div>
          <input name="phone" value="{{ old('phone',$tenant->phone ?? '') }}"
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>
        <label>
          <div class="muted" style="margin-bottom:6px">NIK</div>
          <input name="nik" value="{{ old('nik',$tenant->nik ?? '') }}"
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>
      </div>
      <div style="margin-top:8px">
        <div class="muted" style="margin-bottom:6px">Alamat</div>
        <textarea name="alamat" rows="2"
                  style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">{{ old('alamat',$tenant->alamat ?? '') }}</textarea>
      </div>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">
      <button class="btn" type="submit">Simpan</button>
      <a class="btn ghost" href="{{ route('tenant.dashboard') }}">Batal</a>
    </div>
  </form>

  @if ($errors->any())
    <div class="panel" style="margin-top:12px;border-color:#7a1f1f;background:linear-gradient(180deg,#3b0d0d,#2a0a0a)">
      <strong>Periksa input:</strong>
      <ul style="margin:.4rem 0 0 1rem">
        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif
@endsection
