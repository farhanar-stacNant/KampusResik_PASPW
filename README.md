# KampusResik
  KampusResik adalah platform sistem pelaporan kebersihan lingkungan kampus  
  yang dikembangkan sebagai Proyek Pemrograman Tugas Akhir Semester oleh kelompok Kami  
Sistem ini dirancang untuk mempermudah civitas akademika juga pengguna dalam melaporkan  
permasalahan sampah agar dapat segera ditangani oleh tim kebersihan setempat, Terutama Kampus.

<img width="1862" height="1126" alt="image" src="https://github.com/user-attachments/assets/216590e2-462f-4840-8ea4-b4b785974172" />

#  Latar Belakang
Permasalahan sampah di lingkungan kampus masih menjadi isu yang perlu mendapat perhatian.  
Banyak mahasiswa yang belum memiliki kesadaran penuh untuk membuang sampah pada tempatnya sehingga mengakibatkan lingkungan kampus menjadi kurang bersih dan nyaman. Selain itu, pengelolaan laporan mengenai tumpukan sampah masih dilakukan secara manual sehingga sering terjadi keterlambatan dalam pembersihan dan penanganan.  
Perkembangan Teknologi Informasi dapat dimanfaatkan untuk membantu proses pelaporan dan pengelolaan sampah secara lebih efektif. Oleh karena itu, dibuatlah Aplikasi Berbasis Web bernama Kampus Resik dengan tujuan untuk memudahkan mahasiswa dalam melaporkan lokasi sampah, memantau status laporan, serta membantu petugas kebersihan dalam menangani laporan secara terstruktur. Dengan adanya sistem ini diharapkan tercipta lingkungan kampus yang lebih bersih, sehat, dan nyaman bagi seluruh civitas akademika.

## Arsitektur Sistem

Sistem ini terdiri dari dua bagian utama yang saling berinteraksi:

1. **Frontend**: Dibangun menggunakan PHP Native dan Bootstrap 5. Berfungsi sebagai antarmuka pengguna untuk masyarakat kampus, yang dapat Diakses semua Pengguna.
2. **Backend**: Dibangun menggunakan Laravel 12. Berfungsi sebagai pusat pengelolaan data, logika yang dijalankan, dan manajemen status laporan sesuai database.

## Alur Komunikasi Data

* **Frontend ke Backend**: Frontend melakukan komunikasi dengan Backend melalui *request* HTTP (menggunakan fungsi `api_get_contents` di PHP) dengan menyertakan `X-Api-Token` untuk autentikasi sistem.
* **Keamanan**: Sistem menggunakan *Session Token* untuk memverifikasi hak akses pada halaman **Admin** dan **Petugas**. Pengguna biasa dapat mengirimkan laporan tanpa memerlukan otentikasi khusus.

## Panduan Instalasi & Deployment

Untuk menjalankan sistem ini pada lingkungan *shared hosting*, ikuti langkah berikut:

### 1. Database (Backend)

1. Buat database baru melalui menu **MySQL Databases** di cPanel Anda.
2. Buka file `DB_Kampusresik.sql` di komputer Anda.
3. **Penting:** Hapus baris perintah `CREATE DATABASE ...` dan `USE ...` di bagian awal file SQL tersebut agar tidak terjadi *error* saat impor.
4. Import file SQL tersebut ke database yang telah dibuat melalui phpMyAdmin.
5. Sesuaikan file `.env` di folder utama Backend Laravel dengan kredensial database yang baru dibuat:
```.env
DB_HOST=db_kampusresik
DB_DATABASE=db_domain
DB_USERNAME=username_domain
DB_PASSWORD=password_domain

```

### 2. Konfigurasi Frontend

1. Upload folder Frontend ke direktori `htdocs` di server Anda.
2. Buka file `includes/api-config.php`.
3. Ubah `API_BASE_URL` agar mengarah ke alamat *endpoint* API Backend Laravel Anda:
```php
define('API_BASE_URL', 'https://nama_DomainBaru/api');

```

## Struktur Proyek

```Domain Hosting
/htdocs
├── Backend/           # Source code Laravel (API,Logic)
└── FrontEnd/          # Source code PHP Native & Bootstrap
    ├── Assets/        # Gambar, CSS, JS
```

## Kontribusi

Proyek ini dikembangkan oleh kelompok 3 dengan Nama KampusResik,  
Berikut Nama Anggota dari Anggota Kelompok kami
| Nama Anggota | NIM |
| :--- | :--- |
| Erika Salsabila Intania | 24102009 |
| Moh Farhan Ali | 24102016 |
| Viqro Tunada Hanunnisa | 24102022 |
| Fera Adelia | 24102032 |

## Catatan
  Sedemikianlah Kode Diatas Dibuat sebagai salah satu Project Kami  
  Sekian, Terimakasih karena sudah mengunjungi Repository ini
