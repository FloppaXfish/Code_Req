<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables - Complete wipe
$_SESSION = array();

// If it's a session timeout, we can pass a parameter
$timeout = isset($_GET['timeout']) ? true : false;

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Force clear any remaining session data
if (isset($_SESSION)) {
    unset($_SESSION);
}

// Clear all custom cookies
$cookies_to_clear = ['dr_admin_email', 'dr_admin_remember', 'dr_admin_id', 'dr_admin_name'];
foreach ($cookies_to_clear as $cookie) {
    if (isset($_COOKIE[$cookie])) {
        setcookie($cookie, '', time() - 3600, '/');
        setcookie($cookie, '', time() - 3600, '/', '', false, true);
    }
}

// Regenerate session ID to ensure clean start
session_regenerate_id(true);

// Redirect to login page
if ($timeout) {
    header('Location: ../login/login.php?timeout=1');
} else {
    header('Location: ../login/login.php');
}
exit();
?>