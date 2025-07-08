<?php
// Start the session
session_start();

// Clear all session data
session_unset();
session_destroy();

// Clear any cookies related to the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect back to the index page
header('Location: index.php');
exit;
?>