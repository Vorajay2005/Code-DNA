<?php
// GitHub OAuth credentials
// To get these, go to GitHub > Settings > Developer settings > OAuth Apps > New OAuth App
define('GITHUB_CLIENT_ID', 'Ov23lioFhnxcEhoeRPZC');
define('GITHUB_CLIENT_SECRET', 'ebf8f4708540b324c9585b3d3a68cdd63787c03a');

// Set a fixed redirect URI that matches what you registered with GitHub
define('GITHUB_REDIRECT_URI', 'http://localhost/projects/code-dna/auth_callback.php');

// Cache settings (to avoid GitHub API rate limits)
define('CACHE_ENABLED', true);
define('CACHE_DURATION', 3600); // 1 hour in seconds

// Database config (optional - for persistent storage)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'code_dna');
?>