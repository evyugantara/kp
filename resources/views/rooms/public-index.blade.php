@extends('layouts.app')

@section('content')
  <span id="rooms"></span>
  @if($rooms->count() === 0)
    <article class="card reveal" style="padding:16px; text-align:center">
      <h3 style="margin:.2rem 0">Belum ada kamar tersedia</h3>
      <p class="muted">Pengelola belum menambahkan data kamar. Silakan cek lagi nanti.</p>
    </article>
  @else
    <div class="grid">
      @foreach($rooms as $room)
        @php
          $thumbs = $room->images->take(3);
          $main = $room->cover_url ?? optional($room->images->first())->url;
        @endphp
        <article class="card reveal">
          <div class="imgwrap">
            @if($main)
              <img src="{{ $main }}" alt="{{ $room->nama }}">
            @endif
            <span class="badge">Rp {{ number_format($room->harga,0,',','.') }}/bln</span>
            <span class="status">{{ $room->tersedia ? 'Tersedia' : 'Penuh' }}</span>
          </div>

          @if($thumbs->count() > 1)
            <div style="display:flex;gap:8px;padding:10px 12px 0">
              @foreach($thumbs as $img)
                <img src="{{ $img->url }}" alt="thumb" style="width:72px;height:56px;object-fit:cover;border-radius:8px;border:1px solid #2a3a68;opacity:.9">
              @endforeach
            </div>
          @endif

          <div class="content">
            <h3 class="title">{{ $room->nama }}</h3>
            @if($room->deskripsi)
              <p class="muted" style="margin:.25rem 0">{{ \Illuminate\Support\Str::limit($room->deskripsi, 100) }}</p>
            @endif

            @if(is_array($room->fasilitas) && count($room->fasilitas))
              <div class="chips">
                @foreach(array_slice($room->fasilitas,0,4) as $f)
                  <span class="chip">✓ {{ $f }}</span>
                @endforeach
                @if(count($room->fasilitas) > 4)
                  <span class="chip">+{{ count($room->fasilitas)-4 }} lagi</span>
                @endif
              </div>
            @endif

            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:8px">
              <a class="btn" href="{{ route('rooms.public.show',$room->kode) }}">Lihat Detail</a>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    <div class="reveal" style="margin-top:1rem">{{ $rooms->links() }}</div>
  @endif
@endsection
