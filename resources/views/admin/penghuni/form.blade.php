@extends('layouts.admin')

@php
  /** @var \App\Models\Tenant|null $tenant */
  $isEdit = isset($tenant);
  $title  = $isEdit ? 'Ubah Penghuni' : 'Tambah Penghuni';
@endphp

@section('content')
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
    <div>
      <h2 style="margin:.2rem 0">{{ $title }}</h2>
      <p class="muted" style="margin:0">
        {{ $isEdit ? 'Perbarui data penghuni & keterkaitan dengan kamar.' : 'Buat akun penghuni baru & hubungkan ke kamar.' }}
      </p>
    </div>
    <a class="btn ghost" href="{{ route('admin.penghuni.index') }}">Kembali</a>
  </div>

  @if(session('ok'))
    <div class="panel" style="margin-top:12px;border:1px solid #266b3a;background:#0f1f14;color:#b9f2c5">
      {{ session('ok') }}
    </div>
  @endif
  @if(session('error'))
    <div class="panel" style="margin-top:12px;border:1px solid #6b2626;background:#1f0f0f;color:#f2b9b9">
      {{ session('error') }}
    </div>
  @endif

  <style>
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    @media (max-width:900px){.form-grid{grid-template-columns:1fr}}
    .field{display:flex;flex-direction:column;gap:6px}
    .field label{color:#b9c6ea;font-weight:700}
    .field input,.field select,.field textarea{
      background:#0d1429;border:1px solid #27345e;color:#e6eefb;border-radius:12px;
      padding:.65rem .75rem; outline:none; width:100%
    }
    .field input:focus,.field select:focus,.field textarea:focus{
      box-shadow:0 0 0 3px rgba(0,212,255,.25); border-color:#3350ff
    }
    .err{color:#ffb8ae;font-size:.86rem}
    .panel-form{margin-top:14px;padding:16px;border:1px solid #27345e;border-radius:16px;
      background:linear-gradient(180deg,#0f162e,#121a34)}
    .actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px}
    .btn-danger{background:linear-gradient(180deg,#ff5d5d,#e44141);border:1px solid #e06355}
  </style>

  <div class="panel panel-form">
    <form method="POST" action="{{ $isEdit ? route('admin.penghuni.update',$tenant) : route('admin.penghuni.store') }}">
      @csrf
      @if($isEdit) @method('PUT') @endif

      <div class="form-grid">
        {{-- KOLOM KIRI --}}
        <div class="field">
          <label>Nama</label>
          <input type="text" name="name"
                 value="{{ old('name', $tenant->user->name ?? '') }}" required>
          @error('name')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Email</label>
          <input type="email" name="email"
                 value="{{ old('email', $tenant->user->email ?? '') }}" required>
          @error('email')<div class="err">{{ $message }}</div>@enderror
        </div>

        @unless($isEdit)
          <div class="field">
            <label>Password (opsional)</label>
            <input type="password" name="password" autocomplete="new-password">
            @error('password')<div class="err">{{ $message }}</div>@enderror
          </div>

          <div class="field">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" autocomplete="new-password">
          </div>
        @endunless

        <div class="field">
          <label>No. HP</label>
          <input type="text" name="phone"
                 value="{{ old('phone', $tenant->phone ?? $tenant->user->phone ?? '') }}">
          @error('phone')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>NIK</label>
          <input type="text" name="nik" value="{{ old('nik', $tenant->nik ?? '') }}">
          @error('nik')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field" style="grid-column:1 / -1">
          <label>Alamat</label>
          <textarea name="alamat" rows="2">{{ old('alamat', $tenant->alamat ?? '') }}</textarea>
          @error('alamat')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- KOLOM KANAN --}}
        <div class="field">
          <label>Pilih Kamar</label>
          <select name="room_id" required>
            <option value="" disabled {{ old('room_id', $tenant->room_id ?? '')==''?'selected':'' }}>— pilih kamar —</option>
            @foreach($rooms as $r)
              <option value="{{ $r->id }}"
                {{ (string)old('room_id', $tenant->room_id ?? '') === (string)$r->id ? 'selected' : '' }}>
                {{ $r->nama }} ({{ $r->kode }})
              </option>
            @endforeach
          </select>
          @error('room_id')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Tanggal Masuk</label>
          <input type="date" name="tanggal_masuk"
                 value="{{ old('tanggal_masuk', optional($tenant->tanggal_masuk ?? null)->format('Y-m-d')) }}">
          @error('tanggal_masuk')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Tanggal Keluar</label>
          <input type="date" name="tanggal_keluar"
                 value="{{ old('tanggal_keluar', optional($tenant->tanggal_keluar ?? null)->format('Y-m-d')) }}">
          @error('tanggal_keluar')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Status</label>
          @php $st = old('status', $tenant->status ?? 'Aktif'); @endphp
          <select name="status" required>
            <option value="Aktif"   {{ $st==='Aktif'?'selected':'' }}>Aktif</option>
            <option value="Booking" {{ $st==='Booking'?'selected':'' }}>Booking</option>
            <option value="Selesai" {{ $st==='Selesai'?'selected':'' }}>Selesai</option>
          </select>
          @error('status')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field" style="grid-column:1 / -1">
          <label>Catatan</label>
          <textarea name="catatan" rows="3">{{ old('catatan', $tenant->catatan ?? '') }}</textarea>
          @error('catatan')<div class="err">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="btn">
          {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
        </button>
        <a href="{{ route('admin.penghuni.index') }}" class="btn ghost">Batal</a>
      </div>
    </form>
  </div>
@endsection
