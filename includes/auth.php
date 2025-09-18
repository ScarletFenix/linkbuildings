<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

// Session fixation protection (only once per login ideally)
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Optional: bind session to browser family (not full User-Agent)
$currentUA = $_SERVER['HTTP_USER_AGENT'] ?? '';
function getBrowserFamily($ua) {
    if (stripos($ua, 'chrome') !== false) return 'chrome';
    if (stripos($ua, 'safari') !== false) return 'safari';
    if (stripos($ua, 'firefox') !== false) return 'firefox';
    if (stripos($ua, 'edge') !== false) return 'edge';
    if (stripos($ua, 'opera') !== false || stripos($ua, 'opr') !== false) return 'opera';
    return 'other';
}

$currentFamily = getBrowserFamily($currentUA);
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $currentFamily;
} elseif ($_SESSION['user_agent'] !== $currentFamily) {
    session_unset();
    session_destroy();
    header("Location: ./login.php");
    exit();
}

// Expose user data
$userId    = $_SESSION['user_id'];
$userName  = $_SESSION['user_name']  ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
$userRole  = $_SESSION['user_role']  ?? 'buyer'; // default is buyer

/**
 * Restrict access to certain roles.
 *
 * @param string|array $roles Allowed role(s)
 */
function requireRole($roles) {
    global $userRole;

    if (is_string($roles)) {
        $roles = [$roles];
    }

    if (!in_array($userRole, $roles, true)) {
        // Redirect based on role
        if ($userRole === 'admin') {
            header("Location: ./platform.php");
        } elseif ($userRole === 'buyer') {
            header("Location: ./dashboard.php");
        } else {
            header("Location: ./login.php");
        }
        exit();
    }
}
