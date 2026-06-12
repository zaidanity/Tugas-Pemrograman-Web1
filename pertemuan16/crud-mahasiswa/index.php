<?php
session_start();
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
    <title>Dashboard - Aplikasi Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">SIAKAD Universitas</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Halo, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="logout.php" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar?')">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <ul class="nav nav-tabs" id="mainTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-mahasiswa" data-bs-toggle="tab" data-bs-target="#section-mahasiswa" type="button" role="tab">Mahasiswa</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-dosen" data-bs-toggle="tab" data-bs-target="#section-dosen" type="button" role="tab">Dosen</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-matkul" data-bs-toggle="tab" data-bs-target="#section-matkul" type="button" role="tab">Matkul</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-jadwal" data-bs-toggle="tab" data-bs-target="#section-jadwal" type="button" role="tab">Jadwal</button>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="section-mahasiswa" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Data Mahasiswa</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMahasiswa" onclick="siapkanTambah('mahasiswa')">Tambah Mahasiswa</button>
                </div>
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
                                <tbody id="tbody-mahasiswa"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="section-dosen" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Data Dosen</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDosen" onclick="siapkanTambah('dosen')">Tambah Dosen</button>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-3">No</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-dosen"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="section-matkul" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Data Mata Kuliah</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMatkul" onclick="siapkanTambah('matkul')">Tambah Matkul</button>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-3">No</th>
                                        <th>Mata Kuliah</th>
                                        <th>SKS</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-matkul"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="section-jadwal" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Data Jadwal</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalJadwal" onclick="siapkanTambah('jadwal')">Tambah Jadwal</button>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-3">No</th>
                                        <th>Dosen</th>
                                        <th>Mata Kuliah</th>
                                        <th>Waktu</th>
                                        <th>Ruang</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-jadwal"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Mahasiswa -->
    <div class="modal fade" id="modalMahasiswa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleMahasiswa">Form Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formMahasiswa" onsubmit="simpanData(event, 'mahasiswa')">
                    <div class="modal-body">
                        <input type="hidden" id="mahasiswa_id" name="id">
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="text" class="form-control" id="nim" name="nim" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="nama_mahasiswa" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_mahasiswa" name="nama" required autocomplete="off">
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
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Dosen -->
    <div class="modal fade" id="modalDosen" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleDosen">Form Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formDosen" onsubmit="simpanData(event, 'dosen')">
                    <div class="modal-body">
                        <input type="hidden" id="dosen_id" name="id">
                        <div class="mb-3">
                            <label for="nama_dosen" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama_dosen" name="nama" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Matkul -->
    <div class="modal fade" id="modalMatkul" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleMatkul">Form Mata Kuliah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formMatkul" onsubmit="simpanData(event, 'matkul')">
                    <div class="modal-body">
                        <input type="hidden" id="matkul_id" name="id">
                        <div class="mb-3">
                            <label for="matkul_nama" class="form-label">Mata Kuliah</label>
                            <input type="text" class="form-control" id="matkul_nama" name="matkul" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="sks" class="form-label">SKS</label>
                            <input type="number" class="form-control" id="sks" name="sks" required min="1" max="6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Jadwal -->
    <div class="modal fade" id="modalJadwal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleJadwal">Form Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formJadwal" onsubmit="simpanData(event, 'jadwal')">
                    <div class="modal-body">
                        <input type="hidden" id="jadwal_id" name="id">
                        <div class="mb-3">
                            <label for="id_dosen" class="form-label">Dosen</label>
                            <select class="form-select" id="id_dosen" name="id_dosen" required>
                                <option value="">-- Pilih Dosen --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="id_matkul" class="form-label">Mata Kuliah</label>
                            <select class="form-select" id="id_matkul" name="id_matkul" required>
                                <option value="">-- Pilih Matkul --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="waktu" class="form-label">Waktu</label>
                            <input type="text" class="form-control" id="waktu" name="waktu" placeholder="Contoh: Senin 08:00-09:40" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="ruang" class="form-label">Ruang</label>
                            <input type="text" class="form-control" id="ruang" name="ruang" placeholder="Contoh: A101" required autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
