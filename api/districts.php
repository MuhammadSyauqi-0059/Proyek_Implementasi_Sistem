<?php
// =========================================================
// API: DISTRICTS (Kecamatan)
// Endpoint: /api/districts.php
// Method: GET
// =========================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config/database.php';

$sql = "SELECT 
            d.id,
            d.name AS district_name,
            r.name AS regency_name,
            prov.name AS province_name,
            (SELECT COUNT(*) FROM producers p WHERE p.district_id = d.id AND p.status = 'active') AS total_producers
        FROM districts d
        JOIN regencies r ON d.regency_id = r.id
        JOIN provinces prov ON r.province_id = prov.id
        ORDER BY d.name ASC";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'status' => 'success',
    'total'  => count($data),
    'data'   => $data
], JSON_PRETTY_PRINT);

$conn->close();
?>