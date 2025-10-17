@extends('layouts.admin')

@section('content')
  <h2 style="margin:.25rem 0">{{ isset($tenant) ? 'Edit Penghuni' : 'Tambah Penghuni' }}</h2>
  <p class="muted" style="margin:0">Isikan identitas lengkap dan pilih kamar tujuan.</p>

  <form method="POST" action="{{ isset($tenant) ? route('admin.penghuni.update',$tenant) : route('admin.penghuni.store') }}" style="margin-top:14px">
    @csrf
    @isset($tenant) @method('PUT') @endisset

    <div class="panel" style="margin-bottom:14px">
      <h3 style="margin:.2rem 0">Akun Penghuni</h3>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:8px">
        <label>
          <div class="muted" style="margin-bottom:6px">Nama</div>
          <input name="name" value="{{ old('name', $tenant->user->name ?? '') }}" required
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>
        <label>
          <div class="muted" style="margin-bottom:6px">Email</div>
          <input type="email" name="email" value="{{ old('email', $tenant->user->email ?? '') }}" required
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>

        @empty($tenant)
          {{-- Field password hanya saat CREATE --}}
          <label>
            <div class="muted" style="margin-bottom:6px">Password (opsional)</div>
            <input type="password" name="password" id="pwd"
                   placeholder="Kosongkan untuk password otomatis"
                   style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
          </label>
          <label>
            <div class="muted" style="margin-bottom:6px">Konfirmasi Password</div>
            <input type="password" name="password_confirmation" id="pwd2"
                   style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
          </label>
        @endempty

        <label>
          <div class="muted" style="margin-bottom:6px">No. HP</div>
          <input name="phone" value="{{ old('phone', $tenant->phone ?? '') }}"
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>
        <label>
          <div class="muted" style="margin-bottom:6px">NIK</div>
          <input name="nik" value="{{ old('nik', $tenant->nik ?? '') }}"
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>
      </div>
      <div style="margin-top:10px">
        <div class="muted" style="margin-bottom:6px">Alamat</div>
        <textarea name="alamat" rows="2"
          style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">{{ old('alamat', $tenant->alamat ?? '') }}</textarea>
      </div>
      @empty($tenant)
        <div class="muted" style="margin-top:8px;font-size:.9rem">
          Tips: biarkan password kosong untuk membuat password otomatis (akan tampil di notifikasi & dikirim ke email).
        </div>
      @endempty
    </div>

    <div class="panel" style="margin-bottom:14px">
      <h3 style="margin:.2rem 0">Informasi Hunian</h3>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:8px">
        <label>
          <div class="muted" style="margin-bottom:6px">Kamar</div>
          <select name="room_id" required
                  style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
            <option value="">-- Pilih Kamar --</option>
            @foreach($rooms as $r)
              <option value="{{ $r->id }}" {{ (string)old('room_id', $tenant->room_id ?? '') === (string)$r->id ? 'selected' : '' }}>
                {{ $r->nama }} ({{ $r->kode }}) — Rp {{ number_format($r->harga,0,',','.') }}
              </option>
            @endforeach
          </select>
        </label>

        <label>
          <div class="muted" style="margin-bottom:6px">Tanggal Masuk</div>
          <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', optional($tenant->tanggal_masuk ?? null)->format('Y-m-d')) }}"
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>

        <label>
          <div class="muted" style="margin-bottom:6px">Tanggal Keluar</div>
          <input type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', optional($tenant->tanggal_keluar ?? null)->format('Y-m-d')) }}"
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>

        <label>
          <div class="muted" style="margin-bottom:6px">Status</div>
          @php $opt = ['Aktif','Booking','Selesai']; @endphp
          <select name="status" required
                  style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
            @foreach($opt as $o)
              <option value="{{ $o }}" {{ old('status', $tenant->status ?? 'Aktif') === $o ? 'selected' : '' }}>{{ $o }}</option>
            @endforeach
          </select>
        </label>
      </div>

      <div style="margin-top:10px">
        <div class="muted" style="margin-bottom:6px">Catatan</div>
        <textarea name="catatan" rows="2"
          style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">{{ old('catatan', $tenant->catatan ?? '') }}</textarea>
      </div>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">
      <button type="submit" class="btn">{{ isset($tenant) ? 'Simpan Perubahan' : 'Simpan' }}</button>
      <a class="btn ghost" href="{{ route('admin.penghuni.index') }}">Batal</a>
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
