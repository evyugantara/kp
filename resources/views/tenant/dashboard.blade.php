@extends('layouts.tenant')

@section('content')
<style>
  @media (max-width: 768px) {
    .dashboard-grid {
      grid-template-columns: 1fr !important;
    }
    .header-panel {
      flex-direction: column !important;
      text-align: center;
    }
    .header-panel .btn {
      margin-top: 12px;
    }
  }
</style>
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

{{-- Header dengan sapaan --}}
<div class="panel">
  <div class="header-panel" style="display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap">
    <div>
      <div class="muted" style="font-size:.85rem">Selamat datang,</div>
      <h2 style="margin:.1rem 0">{{ $u->name }}</h2>
      <div class="muted" style="font-size:.9rem">Kelola pembayaran dan informasi kamar Anda</div>
    </div>
  </div>
</div>

{{-- Main Content --}}
<div class="grid dashboard-grid" style="margin-top:20px; grid-template-columns: 2fr 1fr; gap: 20px;">
  {{-- Left Column --}}
  <div>
    {{-- Informasi Kamar --}}
    <div class="panel" style="margin-bottom:16px">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
        <div style="width:48px;height:48px;background:#667eea;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px">🏠</div>
        <div>
          <div style="font-weight:700;font-size:1.1rem;margin-bottom:4px">Informasi Kamar</div>
          @if($room)
            <div class="muted">{{ $room->kode }} — {{ $room->nama }}</div>
          @else
            <div class="muted">Belum terhubung ke kamar</div>
          @endif
        </div>
      </div>
      
      @if($room)
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
          <div style="background:linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);color:#0369a1;padding:8px 16px;border-radius:20px;font-size:0.9rem;font-weight:500;border:none;box-shadow:0 2px 4px rgba(3,105,161,0.1)">
            💰 Rp {{ number_format($harga,0,',','.') }}/bulan
          </div>
          <div style="background:linear-gradient(135deg, {{ $room->tersedia ? '#dcfce7' : '#fef3c7' }} 0%, {{ $room->tersedia ? '#ecfdf5' : '#fefce8' }} 100%);color:{{ $room->tersedia ? '#166534' : '#92400e' }};padding:8px 16px;border-radius:20px;font-size:0.9rem;font-weight:500;border:none;box-shadow:0 2px 4px {{ $room->tersedia ? 'rgba(22,101,52,0.1)' : 'rgba(146,64,14,0.1)' }}">
            {{ $room->tersedia ? '✅ Tersedia' : '🏠 Dihuni' }}
          </div>
        </div>
        
        @if($room->fasilitas && count($room->fasilitas) > 0)
          <div style="margin-bottom:12px">
            <div style="font-weight:600;margin-bottom:8px;font-size:0.9rem">Fasilitas:</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
              @foreach($room->fasilitas as $fasilitas)
                <span style="background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);color:#475569;padding:6px 12px;border-radius:16px;font-size:0.8rem;font-weight:500;border:none;box-shadow:0 1px 3px rgba(71,85,105,0.1)">{{ $fasilitas }}</span>
              @endforeach
            </div>
          </div>
        @endif
      @else
        <div style="text-align:center;padding:24px;color:#6b7280">
          <div style="font-size:48px;margin-bottom:12px">🏠</div>
          <div>Belum terhubung ke kamar</div>
          <div style="font-size:0.9rem;margin-top:4px">Hubungi pengelola untuk mendapatkan akses kamar</div>
        </div>
      @endif
    </div>

    {{-- Status Pembayaran --}}
    @if($room)
    <div class="panel">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:48px;height:48px;background:#10b981;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px">📅</div>
          <div>
            <div style="font-weight:700;font-size:1.1rem;margin-bottom:4px">Jadwal Pembayaran</div>
            <div class="muted">
              @if($due)
                Jatuh tempo: {{ $due->translatedFormat('l, d M Y') }}
                @if($due->diffInDays($today) <= 3)
                  <span style="color:#dc2626;font-weight:600"> ({{ $due->diffInDays($today) }} hari lagi)</span>
                @endif
              @else
                Tanggal masuk belum diatur
              @endif
            </div>
          </div>
        </div>
        <div>
          <a class="btn" href="{{ route('tenant.payments.index') }}">
            💳 Bayar Sekarang
          </a>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- Right Column - Quick Actions --}}
  <div>
    <div class="panel">
      <div style="font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px">
        <span style="font-size:20px">⚡</span>
        Menu Utama
      </div>
      
      <div style="display:flex;flex-direction:column;gap:8px">
        <a class="btn ghost" href="{{ route('tenant.announcements.index') }}" style="justify-content:flex-start;text-align:left">
          <span style="margin-right:8px">📢</span> Pengumuman
        </a>
        
        <a class="btn ghost" href="{{ route('tenant.profile.edit') }}" style="justify-content:flex-start;text-align:left">
          <span style="margin-right:8px">👤</span> Profil Saya
        </a>
      </div>
    </div>
    
    {{-- Status Panel --}}
    @if($room)
    <div class="panel" style="margin-top:16px;background:#f8fafc;border:1px solid #e2e8f0">
      <div style="display:flex;align-items:center;gap:12px;padding:8px">
        <div style="font-size:24px">✅</div>
        <div>
          <div style="font-weight:600;color:#10b981;font-size:0.9rem">Akun Aktif</div>
          <div style="font-size:0.8rem;color:#6b7280">Sistem berjalan normal</div>
        </div>
      </div>
    </div>
    @else
    <div class="panel" style="margin-top:16px;background:#fef2f2;border:1px solid #fecaca">
      <div style="display:flex;align-items:center;gap:12px;padding:8px">
        <div style="font-size:24px">⚠️</div>
        <div>
          <div style="font-weight:600;color:#dc2626;font-size:0.9rem">Perlu Verifikasi</div>
          <div style="font-size:0.8rem;color:#6b7280">Hubungi pengelola</div>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>

@endsection
