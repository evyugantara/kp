<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Pondok Hasanah' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="{{ asset('images/kosan.png') }}">
  <style>
    :root{
      --bg:#0b1020; --panel:#0f162e; --panel-2:#121a34;
      --ink:#e6eefb; --muted:#9bb0d4; --brand:#2f6fff; --brand-2:#00d4ff;
      --line:#24345d; --glass:rgba(255,255,255,.06);
    }

    .btn{
      display:inline-flex;align-items:center;gap:.5rem;border:none;border-radius:12px;
      padding:.6rem .9rem;cursor:pointer;background:linear-gradient(180deg, var(--brand), #2557d6);
      color:white;font-weight:700;box-shadow:0 10px 26px rgba(47,111,255,.25);
      transition:.15s transform ease, .15s filter, .15s box-shadow, .12s background;
      outline: none;
    }
    .btn:hover{transform:translateY(-1px);filter:brightness(1.06)}
    .btn:focus-visible{box-shadow:0 0 0 3px rgba(0,212,255,.35), 0 10px 26px rgba(47,111,255,.25)}
    .btn:active{transform:translateY(0);filter:brightness(.95);background:linear-gradient(180deg, #2557d6, #244dbd)}
    .btn.ghost{background:transparent;color:var(--ink);border:1px solid #2a3a68}
    .btn.ghost:hover{background:#0b1838}
    .btn.ghost:active{background:#0a1530}

    *{box-sizing:border-box}
    html,body{margin:0;background:linear-gradient(180deg,#070b18 0%, #0b1020 30%, #0e1630 100%); color:var(--ink); font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif}
    a{color:var(--ink); text-decoration:none}
    .container{max-width:1150px;margin:0 auto;padding:0 18px}

    nav{position:sticky; top:0; z-index:40; backdrop-filter: blur(10px); background:linear-gradient(180deg, rgba(5,8,17,.75), rgba(5,8,17,.35)); border-bottom:1px solid #0f1a33}
    nav .bar{display:flex; align-items:center; justify-content:space-between; padding:14px 0}
    .brand{display:flex; align-items:center; gap:.6rem; font-weight:800; letter-spacing:.4px}
    .brand .logo{width:36px; height:36px; object-fit:contain; display:block; filter: drop-shadow(0 0 10px rgba(0,212,255,.15))}
    .nav-links{display:flex; gap:14px; align-items:center}

    .site-hero{position:relative; overflow:hidden; border-bottom:1px solid #0f1a33}
    .site-hero .wrap{padding:42px 0 54px; display:grid; grid-template-columns:1.2fr .8fr; gap:22px}
    .site-hero h1{font-size:clamp(28px, 4.5vw, 44px); margin:.5rem 0; line-height:1.15}
    .site-hero p{color:var(--muted); margin:0}
    .orb, .orb2{position:absolute; filter:blur(80px); opacity:.55; pointer-events:none}
    .orb{width:420px; height:420px; top:-120px; left:-120px; background:radial-gradient(closest-side, rgba(47,111,255,.6), transparent 70%)}
    .orb2{width:420px; height:420px; bottom:-120px; right:-120px; background:radial-gradient(closest-side, rgba(0,212,255,.45), transparent 70%)}

    .grid{display:grid; grid-template-columns:repeat(auto-fill,minmax(270px,1fr)); gap:16px}
    .card{background:linear-gradient(180deg, var(--panel), var(--panel-2)); border:1px solid var(--line); border-radius:16px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,.35); transform:translateY(0) scale(1); transition:.25s transform ease, .25s box-shadow, .25s border-color}
    .card:hover{transform:translateY(-6px) scale(1.01); box-shadow:0 30px 60px rgba(0,0,0,.45); border-color:#35509a}
    .imgwrap{position:relative; height:190px; background:#0c1327}
    .imgwrap img{width:100%; height:100%; object-fit:cover; display:block; opacity:.95; transition:.3s opacity ease}
    .card:hover .imgwrap img{opacity:1}
    .badge{position:absolute; top:12px; left:12px; background:rgba(3,8,21,.7); color:#dfe8ff; padding:.36rem .55rem; border-radius:10px; border:1px solid #263a6d; font-size:.9rem; font-weight:700}
    .status{position:absolute; top:12px; right:12px; background:linear-gradient(180deg,#1f45ff,#1850ff); color:#fff; padding:.32rem .5rem; border-radius:999px; font-size:.78rem; border:1px solid #3350ff}
    .content{padding:14px}
    .title{font-weight:800; margin:.1rem 0 .35rem}
    .muted{color:#9bb0d4}
    .chips{display:flex; gap:8px; flex-wrap:wrap; margin:.35rem 0 .5rem}
    .chip{font-size:.78rem; padding:.26rem .52rem; border:1px solid #2a3a68; border-radius:999px; color:#b9c8ea; background:var(--glass)}

    .detail{display:grid; grid-template-columns:1.2fr .8fr; gap:18px}
    @media (max-width: 950px){ .site-hero .wrap, .detail{grid-template-columns:1fr} }
    .slider{position:relative; overflow:hidden; border-radius:16px; border:1px solid var(--line); background:#0c1327}
    .slides{display:flex; transition:transform .45s cubic-bezier(.2,.7,.2,1); will-change:transform}
    .slides img{flex:0 0 100%; width:100%; max-height:520px; object-fit:cover}

    .reveal{opacity:0; transform:translateY(14px) scale(.98); transition:.5s opacity ease,.5s transform ease}
    .reveal.show{opacity:1; transform:none}

    .wa-float{position:fixed; z-index:60; right:16px; top:50%; transform:translateY(-50%);
      width:56px; height:56px; border-radius:14px; display:flex; align-items:center; justify-content:center;
      background:linear-gradient(180deg,#24d366,#149c46); border:1px solid #48e387; box-shadow:0 18px 32px rgba(0,0,0,.35)}
    .wa-float svg{width:27px; height:27px; fill:#02240f}
    @media (max-width:640px){ .wa-float{top:auto; bottom:18px; transform:none} }

    /* === Typewriter (judul dua baris) === */
    .typewriter { display:inline-block }
    .cursor {
      display:inline-block; width:1ch; margin-left:4px;
      color:#9db7ff; animation:blink 1s step-end infinite;
    }
    @keyframes blink { 50% { opacity:0; } }
  </style>
</head>
<body>
@php
  $role = strtolower(auth()->user()->role ?? '');
  $isPengelola = auth()->check() && $role === 'pengelola';
  $onAdminPage = request()->is('dashboard') || request()->is('admin*') || (request()->is('rooms*') && auth()->check());
  $hideHero = $onAdminPage;
@endphp

<nav>
  <div class="container bar">
    <div class="brand">
      <img class="logo" src="{{ asset('images/kosan.png') }}" alt="Pondok Hasanah">
      <a href="{{ route('home') }}">Pondok Hasanah</a>
    </div>

    <div class="nav-links">
      {{-- Tamu hanya Login; user login lihat Dashboard + Logout --}}
      @if(auth()->check())
        @php $role = strtolower(auth()->user()->role ?? ''); @endphp
        @if($role === 'pengelola')
          <a class="btn ghost" href="{{ route('admin.dashboard') }}">Dashboard</a>
        @elseif($role === 'penghuni')
          <a class="btn ghost" href="{{ route('tenant.dashboard') }}">Dashboard</a>
        @else
          <a class="btn ghost" href="{{ route('dashboard') }}">Dashboard</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="margin:0">@csrf
          <button class="btn ghost">Logout</button>
        </form>
      @else
        <a class="btn ghost" href="{{ route('login') }}">Login</a>
      @endif
    </div>
  </div>
</nav>

@unless($hideHero)
  <header class="site-hero">
    <span class="orb"></span><span class="orb2"></span>
    <div class="container wrap">
      <div>
        <h1 class="typewriter">
          <span id="tw1"></span><br>
          <span id="tw2" style="color:#9db7ff"></span>
          <span class="cursor">|</span>
        </h1>
        <p>Kualitas prima, harga bersahabat. Jelajahi pilihan kamar dengan fasilitas lengkap.</p>
        <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap">
          <a class="btn" href="#rooms">Lihat Kamar</a>
          <a class="btn ghost" href="https://wa.me/6281234567890?text={{ rawurlencode('Halo, saya ingin menanyakan ketersediaan kamar di Pondok Hasanah.') }}" target="_blank">Tanya via WhatsApp</a>
        </div>
      </div>
      <div style="align-self:center">
        {{-- Gambar/slider TIDAK diubah supaya ukuran tetap --}}
        <div class="slider" aria-hidden="true">
          <div class="slides" style="transform:translateX(0)">
            <img src="https://images.unsplash.com/photo-1505692952047-1a78307da8f2?q=80&w=1600&auto=format&fit=crop" alt="preview 1">
            <img src="https://images.unsplash.com/photo-1554995207-c18c203602cb?q=80&w=1600&auto=format&fit=crop" alt="preview 2">
            <img src="https://images.unsplash.com/photo-1582582429416-ea8f1d59f5c0?q=80&w=1600&auto=format&fit=crop" alt="preview 3">
          </div>
        </div>
      </div>
    </div>
  </header>
@endunless

<main class="container" style="padding:20px 0 34px">
  @yield('content')
</main>

@php
  $showWa = !($isPengelola || $onAdminPage);
  $wa = '6281234567890';
  $msg = rawurlencode('Halo, saya ingin menanyakan ketersediaan kamar di Pondok Hasanah.');
  $waLink = "https://wa.me/{$wa}?text={$msg}";
@endphp
@if($showWa)
  <a class="wa-float" href="{{ $waLink }}" target="_blank" aria-label="WhatsApp Pengelola">
    <svg viewBox="0 0 24 24"><path d="M20.5 3.5A12 12 0 0 0 1.6 15.6L0 24l8.3-1.7A12 12 0 0 0 24 12 12 12 0 0 0 20.5 3.5ZM12 21.5c-1.9 0-3.7-.5-5.2-1.5l-.4-.2-3.7 1 1-3.6-.2-.4a9.6 9.6 0 1 1 8.5 4.7ZM17.7 14c-.3-.2-1.8-.9-2.1-1-.4-.1-.6-.1-.8.2l-.8 1c-.2.2-.4.2-.7.1-.3-.2-1.3-.5-2.5-1.5-1-.9-1.7-1.9-1.9-2.2s-.1-.5.1-.8l.5-.6c.1-.2.2-.4.3-.6 0-.1 0-.4-.1-.5l-.9-2.2c-.2-.6-.5-.5-.7-.6l-.6-.1c-.2 0-.6.1-.8.4-.3.3-1.1 1.1-1.1 2.6s1.1 3 1.3 3.2 2.1 3.2 5 4.4c2.5 1 2.5.7 3 .7.5 0 1.9-.7 2.2-1.4.3-.7.3-1.3.2-1.5-.1-.2-.3-.3-.6-.4Z"/></svg>
  </a>
@endif

<script>
  // Reveal-on-scroll
  const io=new IntersectionObserver((e)=>e.forEach(x=>x.isIntersecting&&x.target.classList.add('show')), {threshold:.15});
  document.querySelectorAll('.reveal').forEach(el=>io.observe(el));

  // === Typewriter dua baris, LOOP tiap 2 detik ===
  (function(){
    const el1 = document.getElementById('tw1'); // baris 1
    const el2 = document.getElementById('tw2'); // baris 2
    if(!el1 || !el2) return;

    const line1 = 'Temukan kamar terbaikmu di';
    const line2 = 'Pondok Hasanah';

    const typeSpeed1 = 40;   // ms/karakter baris 1
    const typeSpeed2 = 40;   // ms/karakter baris 2
    const eraseSpeed  = 30;  // ms/karakter saat hapus
    const holdTime    = 2000; // 2 detik menahan teks penuh sebelum hapus

    let i=0, j=0;

    function typeLine1(){
      el1.textContent = line1.slice(0, i);
      if (i < line1.length){
        i++; setTimeout(typeLine1, typeSpeed1);
      } else {
        setTimeout(() => { j=0; typeLine2(); }, 200); // jeda kecil sebelum baris 2
      }
    }

    function typeLine2(){
      el2.textContent = line2.slice(0, j);
      if (j < line2.length){
        j++; setTimeout(typeLine2, typeSpeed2);
      } else {
        // Tahan 2 detik, lalu hapus balik
        setTimeout(() => eraseLine2(), holdTime);
      }
    }

    function eraseLine2(){
      el2.textContent = line2.slice(0, j);
      if (j > 0){
        j--; setTimeout(eraseLine2, eraseSpeed);
      } else {
        setTimeout(() => eraseLine1(), 100);
      }
    }

    function eraseLine1(){
      el1.textContent = line1.slice(0, i);
      if (i > 0){
        i--; setTimeout(eraseLine1, eraseSpeed);
      } else {
        // Ulang dari awal
        setTimeout(() => { i=0; j=0; typeLine1(); }, 200);
      }
    }

    // mulai
    typeLine1();
  })();
</script>
</body>
</html>
