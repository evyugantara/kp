@extends('layouts.tenant')

@section('content')
  <div style="text-align: center; padding: 48px 24px;">
    <!-- Success Icon -->
    <div style="margin-bottom: 32px;">
      <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);">
        <span style="font-size: 48px; color: white;">✅</span>
      </div>
    </div>

    <!-- Success Message -->
    <h1 style="font-size: 2.5rem; font-weight: 800; color: #065f46; margin-bottom: 16px;">
      Pembayaran Berhasil!
    </h1>
    
    <p style="font-size: 1.1rem; color: #6b7280; margin-bottom: 32px; max-width: 500px; margin-left: auto; margin-right: auto;">
      Terima kasih! Pembayaran sewa kamar Anda telah berhasil diproses.
    </p>

    <!-- Payment Details -->
    @if($payment)
      <div class="panel" style="max-width: 500px; margin: 0 auto 32px auto; text-align: left;">
        <h3 style="font-weight: 700; margin-bottom: 16px; text-align: center;">📋 Detail Pembayaran</h3>
        
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
          <span style="color: #6b7280;">Order ID:</span>
          <span style="font-weight: 600;">#{{ $payment->order_id }}</span>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
          <span style="color: #6b7280;">Jumlah:</span>
          <span style="font-weight: 600; color: #059669;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
          <span style="color: #6b7280;">Status:</span>
          <span style="padding: 4px 12px; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #166534; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
            {{ strtoupper($payment->status) }}
          </span>
        </div>
        
        @if($payment->payment_type)
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
          <span style="color: #6b7280;">Metode:</span>
          <span style="font-weight: 600;">{{ strtoupper($payment->payment_type) }}</span>
        </div>
        @endif
        
        @if($payment->paid_at)
        <div style="display: flex; justify-content: space-between; padding: 8px 0;">
          <span style="color: #6b7280;">Waktu Bayar:</span>
          <span style="font-weight: 600;">{{ $payment->paid_at->format('d M Y H:i') }}</span>
        </div>
        @endif
      </div>
    @else
      <div class="panel" style="max-width: 500px; margin: 0 auto 32px auto;">
        <div style="text-align: center; padding: 24px;">
          <span style="font-size: 48px; margin-bottom: 16px; display: block;">📄</span>
          <p style="color: #6b7280; margin: 0;">Detail pembayaran sedang diproses...</p>
        </div>
      </div>
    @endif

    <!-- Action Buttons -->
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="{{ route('tenant.payments.index') }}" class="btn" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none; padding: 12px 24px;">
        📊 Lihat Status Pembayaran
      </a>
      
      <a href="{{ route('tenant.dashboard') }}" class="btn ghost" style="padding: 12px 24px;">
        🏠 Kembali ke Dashboard
      </a>
    </div>

    <!-- Success Tips -->
    <div class="panel" style="max-width: 600px; margin: 48px auto 0 auto; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd;">
      <h4 style="color: #0369a1; font-weight: 700; margin-bottom: 12px;">💡 Informasi Penting</h4>
      <ul style="text-align: left; color: #1e40af; margin: 0; padding-left: 20px;">
        <li style="margin-bottom: 8px;">Pembayaran telah diterima dan sedang diverifikasi</li>
        <li style="margin-bottom: 8px;">Bukti pembayaran akan dikirim ke email Anda</li>
        <li style="margin-bottom: 8px;">Jika ada pertanyaan, hubungi pengelola kos</li>
        <li>Status pembayaran dapat dilihat di dashboard</li>
      </ul>
    </div>
  </div>
@endsection