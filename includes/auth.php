<?php
// includes/auth.php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

// Expose user data
$userId    = $_SESSION['user_id'];
$userName  = $_SESSION['user_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
$userRole  = $_SESSION['user_role'] ?? '';

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

    if (!in_array($userRole, $roles)) {
        // Optional: different redirect based on role
        if ($userRole === 'admin') {
            header("Location: ./platform.php");
        } else {
            header("Location: ./dashboard.php");
        }
        exit();
    }
}
