<?php
// =========================================================
// API LOGIN
// =========================================================
session_start();
header('Content-Type: application/json');

require_once 'config/database.php';

$input    = json_decode(file_get_contents('php://input'), true);
$email    = $input['email'] ?? '';
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email dan password wajib diisi']);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan']);
    exit;
}

$user = $result->fetch_assoc();

// Cek password (mendukung plain & bcrypt)
$passwordOk = false;
if (password_verify($password, $user['password'])) {
    $passwordOk = true;
} elseif ($password === $user['password']) {
    $passwordOk = true; // fallback untuk data lama
}

if (!$passwordOk) {
    echo json_encode(['status' => 'error', 'message' => 'Password salah']);
    exit;
}

// Set session
$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];

echo json_encode([
    'status'  => 'success',
    'message' => 'Login berhasil',
    'user'    => [
        'id'   => $user['id'],
        'name' => $user['name'],
        'role' => $user['role']
    ]
]);

$stmt->close();
$conn->close();
?>