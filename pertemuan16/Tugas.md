Berikut adalah panduan langkah demi langkah yang lengkap, detail, dan terstruktur untuk membuat aplikasi CRUD Data Mahasiswa dengan sistem Login. Kita akan menggunakan PHP Natif, MySQL, JavaScript (Fetch API) untuk proses AJAX (tanpa reload halaman), serta Bootstrap 5 agar tampilannya modern dan responsif.
Struktur Folder Proyek
Buat sebuah folder baru di dalam direktori htdocs Anda (misal: C:\xampp\htdocs\crud-mahasiswa\). Buat struktur file seperti berikut:

crud-mahasiswa/
├── koneksi.php
├── login.php
├── logout.php
├── index.php
├── api.php
└── script.js


Langkah 1: Menyiapkan Database dan Tabel MySQL
Buka browser dan akses http://localhost/phpmyadmin/.
Klik menu SQL atau buat database baru bernama db_mahasiswa.
Eksekusi perintah SQL berikut untuk membuat tabel users (untuk login) dan tabel mahasiswa (untuk data CRUD):

CREATE DATABASE IF NOT EXISTS db_mahasiswa;
USE db_mahasiswa;

-- 1. Membuat Tabel Pengguna (Login)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- 2. Membuat Tabel Mahasiswa
CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);

-- 3. Memasukkan Akun Admin Default (Username: admin, Password: admin123)
-- Password di-hash menggunakan fungsi password_hash() PHP demi keamanan
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$8v8zN6rW1bVd6gGf7Y2y8uV1mO9wF7tE3M8R8f3K5l7o9p0q1r2s3');


Langkah 2: Membuat Koneksi Database (koneksi.php)
File ini berfungsi menghubungkan backend PHP dengan server database MySQL Anda.

<?php
$host = "localhost";
$user = "root";     // Default XAMPP
$pass = "";         // Default XAMPP (kosong)
$db   = "db_mahasiswa";

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>


Langkah 3: Membuat Sistem Autentikasi (login.php & logout.php)
File login.php
Halaman ini menangani proses pengecekan akun dan pembuatan sesi (session).

<?php
session_start();
include 'koneksi.php';

// Jika pengguna sudah login, langsung alihkan ke halaman utama (index.php)
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = false;

if (isset($_POST['submit_login'])) {
    // Ambil data form dan proteksi dari SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Cari username di database
    $query  = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verifikasi password yang di-input dengan hash di database
        if (password_verify($password, $row['password'])) {
            // Set session sukses login
            $_SESSION['login'] = true;
            $_SESSION['username'] = $row['username'];
            
            header("Location: index.php");
            exit;
        }
    }
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Mahasiswa</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 font-weight-bold">Sign In</h3>
                        
                        <?php if ($error) : ?>
                            <div class="alert alert-danger p-2 text-center" style="font-size: 14px;">
                                Username atau password Anda salah!
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                            </div>
                            <button type="submit" name="submit_login" class="btn btn-primary w-100 py-2">Masuk</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


File logout.php
Menghapus seluruh rekaman session aktif dan mengembalikan user ke login.

<?php
session_start();
$_SESSION = [];
session_unset();
session_destroy();

header("Location: login.php");
exit;
?>


Langkah 4: Membuat Halaman Utama & Tampilan Antarmuka (index.php)
Halaman ini dilindungi sistem session. Kami menyematkan Bootstrap Modal untuk formulir tambah dan edit data mahasiswa agar menghemat ruang halaman.

<?php
session_start();
// Proteksi halaman: Jika belum login, tendang kembali ke halaman login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CRUD Data Mahasiswa</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">SIAKAD Universitas</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Halo, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="logout.php" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Mahasiswa</h2>
            <!-- Tombol untuk memicu Modal Tambah -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mahasiswaModal" onclick="siapkanTambah()">Tambah Mahasiswa</button>
        </div>

        <!-- Tabel Data -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>NIM</th>
                                <th>Nama Lengkap</th>
                                <th>Jurusan</th>
                                <th>Email</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tempat-data-mahasiswa">
                            <!-- Data akan dimuat otomatis oleh JavaScript melalui API -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal untuk Form Tambah / Edit -->
    <div class="modal fade" id="mahasiswaModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Form Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-end="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formMahasiswa" onsubmit="simpanData(event)">
                    <div class="modal-body">
                        <!-- Input Hidden untuk menyimpan ID saat Edit -->
                        <input type="hidden" id="mahasiswa_id" name="id">
                        
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="text" class="form-control" id="nim" name="nim" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="jurusan" class="form-label">Jurusan</label>
                            <input type="text" class="form-control" id="jurusan" name="jurusan" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JavaScript Bundle CDN -->
    <script src="https://jsdelivr.net"></script>
    <!-- Script Integrasi CRUD JS -->
    <script src="script.js"></script>
</body>
</html>


Langkah 5: Membuat RESTful Engine Backend API (api.php)
File ini adalah inti pemrosesan CRUD. PHP bertugas menerima sinyal aksi dari Javascript, melakukan manipulasi database, dan mengembalikan respons berupa format JSON.

<?php
session_start();
header('Content-Type: application/json');

// Proteksi API: Jika tidak ada session login, cegah akses
if (!isset($_SESSION['login'])) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ilegal terdeteksi. Silakan login.']);
    exit;
}

include 'koneksi.php';

$action = $_GET['action'] ?? '';

