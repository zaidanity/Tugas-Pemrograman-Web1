CREATE DATABASE IF NOT EXISTS db_mahasiswa;
USE db_mahasiswa;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS matkul (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matkul VARCHAR(100) NOT NULL,
    sks INT NOT NULL
);

CREATE TABLE IF NOT EXISTS jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_dosen INT NOT NULL,
    id_matkul INT NOT NULL,
    waktu VARCHAR(50) NOT NULL,
    ruang VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_dosen) REFERENCES dosen(id) ON DELETE CASCADE,
    FOREIGN KEY (id_matkul) REFERENCES matkul(id) ON DELETE CASCADE
);

INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$r9/orY6SpcHDANMPWTQf1eMc.0FOFJLS9ZeTjJZpzeBcFM7alkaq6'),
('dosen1', '$2y$10$r9/orY6SpcHDANMPWTQf1eMc.0FOFJLS9ZeTjJZpzeBcFM7alkaq6'),
('staff', '$2y$10$r9/orY6SpcHDANMPWTQf1eMc.0FOFJLS9ZeTjJZpzeBcFM7alkaq6');

INSERT INTO mahasiswa (nim, nama, jurusan, email) VALUES
('2310114001', 'Ahmad Fauzi', 'Teknik Informatika', 'ahmad.fauzi@email.com'),
('2310114002', 'Bunga Citra', 'Sistem Informasi', 'bunga.citra@email.com'),
('2310114003', 'Charlie Putra', 'Teknik Informatika', 'charlie.putra@email.com'),
('2310114004', 'Dewi Lestari', 'Manajemen Informatika', 'dewi.lestari@email.com'),
('2310114005', 'Eko Prasetyo', 'Teknik Informatika', 'eko.prasetyo@email.com'),
('2310114006', 'Fitri Handayani', 'Sistem Informasi', 'fitri.handayani@email.com'),
('2310114007', 'Gilang Ramadhan', 'Manajemen Informatika', 'gilang.ramadhan@email.com'),
('2310114008', 'Hana Safira', 'Teknik Informatika', 'hana.safira@email.com'),
('2310114009', 'Irwan Saputra', 'Sistem Informasi', 'irwan.saputra@email.com'),
('2310114010', 'Jasmine Putri', 'Manajemen Informatika', 'jasmine.putri@email.com');

INSERT INTO dosen (nama, alamat) VALUES
('Dr. Budi Santoso, M.Kom', 'Jl. Merdeka No. 10, Jakarta'),
('Dr. Siti Rahmawati, M.T', 'Jl. Sudirman No. 25, Bandung'),
('Dr. Andi Pratama, M.Kom', 'Jl. Diponegoro No. 7, Surabaya'),
('Dr. Rina Amelia, M.T', 'Jl. Gatot Subroto No. 15, Yogyakarta');

INSERT INTO matkul (matkul, sks) VALUES
('Pemrograman Web 1', 3),
('Basis Data', 3),
('Struktur Data', 3),
('Algoritma Pemrograman', 3),
('Jaringan Komputer', 3),
('Sistem Operasi', 2),
('Pemrograman Mobile', 3),
('Rekayasa Perangkat Lunak', 3);

INSERT INTO jadwal (id_dosen, id_matkul, waktu, ruang) VALUES
(1, 1, 'Senin 08:00-10:00', 'Ruang 101'),
(1, 3, 'Senin 13:00-15:00', 'Ruang 102'),
(2, 2, 'Selasa 08:00-10:00', 'Ruang 201'),
(2, 5, 'Selasa 10:00-12:00', 'Ruang 202'),
(3, 4, 'Rabu 08:00-10:00', 'Ruang 101'),
(3, 7, 'Rabu 13:00-15:00', 'Ruang 103'),
(4, 6, 'Kamis 08:00-10:00', 'Ruang 201'),
(4, 8, 'Kamis 10:00-12:00', 'Ruang 202'),
(1, 1, 'Jumat 08:00-10:00', 'Ruang 101'),
(2, 2, 'Jumat 13:00-15:00', 'Ruang 102');
