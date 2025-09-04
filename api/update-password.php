<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['token']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
    header("Location: /linkbuildings/login.php?error=invalid_request");
    exit;
}

$token = $_POST['token'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($password !== $confirm) {
    header("Location: /linkbuildings/reset-password.php?token=" . urlencode($token) . "&status=error");
    exit;
}

// Verify token
$stmt = $pdo->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset || strtotime($reset['expires_at']) < time()) {
    header("Location: /linkbuildings/login.php?error=expired_token");
    exit;
}

// Update user password
$hashed = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$hashed, $reset['email']]);

// Remove used token
$stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
$stmt->execute([$token]);

// Redirect to login
header("Location: /linkbuildings/login.php?status=reset_success");
exit;
