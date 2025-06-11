<?php
include 'koneksi.php';

// Set header untuk JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Mulai transaksi
    $conn->begin_transaction();
    
    try {
        // Ambil nama siswa sebelum dihapus untuk response
        $check_query = "SELECT nama FROM siswa WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $student = $check_result->fetch_assoc();
            $nama_siswa = $student['nama'];
            
            // Hapus data siswa
            $delete_query = "DELETE FROM siswa WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("i", $id);
            
            if ($delete_stmt->execute()) {
                // Cek apakah tabel sudah kosong
                $count_result = $conn->query("SELECT COUNT(*) as total FROM siswa");
                $count_row = $count_result->fetch_assoc();
                
                // Jika tabel kosong, reset auto-increment
                if ($count_row['total'] == 0) {
                    $conn->query("ALTER TABLE siswa AUTO_INCREMENT = 1");
                }
                
                $conn->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => "Data siswa '$nama_siswa' berhasil dihapus!"
                ]);
            } else {
                $conn->rollback();
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menghapus data siswa!'
                ]);
            }
            
            $delete_stmt->close();
        } else {
            $conn->rollback();
            echo json_encode([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan!'
            ]);
        }
        
        $check_stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Request tidak valid!'
    ]);
}

$conn->close();
?>