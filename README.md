# antrianQ

Sistem antrean digital untuk toko fisik (barbershop, kafe, klinik, apotek, ritel). Pelanggan mengambil nomor sendiri, staf memanggil dari panel loket, pemilik memantau laporan harian.

Dibangun dengan Laravel 13, Tailwind CSS 4, Alpine.js, dan Reverb (WebSocket).

## Fitur

**Pelanggan (tanpa login)**

- Kiosk pengambilan nomor per kategori layanan (`A-001`, `B-001`).
- Tiket berisi estimasi waktu tunggu dan sisa antrean di depan, diperbarui otomatis.
- Layar TV ruang tunggu: nomor yang dipanggil, riwayat panggilan, dan daftar antrean berikutnya.

**Operator loket**

- Panggil berikutnya, panggil ulang, lewati, dan selesaikan layanan.
- Daftar nomor yang dilewati bisa dipanggil kembali.
- Hanya memproses antrean sesuai kategori loket yang ditugaskan.

**Admin / pemilik**

- Dashboard harian: total pengunjung, rata-rata waktu tunggu, durasi layanan, tingkat dilewati, grafik jam sibuk, komposisi status.
- Manajemen staf (tambah, ubah, hapus, atur peran dan loket).
- Manajemen loket (tambah, buka/tutup, hapus).
- Export CSV antrean hari ini dan reset antrean.

## Kebutuhan

- PHP 8.3+ dengan ekstensi standar Laravel
- Composer 2
- Node.js 20.19+ atau 22.12+
- SQLite (default) atau MySQL/PostgreSQL

## Instalasi

```bash
composer setup      # install dependency, buat .env, key, migrate, build asset
php artisan db:seed # isi akun contoh + 2 loket
php artisan serve
```

Manual, kalau tidak memakai `composer setup`:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # Windows: type nul > database\database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Untuk pengembangan (server + queue worker + log + Vite sekaligus):

```bash
composer dev
```

Realtime memakai Reverb. Jalankan di terminal terpisah:

```bash
php artisan reverb:start
```

Tanpa Reverb aplikasi tetap jalan: halaman tiket, layar TV, dan panel operator otomatis fallback ke polling (10–15 detik).

## Akun contoh

Dibuat oleh `php artisan db:seed`:

| Peran | Email | Password | Loket |
|---|---|---|---|
| Admin | `admin@antrianq.test` | `password` | — |
| Operator | `operator@antrianq.test` | `password` | Kasir |

## Halaman

| URL | Akses | Keterangan |
|---|---|---|
| `/` | Publik | Kiosk ambil nomor |
| `/ticket/{queue}` | Publik | Tiket + status live |
| `/queue/display` | Publik | Layar TV ruang tunggu |
| `/login` | Publik | Login staf |
| `/operator/counter` | Operator | Panel panggilan loket |
| `/admin/dashboard` | Admin | Laporan harian |
| `/admin/counters` | Admin | Manajemen loket |
| `/admin/staff` | Admin | Manajemen staf |
| `/admin/export` | Admin | Download CSV hari ini |

## Konfigurasi

Kategori layanan dan estimasi durasi ada di `config/antrianq.php`:

```php
'categories' => [
    'A' => 'Kasir Utama',
    'B' => 'Konsultasi',
],
'avg_service_minutes' => 6,
```

Prefix di sini harus sama dengan `prefix` pada tabel `counters`. Menambah kategori berarti menambah entri di file ini.

Variabel `.env` penting:

| Variabel | Default | Keterangan |
|---|---|---|
| `DB_CONNECTION` | `sqlite` | Ganti ke `mysql` bila perlu |
| `BROADCAST_CONNECTION` | `reverb` | WebSocket untuk panggilan realtime |
| `QUEUE_CONNECTION` | `database` | Event broadcast diproses lewat queue |
| `REVERB_*` | nilai lokal | Host/port/key Reverb |

## Struktur singkat

```
app/
  Events/            QueueCreated, QueueCalled, QueueReset (broadcast)
  Http/Controllers/  QueueController (kiosk/tiket/display), OperatorController, AdminController, LoginController
  Models/            Queue (penomoran harian), Counter, User
config/antrianq.php  kategori layanan + estimasi durasi
resources/views/
  queue/             kiosk, ticket, display
  operator/          panel loket
  admin/             dashboard, counters, staff, _nav (sidebar shell)
  layouts/           app (base), admin (shell admin)
database/seeders/    akun & loket contoh
tests/Feature/       alur operator, nomor antrean, halaman
```

## Perilaku antrean

- Nomor berurutan per kategori (`prefix`), mulai dari 1 setiap hari.
- Nomor baru diterbitkan dalam transaksi dengan `lockForUpdate` agar tidak bentrok saat banyak pelanggan mengambil bersamaan.
- Estimasi tunggu = jumlah antrean menunggu di depan × `avg_service_minutes`.
- Semua laporan dan nomor dibatasi ke tanggal berjalan; reset harian tidak otomatis, gunakan tombol **Reset Antrean** di dashboard.

## Testing

```bash
php artisan test
```

## Lisensi

MIT.
