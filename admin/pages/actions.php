<?php
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);

if (!$id || !$action) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

try {
    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM sites WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Deleted successfully']);
    } elseif ($action === 'toggle') {
        $stmt = $pdo->prepare("UPDATE sites SET status = IF(status='active','inactive','active') WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Status updated']);
    } elseif ($action === 'edit') {
        echo json_encode(['status' => 'success', 'message' => 'Edit action triggered']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
