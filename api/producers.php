<?php
// =========================================================
// API: PRODUCERS (Petani & UMKM Kopi)
// Endpoint: /api/producers.php
// Method: GET
// =========================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once 'config/database.php';

// Ambil parameter filter (opsional)
$district_id = isset($_GET['district_id']) ? intval($_GET['district_id']) : null;
$type        = isset($_GET['type']) ? $_GET['type'] : null;
$search      = isset($_GET['search']) ? $_GET['search'] : null;

// Bangun query dasar
$sql = "SELECT 
            p.id,
            p.name,
            p.type,
            p.owner_name,
            p.description,
            p.address,
            p.latitude,
            p.longitude,
            p.phone,
            p.whatsapp,
            p.instagram,
            p.photo,
            p.status,
            d.name AS district_name,
            r.name AS regency_name,
            prov.name AS province_name
        FROM producers p
        JOIN districts d ON p.district_id = d.id
        JOIN regencies r ON d.regency_id = r.id
        JOIN provinces prov ON r.province_id = prov.id
        WHERE p.status = 'active'";

// Tambahkan filter
$params = [];
$types  = '';

if ($district_id) {
    $sql .= " AND p.district_id = ?";
    $params[] = $district_id;
    $types .= 'i';
}

if ($type && in_array($type, ['petani', 'umkm'])) {
    $sql .= " AND p.type = ?";
    $params[] = $type;
    $types .= 's';
}

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.owner_name LIKE ?)";
    $searchParam = '%' . $search . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ss';
}

$sql .= " ORDER BY p.name ASC";

// Prepare & execute
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Response
echo json_encode([
    'status' => 'success',
    'total'  => count($data),
    'data'   => $data
], JSON_PRETTY_PRINT);

$stmt->close();
$conn->close();
?>