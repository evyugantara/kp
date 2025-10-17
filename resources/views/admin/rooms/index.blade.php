@extends('layouts.admin')

@section('content')
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
    <div>
      <h2 style="margin:.2rem 0">Kelola Kamar</h2>
      <p class="muted" style="margin:0">Tambah/ubah/hapus data kamar yang tampil ke publik.</p>
    </div>
    <a class="btn" href="{{ route('admin.rooms.create') }}">Tambah Kamar</a>
  </div>

  @if(session('ok'))
    <div class="panel" style="margin-top:12px;border:1px solid #266b3a;background:#0f1f14;color:#b9f2c5">{{ session('ok') }}</div>
  @endif
  @if(session('error'))
    <div class="panel" style="margin-top:12px;border:1px solid #6b2626;background:#1f0f0f;color:#f2b9b9">{{ session('error') }}</div>
  @endif

  <style>
    .tbl{margin-top:14px;border:1px solid #23325c;border-radius:14px;overflow:hidden}
    .tbl-row{display:grid;grid-template-columns:110px 1fr 140px 80px 80px 120px 180px;gap:12px;align-items:center;
      padding:12px 14px;border-bottom:1px solid #19274d}
    .tbl-head{background:#0f162e;color:#b9c6ea;font-weight:700}
    .tbl-empty{padding:22px 14px;color:#b9c6ea}
    .tag-ok{display:inline-block;padding:.22rem .6rem;border:1px solid #2c7be5;border-radius:8px;background:#0c1733;color:#dfe8ff}
    .tag-no{display:inline-block;padding:.22rem .6rem;border:1px solid #7a2f39;border-radius:8px;background:#221018;color:#f2b9b9}
    .btn.sm{padding:.45rem .7rem;border-radius:10px;font-weight:700}
    .btn-danger{background:linear-gradient(180deg,#ff5d5d,#e44141);border:1px solid #e06355}
  </style>

  <div class="tbl">
    <div class="tbl-row tbl-head">
      <div>Kode</div><div>Nama</div><div>Harga</div><div>Cover</div><div>Galeri</div><div>Status</div><div>Aksi</div>
    </div>

    @forelse($rooms as $r)
      <div class="tbl-row">
        <div>{{ $r->kode }}</div>
        <div>{{ $r->nama }}</div>
        <div>Rp {{ number_format($r->harga,0,',','.') }}</div>
        <div>{{ $r->cover_path ? 'Ada' : '-' }}</div>
        <div>{{ $r->images_count }}</div>
        <div>@if($r->tersedia)<span class="tag-ok">Tersedia</span>@else<span class="tag-no">Tidak</span>@endif</div>
        <div style="display:flex;gap:8px">
          <a class="btn sm" href="{{ route('admin.rooms.edit', $r) }}">Edit</a>
          <form method="POST" action="{{ route('admin.rooms.destroy', $r) }}">
            @csrf @method('DELETE')
            <button class="btn sm btn-danger" onclick="return confirm('Hapus kamar ini beserta fotonya?')">Hapus</button>
          </form>
        </div>
      </div>
    @empty
      <div class="tbl-empty">Belum ada kamar.</div>
    @endforelse
  </div>

  <div style="margin-top:12px">{{ $rooms->links() }}</div>
@endsection
