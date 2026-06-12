const modalMap = {
    mahasiswa: { modal: null, formId: 'formMahasiswa', titleId: 'modalTitleMahasiswa', tbodyId: 'tbody-mahasiswa' },
    dosen:     { modal: null, formId: 'formDosen',     titleId: 'modalTitleDosen',     tbodyId: 'tbody-dosen' },
    matkul:    { modal: null, formId: 'formMatkul',    titleId: 'modalTitleMatkul',    tbodyId: 'tbody-matkul' },
    jadwal:    { modal: null, formId: 'formJadwal',    titleId: 'modalTitleJadwal',    tbodyId: 'tbody-jadwal' }
};

document.addEventListener('DOMContentLoaded', function () {
    for (const key in modalMap) {
        const el = document.getElementById('modal' + key.charAt(0).toUpperCase() + key.slice(1));
        if (el) modalMap[key].modal = new bootstrap.Modal(el);
    }
    loadData('mahasiswa');
});

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
                    html += '<tr>';
                    html += '<td class="ps-3 align-middle">' + (index + 1) + '</td>';
                    if (entity === 'mahasiswa') {
                        html += '<td class="align-middle">' + item.nim + '</td>';
                        html += '<td class="align-middle">' + item.nama + '</td>';
                        html += '<td class="align-middle">' + item.jurusan + '</td>';
                        html += '<td class="align-middle">' + item.email + '</td>';
                    } else if (entity === 'dosen') {
                        html += '<td class="align-middle">' + item.nama + '</td>';
                        html += '<td class="align-middle">' + item.alamat + '</td>';
                    } else if (entity === 'matkul') {
                        html += '<td class="align-middle">' + item.matkul + '</td>';
                        html += '<td class="align-middle">' + item.sks + '</td>';
                    } else if (entity === 'jadwal') {
                        html += '<td class="align-middle">' + (item.nama_dosen || '-') + '</td>';
                        html += '<td class="align-middle">' + (item.nama_matkul || '-') + '</td>';
                        html += '<td class="align-middle">' + item.waktu + '</td>';
                        html += '<td class="align-middle">' + item.ruang + '</td>';
                    }
                    html += '<td class="text-center align-middle">';
                    html += '<button class="btn btn-warning btn-sm me-1" onclick="siapkanEdit(' + item.id + ', \'' + entity + '\')">Edit</button>';
                    html += '<button class="btn btn-danger btn-sm" onclick="hapusData(' + item.id + ', \'' + entity + '\')">Hapus</button>';
                    html += '</td></tr>';
                });
            }
            tbody.innerHTML = html;
        })
        .catch(err => console.error('Gagal memuat data:', err));
}

function siapkanTambah(entity) {
    const m = modalMap[entity];
    document.getElementById(m.titleId).innerText = 'Tambah ' + entity.charAt(0).toUpperCase() + entity.slice(1);
    document.getElementById(m.formId).reset();
    document.getElementById(entity + '_id').value = '';

    if (entity === 'jadwal') {
        muatRelasiJadwal();
    }
}

function siapkanEdit(id, entity) {
    const m = modalMap[entity];
    document.getElementById(m.titleId).innerText = 'Ubah ' + entity.charAt(0).toUpperCase() + entity.slice(1);

    fetch('api.php?entity=' + entity + '&action=get&id=' + id)
        .then(res => res.json())
        .then(data => {
            document.getElementById(entity + '_id').value = data.id;

            if (entity === 'mahasiswa') {
                document.getElementById('nim').value = data.nim;
                document.getElementById('nama_mahasiswa').value = data.nama;
                document.getElementById('jurusan').value = data.jurusan;
                document.getElementById('email').value = data.email;
            } else if (entity === 'dosen') {
                document.getElementById('nama_dosen').value = data.nama;
                document.getElementById('alamat').value = data.alamat;
            } else if (entity === 'matkul') {
                document.getElementById('matkul_nama').value = data.matkul;
                document.getElementById('sks').value = data.sks;
            } else if (entity === 'jadwal') {
                muatRelasiJadwal(function () {
                    document.getElementById('id_dosen').value = data.id_dosen;
                    document.getElementById('id_matkul').value = data.id_matkul;
                });
                document.getElementById('waktu').value = data.waktu;
                document.getElementById('ruang').value = data.ruang;
            }

            m.modal.show();
        })
        .catch(err => console.error('Gagal ambil data:', err));
}

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
        })
        .catch(err => console.error('Gagal simpan data:', err));
}

function hapusData(id, entity) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('api.php?entity=' + entity + '&action=delete', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    alert('Data berhasil dihapus!');
                    loadData(entity);
                } else {
                    alert('Error: ' + res.message);
                }
            })
            .catch(err => console.error('Gagal hapus data:', err));
    }
}

function muatRelasiJadwal(callback) {
    fetch('api.php?entity=jadwal&action=relasi')
        .then(res => res.json())
        .then(data => {
            const selDosen = document.getElementById('id_dosen');
            const selMatkul = document.getElementById('id_matkul');
            selDosen.innerHTML = '<option value="">-- Pilih Dosen --</option>';
            selMatkul.innerHTML = '<option value="">-- Pilih Matkul --</option>';
            data.dosen.forEach(d => {
                selDosen.innerHTML += '<option value="' + d.id + '">' + d.nama + '</option>';
            });
            data.matkul.forEach(m => {
                selMatkul.innerHTML += '<option value="' + m.id + '">' + m.matkul + '</option>';
            });
            if (callback) callback();
        })
        .catch(err => console.error('Gagal muat relasi:', err));
}

document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', function (e) {
        const id = e.target.id;
        if (id === 'tab-mahasiswa') loadData('mahasiswa');
        else if (id === 'tab-dosen') loadData('dosen');
        else if (id === 'tab-matkul') loadData('matkul');
        else if (id === 'tab-jadwal') loadData('jadwal');
    });
});
