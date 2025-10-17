<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk • Pondok Hasanah</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="icon" type="image/png" href="{{ asset('images/kosan.png') }}">
  <style>
    :root{
      --bg:#0b1020; --panel:#0f162e; --panel2:#121a34; --ink:#e6eefb; --muted:#9bb0d4;
      --brand:#2f6fff; --brand-dark:#2557d6; --line:#24345d;
    }
    *{box-sizing:border-box}
    body{margin:0;min-height:100vh;display:grid;place-items:center;background:linear-gradient(180deg,#070b18 0,#0b1020 35%,#0e1630 100%);color:var(--ink);font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
    .card{width:min(92vw,420px);background:linear-gradient(180deg,var(--panel),var(--panel2));border:1px solid var(--line);border-radius:16px;box-shadow:0 20px 40px rgba(0,0,0,.35);padding:26px;text-align:center}
    .logo{width:74px;height:74px;object-fit:contain;margin:0 auto 8px;filter: drop-shadow(0 0 10px rgba(0,212,255,.15))}
    h1{font-size:22px;margin:.2rem 0}
    label{display:block;margin:.6rem 0 .25rem;text-align:left}
    input[type="email"],input[type="password"]{width:100%;padding:.7rem .8rem;border-radius:12px;border:1px solid #2a3a68;background:#0c142b;color:var(--ink)}
    .row{display:flex;justify-content:space-between;align-items:center;margin:.5rem 0}
    .btn{display:inline-flex;align-items:center;gap:.5rem;border:none;border-radius:12px;padding:.7rem 1rem;cursor:pointer;background:linear-gradient(180deg,var(--brand),var(--brand-dark));color:#fff;font-weight:700;box-shadow:0 10px 26px rgba(47,111,255,.25);transition:.18s transform ease;width:100%}
    .btn:hover{transform:translateY(-1px)}
    .muted{color:var(--muted)}
    .help{margin-top:10px}
    .error{background:#3b0d0d;border:1px solid #7a1f1f;padding:10px;border-radius:12px;margin-bottom:8px;text-align:left}
  </style>
</head>
<body>
  <form class="card" method="POST" action="{{ route('login') }}">
    @csrf

    <img class="logo" src="{{ asset('images/kosan.png') }}" alt="Logo">
    <div class="muted" style="margin-bottom:8px">Pondok Hasanah</div>
    <h1>Masuk Sekarang</h1>
    @if ($errors->any())
      <div class="error">
        <ul style="margin:0;padding-left:18px">
          @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">

    <label for="password">Password</label>
    <input id="password" type="password" name="password" required autocomplete="current-password">

    <div class="row">
      <label style="display:flex;align-items:center;gap:.5rem;font-size:.95rem">
        <input type="checkbox" name="remember"> <span class="muted">Ingat saya</span>
      </label>
      <a href="{{ route('password.request') }}" class="muted">Lupa password?</a>
    </div>

    <button class="btn" type="submit">Masuk</button>

    {{-- Jika masih ingin link daftar, bisa tambahkan di sini; default disembunyikan sesuai permintaan --}}
  </form>
</body>
</html>
