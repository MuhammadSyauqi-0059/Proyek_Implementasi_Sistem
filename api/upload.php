<?php
// =========================================================
// API UPLOAD FOTO
// =========================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_FILES['photo'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada file']);
    exit;
}

$file     = $_FILES['photo'];
$allowed  = ['jpg', 'jpeg', 'png', 'webp'];
$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Format file tidak didukung']);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal 5 MB']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = 'kopi_' . time() . '_' . uniqid() . '.' . $ext;
$target   = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode([
        'status'   => 'success',
        'filename' => $filename,
        'url'      => '/kopi-pekalongan/uploads/' . $filename
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal upload']);
}
?>