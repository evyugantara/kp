# Setup Midtrans Payment Gateway

## Quick Setup Guide

### 1. Buat Akun Midtrans Sandbox
1. Kunjungi [Midtrans Dashboard Sandbox](https://dashboard.sandbox.midtrans.com/)
2. Register dengan email valid
3. Verifikasi email dan login

### 2. Dapatkan Credentials
Setelah login, dapatkan credentials di dashboard:

```
Merchant ID: G747223252 (contoh)
Server Key: SB-Mid-server-xxxxxxxxx
Client Key: SB-Mid-client-xxxxxxxxx
```

### 3. Konfigurasi .env
Update file `.env` dengan credentials Midtrans:

```env
MIDTRANS_MERCHANT_ID=your_merchant_id
MIDTRANS_SERVER_KEY=your_server_key  
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

### 4. Setting URL di Midtrans Dashboard

#### Path di Dashboard:
`Settings` → `Configuration` → `Notification`

#### URL yang harus diset:

**Development (localhost):**
```
Payment Notification URL: 
http://127.0.0.1:8000/api/payments/midtrans/notify

Finish Redirect URL: 
http://127.0.0.1:8000/penghuni/pembayaran/success

Unfinish Redirect URL: 
http://127.0.0.1:8000/penghuni/pembayaran

Error Redirect URL: 
http://127.0.0.1:8000/penghuni/pembayaran
```

**Production:**
```
Payment Notification URL: 
https://yourdomain.com/api/payments/midtrans/notify

Finish Redirect URL: 
https://yourdomain.com/penghuni/pembayaran/success

Unfinish Redirect URL: 
https://yourdomain.com/penghuni/pembayaran

Error Redirect URL: 
https://yourdomain.com/penghuni/pembayaran
```

### 5. Testing dengan ngrok (Optional)

Jika ingin test callback notification yang real:

```bash
# Install ngrok dari https://ngrok.com/
ngrok http 8000
```

Gunakan URL ngrok untuk notification URL di Midtrans dashboard:
```
https://abc123.ngrok.io/api/payments/midtrans/notify
```

## Payment Flow

### User Flow:
1. User login sebagai penghuni
2. Masuk ke Dashboard → Klik "Bayar Sekarang"
3. Sistem create payment record dengan order_id unik
4. Midtrans Snap popup muncul
5. User pilih metode pembayaran (QRIS/GoPay/Bank Transfer)
6. User selesaikan pembayaran
7. Midtrans kirim notification ke callback URL
8. Sistem update status pembayaran otomatis
9. User redirect ke halaman sukses

### Technical Flow:
1. `PaymentController@index` - Generate payment & snap token
2. Frontend call Midtrans Snap API
3. User pay via Midtrans
4. Midtrans POST to `/api/payments/midtrans/notify`
5. `MidtransWebhookController@notify` - Update payment status
6. User redirect to success page

## Callback Details

### Endpoint: 
`POST /api/payments/midtrans/notify`

### Security:
- Signature verification menggunakan SHA512
- Hanya request dengan signature valid yang diproses

### Status Mapping:
```php
'capture', 'settlement' => 'paid'
'pending'              => 'pending'  
'deny', 'cancel'       => 'cancel'
'expire'               => 'expire'
default                => 'failed'
```

### Logging:
Semua callback activity tercatat di `storage/logs/laravel.log`:
- Incoming notifications
- Signature verification results  
- Payment status updates
- Errors

## Troubleshooting

### 1. Snap Token Null/Error
**Kemungkinan penyebab:**
- Server Key salah
- Client Key salah  
- Midtrans service down
- Network issue

**Solusi:**
- Verify credentials di `.env`
- Check `storage/logs/laravel.log`
- Test credentials dengan `curl`

### 2. Callback Tidak Berfungsi
**Kemungkinan penyebab:**
- URL tidak accessible dari internet
- Signature verification failed
- Database connection error

**Solusi:**
- Pastikan URL public accessible
- Test dengan ngrok
- Check server logs

### 3. Payment Status Tidak Update
**Kemungkinan penyebab:**
- Callback URL salah
- Signature mismatch
- Database error

**Solusi:**
- Verify URL di Midtrans dashboard
- Check signature calculation
- Monitor database changes

## Testing Commands

### Test Callback Endpoint:
```bash
curl -X POST http://127.0.0.1:8000/api/payments/midtrans/notify \
  -H "Content-Type: application/json" \
  -d '{"order_id":"test","transaction_status":"pending"}'
```

Expected response: `{"message":"Invalid signature"}` (karena tidak ada signature)

### Check Routes:
```bash
php artisan route:list --path=api
```

### Monitor Logs:
```bash
tail -f storage/logs/laravel.log
```

## Production Checklist

- [ ] Update `.env` dengan production credentials
- [ ] Set `MIDTRANS_IS_PRODUCTION=true`
- [ ] Update notification URL di Midtrans dashboard
- [ ] Test pembayaran dengan amount kecil
- [ ] Monitor logs untuk errors
- [ ] Setup SSL certificate (HTTPS required)

## Sandbox Test Cards

Midtrans menyediakan test cards untuk development:

**Success Payment:**
- Card: 4811 1111 1111 1114
- CVV: 123  
- Exp: Any future date

**Failed Payment:**
- Card: 4911 1111 1111 1113
- CVV: 123
- Exp: Any future date

**Untuk QRIS/GoPay:** Gunakan Midtrans simulator di dashboard.