<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['login'])) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ilegal terdeteksi. Silakan login.']);
    exit;
}

include 'koneksi.php';

$entity = $_GET['entity'] ?? '';
$action = $_GET['action'] ?? '';

if ($entity == 'mahasiswa') {

    if ($action == 'list') {
        $query = mysqli_query($conn, "SELECT * FROM mahasiswa ORDER BY id DESC");
        $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        echo json_encode($data);
        exit;
    }

    if ($action == 'get') {
        $id = intval($_GET['id']);
        $query = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id = $id");
        echo json_encode(mysqli_fetch_assoc($query));
        exit;
    }

    if ($action == 'save') {
        $id      = $_POST['id'] ?? '';
        $nim     = mysqli_real_escape_string($conn, $_POST['nim']);
        $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
        $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
        $email   = mysqli_real_escape_string($conn, $_POST['email']);

        if (empty($id)) {
            $sql = "INSERT INTO mahasiswa (nim, nama, jurusan, email) VALUES ('$nim', '$nama', '$jurusan', '$email')";
        } else {
            $sql = "UPDATE mahasiswa SET nim='$nim', nama='$nama', jurusan='$jurusan', email='$email' WHERE id=$id";
        }

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

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
}

if ($entity == 'dosen') {

    if ($action == 'list') {
        $query = mysqli_query($conn, "SELECT * FROM dosen ORDER BY id DESC");
        $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        echo json_encode($data);
        exit;
    }

    if ($action == 'get') {
        $id = intval($_GET['id']);
        $query = mysqli_query($conn, "SELECT * FROM dosen WHERE id = $id");
        echo json_encode(mysqli_fetch_assoc($query));
        exit;
    }

    if ($action == 'save') {
        $id     = $_POST['id'] ?? '';
        $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

        if (empty($id)) {
            $sql = "INSERT INTO dosen (nama, alamat) VALUES ('$nama', '$alamat')";
        } else {
            $sql = "UPDATE dosen SET nama='$nama', alamat='$alamat' WHERE id=$id";
        }

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

    if ($action == 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM dosen WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }
}

if ($entity == 'matkul') {

    if ($action == 'list') {
        $query = mysqli_query($conn, "SELECT * FROM matkul ORDER BY id DESC");
        $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        echo json_encode($data);
        exit;
    }

    if ($action == 'get') {
        $id = intval($_GET['id']);
        $query = mysqli_query($conn, "SELECT * FROM matkul WHERE id = $id");
        echo json_encode(mysqli_fetch_assoc($query));
        exit;
    }

    if ($action == 'save') {
        $id     = $_POST['id'] ?? '';
        $matkul = mysqli_real_escape_string($conn, $_POST['matkul']);
        $sks    = intval($_POST['sks']);

        if (empty($id)) {
            $sql = "INSERT INTO matkul (matkul, sks) VALUES ('$matkul', $sks)";
        } else {
            $sql = "UPDATE matkul SET matkul='$matkul', sks=$sks WHERE id=$id";
        }

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

    if ($action == 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM matkul WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }
}

if ($entity == 'jadwal') {

    if ($action == 'list') {
        $query = mysqli_query($conn, "
            SELECT jadwal.*, dosen.nama AS nama_dosen, matkul.matkul AS nama_matkul
            FROM jadwal
            LEFT JOIN dosen ON jadwal.id_dosen = dosen.id
            LEFT JOIN matkul ON jadwal.id_matkul = matkul.id
            ORDER BY jadwal.id DESC
        ");
        $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        echo json_encode($data);
        exit;
    }

    if ($action == 'get') {
        $id = intval($_GET['id']);
        $query = mysqli_query($conn, "SELECT * FROM jadwal WHERE id = $id");
        echo json_encode(mysqli_fetch_assoc($query));
        exit;
    }

    if ($action == 'save') {
        $id        = $_POST['id'] ?? '';
        $id_dosen  = intval($_POST['id_dosen']);
        $id_matkul = intval($_POST['id_matkul']);
        $waktu     = mysqli_real_escape_string($conn, $_POST['waktu']);
        $ruang     = mysqli_real_escape_string($conn, $_POST['ruang']);

        if (empty($id)) {
            $sql = "INSERT INTO jadwal (id_dosen, id_matkul, waktu, ruang) VALUES ($id_dosen, $id_matkul, '$waktu', '$ruang')";
        } else {
            $sql = "UPDATE jadwal SET id_dosen=$id_dosen, id_matkul=$id_matkul, waktu='$waktu', ruang='$ruang' WHERE id=$id";
        }

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

    if ($action == 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM jadwal WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

    if ($action == 'relasi') {
        $dosen  = [];
        $matkul = [];
        $q = mysqli_query($conn, "SELECT id, nama FROM dosen ORDER BY nama ASC");
        while ($r = mysqli_fetch_assoc($q)) {
            $dosen[] = $r;
        }
        $q = mysqli_query($conn, "SELECT id, matkul FROM matkul ORDER BY matkul ASC");
        while ($r = mysqli_fetch_assoc($q)) {
            $matkul[] = $r;
        }
        echo json_encode(['dosen' => $dosen, 'matkul' => $matkul]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenal']);
?>
