<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$email = $_GET['email'] ?? '';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$stmt->execute([$email]);
$count = $stmt->fetchColumn();

echo json_encode(['exists' => $count > 0]);
