<?php
// GitHub OAuth credentials
// To get these, go to GitHub > Settings > Developer settings > OAuth Apps > New OAuth App

// Use environment variables in production, fallback to localhost for development
define('GITHUB_CLIENT_ID', $_ENV['GITHUB_CLIENT_ID'] ?? 'Ov23lioFhnxcEhoeRPZC');
define('GITHUB_CLIENT_SECRET', $_ENV['GITHUB_CLIENT_SECRET'] ?? 'ebf8f4708540b324c9585b3d3a68cdd63787c03a');

// Set redirect URI based on environment
$app_url = $_ENV['APP_URL'] ?? 'http://localhost/projects/code-dna';
define('GITHUB_REDIRECT_URI', $app_url . '/auth_callback.php');

// Cache settings (to avoid GitHub API rate limits)
define('CACHE_ENABLED', true);
define('CACHE_DURATION', 3600); // 1 hour in seconds

// Database config (optional - for persistent storage)
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'code_dna');
?>