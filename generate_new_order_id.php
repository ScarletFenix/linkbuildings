<?php
session_start();

// Generate a new random Order ID
$new_order_id = 'ORD' . strtoupper(substr(uniqid(), -6));

// Store in session for next order
$_SESSION['current_order_id'] = $new_order_id;

// Return JSON for JavaScript
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'new_order_id' => $new_order_id
]);
?>
