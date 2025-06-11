<?php
header('Content-Type: application/json');
include 'koneksi.php';

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
    exit;
}

// Ambil ID jurusan dari POST data
$id_jurusan = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_jurusan <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID jurusan tidak valid'
    ]);
    exit;
}

try {
    // Cek apakah jurusan masih digunakan oleh siswa
    $check_query = "SELECT COUNT(*) as count FROM siswa WHERE id_jurusan = ?";
    $check_stmt = $conn->prepare($check_query);
    
    if (!$check_stmt) {
        throw new Exception("Error preparing check statement: " . $conn->error);
    }
    
    $check_stmt->bind_param("i", $id_jurusan);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();
    $check_stmt->close();
    
    // Jika masih ada siswa yang menggunakan jurusan ini
    if ($check_row['count'] > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Tidak dapat menghapus jurusan karena masih digunakan oleh ' . $check_row['count'] . ' siswa'
        ]);
        exit;
    }
    
    // Ambil nama jurusan sebelum menghapus untuk pesan konfirmasi
    $select_query = "SELECT nama_jurusan FROM jurusan WHERE id_jurusan = ?";
    $select_stmt = $conn->prepare($select_query);
    
    if (!$select_stmt) {
        throw new Exception("Error preparing select statement: " . $conn->error);
    }
    
    $select_stmt->bind_param("i", $id_jurusan);
    $select_stmt->execute();
    $select_result = $select_stmt->get_result();
    $jurusan_data = $select_result->fetch_assoc();
    $select_stmt->close();
    
    if (!$jurusan_data) {
        echo json_encode([
            'success' => false,
            'message' => 'Data jurusan tidak ditemukan'
        ]);
        exit;
    }
    
    $nama_jurusan = $jurusan_data['nama_jurusan'];
    
    // Hapus data jurusan
    $delete_query = "DELETE FROM jurusan WHERE id_jurusan = ?";
    $delete_stmt = $conn->prepare($delete_query);
    
    if (!$delete_stmt) {
        throw new Exception("Error preparing delete statement: " . $conn->error);
    }
    
    $delete_stmt->bind_param("i", $id_jurusan);
    
    if ($delete_stmt->execute()) {
        if ($delete_stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Data jurusan "' . $nama_jurusan . '" berhasil dihapus'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Data jurusan tidak ditemukan atau sudah terhapus'
            ]);
        }
    } else {
        throw new Exception("Error executing delete: " . $delete_stmt->error);
    }
    
    $delete_stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    // Tutup koneksi database
    if (isset($conn)) {
        $conn->close();
    }
}
?>