# Sistem Manajemen Kos - Pondok Hasanah

## Tentang Project

Aplikasi web manajemen kos yang memungkinkan pengelola untuk mengelola kamar, penghuni, dan pembayaran. Dilengkapi dengan integrasi payment gateway Midtrans untuk pembayaran sewa.

## Fitur Utama

### Admin (Pengelola)
- Dashboard dengan statistik
- Manajemen kamar dan fasilitas
- Manajemen penghuni
- Upload multiple foto kamar
- Pengumuman untuk penghuni
- Laporan pembayaran

### Penghuni
- Dashboard personal
- Pembayaran sewa via Midtrans (QRIS, GoPay, Bank Transfer)
- Melihat informasi kamar
- Membaca pengumuman
- Update profil

## Instalasi

### Requirements
- PHP 8.2+
- MySQL/MariaDB
- Composer
- Node.js & NPM

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/evyugantara/kp.git
   cd kp
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database di `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kosan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Konfigurasi Midtrans di `.env`**
   ```env
   MIDTRANS_MERCHANT_ID=your_merchant_id
   MIDTRANS_SERVER_KEY=your_server_key
   MIDTRANS_CLIENT_KEY=your_client_key
   MIDTRANS_IS_PRODUCTION=false
   ```

6. **Run migrations dan seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Compile assets**
   ```bash
   npm run dev
   ```

8. **Start development server**
   ```bash
   php artisan serve
   ```

## Konfigurasi Midtrans

### 1. Daftar Akun Midtrans
1. Daftar di [https://dashboard.sandbox.midtrans.com/](https://dashboard.sandbox.midtrans.com/)
2. Buat merchant baru
3. Catat Merchant ID, Server Key, dan Client Key

### 2. Konfigurasi Callback URL

Setelah aplikasi berjalan, daftarkan URL berikut di Midtrans Dashboard:

#### Di Midtrans Dashboard:
1. Login ke [Midtrans Dashboard](https://dashboard.sandbox.midtrans.com/)
2. Pilih merchant Anda
3. Masuk ke **Settings** → **Configuration**
4. Set URL berikut:

**Untuk Development (localhost):**
```
Payment Notification URL: http://127.0.0.1:8000/api/payments/midtrans/notify
Finish Redirect URL: http://127.0.0.1:8000/penghuni/pembayaran/success
Unfinish Redirect URL: http://127.0.0.1:8000/penghuni/pembayaran
Error Redirect URL: http://127.0.0.1:8000/penghuni/pembayaran
```

**Untuk Production:**
```
Payment Notification URL: https://yourdomain.com/api/payments/midtrans/notify
Finish Redirect URL: https://yourdomain.com/penghuni/pembayaran/success
Unfinish Redirect URL: https://yourdomain.com/penghuni/pembayaran
Error Redirect URL: https://yourdomain.com/penghuni/pembayaran
```

### 3. Testing dengan ngrok (Development)

Jika ingin test callback yang sebenarnya di development:

1. **Install ngrok**
   ```bash
   # Download dari https://ngrok.com/
   ngrok http 8000
   ```

2. **Update URL di Midtrans Dashboard**
   ```
   Payment Notification URL: https://your-ngrok-url.ngrok.io/api/payments/midtrans/notify
   Finish Redirect URL: https://your-ngrok-url.ngrok.io/penghuni/pembayaran/success
   ```

### 4. Verification

Test callback dengan:
```bash
curl -X POST http://127.0.0.1:8000/api/payments/midtrans/notify \
  -H "Content-Type: application/json" \
  -d '{"order_id":"test","transaction_status":"pending"}'
```

Response seharusnya: `{"message":"Invalid signature"}` (expected karena tidak ada signature yang valid)

## Login Credentials

**Admin/Pengelola:**
- Email: `admin@pondokhasanah.test`
- Password: `admin12345`

## Flow Pembayaran

1. **Penghuni** masuk ke dashboard
2. Klik **"Bayar Sekarang"** di card jadwal pembayaran
3. Sistem generate payment record dengan order_id unik
4. Midtrans Snap popup muncul dengan pilihan metode pembayaran
5. User pilih metode (QRIS/GoPay/Bank Transfer) dan bayar
6. Midtrans kirim notification ke callback URL
7. System update status pembayaran otomatis
8. User diarahkan ke halaman sukses

## File Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/           # Controller untuk admin
│   │   ├── Api/            # API controllers (Midtrans callback)
│   │   └── Tenant/         # Controller untuk penghuni
│   ├── Models/             # Eloquent models
│   └── Services/           # Service classes (MidtransService)
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/           # Database seeders
├── resources/views/
│   ├── admin/             # Views untuk admin
│   └── tenant/            # Views untuk penghuni
└── routes/
    ├── web.php            # Web routes
    └── api.php            # API routes (callback)
```

## Troubleshooting

### Payment Issues

1. **Signature Invalid**
   - Pastikan Server Key di `.env` benar
   - Check log di `storage/logs/laravel.log`

2. **Snap Token Null**
   - Periksa Client Key dan Server Key
   - Pastikan Midtrans service key valid

3. **Callback Not Working**
   - Pastikan URL callback accessible dari internet
   - Check firewall/port settings
   - Verify URL di Midtrans dashboard

### Database Issues

1. **Migration Failed**
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Permission Issues**
   ```bash
   chmod -R 775 storage/
   chmod -R 775 bootstrap/cache/
   ```

## API Endpoints

### Public Routes
- `GET /` - Homepage dengan daftar kamar
- `GET /kamar/{kode}` - Detail kamar

### Admin Routes (Prefix: `/admin`)
- `GET /dashboard` - Dashboard admin
- `RESOURCE /rooms` - CRUD kamar
- `RESOURCE /penghuni` - CRUD penghuni
- `RESOURCE /pengumuman` - CRUD pengumuman

### Tenant Routes (Prefix: `/penghuni`)
- `GET /dashboard` - Dashboard penghuni
- `GET /pembayaran` - Halaman pembayaran
- `GET /pembayaran/success` - Halaman sukses pembayaran
- `GET /pengumuman` - Daftar pengumuman

### API Routes
- `POST /api/payments/midtrans/notify` - Midtrans callback

## Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/nama-fitur`)
3. Commit changes (`git commit -m 'Add: nama fitur'`)
4. Push to branch (`git push origin feature/nama-fitur`)
5. Create Pull Request

## License

MIT License. See LICENSE file for details.

## Support

Jika ada pertanyaan atau issues, silakan buat issue di GitHub repository.
