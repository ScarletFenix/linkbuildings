<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireRole('buyer');

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated.');
    }

    $user_id = $_SESSION['user_id'];

    // ✅ Collect form data
    $site_id = $_POST['site_id'] ?? null;
    $total_before = isset($_POST['total_before']) ? (float)$_POST['total_before'] : 0;
    $final_total = isset($_POST['final_total']) ? (float)$_POST['final_total'] : 0;
    $target_url = trim($_POST['target_url'] ?? '');
    $anchor_text = trim($_POST['anchor_text'] ?? '');
    $requirements = trim($_POST['requirements'] ?? ''); // optional
    $payment_method = $_POST['payment_method'] ?? 'invoice';

    // ✅ Billing fields
    $billing_company = trim($_POST['billing_company'] ?? '');
    $billing_vat = trim($_POST['billing_vat'] ?? '');
    $billing_address = trim($_POST['billing_address'] ?? '');
    $billing_city = trim($_POST['billing_city'] ?? '');
    $billing_postal = trim($_POST['billing_postal'] ?? '');
    $billing_country = trim($_POST['billing_country'] ?? '');
    $billing_email = trim($_POST['billing_email'] ?? '');

    // ✅ Validate essential fields
    if (empty($site_id) || empty($target_url) || empty($anchor_text)) {
        throw new Exception("Missing required fields.");
    }

    if (!in_array($payment_method, ['invoice', 'wise'])) {
        throw new Exception("Invalid payment method.");
    }

    // ✅ Generate or reuse order ID
    $order_identifier = $_SESSION['current_order_id'] ?? 'ORD' . strtoupper(substr(uniqid(), -6));

    // ✅ Define statuses [pending, InProgress, Completed, Rejected]
    $order_status = 'pending'; // default
    $payment_status = 'pending'; // default

    // ✅ Insert order into `orders` table
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id,
            site_id,
            order_identifier,
            total_before,
            final_total,
            target_url,
            anchor_text,
            requirements,
            payment_method,
            payment_status,
            order_status,
            billing_company,
            billing_vat,
            billing_address,
            billing_city,
            billing_postal,
            billing_country,
            billing_email,
            created_at
        ) VALUES (
            :user_id,
            :site_id,
            :order_identifier,
            :total_before,
            :final_total,
            :target_url,
            :anchor_text,
            :requirements,
            :payment_method,
            :payment_status,
            :order_status,
            :billing_company,
            :billing_vat,
            :billing_address,
            :billing_city,
            :billing_postal,
            :billing_country,
            :billing_email,
            NOW()
        )
    ");

    $stmt->execute([
        ':user_id' => $user_id,
        ':site_id' => $site_id,
        ':order_identifier' => $order_identifier,
        ':total_before' => $total_before,
        ':final_total' => $final_total,
        ':target_url' => $target_url,
        ':anchor_text' => $anchor_text,
        ':requirements' => $requirements,
        ':payment_method' => $payment_method,
        ':payment_status' => $payment_status,
        ':order_status' => $order_status,
        ':billing_company' => $billing_company,
        ':billing_vat' => $billing_vat,
        ':billing_address' => $billing_address,
        ':billing_city' => $billing_city,
        ':billing_postal' => $billing_postal,
        ':billing_country' => $billing_country,
        ':billing_email' => $billing_email
    ]);

    $orderId = $pdo->lastInsertId();
    unset($_SESSION['current_order_id']);

    echo json_encode([
        'success' => true,
        'message' => 'Order successfully created.',
        'order_id' => $orderId,
        'order_identifier' => $order_identifier,
        'redirect' => '/linkbuildings/buyer/orders.php'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error processing order: ' . $e->getMessage()
    ]);
}
