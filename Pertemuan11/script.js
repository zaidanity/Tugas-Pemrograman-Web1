function hitung() {
  var nama = document.getElementById('nama').value.trim();
  var hadir = parseInt(document.getElementById('hadir').value);
  var total = parseInt(document.getElementById('total').value);

  if (!nama || isNaN(hadir) || isNaN(total) || total <= 0) {
    alert('Harap isi semua data dengan benar!');
    return;
  }

  var persentase = (hadir / total) * 100;
  var status = persentase >= 75 ? 'Boleh Mengikuti Ujian' : 'Tidak Boleh Mengikuti Ujian';
  var statusClass = persentase >= 75 ? 'status-lulus' : 'status-gagal';

  document.getElementById('rNama').textContent = nama;
  document.getElementById('rHadir').textContent = hadir;
  document.getElementById('rTotal').textContent = total;
  document.getElementById('rPersen').textContent = persentase.toFixed(2) + '%';
  document.getElementById('rStatus').textContent = status;
  document.getElementById('rStatus').className = statusClass;

  document.getElementById('result').classList.add('show');
}
