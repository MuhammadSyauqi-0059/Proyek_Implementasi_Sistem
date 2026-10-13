<?php
// =========================================================
// API DELETE PRODUCER (Soft delete - set status inactive)
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

$stmt = $conn->prepare("UPDATE producers SET status = 'inactive' WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>