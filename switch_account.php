<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start or resume the session
session_start();

// Clear authentication-related session data
if (isset($_SESSION['access_token'])) {
    unset($_SESSION['access_token']);
}
if (isset($_SESSION['github_user'])) {
    unset($_SESSION['github_user']);
}
if (isset($_SESSION['github_avatar'])) {
    unset($_SESSION['github_avatar']);
}

// Set a special flag to force GitHub to show the login page
$_SESSION['force_github_login'] = true;

// Include the auth file to get the GitHub auth URL
require_once 'includes/auth.php';

// Generate a special GitHub auth URL that will force a new login
$authUrl = getGitHubAuthUrl(true); // Pass true to force login

// Redirect to GitHub's login page
header('Location: ' . $authUrl);
exit;
?>