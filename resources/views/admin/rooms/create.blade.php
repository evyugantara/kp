@extends('layouts.admin')

@section('content')
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h2 style="margin:.2rem 0">Tambah Kamar</h2>
    <a class="btn ghost" href="{{ route('admin.rooms.index') }}">← Kembali</a>
  </div>

  @if($errors->any())
    <div class="panel" style="margin-top:12px;border:1px solid #6b2626;background:#1f0f0f;color:#f2b9b9">
      <strong>Gagal:</strong>
      <ul style="margin:.35rem 0 0 1rem">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data"
        style="margin-top:14px;display:grid;gap:14px">
    @csrf

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
      <div>
        <label class="muted">Kode</label>
        <input name="kode" class="input" value="{{ old('kode') }}" required>
      </div>
      <div>
        <label class="muted">Nama</label>
        <input name="nama" class="input" value="{{ old('nama') }}" required>
      </div>

      <div>
        <label class="muted">Harga</label>
        <input name="harga" class="input" value="{{ old('harga') }}" required>
      </div>
      <div>
        <label class="muted">Status</label>
        <select name="status" class="input" required>
          <option value="Tersedia" {{ old('status')=='Tersedia'?'selected':'' }}>Tersedia</option>
          <option value="Tidak" {{ old('status')=='Tidak'?'selected':'' }}>Tidak</option>
        </select>
      </div>

      <div style="grid-column:1/-1">
        <label class="muted">Deskripsi</label>
        <textarea name="deskripsi" rows="6" class="input">{{ old('deskripsi') }}</textarea>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
      <div>
        <label class="muted">Cover (opsional)</label>
        <input type="file" name="cover" class="input" accept="image/*">
      </div>
      <div>
        <label class="muted">Galeri (bisa lebih dari 1)</label>
        <input type="file" name="images[]" class="input" multiple accept="image/*">
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit" class="btn">Simpan</button>
      <a class="btn ghost" href="{{ route('admin.rooms.index') }}">Batal</a>
    </div>
  </form>
@endsection
