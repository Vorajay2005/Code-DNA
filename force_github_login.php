<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start or resume the session
session_start();

try {
    // Log the request
    error_log("Switch Account request received");
    
    // Clear only authentication-related session data
    if (isset($_SESSION['access_token'])) {
        unset($_SESSION['access_token']);
        error_log("Cleared access_token from session");
    }
    if (isset($_SESSION['github_user'])) {
        unset($_SESSION['github_user']);
        error_log("Cleared github_user from session");
    }
    if (isset($_SESSION['github_avatar'])) {
        unset($_SESSION['github_avatar']);
        error_log("Cleared github_avatar from session");
    }
    
    // Set a special flag to force GitHub to show the login page
    $_SESSION['force_github_login'] = true;
    error_log("Set force_github_login flag in session");
    
    // Include the auth file to get the GitHub auth URL
    require_once 'includes/auth.php';
    
    // Generate a special GitHub auth URL that will force a new login
    $authUrl = getGitHubAuthUrl(true); // Pass true to force login
    error_log("Generated GitHub auth URL: " . $authUrl);
    
    // Return the URL as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'url' => $authUrl
    ]);
} catch (Exception $e) {
    // Log the error
    error_log("Error in force_github_login.php: " . $e->getMessage());
    
    // Return an error response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>