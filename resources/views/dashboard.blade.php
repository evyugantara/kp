@extends('layouts.tenant')

@section('content')
  <h2 style="margin:.2rem 0">Dashboard Penghuni</h2>
  <p class="muted" style="margin:0">Ringkasan hunian & pembayaran Anda.</p>

  @if(!$tenant)
    <div class="panel" style="margin-top:14px">
      <strong>Data hunian belum tersedia.</strong>
      <p class="muted" style="margin:.4rem 0 0">Pengelola belum mengaitkan akun Anda ke kamar. Silakan hubungi pengelola.</p>
    </div>
  @else
    <div class="grid" style="margin-top:14px">
      {{-- Kamar Saya --}}
      <div class="panel">
        <h3 style="margin:.2rem 0">Kamar Saya</h3>
        <div style="margin-top:4px;font-weight:800">{{ $tenant->room->nama }} <span class="muted">• {{ $tenant->room->kode }}</span></div>
        <div class="muted">Harga: Rp {{ number_format($tenant->room->harga,0,',','.') }}/bulan</div>
        @if($tenant->room->cover_url)
          <img src="{{ $tenant->room->cover_url }}" alt="cover" style="width:100%;height:180px;object-fit:cover;border-radius:12px;margin-top:10px;border:1px solid #2a3a68">
        @endif
      </div>

      {{-- Batas Pembayaran Selanjutnya --}}
      <div class="panel">
        <h3 style="margin:.2rem 0">Batas Pembayaran Selanjutnya</h3>
        @if($nextDue)
          <div style="font-size:1.6rem;font-weight:800;margin-top:4px">{{ $nextDue->translatedFormat('d F Y') }}</div>
          @php
            $statusTxt = $daysLeft > 0 ? "({$daysLeft} hari lagi)" : ($daysLeft === 0 ? "(hari ini)" : "(terlewat ".abs($daysLeft)." hari)");
          @endphp
          <div class="muted" style="margin-top:6px">{{ $statusTxt }}</div>
        @else
          <div class="muted">Tanggal masuk belum diisi, hubungi pengelola.</div>
        @endif

        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
          <a class="btn" href="#">Bayar Sekarang (Coming Soon)</a>
          <a class="btn ghost" href="{{ route('tenant.profile.edit') }}">Perbarui Profil</a>
        </div>
      </div>

      {{-- Status Hunian --}}
      <div class="panel">
        <h3 style="margin:.2rem 0">Status Hunian</h3>
        <p style="margin:.4rem 0 0">
          Status:
          <span style="display:inline-block;background:#0b1838;border:1px solid #35509a;border-radius:10px;padding:.22rem .55rem">
            {{ $tenant->status }}
          </span>
        </p>
        <p class="muted" style="margin:.2rem 0">
          Masuk: {{ $tenant->tanggal_masuk?->format('d M Y') ?? '-' }}<br>
          Keluar: {{ $tenant->tanggal_keluar?->format('d M Y') ?? '-' }}
        </p>
        @if($tenant->catatan)
          <p class="muted" style="margin:.4rem 0 0">Catatan: {{ $tenant->catatan }}</p>
        @endif
      </div>
    </div>
  @endif
@endsection
