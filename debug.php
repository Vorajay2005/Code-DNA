<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

echo "<h1>GitHub Wrapped Debug Page</h1>";

// Check PHP version
echo "<h2>PHP Environment</h2>";
echo "<p>PHP version: " . phpversion() . "</p>";
echo "<p>Server software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p>Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
echo "<p>Script filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Unknown') . "</p>";
echo "<p>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";
echo "<p>PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'Unknown') . "</p>";
echo "<p>HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "</p>";
echo "<p>Full URL: " . (isset($_SERVER['HTTP_HOST']) ? 
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . 
    $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : 'Unknown') . "</p>";
echo "<p>Project path: " . dirname($_SERVER['PHP_SELF']) . "</p>";

// Check session status
echo "<h2>Session Status</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>Session is active</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
} else {
    echo "<p>Session is not active</p>";
    session_start();
    echo "<p>Started new session with ID: " . session_id() . "</p>";
}

// Check required directories
echo "<h2>Directory Status</h2>";
$directories = [
    'cache' => __DIR__ . '/cache',
    'logs' => __DIR__ . '/logs',
    'vendor' => __DIR__ . '/vendor',
    'includes' => __DIR__ . '/includes',
    'assets' => __DIR__ . '/assets'
];

foreach ($directories as $name => $path) {
    echo "<p>$name directory: ";
    if (is_dir($path)) {
        echo "Exists";
        echo is_writable($path) ? " (Writable)" : " (Not writable)";
    } else {
        echo "Does not exist";
    }
    echo "</p>";
}

// Check required files
echo "<h2>File Status</h2>";
$files = [
    'index.php' => __DIR__ . '/index.php',
    'auth_callback.php' => __DIR__ . '/auth_callback.php',
    'wrapped.php' => __DIR__ . '/wrapped.php',
    'auth.php' => __DIR__ . '/includes/auth.php',
    'config.php' => __DIR__ . '/includes/config.php',
    'composer.json' => __DIR__ . '/composer.json',
    'autoload.php' => __DIR__ . '/vendor/autoload.php'
];

foreach ($files as $name => $path) {
    echo "<p>$name: ";
    if (file_exists($path)) {
        echo "Exists";
        echo is_readable($path) ? " (Readable)" : " (Not readable)";
    } else {
        echo "Does not exist";
    }
    echo "</p>";
}

// Check GitHub OAuth configuration
echo "<h2>GitHub OAuth Configuration</h2>";
require_once __DIR__ . '/includes/config.php';

echo "<p>GITHUB_CLIENT_ID: " . (defined('GITHUB_CLIENT_ID') ? substr(GITHUB_CLIENT_ID, 0, 5) . '...' : 'Not defined') . "</p>";
echo "<p>GITHUB_CLIENT_SECRET: " . (defined('GITHUB_CLIENT_SECRET') ? 'Defined (hidden)' : 'Not defined') . "</p>";
echo "<p>GITHUB_REDIRECT_URI: " . (defined('GITHUB_REDIRECT_URI') ? GITHUB_REDIRECT_URI : 'Not defined') . "</p>";

// Test GitHub Auth URL generation
echo "<h2>GitHub Auth URL Test</h2>";
try {
    require_once __DIR__ . '/includes/auth.php';
    $authUrl = getGitHubAuthUrl();
    echo "<p>Auth URL generated successfully: " . htmlspecialchars($authUrl) . "</p>";
} catch (Exception $e) {
    echo "<p>Error generating auth URL: " . $e->getMessage() . "</p>";
}

// Check for Guzzle
echo "<h2>Guzzle HTTP Client</h2>";
if (class_exists('GuzzleHttp\Client')) {
    echo "<p>GuzzleHttp\Client class exists</p>";
    
    // Test a simple request to GitHub
    try {
        $client = new GuzzleHttp\Client();
        $response = $client->get('https://api.github.com/zen', [
            'headers' => [
                'User-Agent' => 'GitHub-Wrapped-Debug'
            ]
        ]);
        echo "<p>Test request to GitHub API successful</p>";
        echo "<p>Response: " . $response->getBody() . "</p>";
    } catch (Exception $e) {
        echo "<p>Error making test request: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>GuzzleHttp\Client class does not exist</p>";
}

// Display any active sessions
echo "<h2>Active Session Data</h2>";
if (!empty($_SESSION)) {
    echo "<ul>";
    foreach ($_SESSION as $key => $value) {
        if ($key === 'access_token') {
            echo "<li>$key: " . substr($value, 0, 5) . '...' . "</li>";
        } else {
            echo "<li>$key: " . (is_array($value) ? 'Array' : htmlspecialchars($value)) . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>No session data found</p>";
}

// Check for recent errors in the log
echo "<h2>Recent Error Logs</h2>";
$errorLog = __DIR__ . '/logs/php_errors.log';
if (file_exists($errorLog)) {
    $logContent = file_get_contents($errorLog);
    if (!empty($logContent)) {
        echo "<pre>" . htmlspecialchars(substr($logContent, -2000)) . "</pre>";
    } else {
        echo "<p>Error log exists but is empty</p>";
    }
} else {
    echo "<p>No error log file found</p>";
}
?>