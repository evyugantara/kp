@extends('layouts.app')

@section('content')
  <section class="detail">
    {{-- GALERI SLIDER --}}
    <article class="reveal">
      @php $images = $room->images; @endphp
      @if($images->count() >= 1)
        <div class="slider" id="g">
          <button class="navbtn left" aria-label="Sebelumnya" id="prev">‹</button>
          <div class="slides" id="slides">
            @foreach($images as $img)
              <img src="{{ $img->url }}" alt="{{ $room->nama }}">
            @endforeach
          </div>
          <button class="navbtn right" aria-label="Berikutnya" id="next">›</button>
          <div class="dots" id="dots">
            @foreach($images as $i=>$img)
              <span data-i="{{ $i }}"></span>
            @endforeach
          </div>
        </div>
      @elseif($room->cover_url)
        <div class="slider reveal"><img src="{{ $room->cover_url }}" alt="{{ $room->nama }}" style="width:100%;max-height:520px;object-fit:cover"></div>
      @endif

      <div class="card" style="margin-top:14px; padding:16px">
        <h2 style="margin:.2rem 0">{{ $room->nama }}</h2>
        <div style="display:flex; gap:10px; flex-wrap:wrap">
          <span class="chip">Kode: {{ $room->kode }}</span>
          <span class="chip">{{ $room->tersedia ? 'Tersedia' : 'Tidak Tersedia' }}</span>
          <span class="chip">Rp {{ number_format($room->harga,0,',','.') }}/bulan</span>
        </div>
        @if($room->deskripsi)
          <div style="height:10px"></div>
          <p class="muted" style="line-height:1.8">{{ $room->deskripsi }}</p>
        @endif

        @if(is_array($room->fasilitas) && count($room->fasilitas))
          <h4 style="margin:.6rem 0 .3rem">Fasilitas</h4>
          <div class="chips">
            @foreach($room->fasilitas as $f)
              <span class="chip">✓ {{ $f }}</span>
            @endforeach
          </div>
        @endif

        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px">
          <a class="btn" href="https://wa.me/6281234567890?text={{ rawurlencode('Halo, saya ingin booking kamar: '.$room->nama.' (Kode '.$room->kode.')') }}" target="_blank">Reservasi via WhatsApp</a>
          <a class="btn ghost" href="{{ route('home') }}">← Kembali</a>
        </div>
      </div>
    </article>

    {{-- PANEL INFO SAMPING --}}
    <aside class="reveal">
      <div class="card" style="padding:16px">
        <h3 style="margin:.2rem 0">Informasi</h3>
        <p class="muted" style="margin-top:-.2rem">Gunakan tombol WhatsApp untuk menanyakan ketersediaan & reservasi.</p>
        <div style="height:8px"></div>
        <div style="display:flex; gap:8px; flex-wrap:wrap">
          <span class="chip">Lokasi strategis</span>
          <span class="chip">Akses 24 jam</span>
          <span class="chip">Keamanan terjaga</span>
        </div>
      </div>

      <div class="card" style="padding:16px; margin-top:14px">
        <h4 style="margin:.2rem 0">Kamar lain</h4>
        <p class="muted">Lihat pilihan lainnya di halaman utama.</p>
        <a class="btn ghost" href="{{ route('home') }}">Lihat Semua</a>
      </div>
    </aside>
  </section>

  {{-- Slider JS: keyboard, klik, swipe --}}
  <script>
    (function(){
      const slides=document.getElementById('slides'); if(!slides) return;
      const dotsBox=document.getElementById('dots');
      const dots=[...dotsBox.children];
      const total=slides.children.length;
      const prev=document.getElementById('prev');
      const next=document.getElementById('next');
      let i=0, w=()=>slides.clientWidth;

      function go(n){
        i=(n+total)%total;
        slides.style.transform=`translateX(-${i*w()}px)`;
        dots.forEach((d,idx)=>d.classList.toggle('active', idx===i));
      }
      // resize handler
      new ResizeObserver(()=>go(i)).observe(slides);

      // controls
      prev.addEventListener('click',()=>go(i-1));
      next.addEventListener('click',()=>go(i+1));
      dots.forEach(d=>d.addEventListener('click',()=>go(parseInt(d.dataset.i))));

      // keyboard
      window.addEventListener('keydown',e=>{
        if(e.key==='ArrowLeft') go(i-1);
        if(e.key==='ArrowRight') go(i+1);
      });

      // swipe
      let sx=0, dx=0;
      slides.addEventListener('pointerdown',e=>{ sx=e.clientX; slides.setPointerCapture(e.pointerId); });
      slides.addEventListener('pointermove',e=>{ if(!sx) return; dx=e.clientX-sx; slides.style.transform=`translateX(calc(-${i*w()}px + ${dx}px))`; });
      slides.addEventListener('pointerup',()=>{
        if(Math.abs(dx)>60) go(i+(dx<0?1:-1)); else go(i);
        sx=0; dx=0;
      });

      go(0);
    })();
  </script>
@endsection
