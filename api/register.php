<?php
session_start();
require_once '../includes/db.php';

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';


// 1. Basic Checks
if (!$name || !$email || !$password) {
    exit("All fields are required.");
}
if (strlen($name) < 3 || strlen($name) > 30 || !preg_match('/^[A-Za-z0-9_]+$/', $name)) {
    exit("Invalid username. Use 3–30 chars, letters/numbers/underscore only.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Invalid email format.");
}
if (strlen($password) < 6 || strlen($password) > 64) {
    exit("Password must be 6–64 characters.");
}

// 2. Check for duplicates
$checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$checkEmail->execute([$email]);
if ($checkEmail->rowCount() > 0) {
    exit("This email is already registered.");
}

$checkName = $pdo->prepare("SELECT id FROM users WHERE name = ?");
$checkName->execute([$name]);
if ($checkName->rowCount() > 0) {
    exit("This username is already taken.");
}

// 3. Insert new user
$hashedPw = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

try {
    $stmt->execute([$name, $email, $hashedPw]);
    header("Location: ../login.php");
    exit;
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    exit("Unexpected server error. Please try again later.");
}
