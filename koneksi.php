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

// Fungsi untuk menambah agama baru
function addAgama($conn, $id_agama, $nama_agama) {
    $stmt = $conn->prepare("INSERT INTO agama (id_agama, nama_agama) VALUES (?, ?)");
    $stmt->bind_param("is", $id_agama, $nama_agama);
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

// Fungsi untuk hapus agama
function deleteAgama($conn, $id_agama) {
    $stmt = $conn->prepare("DELETE FROM agama WHERE id_agama = ?");
    $stmt->bind_param("i", $id_agama);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Inisialisasi tabel dan data agama
createAgamaTable($conn);
insertInitialAgamaData($conn);

// Query dengan JOIN untuk mengambil nama jurusan dan agama
$sql = "SELECT s.*, j.nama_jurusan, a.nama_agama 
        FROM siswa s 
        LEFT JOIN jurusan j ON s.id_jurusan = j.id_jurusan 
        LEFT JOIN agama a ON s.id_agama = a.id_agama
        ORDER BY s.id";
$result = $conn->query($sql);

// Query untuk mendapatkan semua data agama
$agama_result = getAllAgama($conn);


?>
