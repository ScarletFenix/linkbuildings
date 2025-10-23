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

    // Collect form data
    $site_id = $_POST['site_id'] ?? null;
    $total_before = isset($_POST['total_before']) ? (float)$_POST['total_before'] : 0;
    $final_total = isset($_POST['final_total']) ? (float)$_POST['final_total'] : 0;
    $target_url = trim($_POST['target_url'] ?? '');
    $anchor_text = trim($_POST['anchor_text'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'invoice';

    // Billing fields
    $billing_company = trim($_POST['billing_company'] ?? '');
    $billing_vat = trim($_POST['billing_vat'] ?? '');
    $billing_address = trim($_POST['billing_address'] ?? '');
    $billing_city = trim($_POST['billing_city'] ?? '');
    $billing_postal = trim($_POST['billing_postal'] ?? '');
    $billing_country = trim($_POST['billing_country'] ?? '');
    $billing_email = trim($_POST['billing_email'] ?? '');

    // ✅ Add Content fields
    $add_content = isset($_POST['add_content']) ? (int)$_POST['add_content'] : 0;

    // Only store content data if "Add Content" is checked
    $link_destination = $add_content ? trim($_POST['link_destination'] ?? '') : null;
    $topic_suggestion = $add_content ? trim($_POST['topic_suggestion'] ?? '') : null;
    $anchor_text_content = $add_content ? trim($_POST['anchor_text_content'] ?? '') : null;
    $trust_links = $add_content ? trim($_POST['trust_links'] ?? '') : null;

    // Validate essential fields
    if (empty($site_id) || empty($target_url) || empty($anchor_text) || empty($requirements)) {
        throw new Exception("Missing required fields.");
    }

    if (!in_array($payment_method, ['invoice', 'wise'])) {
        throw new Exception("Invalid payment method.");
    }

    $payment_status = 'pending';

    // Insert order into single `orders` table
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id,
            site_id,
            total_before,
            final_total,
            target_url,
            anchor_text,
            requirements,
            payment_method,
            payment_status,
            billing_company,
            billing_vat,
            billing_address,
            billing_city,
            billing_postal,
            billing_country,
            billing_email,
            add_content,
            link_destination,
            topic_suggestion,
            anchor_text_content,
            trust_links,
            created_at
        ) VALUES (
            :user_id,
            :site_id,
            :total_before,
            :final_total,
            :target_url,
            :anchor_text,
            :requirements,
            :payment_method,
            :payment_status,
            :billing_company,
            :billing_vat,
            :billing_address,
            :billing_city,
            :billing_postal,
            :billing_country,
            :billing_email,
            :add_content,
            :link_destination,
            :topic_suggestion,
            :anchor_text_content,
            :trust_links,
            NOW()
        )
    ");

    $stmt->execute([
        ':user_id' => $user_id,
        ':site_id' => $site_id,
        ':total_before' => $total_before,
        ':final_total' => $final_total,
        ':target_url' => $target_url,
        ':anchor_text' => $anchor_text,
        ':requirements' => $requirements,
        ':payment_method' => $payment_method,
        ':payment_status' => $payment_status,
        ':billing_company' => $billing_company,
        ':billing_vat' => $billing_vat,
        ':billing_address' => $billing_address,
        ':billing_city' => $billing_city,
        ':billing_postal' => $billing_postal,
        ':billing_country' => $billing_country,
        ':billing_email' => $billing_email,
        ':add_content' => $add_content,
        ':link_destination' => $link_destination,
        ':topic_suggestion' => $topic_suggestion,
        ':anchor_text_content' => $anchor_text_content,
        ':trust_links' => $trust_links
    ]);

    $orderId = $pdo->lastInsertId();
    unset($_SESSION['current_order_id']);

    echo json_encode([
        'success' => true,
        'message' => 'Order successfully created.',
        'order_id' => $orderId,
        'redirect' => '/linkbuildings/buyer/orders.php'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error processing order: ' . $e->getMessage()
    ]);
}
