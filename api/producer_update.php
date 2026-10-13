<?php
// =========================================================
// API UPDATE PRODUCER
// =========================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once 'config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$id    = intval($input['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}

$sql = "UPDATE producers SET 
            district_id = ?, name = ?, type = ?, owner_name = ?, 
            description = ?, address = ?, latitude = ?, longitude = ?, 
            phone = ?, whatsapp = ?, instagram = ?, photo = ?, status = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'isssssddsssssi',
    $input['district_id'],
    $input['name'],
    $input['type'],
    $input['owner_name'] ?? '',
    $input['description'] ?? '',
    $input['address'] ?? '',
    $input['latitude'],
    $input['longitude'],
    $input['phone'] ?? '',
    $input['whatsapp'] ?? '',
    $input['instagram'] ?? '',
    $input['photo'] ?? '',
    $input['status'] ?? 'active',
    $id
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil diupdate']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>