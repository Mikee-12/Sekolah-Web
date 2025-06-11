<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root"; 
$pass = "";     
$db   = "sekolah"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$sql = "SELECT id, nama_jurusan FROM jurusan ORDER BY nama_jurusan ASC";
$result = $conn->query($sql);

$jurusan = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $jurusan[] = $row;
    }
}

echo json_encode($jurusan);
$conn->close();
?>