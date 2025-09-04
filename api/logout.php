<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session itself
session_destroy();

// Force browser not to cache anything
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Pragma: no-cache");

// Redirect to login page
header('Location: ../login.php');
exit;
