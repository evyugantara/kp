@extends('layouts.tenant')

@section('content')
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
    <div>
      <h2 style="margin:.2rem 0">Pengumuman</h2>
      <p class="muted" style="margin:0">Informasi terbaru dari pengelola.</p>
    </div>
    <div style="display:flex;gap:8px">
      <a class="btn {{ ($mode ?? 'aktif')==='aktif' ? '' : 'ghost' }}"
         href="{{ route('tenant.announcements.index', ['mode'=>'aktif']) }}">
        Aktif
      </a>
      <a class="btn {{ ($mode ?? 'aktif')==='all' ? '' : 'ghost' }}"
         href="{{ route('tenant.announcements.index', ['mode'=>'all']) }}">
        Semua
      </a>
    </div>
  </div>

  @if(($mode ?? 'aktif')==='aktif' && $items->total()===0)
    <div class="panel" style="margin-top:12px">
      Belum ada pengumuman aktif. Coba tab <strong>Semua</strong>.
    </div>
  @endif

  <div class="grid" style="margin-top:12px">
    @forelse($items as $it)
      @php
        $now = now();
        $isActive = ($it->is_published)
                    && (is_null($it->starts_at) || $it->starts_at->lte($now))
                    && (is_null($it->ends_at)   || $it->ends_at->gte($now));
        $label = $isActive ? 'Aktif' : (($it->ends_at && $it->ends_at->isPast()) ? 'Selesai' : 'Terjadwal');
      @endphp

      <div class="panel">
        <div style="display:flex;justify-content:space-between;gap:10px;align-items:start">
          <div>
            <div style="font-weight:800">{{ $it->judul }}</div>
            <div class="muted" style="font-size:.85rem;margin-top:2px">
              {{ $it->starts_at?->translatedFormat('d M Y H:i') ?? 'segera' }}
              @if($it->ends_at) — s/d {{ $it->ends_at->translatedFormat('d M Y H:i') }} @endif
            </div>
            <div style="margin-top:8px">{!! nl2br(e($it->isi)) !!}</div>
          </div>
          <div style="white-space:nowrap;align-self:center">
            <span style="display:inline-block;background:#0b1838;border:1px solid #35509a;border-radius:10px;padding:.2rem .55rem;font-size:.8rem">
              {{ $label }}
            </span>
          </div>
        </div>
      </div>
    @empty
      <div class="panel">Belum ada pengumuman.</div>
    @endforelse
  </div>

  <div style="margin-top:12px">
    {{ $items->links() }}
  </div>
@endsection
