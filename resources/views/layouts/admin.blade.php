<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Panel Pengelola' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="icon" type="image/png" href="{{ asset('images/kosan.png') }}">
  <style>
    :root{
      --bg:#0b1020; --panel:#0f162e; --panel2:#121a34;
      --ink:#e6eefb; --muted:#9bb0d4; --brand:#2f6fff; --brand-dark:#2557d6;
      --line:#24345d; --ok:#16a34a; --err:#ef4444; --glass:rgba(255,255,255,.06);
    }
    *{box-sizing:border-box}
    body{margin:0;background:linear-gradient(180deg,#070b18 0,#0b1020 35%,#0e1630 100%);color:var(--ink);font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
    a{color:var(--ink);text-decoration:none}
    .btn{display:inline-flex;align-items:center;gap:.5rem;border:none;border-radius:12px;padding:.6rem .9rem;cursor:pointer;background:linear-gradient(180deg,var(--brand),var(--brand-dark));color:#fff;font-weight:700;box-shadow:0 10px 26px rgba(47,111,255,.25);transition:.18s transform ease,.18s filter}
    .btn:hover{transform:translateY(-1px);filter:brightness(1.05)}
    .btn.ghost{background:transparent;color:var(--ink);border:1px solid #2a3a68}

    .layout{display:grid;grid-template-columns:260px 1fr;min-height:100vh}

    /* >>> sidebar jadi FLEX COLUMN supaya logout nempel paling bawah */
    .sidebar{
      background:linear-gradient(180deg,var(--panel),var(--panel2));
      border-right:1px solid var(--line);
      padding:18px 14px;
      position:sticky;top:0;height:100vh;
      display:flex;flex-direction:column;
    }
    .welcome{display:flex;align-items:center;gap:.7rem;margin-bottom:14px}
    .avatar{width:44px;height:44px;border-radius:9999px;background:#0c1327;border:1px solid #1e2e57;display:flex;align-items:center;justify-content:center;font-weight:800}
    .avatar img{width:100%;height:100%;object-fit:contain;border-radius:50%}
    .hello small{color:var(--muted)}

    .menu{list-style:none;margin:12px 0 0;padding:0;display:flex;flex-direction:column;gap:6px;flex:1} /* flex:1 dorong logout ke bawah */
    .menu a{display:flex;align-items:center;gap:.6rem;padding:.7rem .8rem;border-radius:10px;border:1px solid transparent;color:var(--ink)}
    .menu a:hover{background:#0c142b;border-color:#2a3a68}
    .menu a.active{background:#0b1838;border-color:#35509a}
    .menu svg{width:18px;height:18px}

    .logout{margin-top:auto;padding-top:10px;border-top:1px dashed var(--line)}
    .topbar{display:flex;justify-content:flex-end;align-items:center;padding:12px 16px;border-bottom:1px solid var(--line);background:#0f162e}
    main{padding:18px}
    .panel{background:linear-gradient(180deg,var(--panel),var(--panel2));border:1px solid var(--line);border-radius:14px;padding:16px}
    .muted{color:var(--muted)}

    .toast{position:fixed;right:16px;bottom:16px;z-index:60;min-width:260px;max-width:90vw;padding:12px 14px;border-radius:12px;color:#fff;box-shadow:0 12px 28px rgba(0,0,0,.35);display:flex;gap:10px}
    .toast.ok{background:linear-gradient(180deg,#19b15f,#118743)}
    .toast.err{background:linear-gradient(180deg,#f05252,#cc2f2f)}
    .toast .close{margin-left:auto;background:transparent;border:none;color:#fff;font-weight:800;font-size:18px;line-height:1;cursor:pointer}
    @media (max-width:900px){.layout{grid-template-columns:1fr}.sidebar{position:relative;height:auto;border-right:none;border-bottom:1px solid var(--line)}}
  </style>
</head>
<body>
  @php $me = auth()->user(); @endphp

  <div class="layout">
    <aside class="sidebar">
      <div class="welcome">
        <div class="avatar"><img src="{{ asset('images/kosan.png') }}" alt="Logo"></div>
        <div class="hello">
          <small>Selamat datang</small>
          <div style="font-weight:800">{{ $me->name }}</div>
        </div>
      </div>

      <ul class="menu">
        <li><a class="{{ request()->routeIs('admin.dashboard')||request()->is('dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
          <svg viewBox="0 0 24 24" fill="none"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-4.5a1 1 0 0 1-1-1v-4.5h-3V20a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
          Dashboard
        </a></li>

        <li><a class="{{ request()->is('admin/rooms*') ? 'active' : '' }}" href="{{ route('admin.rooms.index') }}">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 17v-6a2 2 0 0 1 2-2h7a4 4 0 0 1 4 4v4M3 13h18v4M7 9h2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Kelola Kamar
        </a></li>

        <li><a class="{{ request()->is('admin/penghuni*') ? 'active' : '' }}" href="{{ route('admin.penghuni.index') }}">
          <svg viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M20 21v-2a3 3 0 0 0-2-2.82M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-2a3 3 0 1 0 0-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Kelola Penghuni
        </a></li>

        <li><a class="{{ request()->is('admin/tagihan*') ? 'active' : '' }}" href="{{ route('admin.tagihan.index') }}">
          <svg viewBox="0 0 24 24" fill="none"><path d="M6 3h10l2 3v14H6V3Zm0 4h12M9 12h6m-6 4h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Kelola Tagihan
        </a></li>

        <li><a class="{{ request()->is('admin/pengumuman*') ? 'active' : '' }}" href="{{ route('admin.pengumuman.index') }}">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 11v2m18-6v10l-8-3H8a3 3 0 0 1-3-3v0a3 3 0 0 1 3-3h5l8-3Z M8 13v6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Pengumuman
        </a></li>

        <li class="logout">
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="btn ghost" style="width:100%">Logout</button>
          </form>
        </li>
      </ul>
    </aside>

    <section>
      <div class="topbar"></div>
      <main>
        @if(session('ok') || session('error'))
          <div id="toast" class="toast {{ session('ok') ? 'ok' : 'err' }}">
            <div style="font-weight:700">{{ session('ok') ? 'Berhasil' : 'Gagal' }}</div>
            <div>{{ session('ok') ?? session('error') }}</div>
            <button class="close" aria-label="Tutup" onclick="this.parentElement.remove()">×</button>
          </div>
          <script>setTimeout(()=>{const t=document.getElementById('toast');if(t)t.remove();},3500);</script>
        @endif

        @yield('content')
      </main>
    </section>
  </div>

  <script>
    document.addEventListener('click', function(e){
      const btn = e.target.closest('form.js-confirm button[type="submit"], form.js-confirm input[type="submit"]');
      if(!btn) return;
      const form = btn.closest('form.js-confirm');
      const msg = form?.dataset?.message || 'Yakin ingin menghapus data ini?';
      if(!confirm(msg)){ e.preventDefault(); }
    });
  </script>
</body>
</html>
