<?php
require_once 'includes/auth.php';

// Start output buffering to prevent headers already sent errors
ob_start();

// Start session if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Log the logout event
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logFile = $logDir . '/logout.log';
$timestamp = date('Y-m-d H:i:s');
$username = isset($_SESSION['github_user']['login']) ? $_SESSION['github_user']['login'] : 'unknown';
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

file_put_contents($logFile, 
    sprintf("[%s] Logout - User: %s, IP: %s, User-Agent: %s\n", 
        $timestamp, $username, $ip, $userAgent
    ), 
    FILE_APPEND | LOCK_EX
);

// Clear all session data
session_unset();
session_destroy();

// Start a new session and set flags for forced login
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['force_github_login'] = true;
$_SESSION['force_new_auth'] = true;

// Clear any cookies related to the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear any additional cookies that might be set
setcookie('github_token', '', time() - 42000, '/');
setcookie('github_user', '', time() - 42000, '/');

// Clear any GitHub-related cookies more thoroughly
$cookiesToClear = ['github_token', 'github_user', 'gh_sess', 'user_session', 'logged_in'];
foreach ($cookiesToClear as $cookie) {
    setcookie($cookie, '', time() - 42000, '/');
    setcookie($cookie, '', time() - 42000, '/', '.github.com');
}

// GitHub logout URL - this will log the user out from GitHub
$githubLogoutUrl = 'https://github.com/logout';

// Our application URL after logout (use environment variable for production)
$app_url = $_ENV['APP_URL'] ?? 'http://localhost/projects/code-dna';
$returnUrl = $app_url . '/index.php?logged_out=1';

// Create a form that will automatically submit to GitHub logout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging out...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .logout-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 400px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
        }
        .btn:hover {
            background: #5a6fd8;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="spinner"></div>
        <h2>Logging you out...</h2>
        <p>Please wait while we log you out from GitHub and clear your session.</p>
        
        <!-- Form to logout from GitHub -->
        <form id="githubLogoutForm" action="<?php echo $githubLogoutUrl; ?>" method="post" style="display: none;">
            <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($returnUrl); ?>">
        </form>
        
        <div style="margin-top: 2rem;">
            <p style="margin-bottom: 1rem;">
                <small>A new window will open to log you out from GitHub. Please complete the logout process in that window.</small>
            </p>
            <p>
                <small>If you're not redirected automatically, 
                <a href="<?php echo htmlspecialchars($returnUrl); ?>" class="btn">click here</a>
                </small>
            </p>
        </div>
    </div>

    <script>
        // Function to logout from GitHub
        function logoutFromGitHub() {
            // Open GitHub logout in a new window
            var logoutWindow = window.open('<?php echo $githubLogoutUrl; ?>', '_blank', 'width=500,height=600');
            
            // Check if window is closed (user completed logout)
            var checkClosed = setInterval(function() {
                if (logoutWindow.closed) {
                    clearInterval(checkClosed);
                    // After GitHub logout, redirect to our app
                    setTimeout(function() {
                        window.location.href = '<?php echo $returnUrl; ?>';
                    }, 1000);
                }
            }, 1000);
            
            // Fallback: close window and redirect after 10 seconds
            setTimeout(function() {
                if (!logoutWindow.closed) {
                    logoutWindow.close();
                }
                window.location.href = '<?php echo $returnUrl; ?>';
            }, 10000);
        }
        
        // Start logout process after page loads
        setTimeout(function() {
            logoutFromGitHub();
        }, 1500);
    </script>
</body>
</html>
<?php
// Flush output buffer
ob_end_flush();
?>