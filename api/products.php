<?php
// =========================================================
// API: PRODUCTS (Produk Kopi)
// Endpoint: /api/products.php
// Method: GET
// =========================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once 'config/database.php';

// Parameter filter
$producer_id = isset($_GET['producer_id']) ? intval($_GET['producer_id']) : null;
$category    = isset($_GET['category']) ? $_GET['category'] : null;
$coffee_type = isset($_GET['coffee_type']) ? $_GET['coffee_type'] : null;

// Query dasar
$sql = "SELECT 
            pr.id,
            pr.producer_id,
            pr.name,
            pr.category,
            pr.coffee_type,
            pr.description,
            pr.price_range,
            pr.photo,
            p.name AS producer_name,
            p.type AS producer_type,
            d.name AS district_name
        FROM products pr
        JOIN producers p ON pr.producer_id = p.id
        JOIN districts d ON p.district_id = d.id
        WHERE p.status = 'active'";

$params = [];
$types  = '';

if ($producer_id) {
    $sql .= " AND pr.producer_id = ?";
    $params[] = $producer_id;
    $types .= 'i';
}

if ($category && in_array($category, ['green_bean', 'roasted_bean', 'bubuk', 'lainnya'])) {
    $sql .= " AND pr.category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($coffee_type) {
    $sql .= " AND pr.coffee_type = ?";
    $params[] = $coffee_type;
    $types .= 's';
}

$sql .= " ORDER BY pr.name ASC";

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

echo json_encode([
    'status' => 'success',
    'total'  => count($data),
    'data'   => $data
], JSON_PRETTY_PRINT);

$stmt->close();
$conn->close();
?>