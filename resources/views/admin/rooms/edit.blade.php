@extends('layouts.admin')

@section('content')
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
    <div>
      <h2 style="margin:.2rem 0">Edit Kamar</h2>
      <p class="muted" style="margin:0">Perbarui data kamar & foto.</p>
    </div>
    <a class="btn ghost" href="{{ route('admin.rooms.index') }}">← Kembali</a>
  </div>

  @if(session('ok'))
    <div class="panel" style="margin-top:12px;border:1px solid #266b3a;background:#0f1f14;color:#b9f2c5">{{ session('ok') }}</div>
  @endif
  @if($errors->any())
    <div class="panel" style="margin-top:12px;border:1px solid #6b2626;background:#1f0f0f;color:#f2b9b9">
      <strong>Gagal:</strong>
      <ul style="margin:.35rem 0 0 1rem">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form id="roomForm" action="{{ route('admin.rooms.update',$room) }}" method="POST"
        enctype="multipart/form-data" style="margin-top:14px;display:grid;gap:14px">
    @csrf @method('PUT')

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
      <div>
        <label class="muted">Kode</label>
        <input name="kode" class="input" value="{{ old('kode',$room->kode) }}" required>
      </div>
      <div>
        <label class="muted">Nama</label>
        <input name="nama" class="input" value="{{ old('nama',$room->nama) }}" required>
      </div>

      <div>
        <label class="muted">Harga</label>
        <input name="harga" class="input" value="{{ old('harga',$room->harga) }}" required>
      </div>
      <div>
        <label class="muted">Status</label>
        <select name="status" class="input" required>
          @php $st = old('status', $room->tersedia ? 'Tersedia' : 'Tidak'); @endphp
          <option value="Tersedia" {{ $st=='Tersedia'?'selected':'' }}>Tersedia</option>
          <option value="Tidak" {{ $st=='Tidak'?'selected':'' }}>Tidak</option>
        </select>
      </div>

      <div style="grid-column:1/-1">
        <label class="muted">Deskripsi</label>
        <textarea name="deskripsi" rows="6" class="input">{{ old('deskripsi',$room->deskripsi) }}</textarea>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
      <div>
        <label class="muted">Cover (opsional)</label>
        <input type="file" name="cover" class="input" accept="image/*">
        @if($room->cover_path)
          <div style="margin-top:10px">
            <div class="muted" style="margin-bottom:6px">Cover saat ini:</div>
            <img src="{{ asset('storage/'.$room->cover_path) }}"
                 style="max-width:380px;border-radius:14px;border:1px solid #24345d" alt="cover">
          </div>
        @endif
      </div>

      <div>
        <label class="muted">Galeri (bisa lebih dari 1)</label>
        <input type="file" name="images[]" class="input" multiple accept="image/*">
        <div class="muted" style="margin-top:6px">Maks 6MB per foto.</div>
      </div>
    </div>

    <div>
      <div class="muted" style="margin-bottom:6px">Galeri saat ini:</div>
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        @forelse($room->images as $img)
          <div style="width:210px;border:1px solid #24345d;border-radius:14px;padding:10px;background:#0f162e">
            <img src="{{ asset('storage/'.$img->path) }}" style="width:100%;height:150px;object-fit:cover;border-radius:10px">
            <form method="POST" action="{{ route('admin.rooms.images.destroy',[$room->id,$img->id]) }}"
                  onsubmit="return confirm('Hapus foto ini?')" style="margin-top:8px">
              @csrf @method('DELETE')
              <button class="btn btn-danger" style="width:100%">Hapus</button>
            </form>
          </div>
        @empty
          <div class="muted">Belum ada foto galeri.</div>
        @endforelse
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button id="saveBtn" type="submit" class="btn">Simpan Perubahan</button>
      <a class="btn ghost" href="{{ route('admin.rooms.index') }}">Batal</a>
    </div>
  </form>

  <script>
    const f=document.getElementById('roomForm'), b=document.getElementById('saveBtn');
    f.addEventListener('submit',()=>{ b.disabled=true; b.textContent='Menyimpan...'; });
  </script>
@endsection
