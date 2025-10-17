@extends('layouts.admin')

@section('content')
  <h2 style="margin:.25rem 0">{{ isset($room) ? 'Edit Kamar' : 'Tambah Kamar' }}</h2>
  <p class="muted" style="margin:0">Lengkapi data kamar. Gunakan foto yang jelas agar menarik calon penghuni.</p>

  <form method="POST" enctype="multipart/form-data"
        action="{{ isset($room) ? route('admin.rooms.update',$room) : route('admin.rooms.store') }}"
        style="margin-top:14px">
    @csrf
    @isset($room) @method('PUT') @endisset

    {{-- SECTION: Data Utama --}}
    <div class="panel" style="margin-bottom:14px">
      <h3 style="margin:.2rem 0">Data Utama</h3>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:8px">
        <label>
          <div class="muted" style="margin-bottom:6px">Kode</div>
          <input name="kode" value="{{ old('kode', $room->kode ?? '') }}" required
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>

        <label>
          <div class="muted" style="margin-bottom:6px">Nama</div>
          <input name="nama" value="{{ old('nama', $room->nama ?? '') }}" required
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>

        <label>
          <div class="muted" style="margin-bottom:6px">Harga / bulan</div>
          <input type="number" name="harga" value="{{ old('harga', $room->harga ?? 0) }}" min="0" required
                 style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
        </label>

        <label style="display:flex;align-items:center;gap:.6rem;margin-top:28px">
          <input type="checkbox" name="tersedia" value="1" {{ old('tersedia', $room->tersedia ?? true) ? 'checked' : '' }}>
          <span class="muted">Tersedia</span>
        </label>
      </div>

      <div style="margin-top:10px">
        <div class="muted" style="margin-bottom:6px">Deskripsi</div>
        <textarea name="deskripsi" rows="4"
          style="width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">{{ old('deskripsi', $room->deskripsi ?? '') }}</textarea>
      </div>
    </div>

    {{-- SECTION: Fasilitas --}}
    <div class="panel" style="margin-bottom:14px">
      <h3 style="margin:.2rem 0">Fasilitas</h3>
      @php
        $all = ['AC','KM Dalam','Water Heater','Wifi','Kasur','Lemari','Meja','Parkir'];
        $val = old('fasilitas', $room->fasilitas ?? []);
      @endphp
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;margin-top:8px">
        @foreach($all as $f)
          <label style="display:flex;align-items:center;gap:.6rem;background:#0c142b;border:1px solid #2a3a68;padding:.6rem .8rem;border-radius:12px">
            <input type="checkbox" name="fasilitas[]" value="{{ $f }}" {{ in_array($f,$val ?? []) ? 'checked' : '' }}>
            <span>{{ $f }}</span>
          </label>
        @endforeach
      </div>
    </div>

    {{-- SECTION: Foto --}}
    <div class="panel">
      <h3 style="margin:.2rem 0">Foto</h3>

      <div style="display:grid;grid-template-columns:1.2fr .8fr;gap:12px;align-items:start;margin-top:8px">
        <label>
          <div class="muted" style="margin-bottom:6px">Cover (opsional)</div>
          <input type="file" name="foto" accept="image/*"
                 style="width:100%;padding:.6rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
          @if(isset($room) && $room->cover_url)
            <div style="margin-top:8px" class="muted">Cover saat ini:</div>
            <img src="{{ $room->cover_url }}" alt="cover" style="width:220px;height:auto;border-radius:12px;margin-top:6px;border:1px solid #2a3a68">
          @endif
        </label>

        <label>
          <div class="muted" style="margin-bottom:6px">Galeri (bisa lebih dari 1)</div>
          <input type="file" name="galeri[]" accept="image/*" multiple
                 style="width:100%;padding:.6rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:#e6eefb">
          <div class="muted" style="margin-top:6px;font-size:.9rem">Maks 6MB per foto.</div>
        </label>
      </div>

      @if(isset($room) && $room->images->count())
        <div style="height:8px"></div>
        <div class="muted">Galeri saat ini:</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;margin-top:.5rem">
          @foreach($room->images as $img)
            <div style="border:1px solid #2a3a68;border-radius:12px;padding:6px;text-align:center;background:#0c142b">
              <img src="{{ $img->url }}" alt="img" style="width:100%;height:110px;object-fit:cover;border-radius:8px">
              <form class="js-confirm" data-message="Hapus gambar ini?" action="{{ route('admin.rooms.images.destroy',[$room,$img]) }}" method="POST" style="margin-top:6px">
                @csrf @method('DELETE')
                <button class="btn" style="background:#ef4444;width:100%">Hapus</button>
              </form>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- ACTIONS --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">
      <button type="submit" class="btn">{{ isset($room) ? 'Simpan Perubahan' : 'Simpan' }}</button>
      <a class="btn ghost" href="{{ route('admin.rooms.index') }}">Batal</a>
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
