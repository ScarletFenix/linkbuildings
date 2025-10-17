<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/auth.php';

// -------------------- AUTH CHECK --------------------
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die(json_encode(["success" => false, "message" => "Access denied. You must be logged in."]));
}

$user_id = $_SESSION['user_id'];

// -------------------- PROCESS POST --------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["success" => false, "message" => "Invalid request."]));
}

// ✅ Get posted items from form
$cart = $_POST['items'] ?? [];
if (empty($cart)) {
    die(json_encode(["success" => false, "message" => "Cart is empty or invalid."]));
}

// ✅ Capture extra form info
$other_instructions = $_POST['other_instructions'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'invoice';

// ✅ Billing Info
$billing_company  = $_POST['billing_company'] ?? '';
$billing_vat      = $_POST['billing_vat'] ?? '';
$billing_address  = $_POST['billing_address'] ?? '';
$billing_city     = $_POST['billing_city'] ?? '';
$billing_postal   = $_POST['billing_postal'] ?? '';
$billing_country  = $_POST['billing_country'] ?? '';
$billing_email    = $_POST['billing_email'] ?? '';

// ✅ Totals (from frontend or calculated)
$total_before   = isset($_POST['total_before']) ? (float)$_POST['total_before'] : 0;
$discount_total = isset($_POST['discount_amount']) ? (float)$_POST['discount_amount'] : 0;
$grand_total    = isset($_POST['final_total']) ? (float)$_POST['final_total'] : 0;

// -------------------- VALIDATE TOTALS --------------------
// If no frontend totals were passed, fallback to backend calculation
if ($total_before === 0 && $grand_total === 0) {
    $total_before = 0;
    $discount_total = 0;
    $grand_total = 0;

    foreach ($cart as &$item) {
        $price = (float)($item['price'] ?? 0);
        $discount_amount = (float)($item['discount_amount'] ?? 0);
        $final_price = (float)($item['final_price'] ?? $price);
        $content_added = !empty($item['linkDestination']) ? 1 : 0;

        if ($content_added) {
            $final_price += 20;
        }

        $total_before += $price;
        $discount_total += $discount_amount;
        $grand_total += $final_price;

        // store updated values back
        $item['content_added'] = $content_added;
        $item['final_price'] = $final_price;
    }
}

// -------------------- DATABASE INSERTS --------------------
try {
    $pdo->beginTransaction();

    // ✅ Insert into orders
    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, total_before, discount_amount, final_total, other_instructions, payment_method,
         billing_company, billing_vat, billing_address, billing_city, billing_postal, billing_country, billing_email)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->execute([
        $user_id,
        $total_before,
        $discount_total,
        $grand_total,
        $other_instructions,
        $payment_method,
        $billing_company,
        $billing_vat,
        $billing_address,
        $billing_city,
        $billing_postal,
        $billing_country,
        $billing_email
    ]);

    $order_id = $pdo->lastInsertId();

    // ✅ Insert order items
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items 
        (order_id, site_name, site_url, price, discount_amount, final_price, content_added, 
         link_destination, topic_suggestion, anchor, trust_link)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)
    ");

    foreach ($cart as $item) {
        $stmtItem->execute([
            $order_id,
            $item['site_name'] ?? '',
            $item['site_url'] ?? '',
            (float)($item['price'] ?? 0),
            (float)($item['discount_amount'] ?? 0),
            (float)($item['final_price'] ?? 0),
            (int)($item['content_added'] ?? 0),
            $item['linkDestination'] ?? null,
            $item['topicSuggestion'] ?? null,
            $item['anchor'] ?? null,
            $item['trustLink'] ?? null
        ]);
    }

    $pdo->commit();

    // ✅ Clear cart
    unset($_SESSION['cart']);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => "Order submitted successfully.",
        'total' => $grand_total
    ]);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error processing order: " . $e->getMessage()
    ]);
    exit;
}
?>
