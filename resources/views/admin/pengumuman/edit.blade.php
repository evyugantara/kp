@extends('layouts.admin')

@section('content')
  <h2 style="margin:.2rem 0">Edit Pengumuman</h2>
  <p class="muted" style="margin:0">Perbarui informasi lalu simpan.</p>

  <form class="panel" method="POST" action="{{ route('admin.pengumuman.update', $pengumuman) }}" style="margin-top:12px">
    @csrf @method('PUT')

    <div>
      <label>Judul</label>
      <input class="input" type="text" name="judul" value="{{ old('judul', $pengumuman->judul) }}" maxlength="180" required>
      @error('judul')<div style="color:#ff9b9b;margin-top:6px">{{ $message }}</div>@enderror
    </div>

    <div style="margin-top:10px">
      <label>Isi</label>
      <textarea class="input" name="isi" rows="6" required>{{ old('isi', $pengumuman->isi) }}</textarea>
      @error('isi')<div style="color:#ff9b9b;margin-top:6px">{{ $message }}</div>@enderror
    </div>

    @php $fmt = fn($dt) => $dt ? $dt->format('Y-m-d\TH:i') : ''; @endphp
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:10px">
      <div>
        <label>Mulai Tayang (opsional)</label>
        <input class="input" type="datetime-local" name="starts_at" value="{{ old('starts_at', $fmt($pengumuman->starts_at)) }}">
      </div>
      <div>
        <label>Selesai Tayang (opsional)</label>
        <input class="input" type="datetime-local" name="ends_at" value="{{ old('ends_at', $fmt($pengumuman->ends_at)) }}">
      </div>
    </div>

    <div style="margin-top:10px;display:flex;align-items:center;gap:8px">
      <input id="pub" type="checkbox" name="is_published" value="1" {{ old('is_published', $pengumuman->is_published) ? 'checked' : '' }}>
      <label for="pub">Publish</label>
    </div>

    <div style="margin-top:14px;display:flex;gap:10px">
      <button class="btn" type="submit">Simpan Perubahan</button>
      <a class="btn ghost" href="{{ route('admin.pengumuman.index') }}">Kembali</a>
    </div>
  </form>
@endsection
