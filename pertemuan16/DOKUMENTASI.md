# LAPORAN DOKUMENTASI - Aplikasi CRUD Akademik

**Nama**: REVA ZAIDAN AZMI
**Nim**: 231011402702
**Kelas**: 05TPLE004

---

## Daftar Isi
1. [Overview Aplikasi](#overview-aplikasi)
2. [Struktur File](#struktur-file)
3. [Dokumentasi Backend](#dokumentasi-backend)
4. [Dokumentasi Frontend](#dokumentasi-frontend)
5. [Dokumentasi Database](#dokumentasi-database)
6. [Alur Kerja Aplikasi](#alur-kerja-aplikasi)
7. [Fitur-Fitur Utama](#fitur-fitur-utama)
8. [Penjelasan Code Penting](#penjelasan-code-penting)
9. [Statistik Code](#statistik-code)
10. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
11. [Testing Checklist](#testing-checklist)
12. [Kesimpulan](#kesimpulan)

---

## Overview Aplikasi

### Deskripsi
Aplikasi **CRUD Akademik** adalah sistem informasi akademik berbasis web dengan fitur login dan manajemen data:
- **Mahasiswa**: CRUD data mahasiswa (NIM, nama, jurusan, email)
- **Dosen**: CRUD data dosen (nama, alamat)
- **Mata Kuliah**: CRUD data matkul (matkul, SKS)
- **Jadwal**: CRUD jadwal kuliah (dosen, matkul, waktu, ruang)
- **Autentikasi**: Login system dengan session dan password hashing

### Arsitektur
```
Browser (HTML + JS)
     |
     v (Fetch API)
api.php (RESTful)
     |
     v (MySQLi)
MySQL Database
```

---

## Struktur File

```
crud-mahasiswa/
├── koneksi.php        -- Koneksi database MySQL
├── login.php          -- Halaman autentikasi user
├── logout.php         -- Proses logout
├── index.php          -- Dashboard utama dengan tab CRUD
├── api.php            -- RESTful API untuk semua entitas
├── script.js          -- Frontend JavaScript (Fetch API)
└── database.sql       -- Skema database + data dummy
```

---

## Dokumentasi Backend

### 1. koneksi.php (Koneksi Database)
```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_mahasiswa";
$conn = mysqli_connect($host, $user, $pass, $db);
```

**Fungsi**: Menghubungkan aplikasi PHP ke MySQL server via ekstensi MySQLi.

**Konfigurasi**:
- Host: localhost (XAMPP default)
- User: root
- Password: (kosong)
- Database: db_mahasiswa

---

### 2. login.php (Autentikasi)

**Fungsi**: Memverifikasi kredensial user menggunakan bcrypt.

```php
$query  = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    if (password_verify($password, $row['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $row['username'];
        header("Location: index.php");
        exit;
    }
}
```

**Alur Login**:
1. Cek apakah user sudah login (session)
2. Jika sudah, redirect ke index.php
3. Proses form submit:
   - Escape username untuk cegah SQL injection
   - Query ke tabel users
   - Verifikasi password dengan `password_verify()`
   - Set session jika cocok
   - Tampilkan error jika salah

---

### 3. logout.php (Logout)

```php
session_start();
$_SESSION = [];
session_unset();
session_destroy();
header("Location: login.php");
exit;
```

**Fungsi**: Menghancurkan session dan mengembalikan user ke halaman login.

---

### 4. index.php (Dashboard)

**Fungsi**: Halaman utama setelah login dengan 4 tab CRUD.

**Struktur Halaman**:
- Navbar dengan username + tombol logout
- Tab navigasi: Mahasiswa, Dosen, Matkul, Jadwal
- 4 tabel data (masing-masing di tab terpisah)
- 4 modal form (untuk tambah/edit data)

**Tab Mahasiswa**:
| Kolom | Keterangan |
|-------|-----------|
| No | Nomor urut |
| NIM | Nomor Induk Mahasiswa |
| Nama Lengkap | Nama mahasiswa |
| Jurusan | Jurusan mahasiswa |
| Email | Alamat email |
| Aksi | Tombol Edit & Hapus |

**Tab Dosen**:
| Kolom | Keterangan |
|-------|-----------|
| No | Nomor urut |
| Nama | Nama dosen |
| Alamat | Alamat dosen |
| Aksi | Tombol Edit & Hapus |

**Tab Matkul**:
| Kolom | Keterangan |
|-------|-----------|
| No | Nomor urut |
| Mata Kuliah | Nama mata kuliah |
| SKS | Jumlah SKS |
| Aksi | Tombol Edit & Hapus |

**Tab Jadwal**:
| Kolom | Keterangan |
|-------|-----------|
| No | Nomor urut |
| Dosen | Nama dosen (relasi) |
| Mata Kuliah | Nama matkul (relasi) |
| Waktu | Hari dan jam |
| Ruang | Ruang kuliah |
| Aksi | Tombol Edit & Hapus |

**Modal Form**:
- Setiap entitas punya modal sendiri
- Form Jadwal pakai select dinamis (dosen & matkul dari database)
- Input tersembunyi `id` untuk mode edit

---

### 5. api.php (RESTful API)

**Fungsi**: Menangani semua request CRUD via parameter `entity` dan `action`.

**Parameter**:
- `entity`: mahasiswa, dosen, matkul, jadwal
- `action`: list, get, save, delete, relasi (khusus jadwal)

**Struktur**:
```php
$entity = $_GET['entity'] ?? '';
$action = $_GET['action'] ?? '';

// Pola untuk setiap entitas:
// - list       -> SELECT semua data
// - get        -> SELECT satu data by id
// - save       -> INSERT (jika id kosong) atau UPDATE (jika id ada)
// - delete     -> DELETE by id
```

**Contoh Endpoint**:
| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `api.php?entity=mahasiswa&action=list` | Ambil semua mahasiswa |
| GET | `api.php?entity=mahasiswa&action=get&id=1` | Ambil satu mahasiswa |
| POST | `api.php?entity=mahasiswa&action=save` | Tambah/edit mahasiswa |
| POST | `api.php?entity=mahasiswa&action=delete` | Hapus mahasiswa |

**Jadwal JOIN Query**:
```php
$query = mysqli_query($conn, "
    SELECT jadwal.*, dosen.nama AS nama_dosen, matkul.matkul AS nama_matkul
    FROM jadwal
    LEFT JOIN dosen ON jadwal.id_dosen = dosen.id
    LEFT JOIN matkul ON jadwal.id_matkul = matkul.id
    ORDER BY jadwal.id DESC
");
```

**Action relasi** (khusus jadwal): Mengirim data dosen dan matkul untuk select option.

---

## Dokumentasi Frontend

### script.js (JavaScript)

**Fungsi**: Frontend controller untuk semua operasi CRUD via Fetch API.

**Struktur**:
```
script.js
├── modalMap (konfigurasi modal per entitas)
├── DOMContentLoaded (inisialisasi)
├── loadData(entity)
├── siapkanTambah(entity)
├── siapkanEdit(id, entity)
├── simpanData(event, entity)
├── hapusData(id, entity)
├── muatRelasiJadwal(callback)
└── Tab event listener
```

### 1. Modal Map (Baris 1-6)
```javascript
const modalMap = {
    mahasiswa: { modal: null, formId: 'formMahasiswa', titleId: 'modalTitleMahasiswa', tbodyId: 'tbody-mahasiswa' },
    dosen:     { modal: null, formId: 'formDosen',     titleId: 'modalTitleDosen',     tbodyId: 'tbody-dosen' },
    matkul:    { modal: null, formId: 'formMatkul',    titleId: 'modalTitleMatkul',    tbodyId: 'tbody-matkul' },
    jadwal:    { modal: null, formId: 'formJadwal',    titleId: 'modalTitleJadwal',    tbodyId: 'tbody-jadwal' }
};
```

**Fungsi**: Mapping konfigurasi setiap entitas ke ID element HTML.
**Kegunaan**: Kode reusable untuk 4 entitas tanpa duplikasi.

### 2. Inisialisasi (Baris 8-14)
```javascript
document.addEventListener('DOMContentLoaded', function () {
    for (const key in modalMap) {
        const el = document.getElementById('modal' + key.charAt(0).toUpperCase() + key.slice(1));
        if (el) modalMap[key].modal = new bootstrap.Modal(el);
    }
    loadData('mahasiswa');
});
```

**Fungsi**:
1. Inisialisasi semua Bootstrap Modal object
2. Load data mahasiswa secara default

### 3. Load Data (Baris 16-54)
```javascript
function loadData(entity) {
    fetch('api.php?entity=' + entity + '&action=list')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById(modalMap[entity].tbodyId);
            let html = '';
            if (data.length === 0) {
                html = '<tr><td colspan="10" class="text-center text-muted p-4">Belum ada data.</td></tr>';
            } else {
                data.forEach((item, index) => {
                    html += '<tr><td class="ps-3 align-middle">' + (index + 1) + '</td>';
                    // Render kolom berdasarkan entity
                    if (entity === 'mahasiswa') { ... }
                    else if (entity === 'dosen') { ... }
                    else if (entity === 'matkul') { ... }
                    else if (entity === 'jadwal') { ... }
                    html += '<td class="text-center align-middle">';
                    html += '<button class="btn btn-warning btn-sm me-1" onclick="siapkanEdit(' + item.id + ', \'' + entity + '\')">Edit</button>';
                    html += '<button class="btn btn-danger btn-sm" onclick="hapusData(' + item.id + ', \'' + entity + '\')">Hapus</button>';
                    html += '</td></tr>';
                });
            }
            tbody.innerHTML = html;
        });
}
```

**Alur**:
1. Fetch data dari API berdasarkan entity
2. Generate HTML tabel dengan kolom sesuai entity
3. Tambah tombol Edit & Hapus per baris
4. Inject HTML ke tbody

### 4. Siapkan Tambah (Baris 56-65)
```javascript
function siapkanTambah(entity) {
    const m = modalMap[entity];
    document.getElementById(m.titleId).innerText = 'Tambah ' + entity.charAt(0).toUpperCase() + entity.slice(1);
    document.getElementById(m.formId).reset();
    document.getElementById(entity + '_id').value = '';
    if (entity === 'jadwal') {
        muatRelasiJadwal();
    }
}
```

**Fungsi**: Reset form dan set title untuk mode tambah.

### 5. Siapkan Edit (Baris 67-99)
```javascript
function siapkanEdit(id, entity) {
    fetch('api.php?entity=' + entity + '&action=get&id=' + id)
        .then(res => res.json())
        .then(data => {
            document.getElementById(entity + '_id').value = data.id;
            // Isi form fields berdasarkan entity
            m.modal.show();
        });
}
```

**Fungsi**:
1. Fetch data by ID dari API
2. Isi form fields dengan data yang ada
3. Tampilkan modal

### 6. Simpan Data (Baris 101-121)
```javascript
function simpanData(event, entity) {
    event.preventDefault();
    const form = document.getElementById(modalMap[entity].formId);
    const formData = new FormData(form);
    fetch('api.php?entity=' + entity + '&action=save', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            alert('Data berhasil disimpan!');
            modalMap[entity].modal.hide();
            loadData(entity);
        } else {
            alert('Error: ' + res.message);
        }
    });
}
```

**Alur**:
1. Prevent default form submission
2. Collect data dari form (termasuk hidden id)
3. POST ke API
4. Jika sukses: tutup modal, reload tabel
5. Jika error: tampilkan pesan error

### 7. Hapus Data (Baris 123-143)
```javascript
function hapusData(id, entity) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        const formData = new FormData();
        formData.append('id', id);
        fetch('api.php?entity=' + entity + '&action=delete', {
            method: 'POST', body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                alert('Data berhasil dihapus!');
                loadData(entity);
            }
        });
    }
}
```

### 8. Muat Relasi Jadwal (Baris 145-162)
```javascript
function muatRelasiJadwal(callback) {
    fetch('api.php?entity=jadwal&action=relasi')
        .then(res => res.json())
        .then(data => {
            // Isi select dosen
            data.dosen.forEach(d => {
                selDosen.innerHTML += '<option value="' + d.id + '">' + d.nama + '</option>';
            });
            // Isi select matkul
            data.matkul.forEach(m => {
                selMatkul.innerHTML += '<option value="' + m.id + '">' + m.matkul + '</option>';
            });
            if (callback) callback();
        });
}
```

**Fungsi**: Fetch data dosen dan matkul untuk select dropdown di form jadwal.

### 9. Tab Event Listener (Baris 164-172)
```javascript
document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', function (e) {
        const id = e.target.id;
        if (id === 'tab-mahasiswa') loadData('mahasiswa');
        else if (id === 'tab-dosen') loadData('dosen');
        else if (id === 'tab-matkul') loadData('matkul');
        else if (id === 'tab-jadwal') loadData('jadwal');
    });
});
```

**Fungsi**: Load data setiap kali tab di-click.

---

## Dokumentasi Database

### ERD (Entity Relationship)
```
users     -- (id, username, password)
mahasiswa -- (id, nim, nama, jurusan, email)
dosen     -- (id, nama, alamat)
matkul    -- (id, matkul, sks)
jadwal    -- (id, id_dosen, id_matkul, waktu, ruang)

Relasi:
jadwal.id_dosen  -> dosen.id   (LEFT JOIN)
jadwal.id_matkul -> matkul.id  (LEFT JOIN)
```

### Tabel Users
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
```
- Menyimpan data login
- Password di-hash dengan bcrypt (60 karakter)

### Tabel Mahasiswa
```sql
CREATE TABLE mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);
```

### Tabel Dosen
```sql
CREATE TABLE dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL
);
```

### Tabel Matkul
```sql
CREATE TABLE matkul (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matkul VARCHAR(100) NOT NULL,
    sks INT NOT NULL
);
```

### Tabel Jadwal
```sql
CREATE TABLE jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_dosen INT NOT NULL,
    id_matkul INT NOT NULL,
    waktu VARCHAR(50) NOT NULL,
    ruang VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_dosen) REFERENCES dosen(id) ON DELETE CASCADE,
    FOREIGN KEY (id_matkul) REFERENCES matkul(id) ON DELETE CASCADE
);
```
- `id_dosen` dan `id_matkul` adalah foreign key
- `ON DELETE CASCADE`: jika dosen/matkul dihapus, jadwal terkait ikut terhapus

### Data Default
- 3 user (admin, dosen1, staff) -- password: admin
- 10 mahasiswa
- 4 dosen
- 8 mata kuliah
- 10 jadwal

---

## Alur Kerja Aplikasi

### Login Flow
```
1. User buka login.php
2. Cek session -> jika sudah login, redirect ke index.php
3. User input username & password
4. Submit form -> POST ke halaman yang sama
5. Query user dari database
6. Verifikasi password dengan password_verify()
7. Jika cocok: set session, redirect ke index.php
8. Jika salah: tampilkan error "Username atau password salah"
```

### CRUD Flow
```
User klik tab (Mahasiswa/Dosen/Matkul/Jadwal)
  -> Event shown.bs.tab trigger loadData(entity)
  -> Fetch GET api.php?entity=...&action=list
  -> Render JSON ke tabel HTML

User klik "Tambah"
  -> siapkanTambah(entity)
  -> Reset form, kosongkan hidden id
  -> Tampilkan modal

User klik "Edit"
  -> siapkanEdit(id, entity)
  -> Fetch GET api.php?entity=...&action=get&id=...
  -> Isi form dengan data
  -> Tampilkan modal

User submit form
  -> simpanData(event, entity)
  -> Prevent default
  -> Collect FormData (termasuk hidden id)
  -> POST ke api.php?entity=...&action=save
  -> Jika sukses: hide modal, reload data
  -> Jika error: tampilkan pesan

User klik "Hapus"
  -> hapusData(id, entity)
  -> Confirm dialog
  -> POST ke api.php?entity=...&action=delete
  -> Jika sukses: reload data
```

### Tab Navigation Flow
```
User click tab
  -> shown.bs.tab event
  -> loadData(entity) sesuai tab
  -> Data langsung muncul tanpa reload halaman
```

---

## Fitur-Fitur Utama

### 1. Autentikasi
- Session-based login
- Password hashing dengan bcrypt
- Proteksi halaman (redirect jika belum login)
- Proteksi API (cek session sebelum akses)

### 2. CRUD Multi-Entity
- 4 entitas: Mahasiswa, Dosen, Matkul, Jadwal
- Operasi Create, Read, Update, Delete
- Kode reusable dengan parameter entity

### 3. Single Page Application (Tanpa Reload)
- Fetch API untuk AJAX request
- Bootstrap Tab untuk navigasi
- Bootstrap Modal untuk form
- Data langsung refresh setelah simpan/hapus

### 4. Relasi Database
- Foreign key jadwal ke dosen & matkul
- JOIN query untuk menampilkan nama relasi
- CASCADE delete untuk konsistensi data

### 5. User Experience
- Tab navigasi antar entitas
- Modal form untuk input data
- Konfirmasi sebelum hapus
- Alert feedback sukses/error
- Select dinamis untuk form jadwal

### 6. Keamanan
- SQL Injection protection (`mysqli_real_escape_string`)
- Session authentication
- Password hashing (bcrypt)
- Input validation (`intval` untuk id)

---

## Penjelasan Code Penting

### Code 1: API Routing dengan Entity Parameter
**File**: api.php, Baris 12-13

```php
$entity = $_GET['entity'] ?? '';
$action = $_GET['action'] ?? '';
```

**Penjelasan**:
- API menggunakan 2 parameter: `entity` (mahasiswa/dosen/matkul/jadwal) dan `action` (list/get/save/delete/relasi)
- Dengan pola ini, 1 file api.php bisa handle 4 entitas * 5 aksi = 20 endpoint berbeda
- Setiap blok entity berisi action yang sama (list, get, save, delete)
- Mengurangi duplikasi kode dibanding bikin file terpisah

---

### Code 2: Jadwal JOIN Query
**File**: api.php, Baris 170-176

```php
$query = mysqli_query($conn, "
    SELECT jadwal.*, dosen.nama AS nama_dosen, matkul.matkul AS nama_matkul
    FROM jadwal
    LEFT JOIN dosen ON jadwal.id_dosen = dosen.id
    LEFT JOIN matkul ON jadwal.id_matkul = matkul.id
    ORDER BY jadwal.id DESC
");
```

**Penjelasan**:
- `LEFT JOIN` memastikan jadwal tetap tampil meskipun relasi terhapus
- `AS nama_dosen` dan `AS nama_matkul` membuat alias agar mudah diakses di JavaScript
- Data dosen dan matkul langsung tersedia dalam satu query
- Tanpa JOIN, perlu query terpisah untuk mengambil nama

---

### Code 3: Modal Map Pattern
**File**: script.js, Baris 1-6

```javascript
const modalMap = {
    mahasiswa: { modal: null, formId: 'formMahasiswa', titleId: 'modalTitleMahasiswa', tbodyId: 'tbody-mahasiswa' },
    dosen:     { modal: null, formId: 'formDosen',     titleId: 'modalTitleDosen',     tbodyId: 'tbody-dosen' },
    matkul:    { modal: null, formId: 'formMatkul',    titleId: 'modalTitleMatkul',    tbodyId: 'tbody-matkul' },
    jadwal:    { modal: null, formId: 'formJadwal',    titleId: 'modalTitleJadwal',    tbodyId: 'tbody-jadwal' }
};
```

**Penjelasan**:
- Object mapping yang menyimpan konfigurasi setiap entitas
- Setiap entitas punya: modal, formId, titleId, tbodyId
- Dengan pola ini, fungsi `loadData()`, `siapkanTambah()`, `siapkanEdit()`, `simpanData()`, `hapusData()` bisa generic/satu untuk semua entitas
- Menghindari duplikasi fungsi untuk setiap entitas

---

### Code 4: Inisialisasi Modal Dinamis
**File**: script.js, Baris 8-13

```javascript
document.addEventListener('DOMContentLoaded', function () {
    for (const key in modalMap) {
        const el = document.getElementById('modal' + key.charAt(0).toUpperCase() + key.slice(1));
        if (el) modalMap[key].modal = new bootstrap.Modal(el);
    }
    loadData('mahasiswa');
});
```

**Penjelasan**:
- Loop over modalMap untuk inisialisasi semua Bootstrap Modal object
- Nama element dibangun dengan pola: `modal` + `EntityName` (contoh: modalMahasiswa, modalDosen)
- `charAt(0).toUpperCase() + key.slice(1)` mengubah "mahasiswa" menjadi "Mahasiswa"
- `if (el)` guard untuk menghindari error jika element tidak ditemukan
- Default loadData('mahasiswa') untuk menampilkan data saat halaman pertama dibuka

---

### Code 5: Dinamis Render Tabel per Entity
**File**: script.js, Baris 26-48

```javascript
data.forEach((item, index) => {
    html += '<tr>';
    html += '<td class="ps-3 align-middle">' + (index + 1) + '</td>';
    if (entity === 'mahasiswa') {
        html += '<td>' + item.nim + '</td><td>' + item.nama + '</td><td>' + item.jurusan + '</td><td>' + item.email + '</td>';
    } else if (entity === 'dosen') {
        html += '<td>' + item.nama + '</td><td>' + item.alamat + '</td>';
    } else if (entity === 'matkul') {
        html += '<td>' + item.matkul + '</td><td>' + item.sks + '</td>';
    } else if (entity === 'jadwal') {
        html += '<td>' + (item.nama_dosen || '-') + '</td><td>' + (item.nama_matkul || '-') + '</td><td>' + item.waktu + '</td><td>' + item.ruang + '</td>';
    }
    html += '<td class="text-center">';
    html += '<button class="btn btn-warning btn-sm me-1" onclick="siapkanEdit(' + item.id + ', \'' + entity + '\')">Edit</button>';
    html += '<button class="btn btn-danger btn-sm" onclick="hapusData(' + item.id + ', \'' + entity + '\')">Hapus</button>';
    html += '</td></tr>';
});
```

**Penjelasan**:
- Fungsi `loadData()` generic untuk semua entitas
- Perbedaan hanya di kolom yang dirender (conditional berdasarkan entity)
- Jadwal pakai `item.nama_dosen` dan `item.nama_matkul` dari hasil JOIN
- Operator `|| '-'` untuk fallback jika nama relasi null
- Tombol Edit/Hapus pakai inline onclick dengan parameter id dan entity

---

### Code 6: Foreign Key + CASCADE Delete
**File**: database.sql, Baris 36-37

```sql
FOREIGN KEY (id_dosen) REFERENCES dosen(id) ON DELETE CASCADE,
FOREIGN KEY (id_matkul) REFERENCES matkul(id) ON DELETE CASCADE
```

**Penjelasan**:
- `FOREIGN KEY` memastikan referential integrity: jadwal hanya bisa pakai id_dosen dan id_matkul yang valid
- `ON DELETE CASCADE`: ketika data dosen/matkul dihapus, semua jadwal yang terkait otomatis ikut terhapus
- Mencegah orphan records (data jadwal tanpa induk)

---

## Statistik Code

| Aspek | Detail |
|-------|--------|
| Total Baris (PHP) | ~335 |
| Total Baris (JavaScript) | 172 |
| Total Baris (SQL) | 83 |
| Jumlah Entity | 4 (mahasiswa, dosen, matkul, jadwal) |
| Jumlah Fungsi (JS) | 6 function |
| Jumlah Tabel | 5 tabel |
| Jumlah Endpoint API | 17 endpoint |
| Button Submit dalam 1 Form | 1 (save sesuai mode) |

## Teknologi yang Digunakan

| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| PHP | 8.0+ | Backend logic & API |
| MySQL | 8.0+ | Database |
| Bootstrap | 5.3 | UI framework |
| JavaScript | ES6+ | Frontend logic |
| Fetch API | Modern | AJAX request |
| MySQLi | PHP ext | Koneksi database |
| bcrypt | PHP lib | Password hashing |

---

## Testing Checklist

- [ ] Login dengan admin/admin
- [ ] Login dengan password salah (harus error)
- [ ] Tambah data mahasiswa baru
- [ ] Edit data mahasiswa
- [ ] Hapus data mahasiswa (confirm dialog)
- [ ] CRUD Dosen (tambah, edit, hapus)
- [ ] CRUD Matkul (tambah, edit, hapus)
- [ ] CRUD Jadwal dengan relasi dosen & matkul
- [ ] Switch tab, cek data muncul sesuai entity
- [ ] Logout, cek session terhapus
- [ ] Akses index.php tanpa login (harus redirect)
- [ ] Refresh halaman setelah CRUD

---

## Kesimpulan

Aplikasi CRUD Akademik adalah implementasi nyata dari:

- **PHP Backend**: RESTful API dengan routing entity-based
- **MySQL Database**: Relasi antar tabel dengan foreign key
- **JavaScript Frontend**: Single Page Application dengan Fetch API
- **Bootstrap UI**: Tampilan modern dengan tab dan modal
- **Keamanan**: Session authentication, password hashing, SQL injection protection

Arsitektur memisahkan backend (api.php) dan frontend (script.js) sehingga pengembangan lebih terstruktur dan mudah dikelola.

---

**Link Repository**
https://github.com/zaidanity/Tugas-Pemrograman-Web1