// ==========================================
// ACTION: READ (Mengambil Seluruh Data)
// ==========================================
if ($action == 'list') {
    $query = mysqli_query($conn, "SELECT * FROM mahasiswa ORDER BY id DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// ==========================================
// ACTION: GET SINGLE (Mengambil Satu Data untuk Form Edit)
// ==========================================
if ($action == 'get_single') {
    $id = intval($_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id = $id");
    $data = mysqli_fetch_assoc($query);
    echo json_encode($data);
    exit;
}

// ==========================================
// ACTION: CREATE & UPDATE (Menyimpan Data)
// ==========================================
if ($action == 'save') {
    $id      = $_POST['id'] ?? '';
    $nim     = mysqli_real_escape_string($conn, $_POST['nim']);
    $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);

    if (empty($id)) {
        // Jika ID kosong, tandanya adalah proses Tambah Baru (CREATE)
        $sql = "INSERT INTO mahasiswa (nim, nama, jurusan, email) VALUES ('$nim', '$nama', '$jurusan', '$email')";
    } else {
        // Jika ID ada isinya, tandanya adalah proses Pembaruan (UPDATE)
        $sql = "UPDATE mahasiswa SET nim='$nim', nama='$nama', jurusan='$jurusan', email='$email' WHERE id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}

// ==========================================
// ACTION: DELETE (Menghapus Data)
// ==========================================
if ($action == 'delete') {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM mahasiswa WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}
?>


Langkah 6: Membuat Frontend Controller JavaScript AJAX (script.js)
File ini bertugas mengirim request asinkronus (Fetch API) ke api.php, mengolah data tanggapan, serta melakukan manipulasi struktur HTML (DOM) secara dinamis tanpa melakukan full-reload halaman web.

// Memanggil fungsi loadData pertama kali saat halaman selesai dimuat seluruhnya
document.addEventListener('DOMContentLoaded', loadData);

// Menginisialisasi Bootstrap Modal Object dari index.php supaya bisa ditutup/buka via JS
const mModal = new bootstrap.Modal(document.getElementById('mahasiswaModal'));

// ==========================================
// FUNGSI: READ DATA (Menampilkan ke Tabel HTML)
// ==========================================
function loadData() {
    fetch('api.php?action=list')
        .then(response => response.json())
        .then(data => {
            let html = '';
            if (data.length === 0) {
                html = `<tr><td colspan="6" class="text-center text-muted p-4">Belum ada data mahasiswa.</td></tr>`;
            } else {
                data.forEach((mhs, index) => {
                    html += `
                        <tr>
                            <td class="ps-3 align-middle">${index + 1}</td>
                            <td class="align-middle">${mhs.nim}</td>
                            <td class="align-middle">${mhs.nama}</td>
                            <td class="align-middle">${mhs.jurusan}</td>
                            <td class="align-middle">${mhs.email}</td>
                            <td class="text-center align-middle">
                                <button class="btn btn-warning btn-sm me-1" onclick="siapkanEdit(${mhs.id})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="hapusData(${mhs.id})">Hapus</button>
                            </td>
                        </tr>
                    `;
                });
            }
            document.getElementById('tempat-data-mahasiswa').innerHTML = html;
        })
        .catch(err => console.error("Gagal memuat data: ", err));
}

// ==========================================
// FUNGSI: PRE-CREATE FORM (Mereset Form Tambah)
// ==========================================
function siapkanTambah() {
    document.getElementById('modalTitle').innerText = 'Tambah Data Mahasiswa';
    document.getElementById('formMahasiswa').reset();
    document.getElementById('mahasiswa_id').value = ''; // Kosongkan ID penanda
}

// ==========================================
// FUNGSI: PRE-UPDATE FORM (Mengambil Data Lama ke Form)
// ==========================================
function siapkanEdit(id) {
    document.getElementById('modalTitle').innerText = 'Ubah Data Mahasiswa';
    document.getElementById('formMahasiswa').reset();

    // Ambil data spesifik berdasarkan ID
    fetch(`api.php?action=get_single&id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Isikan data ke dalam input field form modal
            document.getElementById('mahasiswa_id').value = data.id;
            document.getElementById('nim').value = data.nim;
            document.getElementById('nama').value = data.nama;
            document.getElementById('jurusan').value = data.jurusan;
            document.getElementById('email').value = data.email;
            
            // Tampilkan modal setelah data terisi
            mModal.show();
        })
        .catch(err => console.error("Gagal mengambil data detail: ", err));
}

// ==========================================
// FUNGSI: SAVE DATA (Proses Tambah & Edit via POST)
// ==========================================
function simpanData(event) {
    event.preventDefault(); // Mencegah form melakukan reload halaman bawaan browser

    const form = document.getElementById('formMahasiswa');
    const formData = new FormData(form);

    fetch('api.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            alert('Data berhasil disimpan!');
            mModal.hide();     // Sembunyikan modal form
            loadData();         // Refresh konten tabel
        } else {
            alert('Error: ' + res.message);
        }
    })
    .catch(err => console.error("Gagal mengirim data: ", err));
}

// ==========================================
// FUNGSI: DELETE DATA
// ==========================================
function hapusData(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini secara permanen?')) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('api.php?action=delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                alert('Data berhasil dihapus!');
                loadData(); // Refresh konten tabel
            } else {
                alert('Error: ' + res.message);
            }
        })
        .catch(err => console.error("Gagal menghapus data: ", err));
    }
}


TUGAS 
Tambahkan crud untuk dosen(id,nama,alamat) matkul(id,matkul,sks) dan jadwal(id,id_dosen,id_matkul,waktu,ruang)