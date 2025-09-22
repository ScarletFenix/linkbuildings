<?php
session_start();
require_once '../includes/db.php';
$config = require '../includes/.env.php';  // ✅ load config for captcha

$email = trim($_POST['email']);
$password = $_POST['password'];

// --- CAPTCHA Verification ---
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
$secret = $config['GOOGLE_RECAPTCHA_SECRET_KEY'];

$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha_response");
$captcha_success = json_decode($verify);

if (!$captcha_success->success) {
    header("Location: ../login.php?error=captcha");
    exit;
}

// --- Rate limiting ---
$maxAttempts = 5;
$lockoutTime = 600; // 10 minutes in seconds

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['first_attempt_time'] = time();
}

$elapsed = time() - $_SESSION['first_attempt_time'];

if ($elapsed > $lockoutTime) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['first_attempt_time'] = time();
}

if ($_SESSION['login_attempts'] >= $maxAttempts) {
    header("Location: ../login.php?error=rate_limit");
    exit;
}

// --- User lookup ---
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // ✅ Store user info
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];  // make sure 'role' column exists

    $_SESSION['login_attempts'] = 0; // reset attempts

    // ✅ Redirect based on role
    if ($user['role'] === 'admin') {
        header("Location: ../admin/platform.php");
    } else {
        // Default: buyer
        header("Location: ../dashboard.php");
    }
    exit;
} else {
    $_SESSION['login_attempts'] += 1;
    header("Location: ../login.php?error=invalid");
    exit;
}
