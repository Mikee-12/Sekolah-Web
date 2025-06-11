<?php
include 'koneksi.php';

// Set header untuk JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id_agama = intval($_POST['id']);
    
    // Ambil nama agama sebelum dihapus untuk response
    $check_query = "SELECT nama_agama FROM agama WHERE id_agama = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $id_agama);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $religion = $check_result->fetch_assoc();
        $nama_agama = $religion['nama_agama'];
        
        // Cek apakah agama masih digunakan oleh siswa
        $usage_check = "SELECT COUNT(*) as count FROM siswa WHERE id_agama = ?";
        $usage_stmt = $conn->prepare($usage_check);
        $usage_stmt->bind_param("i", $id_agama);
        $usage_stmt->execute();
        $usage_result = $usage_stmt->get_result();
        $usage_row = $usage_result->fetch_assoc();
        
        if ($usage_row['count'] > 0) {
            echo json_encode([
                'success' => false,
                'message' => "Agama '$nama_agama' tidak dapat dihapus karena masih digunakan oleh {$usage_row['count']} siswa!"
            ]);
        } else {
            // Hapus data agama menggunakan fungsi dari konektor
            if (deleteAgama($conn, $id_agama)) {
                echo json_encode([
                    'success' => true,
                    'message' => "Data agama '$nama_agama' berhasil dihapus!"
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menghapus data agama!'
                ]);
            }
        }
        
        $usage_stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data agama tidak ditemukan!'
        ]);
    }
    
    $check_stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Request tidak valid! Method harus POST dan parameter id diperlukan.'
    ]);
}

$conn->close();
?>