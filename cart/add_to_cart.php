<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$siteId = $_POST['site_id'] ?? 0;
$siteId = (int) $siteId;

$response = ['success' => false, 'message' => 'Invalid site'];

if ($siteId > 0) {
    $stmt = $pdo->prepare("SELECT id, site_name, site_url, price FROM sites WHERE id = ?");
    $stmt->execute([$siteId]);
    $site = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($site) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (!isset($_SESSION['cart'][$siteId])) {
            $_SESSION['cart'][$siteId] = $site;
        }

        $response = [
            'success' => true,
            'message' => $site['site_name'] . " added to cart",
            'cart_count' => count($_SESSION['cart'])
        ];
    }
}

echo json_encode($response);
exit;
