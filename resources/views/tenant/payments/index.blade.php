@extends('layouts.tenant')

@section('content')
  <h2 style="margin:.2rem 0">Pembayaran</h2>
  <p class="muted" style="margin:0">Bayar tagihan kamar melalui QRIS.</p>

  @if(session('ok'))
    <div class="panel" style="border-left:3px solid #2f6fff">{{ session('ok') }}</div>
  @endif

  {{-- Error dari server (mis. gagal bikin transaksi) --}}
  @isset($errorMsg)
    @if($errorMsg)
      <div class="panel" style="border-left:3px solid #d33">{{ $errorMsg }}</div>
    @endif
  @endisset

  {{-- Debug ringan biar ketahuan apa yang hilang --}}
  <div class="panel" style="display:flex;gap:18px;flex-wrap:wrap">
    <div>Client Key: <b>{{ $clientKey ? substr($clientKey,0,6).'…' : 'KOSONG' }}</b></div>
    <div>Snap Token: <b>{{ $snapToken ? 'ADA' : 'TIDAK ADA' }}</b></div>
    <div>Redirect URL: <b>{{ $snapUrl ? 'ADA' : 'TIDAK ADA' }}</b></div>
    <div>Order: <b>{{ $payment->order_id ?? '-' }}</b></div>
    <div>Harga: <b>Rp {{ number_format($amount,0,',','.') }}</b></div>
  </div>

  @if(!$room)
    <div class="panel">Akun belum terhubung ke kamar.</div>
  @else
    <div class="grid" style="margin-top:12px">
      <div class="panel">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center">
          <div>
            <div style="font-weight:800">Kamar Saya</div>
            <div class="muted" style="font-size:.9rem;margin-top:2px">
              {{ $room->kode }} — {{ $room->nama }}
            </div>
          </div>
          <div><span class="chip">Harga: Rp {{ number_format($amount,0,',','.') }}</span></div>
        </div>
      </div>

      <div class="panel">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center">
          <div>
            <div style="font-weight:800">Status Pembayaran</div>
            <div class="muted" style="font-size:.9rem;margin-top:2px">
              @if($payment)
                #{{ $payment->order_id }} — <b>{{ strtoupper($payment->status) }}</b>
                @if($payment->paid_at) (dibayar: {{ $payment->paid_at->translatedFormat('d M Y H:i') }}) @endif
              @else
                Belum ada tagihan.
              @endif
            </div>
          </div>
          <div style="display:flex;gap:10px">
            @if(($snapToken ?? null) && $payment && $payment->status === 'pending')
              <button id="btn-pay" class="btn">Bayar Sekarang (QRIS)</button>
            @endif
            @if(($snapUrl ?? null) && $payment && $payment->status === 'pending')
              <a class="btn ghost" href="{{ $snapUrl }}" target="_blank">Buka Link Pembayaran</a>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="panel">
  <div class="muted" style="font-size:.9rem">
    Env: {{ config('midtrans.is_production') ? 'PRODUCTION' : 'SANDBOX' }} ·
    ServerKey: {{ \Illuminate\Support\Str::startsWith(config('midtrans.server_key'),'SB-Mid-server-') ? 'OK (sandbox)' : 'SALAH' }} ·
    ClientKey: {{ \Illuminate\Support\Str::startsWith(config('midtrans.client_key'),'SB-Mid-client-') ? 'OK (sandbox)' : 'SALAH' }}
  </div>
</div>

  @endif

  {{-- Snap JS + fallback otomatis --}}
  @if(($snapToken ?? false))
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{ $clientKey }}"></script>
    <script>
      (function(){
        const fallbackUrl = @json($snapUrl ?? '');
        // Jika Snap JS diblokir, otomatis buka link pembayaran setelah 1.2s
        setTimeout(function(){
          if (!window.snap && fallbackUrl) {
            window.open(fallbackUrl, '_blank');
          }
        }, 1200);

        const btn = document.getElementById('btn-pay');
        if(!btn) return;

        btn.addEventListener('click', function(){
          if (window.snap && typeof window.snap.pay === 'function') {
            snap.pay(@json($snapToken), {
              onSuccess: function(){ window.location.href = @json(route('tenant.payments.finish')); },
              onPending: function(){ window.location.href = @json(route('tenant.payments.finish')); },
              onError:   function(){ alert('Terjadi kesalahan saat memproses pembayaran.'); },
              onClose:   function(){ /* ditutup */ }
            });
          } else if (fallbackUrl) {
            window.open(fallbackUrl, '_blank');
          } else {
            alert('Snap belum termuat & tidak ada link fallback.');
          }
        });
      })();
    </script>
  @endif
@endsection
