<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

require_once 'includes/auth.php';

// Function to display error page
function showErrorPage($title, $message, $details = '') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?> | GitHub Wrapped</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <style>
            .auth-error {
                max-width: 600px;
                margin: 100px auto;
                text-align: center;
                padding: 30px;
                background: var(--card-bg);
                border-radius: 8px;
                border: 1px solid var(--danger-color);
            }
            .auth-error i {
                font-size: 3rem;
                color: var(--danger-color);
                margin-bottom: 20px;
            }
            .error-details {
                margin-top: 20px;
                text-align: left;
                background: rgba(0,0,0,0.2);
                padding: 15px;
                border-radius: 4px;
                font-family: monospace;
                white-space: pre-wrap;
                overflow-x: auto;
            }
        </style>
    </head>
    <body>
        <div class="auth-error">
            <i class="fas fa-exclamation-triangle"></i>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p><?= htmlspecialchars($message) ?></p>
            
            <?php if (!empty($details)): ?>
            <div class="error-details">
                <?= htmlspecialchars($details) ?>
            </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px;">
                <a href="index.php" class="btn">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="debug.php" class="btn btn-outline">
                    <i class="fas fa-bug"></i> Debug Info
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Verify state parameter to prevent CSRF attacks
if (!isset($_GET['state']) || !isset($_SESSION['github_state']) || $_GET['state'] !== $_SESSION['github_state']) {
    error_log("CSRF validation failed: State mismatch or missing");
    showErrorPage(
        "Security Error", 
        "Invalid state parameter. This could be a cross-site request forgery attempt.",
        "GET state: " . ($_GET['state'] ?? 'Not set') . "\nSession state: " . ($_SESSION['github_state'] ?? 'Not set')
    );
}

if (isset($_GET['code'])) {
    try {
        $client = new GuzzleHttp\Client();
        $response = $client->post('https://github.com/login/oauth/access_token', [
            'form_params' => [
                'client_id' => GITHUB_CLIENT_ID,
                'client_secret' => GITHUB_CLIENT_SECRET,
                'code' => $_GET['code'],
                'redirect_uri' => GITHUB_REDIRECT_URI
            ],
            'headers' => ['Accept' => 'application/json']
        ]);

        $data = json_decode($response->getBody(), true);
        
        if (isset($data['access_token'])) {
            // Don't clear the entire session as it contains important state
            // Just clear specific auth-related flags
            if (isset($_SESSION['force_new_auth'])) {
                unset($_SESSION['force_new_auth']);
            }
            if (isset($_SESSION['force_github_login'])) {
                unset($_SESSION['force_github_login']);
            }
            
            // Store the new access token
            $_SESSION['access_token'] = $data['access_token'];
            
            // Fetch user data to store in session
            $user = fetchGitHubData('user', $data['access_token']);
            if (isset($user['login'])) {
                $_SESSION['github_user'] = $user['login'];
                $_SESSION['github_avatar'] = $user['avatar_url'];
                
                // Log successful authentication
                error_log("User authenticated successfully: " . $user['login']);
                
                // Log user login to a file with timestamp
                $logDir = __DIR__ . '/logs';
                if (!file_exists($logDir)) {
                    mkdir($logDir, 0755, true);
                }
                
                $logFile = $logDir . '/user_logins.log';
                $timestamp = date('Y-m-d H:i:s');
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                
                $logMessage = sprintf(
                    "[%s] User: %s, IP: %s, User-Agent: %s\n",
                    $timestamp,
                    $user['login'],
                    $ipAddress,
                    $userAgent
                );
                
                file_put_contents($logFile, $logMessage, FILE_APPEND);
                
                header('Location: wrapped.php');
                exit;
            } else {
                error_log("Failed to fetch user data after authentication: " . print_r($user, true));
                showErrorPage(
                    "Authentication Error", 
                    "Failed to fetch your GitHub profile after authentication.",
                    "API Response: " . print_r($user, true)
                );
            }
        } else if (isset($data['error'])) {
            error_log("GitHub OAuth Error: " . ($data['error_description'] ?? $data['error']));
            showErrorPage(
                "GitHub OAuth Error", 
                $data['error_description'] ?? $data['error'],
                "Full response: " . print_r($data, true)
            );
        } else {
            error_log("Unknown error during GitHub authentication: " . print_r($data, true));
            showErrorPage(
                "Authentication Error", 
                "Unknown error during GitHub authentication.",
                "Response data: " . print_r($data, true)
            );
        }
    } catch (Exception $e) {
        error_log("Exception during GitHub authentication: " . $e->getMessage());
        showErrorPage(
            "Authentication Error", 
            "Error during GitHub authentication: " . $e->getMessage(),
            "Stack trace: " . $e->getTraceAsString()
        );
    }
} else if (isset($_GET['error'])) {
    error_log("GitHub OAuth Error from callback: " . ($_GET['error_description'] ?? $_GET['error']));
    showErrorPage(
        "GitHub OAuth Error", 
        $_GET['error_description'] ?? $_GET['error'],
        "Error: " . $_GET['error'] . "\nDescription: " . ($_GET['error_description'] ?? 'None')
    );
} else {
    error_log("Authorization failed: No code received in callback");
    showErrorPage(
        "Authorization Failed", 
        "No authorization code received from GitHub.",
        "GET parameters: " . print_r($_GET, true)
    );
}
?>