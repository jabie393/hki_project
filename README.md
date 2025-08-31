````markdown
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

---

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

## 📷 Tampilan Sistem

*(Tambahkan screenshot aplikasi di sini agar lebih menarik. Contoh: halaman login, dashboard user, dashboard admin.)*

Contoh placeholder:

![Login Page](screenshots/login.png)
![Dashboard Admin](screenshots/admin.png)

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
Hak cipta © 2025 – Mochamad Fahd Wahyu Rajaby.

---

### 📌 Catatan

Jika ada error saat konfigurasi database, cek kembali:

* Nama database di `phpMyAdmin`
* Username & password MySQL di `config.php`
* Pastikan modul `mysqli` aktif di XAMPP/PHP

```
