<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$siteId = isset($_POST['site_id']) ? (int)$_POST['site_id'] : 0;
$response = ['success' => false, 'message' => 'Invalid site'];

if ($siteId > 0) {
    $stmt = $pdo->prepare("
        SELECT id, site_name, site_url, price, has_discount, discount_percent, discount_start, discount_end
        FROM sites
        WHERE id = ?
    ");
    $stmt->execute([$siteId]);
    $site = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($site) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Initialize variables
        $discountedPrice = (float)$site['price'];
        $isDiscountActive = false;

        // Get current time (same timezone as your DB)
        $now = new DateTime('now', new DateTimeZone('UTC'));

        // Parse dates safely (avoid format issues)
        $start = !empty($site['discount_start']) ? new DateTime($site['discount_start'], new DateTimeZone('UTC')) : null;
        $end   = !empty($site['discount_end']) ? new DateTime($site['discount_end'], new DateTimeZone('UTC')) : null;

        // Check if discount is valid and active
        if (
            (int)$site['has_discount'] === 1 &&
            (float)$site['discount_percent'] > 0 &&
            $start instanceof DateTime &&
            $end instanceof DateTime &&
            $now >= $start && $now <= $end
        ) {
            $isDiscountActive = true;
            $discountedPrice = round($site['price'] * (1 - ($site['discount_percent'] / 100)), 2);
        }

        // Store all data in session
        $_SESSION['cart'][$siteId] = [
            'id' => $site['id'],
            'site_name' => $site['site_name'],
            'site_url' => $site['site_url'],
            'price' => (float)$site['price'],
            'has_discount' => (int)$site['has_discount'],
            'discount_percent' => (float)$site['discount_percent'],
            'discount_start' => $site['discount_start'],
            'discount_end' => $site['discount_end'],
            'discount_active' => $isDiscountActive,
            'discounted_price' => $discountedPrice,
        ];

        $response = [
            'success' => true,
            'message' => $site['site_name'] . " added to cart",
            'cart_count' => count($_SESSION['cart'])
        ];
    } else {
        $response['message'] = 'Site not found';
    }
}

echo json_encode($response);
exit;
