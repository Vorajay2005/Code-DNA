<?php
require_once 'includes/auth.php';

// Ensure the user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized', 'message' => 'You must be logged in to access this API']);
    exit;
}

$token = $_SESSION['access_token'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$username = isset($_SESSION['github_user']) ? $_SESSION['github_user'] : '';

// Set the content type to JSON
header('Content-Type: application/json');

try {
    switch ($action) {
        case 'user':
            // Get basic user info
            $user = fetchGitHubData('user', $token);
            echo json_encode($user);
            break;
            
        case 'languages':
            // Get language distribution
            $languages = getUserLanguages($token);
            echo json_encode($languages);
            break;
            
        case 'stats':
            // Get contribution stats
            $stats = getUserContributionStats($token, $username);
            echo json_encode($stats);
            break;
            
        case 'commits':
            // Get recent commits
            $since = isset($_GET['since']) ? $_GET['since'] : date('Y-m-d', strtotime('-1 year'));
            $commits = getUserCommits($token, $username, $since);
            echo json_encode($commits);
            break;
            
        case 'pull_requests':
            // Get pull requests
            $since = isset($_GET['since']) ? $_GET['since'] : date('Y-m-d', strtotime('-1 year'));
            $prs = getUserPullRequests($token, $username, $since);
            echo json_encode($prs);
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action', 'message' => 'The requested action is not supported']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'API Error', 'message' => $e->getMessage()]);
}
?>