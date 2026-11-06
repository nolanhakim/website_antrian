# System Design Document (design.md): antrianQ

## 1. Arsitektur Sistem & Teknologi (System Architecture & Stack)

antrianQ dirancang dengan arsitektur **Monolithic Modular** menggunakan framework **Laravel**, yang sangat optimal untuk kemudahan *deployment* lokal (*on-premise* di toko fisik) maupun berbasis *cloud*.

* **Backend & Framework:** Laravel 13 (PHP 8.3+) dengan arsitektur MVC (Model-View-Controller) dan RESTful API controllers.
* **Frontend:** Blade Templating dengan Tailwind CSS v4 (via `@tailwindcss/vite`) dan Alpine.js. Tanpa framework SPA (React/Vue) — aset di-bundle sekali lewat Vite lalu di-*serve* secara lokal, sehingga TV Display dan HP kasir memuat halaman dengan cepat dan stabil.
* **Real-Time Communication:** Laravel Reverb (atau Laravel WebSockets / Pusher) untuk sinkronisasi instan nomor antrean antara HP kasir, server, dan layar TV Display.
* **Audio Engine (Text-to-Speech):** Browser Web Speech API (`window.speechSynthesis`) untuk memicu suara pemanggilan otomatis pada TV Display tanpa memerlukan *audio server* eksternal.

## 2. Rancangan Skema Database (Database Schema Design)

Berikut adalah struktur tabel relasional yang dirancang untuk mendukung operasional sistem antrean toko:

### A. Tabel `users` (Pengguna & Staf Toko)
Menyimpan kredensial dan hak akses staf (Admin/Owner dan Operator/Kasir).
* `id` (BIGINT, Primary Key)
* `name` (VARCHAR) - Nama lengkap staf/kasir
* `email` (VARCHAR, Unique) - Email untuk login
* `password` (VARCHAR) - Hash password
* `role` (ENUM: `admin`, `operator`) - Peran pengguna dalam sistem
* `counter_id` (BIGINT, Foreign Key, Nullable) - Relasi ke loket tempat staf bertugas
* `timestamps`

### B. Tabel `counters` (Daftar Loket / Meja Layanan)
Menyimpan daftar loket fisik yang tersedia di toko.
* `id` (BIGINT, Primary Key)
* `name` (VARCHAR) - Nama loket (Contoh: "Loket 1", "Kasir Utama", "Kursi Barbershop 2")
* `is_active` (BOOLEAN) - Status loket aktif atau tutup
* `timestamps`

### C. Tabel `queues` (Data Transaksi & Status Antrean)
Menyimpan riwayat dan status antrean harian pelanggan.
* `id` (BIGINT, Primary Key)
* `queue_number` (VARCHAR) - Nomor antrean terformat (Contoh: `A-001`)
* `prefix` (VARCHAR, Nullable) - Huruf depan kategori antrean (Contoh: `A`, `B`)
* `number` (INTEGER) - Angka urut murni (Contoh: `1`, `2`)
* `counter_id` (BIGINT, Foreign Key, Nullable) - Loket yang memanggil antrean ini
* `status` (ENUM: `waiting`, `called`, `processing`, `completed`, `skipped`)
* `called_at` (TIMESTAMP, Nullable) - Waktu saat nomor dipanggil oleh operator
* `completed_at` (TIMESTAMP, Nullable) - Waktu saat layanan selesai
* `timestamps`

## 3. Komponen Antarmuka & Rute (UI Routes & Components)

Aplikasi dibagi menjadi 4 halaman utama berdasarkan hak akses dan fungsi:

1. **Kiosk / Customer Entry (`/queue/kiosk` atau `/`):**
   * Halaman publik untuk pelanggan mengambil nomor tiket mandiri (bisa diakses via scan QR Code dari HP atau layar sentuh toko).
2. **TV Display (`/queue/display`):**
   * Halaman *fullscreen* untuk ruang tunggu. Menampilkan nomor aktif, loket tujuan, dan memicu *audio speech synthesis* secara otomatis saat ada event panggilan baru.
3. **Operator Dashboard (`/operator/counter`):**
   * Panel kontrol kasir/staf yang berisi tombol **Next** (Panggil Berikutnya), **Recall** (Panggil Ulang), **Skip** (Lewati), dan **Finish** (Selesai).
4. **Admin Dashboard (`/admin/dashboard`):**
   * Panel manajemen untuk melihat rekapitulasi laporan harian, statistik jam sibuk, dan pengaturan staf/loket.

## 4. Desain Fitur Khusus & Pertimbangan Teknis

* **Local Network Resilience:** Karena toko fisik sering mengalami kendala internet, seluruh aset (CSS/JS) di-serve secara lokal tanpa ketergantungan CDN eksternal yang masif, memungkinkan sistem berjalan di atas router lokal (*Local Wi-Fi/LAN*).
* **Auto Reset Scheduler:** Menggunakan Laravel Task Scheduling (`Cron Job` / `Schedule:run`) untuk membersihkan atau mengarsipkan tabel `queues` secara otomatis pada pukul 00:00 setiap harinya.