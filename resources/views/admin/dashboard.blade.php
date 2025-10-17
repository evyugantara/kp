@extends('layouts.admin')

@section('content')
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
    <div>
      <h2 style="margin:.2rem 0">Dashboard Pengelola</h2>
      <p class="muted" style="margin:0">
        Halo, <strong>{{ auth()->user()->name }}</strong>. Berikut ringkasan operasional Pondok Hasanah.
      </p>
    </div>
    {{-- (DIHAPUS) tombol Kelola Kamar di kanan atas --}}
  </div>

  {{-- === Kartu Statistik === --}}
  <div class="grid" style="margin-top:14px">
    {{-- Penghuni Aktif --}}
    <div class="panel" style="display:flex;gap:14px;align-items:center">
      <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(180deg,#2f6fff,#244ecb);display:flex;align-items:center;justify-content:center;border:1px solid #35509a">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="#eaf1ff"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h10v-2.5C11 14.17 6.33 13 4 13zm12 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
      </div>
      <div>
        <div class="muted" style="font-size:.85rem">Penghuni Aktif</div>
        <div style="font-size:1.8rem;font-weight:800;letter-spacing:.3px">{{ $activeTenants ?? 0 }}</div>
      </div>
      <div style="margin-left:auto;align-self:center">
        <a class="btn ghost" href="{{ route('admin.penghuni.index') }}">Lihat</a>
      </div>
    </div>

    {{-- Kamar Tersedia --}}
    <div class="panel" style="display:flex;gap:14px;align-items:center">
      <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(180deg,#00d4ff,#0ab0d6);display:flex;align-items:center;justify-content:center;border:1px solid #2ea7c9">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="#eafcff"><path d="M4 10V7l8-5 8 5v3h-2V8l-6-3.75L6 8v2H4zm0 2h16v8h-2v-6H6v6H4v-8z"/></svg>
      </div>
      <div>
        <div class="muted" style="font-size:.85rem">Kamar Tersedia</div>
        <div style="font-size:1.8rem;font-weight:800;letter-spacing:.3px">{{ $availableRooms ?? 0 }}</div>
      </div>
      <div style="margin-left:auto;align-self:center">
        <a class="btn ghost" href="{{ route('admin.rooms.index') }}">Kelola</a>
      </div>
    </div>

    {{-- Kamar Terisi --}}
    <div class="panel" style="display:flex;gap:14px;align-items:center">
      <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(180deg,#ff7b55,#ff4a4a);display:flex;align-items:center;justify-content:center;border:1px solid #e06355">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="#fff5f2"><path d="M4 10h16v10h-2v-4H6v4H4V10zm2-2V6l6-3 6 3v2H6z"/></svg>
      </div>
      <div>
        <div class="muted" style="font-size:.85rem">Kamar Terisi</div>
        <div style="font-size:1.8rem;font-weight:800;letter-spacing:.3px">{{ $occupiedRooms ?? 0 }}</div>
      </div>
      <div style="margin-left:auto;align-self:center">
        <a class="btn ghost" href="{{ route('admin.rooms.index') }}">Detail</a>
      </div>
    </div>

    {{-- Pengumuman Aktif --}}
    <div class="panel" style="display:flex;gap:14px;align-items:center">
      <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(180deg,#9b7bff,#6b5bff);display:flex;align-items:center;justify-content:center;border:1px solid #7c6cff">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="#f4f1ff"><path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 0 0 2 2zM18 16v-5c0-3.07-1.63-5.64-4.5-6.32V4a1.5 1.5 0 0 0-3 0v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
      </div>
      <div>
        <div class="muted" style="font-size:.85rem">Pengumuman Aktif</div>
        <div style="font-size:1.8rem;font-weight:800;letter-spacing:.3px">
          {{ $activeAnnouncements ?? 0 }} <span class="muted" style="font-size:.9rem">/ {{ $allAnnouncements ?? 0 }}</span>
        </div>
      </div>
      <div style="margin-left:auto;align-self:center">
        <a class="btn ghost" href="{{ route('admin.pengumuman.index') }}">Kelola</a>
      </div>
    </div>
  </div>

  {{-- (DIHAPUS) panel "Aksi cepat" --}}
@endsection
