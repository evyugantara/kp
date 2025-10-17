@extends('layouts.tenant')

@section('content')
  <h2 style="margin:.2rem 0">Pembayaran</h2>
  <p class="muted" style="margin:0">Bayar sewa kamar melalui QRIS, GoPay, atau Bank Transfer.</p>

  @if(session('ok'))
    <div class="panel" style="border-left:3px solid #2f6fff">{{ session('ok') }}</div>
  @endif

  @isset($errorMsg)
    @if($errorMsg)
      <div class="panel" style="border-left:3px solid #d33">{{ $errorMsg }}</div>
    @endif
  @endisset

  @if($payment && $payment->status === 'pending' && $snapToken)
    <div class="panel" style="border-left:3px solid #2f6fff">
      <div style="font-weight:600">🚀 Pembayaran siap diproses</div>
      <div class="muted" style="font-size:.9rem;margin-top:4px">
        Order: {{ $payment->order_id }} • Jumlah: Rp {{ number_format($amount,0,',','.') }}
      </div>
    </div>
  @endif

  @if(!$room)
    <div class="panel">Akun belum terhubung ke kamar.</div>
  @else
    <div class="grid" style="margin-top:12px">
      <div class="panel">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center">
          <div>
            <div style="font-weight:800">🏠 Kamar Saya</div>
            <div class="muted" style="font-size:.9rem;margin-top:2px">
              {{ $room->kode }} — {{ $room->nama }}
            </div>
          </div>
          <div>
            <span class="chip">Rp {{ number_format($amount,0,',','.') }}/bulan</span>
          </div>
        </div>
        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
          <span style="background:linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);color:#0369a1;padding:6px 12px;border-radius:16px;font-size:0.8rem;font-weight:500;border:none;box-shadow:0 1px 3px rgba(3,105,161,0.1)">🎵 QRIS</span>
          <span style="background:linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);color:#0369a1;padding:6px 12px;border-radius:16px;font-size:0.8rem;font-weight:500;border:none;box-shadow:0 1px 3px rgba(3,105,161,0.1)">💱 GoPay</span>
          <span style="background:linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);color:#0369a1;padding:6px 12px;border-radius:16px;font-size:0.8rem;font-weight:500;border:none;box-shadow:0 1px 3px rgba(3,105,161,0.1)">🏦 Bank Transfer</span>
        </div>
      </div>

      <div class="panel">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap">
          <div>
            <div style="font-weight:800">📈 Status Pembayaran</div>
            <div class="muted" style="font-size:.9rem;margin-top:2px">
              @if($payment)
                #{{ $payment->order_id }} — <b>{{ strtoupper($payment->status) }}</b>
                @if($payment->paid_at) (dibayar: {{ $payment->paid_at->translatedFormat('d M Y H:i') }}) @endif
              @else
                Belum ada tagihan.
              @endif
            </div>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            @if(($snapToken ?? null) && $payment && $payment->status === 'pending')
              <button id="btn-pay" class="btn">🚀 Pilih Metode & Bayar</button>
            @endif
            @if(($snapUrl ?? null) && $payment && $payment->status === 'pending')
              <a class="btn ghost" href="{{ $snapUrl }}" target="_blank">🔗 Buka Link Pembayaran</a>
            @endif
            @if($payment && $payment->status === 'pending')
              <a class="btn ghost" href="{{ route('tenant.payments.index', ['new_payment' => '1']) }}" 
                 onclick="return confirm('Yakin ingin membuat pembayaran baru? Pembayaran sebelumnya akan dibatalkan.')">
                 🔁 Ganti Metode Pembayaran
              </a>
            @endif
            @if(!$payment || $payment->status !== 'pending')
              <a class="btn" href="{{ route('tenant.payments.index', ['new_payment' => '1']) }}">✨ Buat Pembayaran Baru</a>
            @endif
          </div>
        </div>
      </div>
    </div>
    
    <div class="panel" style="margin-top:12px">
      <div class="muted" style="font-size:.9rem;text-align:center">
        🔒 Keamanan Terjamin • ⚡ Proses Instan
      </div>
    </div>

  @endif
</div>

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
              onSuccess: function(result){ 
                var successUrl = @json(route('tenant.payments.success')) + '?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status + '&status_code=' + result.status_code;
                window.location.href = successUrl;
              },
              onPending: function(result){ 
                var successUrl = @json(route('tenant.payments.success')) + '?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status + '&status_code=' + result.status_code;
                window.location.href = successUrl;
              },
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
