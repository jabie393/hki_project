# 📑 Sistem Pendaftaran Hak Cipta - LPPM UNIRA Malang

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Last Commit](https://img.shields.io/github/last-commit/jabie393/hki_project)](https://github.com/jabie393/hki_project/commits/main)
[![Issues](https://img.shields.io/github/issues/jabie393/hki_project)](https://github.com/jabie393/hki_project/issues)
[![Stars](https://img.shields.io/github/stars/jabie393/hki_project?style=social)](https://github.com/jabie393/hki_project/stargazers)

Sistem ini dibangun untuk mempermudah proses **pendaftaran Hak Cipta** di LPPM UNIRA Malang.  
Dilengkapi dengan fitur **akun pengguna**, **dashboard pendaftaran**, **unggah dokumen**, dan **panel admin** untuk mengelola data HKI.

---

## 🚀 Fitur Utama
- 🔑 Registrasi & Login User  
- 📝 Formulir pendaftaran HKI (Hak Cipta)  
- 📂 Upload dokumen pendukung  
- 📊 Dashboard user (status pengajuan, batalkan pengajuan)  
- 🛠️ Panel admin (persetujuan/penolakan pendaftaran, kelola berkas, hapus data)  
- 📢 Carousel pengumuman di beranda  

---

## 📋 Persyaratan
Sebelum menjalankan sistem ini, pastikan sudah terpasang:
- [XAMPP](https://www.apachefriends.org/) (PHP ≥ 7.4, MySQL ≥ 5.7)
- Git (opsional untuk clone repo)
- Web browser modern (Chrome, Edge, Firefox)

## ⚙️ Instalasi & Konfigurasi

### 1. Clone Repository
```bash
git clone https://github.com/jabie393/hki_project.git
````
Atau download langsung file **ZIP** lalu extract ke folder `htdocs` (jika menggunakan XAMPP).

---

### 2. Buat Database di phpMyAdmin

* Buka [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
* Buat database baru, misalnya:

  ```bash
  hak_cipta
  ```

---

### 3. Import Database

* Masuk ke database `hak_cipta`
* Klik tab **Import**
* Pilih file database:

  ```bash
  database/hak_cipta.sql
  ```
* Klik **Go**

---

### 4. Konfigurasi Koneksi Database

Edit file:

```bash
config/config.php
```

Sesuaikan dengan setting lokal:

```php
<?php
$host     = "localhost";
$user     = "root";
$password = "";
$dbname   = "hak_cipta";
?>
```

---

### 5. Jalankan Sistem

Buka di browser:

```bash
http://localhost/nama-repo/
```

---

## 🔑 Akun Admin

Gunakan akun berikut untuk login sebagai admin:

* **Email**: `admin@gmail.com`
* **Password**: `adminlppm`

---

## ⏰ Cron Job (Pembersihan Otomatis)

Sistem ini memiliki fitur **pembersihan otomatis** untuk menghapus pendaftaran yang **Ditolak** lebih dari 7 hari.  
Anda perlu menambahkan cron job agar proses ini berjalan rutin.

### 1. Menggunakan `wget` (Direkomendasikan)

Jika server mendukung cron job, tambahkan baris berikut di **crontab**:

```bash
0 0 * * * wget -qO- "https://domain-anda.com/services/cron_delete.php?key=hapus7hari"
````

📝 **Penjelasan:**

* `0 0 * * *` → Cron akan berjalan setiap hari jam **00:00**
* `wget -qO-` → Mengakses script via HTTP tanpa output ke layar
* `?key=hapus7hari` → Proteksi agar hanya cron job yang bisa menjalankan script ini

### 2. Menggunakan `curl`

Alternatif jika `wget` tidak tersedia:

```bash
0 0 * * * curl -s "https://domain-anda.com/services/cron_delete.php?key=hapus7hari"
```

### 3. Jalankan Manual untuk Testing

Jika ingin mencoba secara manual tanpa menunggu cron:

```bash
wget -qO- "https://domain-anda.com/services/cron_delete.php?key=hapus7hari"
```

Anda akan melihat log pembersihan langsung di terminal.

> ⚠️ **Catatan Keamanan:**
> Pastikan `key=hapus7hari` diganti dengan secret key unik agar tidak bisa diakses sembarang orang.

---

## 🛠️ Konfigurasi .htaccess (Development vs Production)

Proyek ini menggunakan dua konfigurasi `.htaccess` terpisah agar pengembangan lebih mudah dan aman:

- **`.htaccess.development`** → digunakan saat menjalankan sistem di **localhost/XAMPP**
  - Tidak memaksa HTTPS (tidak ada HSTS)
  - Tidak ada Content Security Policy (CSP) ketat
  - Memudahkan debugging dan testing tanpa error browser
  - **Isi minimal:**
    ```apache
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^([^/]+)$ $1.php [L]

    RewriteCond %{QUERY_STRING} ^i=1$
    RewriteRule ^(.*)$ /$1? [R=301,L]
    ```

- **`.htaccess.production`** → digunakan saat deploy ke server/hosting
  - Mengaktifkan **CSP** untuk mencegah XSS
  - Menambahkan **HSTS** agar semua koneksi pakai HTTPS
  - Menambahkan **X-Frame-Options, Referrer-Policy, X-Content-Type-Options**
  - Menghilangkan informasi server (X-Powered-By)

### 📌 Cara Menggunakan
1. **Saat develop di localhost:**  
   - Rename file `.htaccess.development` → `.htaccess`
   - Jalankan sistem seperti biasa di `http://localhost/`

2. **Saat deploy ke hosting:**  
   - Rename file `.htaccess.production` → `.htaccess`
   - Pastikan domain sudah punya SSL/HTTPS aktif
   - Cek browser console — jika ada error CSP, tambahkan domain CDN/API yang dibutuhkan ke file `.htaccess.production`

### ➕ Menambahkan CDN atau API Baru ke CSP
Jika Anda menambahkan library baru (misalnya Bootstrap dari CDN, Font Awesome, Tailwind, atau API eksternal), Anda harus menambahkan domainnya ke direktif CSP di `.htaccess.production`.

Contoh:
```apache
Header always set Content-Security-Policy "default-src 'self'; \
    script-src 'self' https://cdn.jsdelivr.net https://cdn.tailwindcss.com; \
    style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; \
    connect-src 'self' https://api.countrystatecity.in;"
```

📌 **Panduan Cepat:**
- **script-src** → untuk file JS (CDN seperti Bootstrap, Tailwind, jQuery)
- **style-src** → untuk CSS (Bootstrap, Font Awesome, Google Fonts)
- **font-src** → untuk font (Google Fonts, CDN font)
- **img-src** → untuk gambar dari luar (contoh: https://flagcdn.com)
- **connect-src** → untuk API atau AJAX request (misal: API negara, kodepos)

> 💡 **Tips:** Buka browser DevTools → Console → cari pesan error CSP → domain yang ditolak akan ditampilkan → tambahkan ke direktif yang sesuai.

---

## 📷 Tampilan Sistem

Berikut adalah beberapa tampilan dari sistem pendaftaran Hak Cipta:

### 🏠 1. Halaman Beranda
<p align="center">
  <img src="https://github.com/user-attachments/assets/c5f912f3-7635-4f26-8397-1239dd166797" alt="Halaman Beranda" width="800"/>
</p>

---

### ⚙️ 2. Dashboard Admin
<p align="center">
  <img src="https://github.com/user-attachments/assets/4bbc64f1-29d4-4ddf-b32d-a6e96064be4b" alt="Dashboard Admin" width="800"/>
</p>

---

### 👤 3. Dashboard User
<p align="center">
  <img src="https://github.com/user-attachments/assets/fec5fe4d-1255-43de-8ed3-6037fbf8130e" alt="Dashboard User" width="800"/>
</p>

---

## 🤝 Kontribusi

Jika ingin berkontribusi:

1. Fork repo ini
2. Buat branch baru

   ```bash
   git checkout -b fitur-baru
   ```
3. Commit perubahan

   ```bash
   git commit -m "Tambah fitur baru"
   ```
4. Push ke branch

   ```bash
   git push origin fitur-baru
   ```
5. Buat **Pull Request**

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan internal **LPPM UNIRA Malang**.
Hak cipta © 2025 – Mochammad Fahd Wahyu Rajaby.

---

### 📌 Catatan

Jika ada error saat konfigurasi database, cek kembali:

* Nama database di `phpMyAdmin`
* Username & password MySQL di `config.php`
* Pastikan modul `mysqli` aktif di XAMPP/PHP
