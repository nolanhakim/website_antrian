# Product Requirements Document (PRD): antrianQ

## 1. Overview & Project Goals

### 1.1. Introduction

antrianQ adalah aplikasi web sistem antrean berbasis **Laravel** yang dirancang khusus untuk toko-toko fisik (seperti *barbershop*, kafe, klinik kecantikan, apotek, dan *tenant* ritel). Sistem ini bertujuan untuk menghilangkan antrean fisik yang menumpuk di depan kasir/meja layanan, memberikan kenyamanan bagi pelanggan melalui pemantauan *real-time* via ponsel, dan membantu pemilik toko melaktur metrik operasional harian.

### 1.2. Target Audience

* **Pelanggan Toko:** Pengunjung yang ingin mengambil nomor antrean dan memantau status antrean tanpa harus berdiri terus-menerus.

* **Kasir / Staf Toko (Operator):** Pegawai yang melayani pelanggan dan mengontrol pemanggilan nomor antrean.

* **Pemilik Toko (Admin/Owner):** Pemilik usaha yang membutuhkan rekapitulasi data kunjungan dan kinerja pelayanan toko.

## 2. User Roles & Permissions

1. **Customer (Publik):** Mengambil tiket antrean dan melihat halaman status secara *live*. Tidak memerlukan akun/login.

2. **Operator / Staff:** Memiliki akses ke panel loket untuk memanggil, memanggil ulang, atau melewati nomor antrean.

3. **Admin / Owner:** Memiliki akses penuh ke panel manajemen staff, pengaturan toko, dan laporan analitik/statistik.

## 3. Core Features & Functional Requirements

### 3.1. Modul Pelanggan (Customer Kiosk & Mobile View)

* **Pengambilan Tiket Mandiri:**

  * Pelanggan dapat memindai QR Code di toko menggunakan kamera HP untuk membuka halaman web antrean.

  * Pelanggan memasukkan nama (opsional) atau langsung klik tombol "Ambil Nomor Antrean".

  * Sistem mengeluarkan nomor antrean unik berdasarkan kategori layanan (Contoh: `A-001`, `B-005`).

* **Halaman Status Antrean Live:**

  * Menampilkan nomor antrean yang sedang aktif dipanggil saat ini.

  * Menampilkan sisa orang di depan pelanggan.

  * Estimasi waktu tunggu (*waiting time*) berdasarkan rata-rata durasi layanan.

### 3.2. Modul Operator / Kasir (Counter Panel)

* **Dashboard Kontrol Panggilan:**

  * Tombol **Next**: Memanggil antrean berikutnya dalam antrean.

  * Tombol **Recall**: Memanggil ulang nomor yang sama jika pelanggan belum merespons.

  * Tombol **Skip**: Melewati nomor antrean jika pelanggan tidak hadir di tempat.

  * Tombol **Finish**: Menandai layanan selesai.

* **Multi-Loket / Multi-Kategori:**

  * Mendukung pembagian loket (misal: Loket 1 untuk Kasir Utama, Loket 2 untuk Konsultasi/Layanan Khusus).

### 3.3. Modul Layar Display Ruang Tunggu (TV Display)

* **Tampilan Layar Besar (Fullscreen):**

  * Nomor antrean aktif ditampilkan dengan ukuran font besar dan kontras tinggi.

  * Menunjukkan nomor loket/meja tujuan (Misal: *"Nomor A-012 ke Kasir 1"*).

* **Text-to-Speech (Suara Otomatis):**

  * Memanfaatkan *Web Speech API* browser untuk mengeluarkan suara audio otomatis saat tombol *Next* ditekan oleh kasir.

* **Area Informasi/Media:**

  * Menyediakan ruang untuk banner promo toko atau informasi produk di sisi layar TV.

### 3.4. Modul Admin / Pemilik Toko (Owner Dashboard)

* **Manajemen Staf:** Menambah, mengedit, atau menghapus akun operator/kasir yang bertugas di loket.

* **Laporan & Analitik:**

  * Total pengunjung per hari/bulan.

  * Grafik jam sibuk (*peak hours*).

  * Rata-rata waktu tunggu dan waktu pelayanan per pelanggan.

* **Reset Harian Otomatis:** Sistem mereset nomor antrean kembali ke `0` pada pergantian hari.

## 4. Non-Functional Requirements

* **Teknologi Backend:** Laravel 13 (PHP 8.3+) untuk kestabilan database dan kemudahan *deployment* lokal (*on-premise*).

* **Real-time Communication:** Menggunakan Laravel Reverb atau Pusher / WebSockets agar pembaruan nomor antrean di TV dan HP pelanggan terjadi seketika tanpa *refresh* halaman.

* **Offline/Local Network Compatibility:** Sistem dapat di-hosting secara lokal di Mini PC toko via jaringan Wi-Fi/LAN lokal jika koneksi internet toko bermasalah.

* **Responsive Design:** Antarmuka pelanggan dan kasir harus optimal diakses melalui layar ponsel (*mobile-first*).

## 5. Milestone & Rencana Pengembangan (Phase 1)

1. **Fase 1 (MVP):** Database migration, autentikasi staff, halaman ambil tiket via QR, dan panel tombol kasir dasar.

2. **Fase 2:** Integrasi *real-time* WebSocket dan halaman TV Display dengan suara otomatis (*Text-to-Speech*).

3. **Fase 3:** Dashboard laporan analitik dan pengujian *deployment* lokal di perangkat server toko.