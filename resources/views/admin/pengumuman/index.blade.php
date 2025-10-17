@extends('layouts.admin')

@section('content')
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
    <div>
      <h2 style="margin:.2rem 0">Pengumuman</h2>
      <p class="muted" style="margin:0">Kelola pengumuman yang tampil di akun penghuni.</p>
    </div>
    <a class="btn" href="{{ route('admin.pengumuman.create') }}">Buat Pengumuman</a>
  </div>

  @if(session('ok'))
    <div class="panel" style="margin-top:12px;border-color:#2f6fff">✅ {{ session('ok') }}</div>
  @endif

  <div class="grid" style="margin-top:12px">
    @forelse($pengumuman as $it)
      <div class="panel">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:start">
          <div>
            <div style="font-weight:800">{{ $it->judul }}</div>
            <div class="muted" style="font-size:.85rem;margin-top:2px">
              {{ $it->starts_at?->translatedFormat('d M Y H:i') ?? 'segera' }}
              @if($it->ends_at) — s/d {{ $it->ends_at->translatedFormat('d M Y H:i') }} @endif
              • {{ $it->is_published ? 'Published' : 'Draft' }}
            </div>
            <div style="margin-top:8px;color:#cfe1ff">{!! nl2br(e(Str::limit($it->isi, 180))) !!}</div>
          </div>
          <div style="display:flex;gap:8px;white-space:nowrap">
            <a class="btn" href="{{ route('admin.pengumuman.edit', $it) }}">Edit</a>
            <form action="{{ route('admin.pengumuman.destroy', $it) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
              @csrf @method('DELETE')
              <button class="btn ghost" type="submit">Hapus</button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="panel">Belum ada pengumuman.</div>
    @endforelse
  </div>

  <div style="margin-top:12px">
    {{ $pengumuman->links() }}
  </div>
@endsection
