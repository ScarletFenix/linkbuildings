<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php'; // Composer autoload
require_once './includes/db.php';

use Google\Client;
use Google\Service\Oauth2;

$config = require __DIR__ . '/includes/.env.php';

$client = new Client();
$client->setClientId($config['GOOGLE_CLIENT_ID']);
$client->setClientSecret($config['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($config['GOOGLE_REDIRECT_URI']);
$client->addScope("email");
$client->addScope("profile");

if (!isset($_GET['code'])) {
    exit('No code parameter found in callback.');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    exit('Google login failed: ' . $token['error_description']);
}

$client->setAccessToken($token['access_token']);
$oauth = new Oauth2($client);
$googleUser = $oauth->userinfo->get();

$email = $googleUser->email;
$name  = $googleUser->name;

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Create new user as buyer by default
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'buyer')");
    $stmt->execute([$name, $email, password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT)]);
    $userId = $pdo->lastInsertId();

    // Fetch new user record
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Set session
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role']  = $user['role'];

// Redirect based on role
if ($user['role'] === 'admin') {
    header('Location: ./platform.php');
} else {
    header('Location: ./dashboard.php');
}
exit;
        