<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'includes/auth.php';

// Old logout handling removed - now handled by logout.php

// Check if this is a new login request
if (isset($_GET['new_login']) || isset($_POST['timestamp'])) {
    // Clear all session data
    session_unset();
    
    // Set a flag in the session to force a new GitHub authorization
    $_SESSION['force_new_auth'] = true;
}

// Check if user is already logged in
if (isLoggedIn() && !isset($_GET['new_login']) && !isset($_GET['logged_out'])) {
    // If they're already logged in, redirect to wrapped.php
    header('Location: wrapped.php');
    exit;
}

// If this is a new login request or logout, we'll show the landing page
// We don't need to clear the session here as that would break the auth flow
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GitHub Wrapped | Your Coding Year in Review</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fab fa-github logo-icon"></i>
            <h1>GitHub Wrapped</h1>
            <p class="tagline">Discover your coding story</p>
        </div>
        
        <?php if (isset($_GET['logged_out'])): ?>
        <div class="logout-success">
            <i class="fas fa-check-circle"></i>
            <p>Successfully logged out! You can now login with a different GitHub account.</p>
        </div>
        <?php endif; ?>
        
        <div class="login-features">
            <div class="feature">
                <i class="fas fa-chart-pie"></i>
                <h3>Language Analysis</h3>
                <p>See which languages you use most</p>
            </div>
            <div class="feature">
                <i class="fas fa-calendar-alt"></i>
                <p>Discover your most productive days and times</p>
            </div>
            <div class="feature">
                <i class="fas fa-share-alt"></i>
                <h3>Shareable Stats</h3>
                <p>Create images to share your coding journey</p>
            </div>
        </div>
        
        <a href="<?= getGitHubAuthUrl(isset($_GET['logged_out']) || isset($_SESSION['force_github_login'])) ?>" class="github-login-btn">
            <i class="fab fa-github"></i>
            Connect with GitHub
        </a>
        
        <p class="privacy-note">
            <i class="fas fa-lock"></i> We only request read access to your public repositories
        </p>
    </div>
    
    
</body>
</html>