<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Penghuni' }} • Pondok Hasanah</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link rel="icon" type="image/png" href="{{ asset('images/kosan.png') }}">
  <style>
    :root{--bg:#0b1020;--panel:#0f162e;--panel2:#121a34;--ink:#e6eefb;--muted:#9bb0d4;--brand:#2f6fff;--brand-dark:#2557d6;--line:#24345d}
    *{box-sizing:border-box}
    body{margin:0;background:linear-gradient(180deg,#070b18 0,#0b1020 35%,#0e1630 100%);color:var(--ink);font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
    a{color:var(--ink);text-decoration:none}
    .btn{display:inline-flex;align-items:center;gap:.5rem;border:none;border-radius:12px;padding:.6rem .9rem;cursor:pointer;background:linear-gradient(180deg,var(--brand),var(--brand-dark));color:#fff;font-weight:700;box-shadow:0 10px 26px rgba(47,111,255,.25);transition:.15s}
    .btn:hover{transform:translateY(-1px);filter:brightness(1.06)}
    .btn:active{transform:translateY(0);filter:brightness(.95);background:linear-gradient(180deg,#2557d6,#244dbd)}
    .btn.ghost{background:transparent;color:var(--ink);border:1px solid #2a3a68}
    .btn.ghost:hover{background:#0b1838}

    .layout{display:grid;grid-template-columns:260px 1fr;min-height:100vh}
    .sidebar{background:linear-gradient(180deg,var(--panel),var(--panel2));border-right:1px solid var(--line);padding:18px 14px;position:sticky;top:0;height:100vh;display:flex;flex-direction:column}
    .welcome{display:flex;align-items:center;gap:.7rem;margin-bottom:14px}
    .avatar{width:44px;height:44px;border-radius:9999px;background:#0c1327;border:1px solid #1e2e57;display:flex;align-items:center;justify-content:center}
    .avatar img{width:100%;height:100%;object-fit:contain;border-radius:50%}
    .menu{list-style:none;margin:12px 0 0;padding:0;display:flex;flex-direction:column;gap:6px;flex:1}
    .menu a{display:flex;align-items:center;gap:.6rem;padding:.7rem .8rem;border-radius:10px;border:1px solid transparent}
    .menu a:hover{background:#0c142b;border-color:#2a3a68}
    .menu a.active{background:#0b1838;border-color:#35509a}
    .logout{margin-top:auto;padding-top:10px;border-top:1px dashed var(--line)}

    main{padding:18px}
    .panel{background:linear-gradient(180deg,var(--panel),var(--panel2));border:1px solid var(--line);border-radius:14px;padding:16px}
    .muted{color:var(--muted)}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px}

    @media (max-width:900px){.layout{grid-template-columns:1fr}.sidebar{position:relative;height:auto;border-right:none;border-bottom:1px solid var(--line)}}
  </style>
</head>
<body>
  @php $me = auth()->user(); @endphp
  <div class="layout">
    <aside class="sidebar">
      <div class="welcome">
        <div class="avatar"><img src="{{ asset('images/kosan.png') }}" alt="Logo"></div>
        <div>
          <div style="font-size:.85rem;color:#9bb0d4">Selamat datang</div>
          <div style="font-weight:800">{{ $me->name }}</div>
        </div>
      </div>
      <ul class="menu">
        <li><a class="{{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}" href="{{ route('tenant.dashboard') }}">Dashboard</a></li>
        <li><a class="{{ request()->routeIs('tenant.profile.*') ? 'active' : '' }}" href="{{ route('tenant.profile.edit') }}">Profil</a></li>
       
        {{-- Menu Pengumuman di sidebar tenant --}}
<li>
  <a class="{{ request()->routeIs('tenant.announcements.*') ? 'active' : '' }}"
     href="{{ route('tenant.announcements.index') }}">
    Pengumuman
  </a>
</li>

        <li class="logout">
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="btn ghost" style="width:100%">Logout</button>
          </form>
        </li>
      </ul>
    </aside>
    <main>
      @if(session('ok') || session('error'))
        <div class="panel" style="border-color:#19442d;background:linear-gradient(180deg,#0f2a1c,#0d2318);margin-bottom:12px">
          <strong>{{ session('ok') ? 'Berhasil' : 'Gagal' }}:</strong>
          <span>{{ session('ok') ?? session('error') }}</span>
        </div>
      @endif
      @yield('content')
    </main>
  </div>
</body>
</html>
