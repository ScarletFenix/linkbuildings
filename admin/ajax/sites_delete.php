<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid site ID']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM sites WHERE id = ?");
if ($stmt->execute([$id])) {
    echo json_encode(['success' => true, 'message' => 'Site deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete site']);
}
