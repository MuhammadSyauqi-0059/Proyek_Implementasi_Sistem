<?php
// =========================================================
// KONFIGURASI DATABASE
// =========================================================

$host = 'localhost';
$user = 'root';
$pass = '';           // Default XAMPP kosong
$db   = 'kopi_pekalongan';

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal: ' . $conn->connect_error
    ]);
    exit;
}

// Set charset
$conn->set_charset('utf8mb4');
?>