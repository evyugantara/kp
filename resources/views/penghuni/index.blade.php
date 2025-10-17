@extends('layouts.admin')

@section('content')
  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
    <div>
      <h2 style="margin:.2rem 0">Kelola Penghuni</h2>
      <p class="muted" style="margin:0">Buat akun penghuni & hubungkan ke kamar yang dipilih.</p>
    </div>
    <a class="btn" href="{{ route('admin.penghuni.create') }}">Tambah Penghuni</a>
  </div>

  <style>
    .table-wrap{margin-top:12px}
    table.tenants{width:100%;border-collapse:separate;border-spacing:0;table-layout:fixed}
    table.tenants thead th{
      text-align:left;padding:10px 12px;font-weight:700;color:#b9c6ea;
      border-bottom:1px solid #2a3a68;background:transparent
    }
    table.tenants tbody td{
      padding:10px 12px;border-bottom:1px solid #1b2747;vertical-align:middle;
      color:#e6eefb;overflow:hidden;text-overflow:ellipsis;white-space:nowrap
    }
    table.tenants tbody tr:hover{background:rgba(255,255,255,.03)}

    /* lebar kolom supaya rapi */
    .col-nama{width:16%}
    .col-email{width:18%}
    .col-hp{width:11%}
    .col-nik{width:13%}
    .col-kamar{width:13%}
    .col-masuk{width:8%}
    .col-keluar{width:8%}
    .col-status{width:6%}
    .col-aksi{width:7%}

    .chip-ok{
      display:inline-block;border:1px solid #2ea7c9;background:rgba(0,212,255,.08);
      color:#cbeeff;padding:.18rem .48rem;border-radius:999px;font-size:.82rem
    }
    .txt-bad{color:#ffb8ae;font-weight:700}
    .text-dim{color:#9bb0d4}
    .btn-sm{padding:.45rem .7rem;border-radius:10px}
    .actions{display:flex;gap:8px;justify-content:flex-start;flex-wrap:wrap}
  </style>

  @php
    use Illuminate\Contracts\Pagination\Paginator as PagiContract;

    // Ambil sumber data dari berbagai kemungkinan nama variabel
    $src = $tenants ?? $penghuni ?? $items ?? $data ?? $list ?? null;

    $paginator = null;
    if ($src instanceof PagiContract) {
        $paginator = $src;              // simpan untuk links()
        $rows = collect($src->items()); // ambil item dari paginator
    } elseif (is_iterable($src)) {
        $rows = collect($src);
    } else {
        $rows = collect();              // fallback kosong
    }

    // Formatter tanggal aman
    $fmt = function($dt){
      if(!$dt) return '-';
      try { return \Illuminate\Support\Carbon::parse($dt)->translatedFormat('d M Y'); }
      catch (\Throwable $e) { return (string)$dt; }
    };
  @endphp

  <div class="table-wrap panel">
    <table class="tenants">
      <colgroup>
        <col class="col-nama">
        <col class="col-email">
        <col class="col-hp">
        <col class="col-nik">
        <col class="col-kamar">
        <col class="col-masuk">
        <col class="col-keluar">
        <col class="col-status">
        <col class="col-aksi">
      </colgroup>
      <thead>
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>No. HP</th>
          <th>NIK</th>
          <th>Kamar</th>
          <th>Masuk</th>
          <th>Keluar</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $t)
          @php
            // Ambil data dari relasi user/room bila ada
            $nama   = $t->user->name    ?? $t->name   ?? '-';
            $email  = $t->user->email   ?? $t->email  ?? '-';
            $hp     = $t->phone         ?? $t->no_hp  ?? '-';
            $nik    = $t->nik           ?? '-';
            $kamar  = $t->room->nama    ?? ($t->room_name ?? '-');
            $kode   = $t->room->kode    ?? ($t->room_code ?? null);
            $masuk  = $fmt($t->tanggal_masuk ?? $t->masuk ?? null);
            $keluar = $fmt($t->tanggal_keluar ?? $t->keluar ?? null);
            $status = strtolower($t->status ?? 'aktif');
            $isAktif = in_array($status, ['aktif','active','valid']);
          @endphp
          <tr>
            <td title="{{ $nama }}">{{ $nama }}</td>
            <td title="{{ $email }}">{{ $email }}</td>
            <td title="{{ $hp }}">{{ $hp }}</td>
            <td title="{{ $nik }}">{{ $nik }}</td>
            <td title="{{ $kamar }}{{ $kode ? ' ('.$kode.')' : '' }}">
              {{ $kamar }}@if($kode) <span class="text-dim"> ({{ $kode }})</span>@endif
            </td>
            <td>{{ $masuk }}</td>
            <td>{{ $keluar }}</td>
            <td>
              @if($isAktif)
                <span class="chip-ok">Aktif</span>
              @else
                <span class="txt-bad">{{ ucfirst($status) }}</span>
              @endif
            </td>
            <td>
              <div class="actions">
                <a class="btn btn-sm ghost" href="{{ route('admin.penghuni.edit', $t) }}">Edit</a>

                <form method="POST" action="{{ route('admin.penghuni.reset', $t) }}"
                      onsubmit="return confirm('Reset kata sandi untuk {{ $nama }}?')">
                  @csrf
                  <button type="submit" class="btn btn-sm ghost">Reset Password</button>
                </form>

                <form method="POST" action="{{ route('admin.penghuni.destroy', $t) }}"
                      onsubmit="return confirm('Hapus penghuni {{ $nama }}?')">
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
            <td colspan="9" style="text-align:center;color:#9bb0d4;padding:16px">Belum ada penghuni.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- tampilkan pagination jika berasal dari paginate() --}}
    @if(isset($paginator) && $paginator)
      <div style="margin-top:12px">
        {{ $paginator->links() }}
      </div>
    @endif
  </div>
@endsection
