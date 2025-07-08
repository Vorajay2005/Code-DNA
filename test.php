<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

echo "<h1>PHP Test Page</h1>";
echo "<p>PHP version: " . phpversion() . "</p>";

// Test if sessions work
session_start();
$_SESSION['test'] = 'Session test';
echo "<p>Session test: " . ($_SESSION['test'] ?? 'Failed') . "</p>";

// Test if includes work
echo "<p>Testing includes:</p>";
try {
    require_once 'includes/config.php';
    echo "<p>Config file loaded successfully</p>";
    echo "<p>GITHUB_CLIENT_ID: " . (defined('GITHUB_CLIENT_ID') ? 'Defined' : 'Not defined') . "</p>";
    echo "<p>GITHUB_REDIRECT_URI: " . (defined('GITHUB_REDIRECT_URI') ? GITHUB_REDIRECT_URI : 'Not defined') . "</p>";
} catch (Exception $e) {
    echo "<p>Error loading config: " . $e->getMessage() . "</p>";
}

// Test if composer autoload works
echo "<p>Testing composer autoload:</p>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "<p>Composer autoload loaded successfully</p>";
    
    // Test if Guzzle is available
    if (class_exists('GuzzleHttp\Client')) {
        echo "<p>GuzzleHttp\Client class exists</p>";
    } else {
        echo "<p>GuzzleHttp\Client class does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p>Error loading composer autoload: " . $e->getMessage() . "</p>";
}

// Check if cache directory exists and is writable
$cacheDir = __DIR__ . '/cache';
echo "<p>Cache directory: " . $cacheDir . "</p>";
echo "<p>Cache directory exists: " . (is_dir($cacheDir) ? 'Yes' : 'No') . "</p>";
echo "<p>Cache directory is writable: " . (is_writable($cacheDir) ? 'Yes' : 'No') . "</p>";

// Check if logs directory exists and is writable
$logsDir = __DIR__ . '/logs';
echo "<p>Logs directory: " . $logsDir . "</p>";
echo "<p>Logs directory exists: " . (is_dir($logsDir) ? 'Yes' : 'No') . "</p>";
echo "<p>Logs directory is writable: " . (is_writable($logsDir) ? 'Yes' : 'No') . "</p>";
?>