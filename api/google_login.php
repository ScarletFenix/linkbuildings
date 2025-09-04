<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoload
$config = require __DIR__ . '/../includes/.env.php'; // load credentials

$client = new Google\Client();
$client->setClientId($config['GOOGLE_CLIENT_ID']);
$client->setClientSecret($config['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($config['GOOGLE_REDIRECT_URI']);
$client->addScope("email");
$client->addScope("profile");

// Redirect to Google login page
header('Location: ' . filter_var($client->createAuthUrl(), FILTER_SANITIZE_URL));
exit;
