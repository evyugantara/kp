@extends('layouts.admin')

@section('content')
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
    <div>
      <h2 style="margin:.2rem 0">Kelola Kamar</h2>
      <p class="muted" style="margin:0">Tambah/ubah/hapus data kamar yang tampil ke publik.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <a class="btn" href="{{ route('admin.rooms.create') }}">Tambah Kamar</a>
      <form method="POST" action="{{ route('admin.rooms.sync') }}">
        @csrf
        <button class="btn ghost" type="submit">Sinkron Ketersediaan</button>
      </form>
    </div>
  </div>

  <style>
    .table-wrap{margin-top:12px}
    table.kamar{width:100%;border-collapse:separate;border-spacing:0;table-layout:fixed}
    table.kamar thead th{
      text-align:left;padding:10px 12px;font-weight:700;color:#b9c6ea;
      border-bottom:1px solid #2a3a68;background:transparent
    }
    table.kamar tbody td{
      padding:10px 12px;border-bottom:1px solid #1b2747;vertical-align:middle;
      color:#e6eefb;overflow:hidden;text-overflow:ellipsis;white-space:nowrap
    }
    table.kamar tbody tr:hover{background:rgba(255,255,255,.03)}
    /* lebar kolom agar rapih */
    .col-kode{width:10%}
    .col-nama{width:26%}
    .col-harga{width:16%}
    .col-cover{width:11%}
    .col-galeri{width:11%}
    .col-status{width:13%}
    .col-aksi{width:13%}

    /* badge untuk state positif */
    .chip-ok{
      display:inline-block;border:1px solid #2ea7c9;background:rgba(0,212,255,.08);
      color:#cbeeff;padding:.18rem .48rem;border-radius:999px;font-size:.82rem
    }
    /* GAYA TEKS (bukan badge) */
    .txt-bad{color:#ffb8ae;font-weight:700}
    .text-dim{color:#9bb0d4}
    .btn-sm{padding:.45rem .7rem;border-radius:10px}
    .actions{display:flex;gap:8px;justify-content:flex-start}
  </style>

  <div class="table-wrap panel">
    <table class="kamar">
      <colgroup>
        <col class="col-kode">
        <col class="col-nama">
        <col class="col-harga">
        <col class="col-cover">
        <col class="col-galeri">
        <col class="col-status">
        <col class="col-aksi">
      </colgroup>
      <thead>
        <tr>
          <th>Kode</th>
          <th>Nama</th>
          <th>Harga</th>
          <th>Cover</th>
          <th>Galeri</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rooms as $room)
          @php
            $harga  = is_numeric($room->harga) ? number_format($room->harga,0,',','.') : $room->harga;
            $galeri = $room->images_count
                       ?? ($room->relationLoaded('images') ? $room->images->count()
                       : \App\Models\RoomImage::where('room_id',$room->id)->count());
            $coverAda = !empty($room->cover_path ?? $room->cover);
          @endphp
          <tr>
            <td>{{ $room->kode }}</td>
            <td title="{{ $room->nama }}">{{ $room->nama }}</td>
            <td>Rp {{ $harga }}</td>

            {{-- COVER: jika ada -> badge "Ada", jika tidak -> teks '-' biasa --}}
            <td>
              @if($coverAda)
                <span class="chip-ok">Ada</span>
              @else
                <span class="text-dim">-</span>
              @endif
            </td>

            <td>{{ $galeri }}</td>

            {{-- STATUS: Tersedia -> badge, Tidak -> TEKS merah (tanpa buletan) --}}
            <td>
              @if($room->tersedia)
                <span class="chip-ok">Tersedia</span>
              @else
                <span class="txt-bad">Tidak</span>
              @endif
            </td>

            <td>
              <div class="actions">
                <a class="btn btn-sm ghost" href="{{ route('admin.rooms.edit', $room) }}">Edit</a>
                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}"
                      onsubmit="return confirm('Hapus kamar {{ $room->nama }}?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm"
                          style="background:linear-gradient(180deg,#ff5d5d,#e44141);border:1px solid #e06355">
                    Hapus
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align:center;color:#9bb0d4;padding:16px">Belum ada kamar.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
