<?php
// Start the session
session_start();

// Log the auto-logout event
$logDir = __DIR__ . '/logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

// Get the username if available
$username = isset($_SESSION['github_user']) ? $_SESSION['github_user'] : 'Unknown';

// Log the auto-logout
$logFile = $logDir . '/auto_logout.log';
$timestamp = date('Y-m-d H:i:s');
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

$logMessage = sprintf(
    "[%s] Auto-logout - User: %s, IP: %s, User-Agent: %s\n",
    $timestamp,
    $username,
    $ipAddress,
    $userAgent
);

file_put_contents($logFile, $logMessage, FILE_APPEND);

// Clear all session data
session_unset();
session_destroy();

// Return a 204 No Content response
http_response_code(204);
?>