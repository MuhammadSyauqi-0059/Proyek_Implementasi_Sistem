<?php
// =========================================================
// API: STATS (Statistik Ringkasan)
// Endpoint: /api/stats.php
// Method: GET
// =========================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config/database.php';

$stats = [];

// Total petani
$r = $conn->query("SELECT COUNT(*) AS total FROM producers WHERE type = 'petani' AND status = 'active'");
$stats['total_petani'] = $r->fetch_assoc()['total'];

// Total UMKM
$r = $conn->query("SELECT COUNT(*) AS total FROM producers WHERE type = 'umkm' AND status = 'active'");
$stats['total_umkm'] = $r->fetch_assoc()['total'];

// Total produk
$r = $conn->query("SELECT COUNT(*) AS total FROM products");
$stats['total_produk'] = $r->fetch_assoc()['total'];

// Total kecamatan yang punya produsen
$r = $conn->query("SELECT COUNT(DISTINCT district_id) AS total FROM producers WHERE status = 'active'");
$stats['total_kecamatan'] = $r->fetch_assoc()['total'];

echo json_encode([
    'status' => 'success',
    'data'   => $stats
], JSON_PRETTY_PRINT);

$conn->close();
?>