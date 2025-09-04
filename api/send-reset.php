<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../includes/db.php';
$config = require __DIR__ . '/../includes/.env.php';

// Import PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// 1. Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['email'])) {
    header('Location: /linkbuildings/forgot-password.php?status=error');
    exit;
}

$email = trim($_POST['email']);

// 2. Check if email exists in DB
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// Always pretend success (don’t expose valid/invalid email)
if (!$user) {
    header('Location: /linkbuildings/forgot-password.php?status=sent');
    exit;
}

// 3. Generate token
$token   = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

// 4. Insert into password_resets
$stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
$stmt->execute([$email, $token, $expires]);

// 5. Build reset link
$resetLink = $config['APP_URL'] . "/reset-password.php?token=" . $token;

// 6. Send email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $config['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['SMTP_USER'];
    $mail->Password   = $config['SMTP_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $config['SMTP_PORT'];

    // Gmail requires FROM = same as SMTP_USER
    $mail->setFrom($config['SMTP_USER'], 'Linkbuildings');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Password Reset Request - Linkbuildings";
    $mail->Body    = "
        <p>Hi,</p>
        <p>We received a request to reset your password.</p>
        <p><a href='$resetLink'>Click here to reset your password</a></p>
        <p>If you didn’t request this, ignore this email.</p>
        <br>
        <p>- Linkbuildings Team</p>
    ";
    $mail->AltBody = "Reset your password: $resetLink";

    $mail->send();
} catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}");
    header('Location: /linkbuildings/forgot-password.php?status=error');
    exit;
}

// 7. Redirect back
header('Location: /linkbuildings/forgot-password.php?status=sent');
exit;
