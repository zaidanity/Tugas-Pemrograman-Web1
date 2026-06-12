# Program Absensi Mahasiswa

Aplikasi web sederhana untuk menghitung persentase kehadiran mahasiswa dan menentukan kelayakan mengikuti ujian (minimal 75% kehadiran).

## Fitur

- Input nama mahasiswa, jumlah hadir, dan total pertemuan
- Hitung persentase kehadiran otomatis
- Status kelulusan ujian berdasarkan ambang batas 75%
- Tampilan hasil yang jelas dengan indikasi warna (hijau = lulus, merah = tidak lulus)

## Teknologi

- HTML5
- CSS3 (tanpa framework)
- JavaScript (vanilla)

## Cara Menggunakan

1. Buka `index.html` di browser
2. Masukkan nama mahasiswa
3. Masukkan jumlah kehadiran dan total pertemuan
4. Klik tombol **HITUNG**
5. Hasil akan ditampilkan di bagian bawah

## Cara Kerja

Persentase dihitung dengan rumus: **(Jumlah Hadir / Total Pertemuan) × 100%**

- Jika persentase ≥ 75% → status *Boleh Mengikuti Ujian*
- Jika persentase < 75% → status *Tidak Boleh Mengikuti Ujian*
