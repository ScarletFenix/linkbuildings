<?php
session_start();
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['site_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing site ID.'
        ]);
        exit;
    }

    $_SESSION['checkout_site_id'] = (int) $data['site_id'];

    echo json_encode([
        'success' => true,
        'message' => 'Site ID stored successfully.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Unexpected error occurred.'
    ]);
}
