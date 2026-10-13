<?php
// =========================================================
// AUTH CHECK - Cek apakah user sudah login
// =========================================================
session_start();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /kopi-pekalongan/admin/login.php');
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    return [
        'id'   => $_SESSION['user_id']   ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'role' => $_SESSION['user_role'] ?? null
    ];
}
?>