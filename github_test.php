<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

require_once 'vendor/autoload.php';
require_once 'includes/config.php';

echo "<h1>GitHub API Test</h1>";

// Test GitHub API connection without authentication
echo "<h2>Testing GitHub API (Unauthenticated)</h2>";
try {
    $client = new GuzzleHttp\Client();
    $response = $client->get('https://api.github.com/zen', [
        'headers' => [
            'User-Agent' => 'GitHub-Wrapped-Test'
        ]
    ]);
    
    echo "<p>Status Code: " . $response->getStatusCode() . "</p>";
    echo "<p>Response: " . $response->getBody() . "</p>";
    echo "<p style='color:green'>✓ Basic GitHub API connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error connecting to GitHub API: " . $e->getMessage() . "</p>";
}

// Test GitHub OAuth configuration
echo "<h2>GitHub OAuth Configuration</h2>";
echo "<p>GITHUB_CLIENT_ID: " . (defined('GITHUB_CLIENT_ID') ? substr(GITHUB_CLIENT_ID, 0, 5) . '...' : 'Not defined') . "</p>";
echo "<p>GITHUB_CLIENT_SECRET: " . (defined('GITHUB_CLIENT_SECRET') ? 'Defined (hidden)' : 'Not defined') . "</p>";
echo "<p>GITHUB_REDIRECT_URI: " . (defined('GITHUB_REDIRECT_URI') ? GITHUB_REDIRECT_URI : 'Not defined') . "</p>";

// Generate a test OAuth URL
echo "<h2>Test OAuth URL</h2>";
$state = bin2hex(random_bytes(16));
$params = [
    'client_id' => GITHUB_CLIENT_ID,
    'redirect_uri' => GITHUB_REDIRECT_URI,
    'scope' => 'user repo',
    'state' => $state
];
$authUrl = 'https://github.com/login/oauth/authorize?' . http_build_query($params);

// Show the redirect URI
echo "<p>Redirect URI: " . GITHUB_REDIRECT_URI . "</p>";
echo "<p>Make sure this matches what you registered in your GitHub OAuth App settings!</p>";
echo "<p>Generated URL: <a href='" . htmlspecialchars($authUrl) . "' target='_blank'>" . htmlspecialchars($authUrl) . "</a></p>";

// Check if we have an access token in the session
echo "<h2>Session Access Token</h2>";
session_start();
if (isset($_SESSION['access_token'])) {
    echo "<p>Access token found in session: " . substr($_SESSION['access_token'], 0, 5) . '...' . "</p>";
    
    // Test the token
    echo "<h2>Testing Access Token</h2>";
    try {
        $client = new GuzzleHttp\Client();
        $response = $client->get('https://api.github.com/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $_SESSION['access_token'],
                'User-Agent' => 'GitHub-Wrapped-Test',
                'Accept' => 'application/vnd.github.v3+json'
            ]
        ]);
        
        $user = json_decode($response->getBody(), true);
        echo "<p>Status Code: " . $response->getStatusCode() . "</p>";
        echo "<p>Username: " . ($user['login'] ?? 'Unknown') . "</p>";
        echo "<p style='color:green'>✓ Access token is valid</p>";
        
        // Display rate limit info
        $rateLimitResponse = $client->get('https://api.github.com/rate_limit', [
            'headers' => [
                'Authorization' => 'Bearer ' . $_SESSION['access_token'],
                'User-Agent' => 'GitHub-Wrapped-Test',
                'Accept' => 'application/vnd.github.v3+json'
            ]
        ]);
        
        $rateLimit = json_decode($rateLimitResponse->getBody(), true);
        echo "<h3>Rate Limit Info</h3>";
        echo "<pre>" . print_r($rateLimit, true) . "</pre>";
        
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ Error testing access token: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>No access token found in session</p>";
    echo "<p>You need to <a href='index.php'>log in</a> first</p>";
}

// Display session data
echo "<h2>All Session Data</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";
?>