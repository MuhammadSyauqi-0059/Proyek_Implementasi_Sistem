<?php
// =========================================================
// API CREATE PRODUCER
// =========================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once 'config/database.php';

$input = json_decode(file_get_contents('php://input'), true);

$required = ['name', 'type', 'district_id', 'latitude', 'longitude'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Field '$field' wajib diisi"]);
        exit;
    }
}

$sql = "INSERT INTO producers 
        (district_id, name, type, owner_name, description, address, 
         latitude, longitude, phone, whatsapp, instagram, photo, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'isssssddsssss',
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
    $input['status'] ?? 'active'
);

if ($stmt->execute()) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Data berhasil disimpan',
        'id'      => $conn->insert_id
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>