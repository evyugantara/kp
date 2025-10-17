@extends('layouts.tenant')

@section('content')
@php
  // Ambil tenant & kamar jika belum di-pass dari controller
  $u = auth()->user();
  $tenant = $tenant ?? \App\Models\Tenant::with('room')->where('user_id', $u->id)->first();
  $room   = $room   ?? ($tenant?->room);

  // Hitung jatuh tempo berikutnya dari tanggal_masuk (pakai hari yang sama setiap bulan)
  use Carbon\Carbon;
  $today = Carbon::today();
  $due = null;
  if ($tenant && $tenant->tanggal_masuk) {
      $start = Carbon::parse($tenant->tanggal_masuk);
      $due = $today->copy()->day($start->day);
      if ($due->lt($today)) $due = $due->addMonth();
  }

  // Harga kamar (untuk ditampilkan cepat)
  $harga = $room?->harga ?? 0;
@endphp

{{-- Sapaan --}}
<div class="panel" style="display:flex;justify-content:space-between;gap:14px;align-items:center">
  <div>
    <div class="muted" style="font-size:.85rem">Selamat datang,</div>
    <h2 style="margin:.1rem 0">{{ $u->name }}</h2>
    <div class="muted" style="font-size:.9rem">Ini ringkasan akun penghuni Anda.</div>
  </div>
  <div>
    {{-- Tombol pembayaran langsung --}}
    <a class="btn" href="{{ route('tenant.payments.index') }}">Bayar Sekarang (QRIS)</a>
  </div>
</div>

{{-- Ringkasan --}}
<div class="grid" style="margin-top:12px">
  {{-- Panel Kamar --}}
  <div class="panel">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:center">
      <div>
        <div style="font-weight:800">Kamar Saya</div>
        @if($room)
          <div class="muted" style="margin-top:4px">
            Kode: <b>{{ $room->kode }}</b> — {{ $room->nama }}
          </div>
          <div class="chips" style="margin-top:8px">
            <span class="chip">Harga: Rp {{ number_format($harga,0,',','.') }}</span>
            <span class="chip">Status Kamar: {{ $room->tersedia ? 'Tersedia' : 'Terisi' }}</span>
          </div>
        @else
          <div class="muted" style="margin-top:4px">Belum terhubung ke kamar.</div>
        @endif
      </div>
      <div>
        <a class="btn ghost" href="{{ route('tenant.profile.edit') }}">Perbarui Profil</a>
      </div>
    </div>
  </div>

  {{-- Panel Batas Pembayaran --}}
  <div class="panel">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:center">
      <div>
        <div style="font-weight:800">Batas Pembayaran Selanjutnya</div>
        <div class="muted" style="margin-top:4px">
          @if($due)
            {{ $due->translatedFormat('l, d M Y') }}
          @else
            Belum ada jadwal (tanggal masuk belum diisi).
          @endif
        </div>
      </div>
      <div>
        <a class="btn" href="{{ route('tenant.payments.index') }}">Bayar Sekarang</a>
      </div>
    </div>
  </div>

  {{-- Panel Pengumuman --}}
  <div class="panel">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:center">
      <div>
        <div style="font-weight:800">Pengumuman</div>
        <div class="muted" style="margin-top:4px">Lihat informasi terbaru dari pengelola.</div>
      </div>
      <div>
        <a class="btn ghost" href="{{ route('tenant.announcements.index') }}">Lihat Pengumuman</a>
      </div>
    </div>
  </div>
</div>

{{-- Aksi Cepat --}}
<div class="panel" style="margin-top:12px">
  <div style="display:flex; gap:10px; flex-wrap:wrap">
    <a class="btn" href="{{ route('tenant.payments.index') }}">Bayar Sekarang (QRIS)</a>
    <a class="btn ghost" href="{{ route('tenant.announcements.index') }}">Pengumuman</a>
    <a class="btn ghost" href="{{ route('tenant.profile.edit') }}">Profil</a>
  </div>
</div>
@endsection
