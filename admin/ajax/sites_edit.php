<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid site ID"]);
    exit;
}

$fields = [
    'site_name'       => $_POST['site_name'] ?? '',
    'description'     => $_POST['description'] ?? '',
    'niche'           => $_POST['niche'] ?? '',
    'site_url'        => $_POST['site_url'] ?? '',
    'price'           => $_POST['price'] ?? 0,
    'dr'              => $_POST['dr'] ?? 0,
    'traffic'         => $_POST['traffic'] ?? 0,
    'country'         => $_POST['country'] ?? '',
    'backlinks'       => $_POST['backlinks'] ?? 0,
    // ✅ Discount fields
    'has_discount'    => isset($_POST['has_discount']) ? 1 : 0,
    'discount_start'  => $_POST['discount_start'] ?? null,
    'discount_end'    => $_POST['discount_end'] ?? null,
];

// ✅ Only add discount_percent if provided in POST
if (isset($_POST['discount_percent'])) {
    $fields['discount_percent'] = $_POST['discount_percent'] === '' 
        ? null 
        : (float)$_POST['discount_percent'];
}

// Handle optional image upload
if (!empty($_FILES['site_img']['name'])) {
    $uploadDir = __DIR__ . '/../../uploads/sites/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = time() . "_" . basename($_FILES['site_img']['name']);
    $target = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['site_img']['tmp_name'], $target)) {
        $fields['site_img'] = $filename;
    }
}

$setPart = implode(", ", array_map(fn($f) => "$f = :$f", array_keys($fields)));
$sql = "UPDATE sites SET $setPart WHERE id = :id";

$stmt = $pdo->prepare($sql);
$fields['id'] = $id;

try {
    $stmt->execute($fields);
    echo json_encode(["success" => true, "message" => "Site updated successfully"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Update failed: " . $e->getMessage()]);
}
