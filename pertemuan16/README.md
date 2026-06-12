# Pertemuan 16 - Aplikasi CRUD Akademik (PHP + MySQL)

Sistem informasi akademik dengan autentikasi login dan manajemen data mahasiswa, dosen, mata kuliah, dan jadwal.

## Struktur Folder

```
crud-mahasiswa/
├── koneksi.php      -- Koneksi database MySQL
├── login.php        -- Halaman login
├── logout.php       -- Proses logout
├── index.php        -- Dashboard utama (Bootstrap 5)
├── api.php          -- RESTful API backend
├── script.js        -- Frontend JavaScript (Fetch API)
└── database.sql     -- Skema database + data dummy
```

## Fitur

- Login dengan session + bcrypt
- CRUD Mahasiswa (NIM, nama, jurusan, email)
- CRUD Dosen (nama, alamat)
- CRUD Mata Kuliah (matkul, SKS)
- CRUD Jadwal (relasi dosen & matkul)
- Single Page Application tanpa reload
- Bootstrap 5 UI dengan tab dan modal

## Cara Menjalankan

1. Jalankan XAMPP (Apache + MySQL)
2. Import `crud-mahasiswa/database.sql` ke phpMyAdmin
3. Akses `http://localhost/Tugas%20Pemograman%20Web%201/pertemuan16/crud-mahasiswa/login.php`
4. Login: `admin` / `admin`

## Teknologi

- PHP 8+
- MySQL
- Bootstrap 5.3
- JavaScript Fetch API
