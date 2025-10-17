@extends('layouts.admin')

@section('content')
  @php
    $isEdit = $room->exists ?? false;
    $title  = $isEdit ? 'Edit Kamar' : 'Tambah Kamar';
  @endphp

  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
    <div>
      <h2 style="margin:.2rem 0">{{ $title }}</h2>
      <p class="muted" style="margin:0">
        {{ $isEdit ? 'Perbarui data kamar & foto.' : 'Lengkapi data kamar yang akan tampil ke publik.' }}
      </p>
    </div>
    <a class="btn ghost" href="{{ route('admin.rooms.index') }}">← Kembali</a>
  </div>

  {{-- Flash --}}
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
  @if($errors->any())
    <div class="panel" style="margin-top:12px;border:1px solid #6b2626;background:#1f0f0f;color:#f2b9b9">
      <strong>Gagal menyimpan:</strong>
      <ul style="margin:.35rem 0 0 1rem">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <style>
    .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    @media(max-width:980px){.grid-2{grid-template-columns:1fr}}
    .field{display:flex;flex-direction:column;gap:6px}
    .field label{color:#b9c6ea;font-weight:700}
    .field input,.field textarea,.field select{
      background:#0d1429;border:1px solid #27345e;color:#e6eefb;border-radius:12px;
      padding:.65rem .75rem;outline:none;width:100%
    }
    .field input:focus,.field textarea:focus,.field select:focus{
      box-shadow:0 0 0 3px rgba(0,212,255,.25);border-color:#3350ff
    }
    .thumb{width:240px;height:170px;object-fit:cover;border-radius:12px;border:1px solid #27345e}
    .galeri-item{display:flex;flex-direction:column;gap:8px;align-items:center;border:1px solid #27345e;border-radius:12px;padding:10px;background:#0f162e}
    .btn-danger{background:linear-gradient(180deg,#ff5d5d,#e44141);border:1px solid #e06355}
  </style>

  {{-- ================= FORM (TANPA TOMBOL) ================= --}}
  <form id="roomForm"
        action="{{ $isEdit ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}"
        method="POST" enctype="multipart/form-data"
        style="margin-top:14px;display:grid;gap:14px">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid-2">
      <div class="field">
        <label>Kode</label>
        <input name="kode" value="{{ old('kode', $room->kode) }}" required>
      </div>
      <div class="field">
        <label>Nama</label>
        <input name="nama" value="{{ old('nama', $room->nama) }}" required>
      </div>

      <div class="field">
        <label>Harga</label>
        <input type="number" min="0" name="harga" value="{{ old('harga', $room->harga) }}" required>
      </div>
      <div class="field">
        <label>Status</label>
        @php $st = old('status', ($room->exists ? ($room->tersedia ? 'Tersedia' : 'Tidak') : 'Tersedia')); @endphp
        <select name="status" required>
          <option value="Tersedia" {{ $st=='Tersedia'?'selected':'' }}>Tersedia</option>
          <option value="Tidak"     {{ $st=='Tidak'?'selected':'' }}>Tidak</option>
        </select>
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="6">{{ old('deskripsi', $room->deskripsi) }}</textarea>
      </div>
    </div>

    <div class="grid-2">
      <div class="field">
        <label>Cover (opsional)</label>
        <input type="file" name="cover" accept="image/*">
        @if($room->cover_path)
          <div style="margin-top:10px">
            <div class="muted" style="margin-bottom:6px">Cover saat ini:</div>
            <img class="thumb" style="width:340px;height:220px" src="{{ asset('storage/'.$room->cover_path) }}" alt="cover">
          </div>
        @endif
      </div>

      <div class="field">
        <label>Galeri (bisa lebih dari 1)</label>
        <input type="file" name="gallery[]" accept="image/*" multiple>
        <div class="muted" style="margin-top:6px">Maks 6MB per foto.</div>
      </div>
    </div>
  </form>
  {{-- =============== SELESAI FORM =============== --}}

  {{-- ===== DAFTAR GALERI (DI LUAR FORM) ===== --}}
  @if($isEdit)
    <div style="margin-top:18px">
      <div class="muted" style="margin-bottom:6px">Galeri saat ini:</div>
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        @forelse($room->images as $img)
          <div class="galeri-item">
            <img class="thumb" src="{{ asset('storage/'.$img->path) }}" alt="img">
            <form method="POST" action="{{ route('admin.rooms.images.destroy', [$room->id, $img->id]) }}"
                  onsubmit="return confirm('Hapus foto ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger" style="width:100%">Hapus</button>
            </form>
          </div>
        @empty
          <div class="panel">Belum ada foto galeri.</div>
        @endforelse
      </div>
    </div>
  @endif

  {{-- ===== TOMBOL AKSI DI PALING BAWAH ===== --}}
  <div style="margin-top:16px;display:flex;gap:10px">
    <button id="saveBtn" type="submit" class="btn" form="roomForm">
      {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
    </button>
    <a class="btn ghost" href="{{ route('admin.rooms.index') }}">Batal</a>
  </div>

  <script>
    const form = document.getElementById('roomForm');
    const btn  = document.getElementById('saveBtn');
    form.addEventListener('submit', () => { btn.disabled = true; btn.textContent = 'Menyimpan...'; });
  </script>
@endsection
