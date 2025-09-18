<?php
require_once __DIR__ . '/../../includes/db.php';


header('Content-Type: application/json');

$response = ['success' => false, 'errors' => [], 'message' => ''];

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = "Invalid request method";
    echo json_encode($response);
    exit;
}

// Collect inputs
$siteName    = trim($_POST['site_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$niche       = trim($_POST['niche'] ?? '');
$siteUrl     = trim($_POST['site_url'] ?? '');
$price       = $_POST['price'] ?? '';
$dr          = $_POST['dr'] ?? '';
$traffic     = $_POST['traffic'] ?? '';
$country     = trim($_POST['country'] ?? '');
$backlinks   = $_POST['backlinks'] ?? '';

$errors = [];

// Validate
if ($siteName === '') $errors['site_name'] = "Site Name is required";
if ($description === '') $errors['description'] = "Description is required";
if (str_word_count($description) > 500) $errors['description'] = "Description cannot exceed 500 words";
if ($niche === '') $errors['niche'] = "Select at least one niche";
if ($siteUrl === '') $errors['site_url'] = "Site URL is required";
if ($price === '' || $price < 0) $errors['price'] = "Price must be zero or positive";
if ($dr === '' || $dr < 0) $errors['dr'] = "DR must be zero or positive";
if ($traffic === '' || $traffic < 0) $errors['traffic'] = "Traffic must be zero or positive";
if ($country === '') $errors['country'] = "Country is required";
if ($backlinks === '' || $backlinks < 0) $errors['backlinks'] = "Backlinks must be zero or positive";

$imgName = null;

// Handle upload
if (!empty($_FILES['site_img']['tmp_name']) && is_uploaded_file($_FILES['site_img']['tmp_name'])) {
    $imgFile = $_FILES['site_img'];
    if ($imgFile['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($imgFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if (!in_array($ext, $allowed)) {
            $errors['site_img'] = "Invalid image format";
        } else {
            $imgName = uniqid("site_") . "." . $ext;
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/linkbuildings/uploads/sites/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            if (!move_uploaded_file($imgFile['tmp_name'], $targetDir . $imgName)) {
                $errors['site_img'] = "Failed to move uploaded image. Check permissions.";
            }
        }
    } else {
        $errors['site_img'] = "Error uploading image";
    }
}

if (empty($errors)) {
    $stmt = $pdo->prepare("INSERT INTO sites 
        (site_name, description, niche, site_url, price, dr, traffic, country, backlinks, status, site_img) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)");
    $stmt->execute([$siteName, $description, $niche, $siteUrl, $price, $dr, $traffic, $country, $backlinks, $imgName]);

    $response['success'] = true;
    $response['message'] = "✅ Site added successfully!";
} else {
    $response['errors'] = $errors;
    $response['message'] = "❌ Please fix the highlighted errors.";
}

echo json_encode($response);
exit;
