<?php
$host = "localhost";
$user = "root"; 
$pass = "";     
$db   = "sekolah"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi untuk membuat tabel agama jika belum ada
function createAgamaTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS agama (
        id_agama TINYINT PRIMARY KEY,
        nama_agama VARCHAR(8) NOT NULL
    )";
    
    if ($conn->query($sql) === FALSE) {
        die("Error membuat tabel agama: " . $conn->error);
    }
}

// Fungsi untuk membuat tabel jurusan jika belum ada
function createJurusanTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS jurusan (
        id_jurusan TINYINT PRIMARY KEY,
        nama_jurusan VARCHAR(50) NOT NULL
    )";
    
    if ($conn->query($sql) === FALSE) {
        die("Error membuat tabel jurusan: " . $conn->error);
    }
}

// Fungsi untuk mengisi data awal jurusan jika tabel kosong
function insertInitialJurusanData($conn) {
    $check = $conn->query("SELECT COUNT(*) as count FROM jurusan");
    $row = $check->fetch_assoc();
    
    if ($row['count'] == 0) {
        $jurusan_data = [
            [1, 'AKL'],
            [2, 'MPLB'],
            [3, 'PM'],
            [4, 'ULP'],
            [5, 'DKV'],
            [6, 'PPLG'],
            [7, 'BC']
        ];
        
        $stmt = $conn->prepare("INSERT INTO jurusan (id_jurusan, nama_jurusan) VALUES (?, ?)");
        
        foreach ($jurusan_data as $jurusan) {
            $stmt->bind_param("is", $jurusan[0], $jurusan[1]);
            $stmt->execute();
        }
        $stmt->close();
    }
}

// Fungsi untuk mengisi data awal agama jika tabel kosong
function insertInitialAgamaData($conn) {
    $check = $conn->query("SELECT COUNT(*) as count FROM agama");
    $row = $check->fetch_assoc();
    
    if ($row['count'] == 0) {
        $agama_data = [
            [1, 'Islam'],
            [2, 'Kristen'],
            [3, 'Katolik'],
            [4, 'Hindu'],
            [5, 'Buddha'],
            [6, 'Konghucu']
        ];
        
        $stmt = $conn->prepare("INSERT INTO agama (id_agama, nama_agama) VALUES (?, ?)");
        
        foreach ($agama_data as $agama) {
            $stmt->bind_param("is", $agama[0], $agama[1]);
            $stmt->execute();
        }
        $stmt->close();
    }
}

// Fungsi untuk mendapatkan semua data agama
function getAllAgama($conn) {
    $sql = "SELECT * FROM agama ORDER BY id_agama";
    return $conn->query($sql);
}

// Fungsi untuk mendapatkan semua data jurusan
function getAllJurusan($conn) {
    $sql = "SELECT * FROM jurusan ORDER BY id_jurusan";
    return $conn->query($sql);
}

// Fungsi untuk mendapatkan agama berdasarkan ID
function getAgamaById($conn, $id_agama) {
    $stmt = $conn->prepare("SELECT * FROM agama WHERE id_agama = ?");
    $stmt->bind_param("i", $id_agama);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row;
}

// Fungsi untuk mendapatkan jurusan berdasarkan ID
function getJurusanById($conn, $id_jurusan) {
    $stmt = $conn->prepare("SELECT * FROM jurusan WHERE id_jurusan = ?");
    $stmt->bind_param("i", $id_jurusan);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row;
}

// Fungsi untuk mendapatkan nama agama berdasarkan ID
function getNamaAgamaById($conn, $id_agama) {
    $stmt = $conn->prepare("SELECT nama_agama FROM agama WHERE id_agama = ?");
    $stmt->bind_param("i", $id_agama);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['nama_agama'] : null;
}

// Fungsi untuk mendapatkan nama jurusan berdasarkan ID
function getNamaJurusanById($conn, $id_jurusan) {
    $stmt = $conn->prepare("SELECT nama_jurusan FROM jurusan WHERE id_jurusan = ?");
    $stmt->bind_param("i", $id_jurusan);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? $row['nama_jurusan'] : null;
}

// Fungsi untuk menambah agama baru
function addAgama($conn, $id_agama, $nama_agama) {
    $stmt = $conn->prepare("INSERT INTO agama (id_agama, nama_agama) VALUES (?, ?)");
    $stmt->bind_param("is", $id_agama, $nama_agama);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Fungsi untuk menambah jurusan baru
function addJurusan($conn, $id_jurusan, $nama_jurusan) {
    $stmt = $conn->prepare("INSERT INTO jurusan (id_jurusan, nama_jurusan) VALUES (?, ?)");
    $stmt->bind_param("is", $id_jurusan, $nama_jurusan);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Fungsi untuk update agama
function updateAgama($conn, $id_agama, $nama_agama) {
    $stmt = $conn->prepare("UPDATE agama SET nama_agama = ? WHERE id_agama = ?");
    $stmt->bind_param("si", $nama_agama, $id_agama);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Fungsi untuk update jurusan
function updateJurusan($conn, $id_jurusan, $nama_jurusan) {
    $stmt = $conn->prepare("UPDATE jurusan SET nama_jurusan = ? WHERE id_jurusan = ?");
    $stmt->bind_param("si", $nama_jurusan, $id_jurusan);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Fungsi untuk hapus agama
function deleteAgama($conn, $id_agama) {
    $stmt = $conn->prepare("DELETE FROM agama WHERE id_agama = ?");
    $stmt->bind_param("i", $id_agama);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Fungsi untuk hapus jurusan
function deleteJurusan($conn, $id_jurusan) {
    $stmt = $conn->prepare("DELETE FROM jurusan WHERE id_jurusan = ?");
    $stmt->bind_param("i", $id_jurusan);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Inisialisasi tabel dan data
createAgamaTable($conn);
createJurusanTable($conn);
insertInitialAgamaData($conn);
insertInitialJurusanData($conn);

// Query dengan JOIN untuk mengambil nama jurusan dan agama
$sql = "SELECT s.*, j.nama_jurusan, a.nama_agama 
        FROM siswa s 
        LEFT JOIN jurusan j ON s.id_jurusan = j.id_jurusan 
        LEFT JOIN agama a ON s.id_agama = a.id_agama
        ORDER BY s.id";
$result = $conn->query($sql);

// Query untuk mendapatkan semua data agama
$agama_result = getAllAgama($conn);

// Query untuk mendapatkan semua data jurusan
$jurusan_result = getAllJurusan($conn);

?>