<?php
session_start();
require_once 'config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

function getGitHubAuthUrl($forceLogin = false) {
    $_SESSION['github_state'] = bin2hex(random_bytes(16));
    
    $params = [
        'client_id' => GITHUB_CLIENT_ID,
        'redirect_uri' => GITHUB_REDIRECT_URI,
        'scope' => 'user repo',
        'state' => $_SESSION['github_state']
    ];
    
    // Only force login when explicitly requested via the Switch Account button
    // or when the force_github_login flag is set
    $shouldForceLogin = $forceLogin || 
                       (isset($_SESSION['force_github_login']) && $_SESSION['force_github_login']);
    
    if ($shouldForceLogin) {
        // These parameters help force GitHub to show the login screen
        $params['login'] = '';  // Force GitHub to show the login form
        $params['allow_signup'] = 'true';
        
        // Add a timestamp to make the URL unique each time to prevent caching
        $params['t'] = time();
        
        // Add a special parameter to force GitHub to show the login page
        // This is the key parameter that ensures GitHub shows the login page
        $params['prompt'] = 'consent';
        
        // Clear the force_github_login flag
        unset($_SESSION['force_github_login']);
    }
    
    // Don't clear force_new_auth here as it's needed for the auth flow
    
    return 'https://github.com/login/oauth/authorize?' . http_build_query($params);
}

function fetchGitHubData($endpoint, $token, $params = []) {
    // Check cache first if enabled
    $cacheKey = md5($endpoint . json_encode($params) . $token);
    $cachedData = getCachedData($cacheKey);
    
    if (CACHE_ENABLED && $cachedData !== false) {
        return $cachedData;
    }
    
    try {
        $client = new Client();
        $response = $client->get("https://api.github.com/$endpoint", [
            'headers' => [
                'Authorization' => "Bearer $token",
                'User-Agent' => 'GitHub-Wrapped-App',
                'Accept' => 'application/vnd.github.v3+json'
            ],
            'query' => $params
        ]);
        
        $data = json_decode($response->getBody(), true);
        
        // Cache the result
        if (CACHE_ENABLED) {
            cacheData($cacheKey, $data);
        }
        
        return $data;
    } catch (RequestException $e) {
        $errorMsg = "GitHub API Error: " . $e->getMessage();
        error_log($errorMsg);
        
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody(), true);
            
            error_log("Status Code: $statusCode");
            error_log("Response: " . print_r($responseBody, true));
            
            return [
                'error' => $errorMsg,
                'status_code' => $statusCode,
                'response' => $responseBody
            ];
        }
        
        return ['error' => $errorMsg];
    } catch (Exception $e) {
        $errorMsg = "General Error: " . $e->getMessage();
        error_log($errorMsg);
        return ['error' => $errorMsg];
    }
}

function fetchAllPages($endpoint, $token, $params = []) {
    $allData = [];
    $page = 1;
    $perPage = 100; // Max allowed by GitHub
    
    do {
        $pageParams = array_merge($params, ['page' => $page, 'per_page' => $perPage]);
        $data = fetchGitHubData($endpoint, $token, $pageParams);
        
        if (isset($data['error']) || !is_array($data) || empty($data)) {
            break;
        }
        
        $allData = array_merge($allData, $data);
        $page++;
    } while (count($data) == $perPage);
    
    return $allData;
}

function getUserCommits($token, $username, $since = null) {
    if ($since === null) {
        $since = date('Y-m-d', strtotime('-1 year'));
    }
    
    // First get user's repos
    $repos = fetchAllPages('user/repos', $token, ['sort' => 'updated']);
    
    $allCommits = [];
    foreach ($repos as $repo) {
        // Skip forks to focus on original work
        if ($repo['fork']) continue;
        
        $repoName = $repo['name'];
        $repoOwner = $repo['owner']['login'];
        
        // Only fetch commits for repos owned by the user
        if ($repoOwner !== $username) continue;
        
        $commits = fetchAllPages("repos/$repoOwner/$repoName/commits", $token, [
            'author' => $username,
            'since' => $since . 'T00:00:00Z'
        ]);
        
        foreach ($commits as $commit) {
            // Add repo info to each commit
            $commit['repo'] = [
                'name' => $repoName,
                'owner' => $repoOwner
            ];
            $allCommits[] = $commit;
        }
    }
    
    return $allCommits;
}

function getUserLanguages($token) {
    $repos = fetchAllPages('user/repos', $token, ['sort' => 'updated']);
    
    $languages = [];
    $totalBytes = 0;
    
    foreach ($repos as $repo) {
        // Skip forks to focus on original work
        if ($repo['fork']) continue;
        
        $repoLanguages = fetchGitHubData("repos/{$repo['full_name']}/languages", $token);
        
        foreach ($repoLanguages as $language => $bytes) {
            if (!isset($languages[$language])) {
                $languages[$language] = 0;
            }
            $languages[$language] += $bytes;
            $totalBytes += $bytes;
        }
    }
    
    // Convert to percentages
    $percentages = [];
    foreach ($languages as $language => $bytes) {
        $percentages[$language] = round(($bytes / $totalBytes) * 100, 1);
    }
    
    // Sort by percentage (descending)
    arsort($percentages);
    
    return $percentages;
}

function getUserPullRequests($token, $username, $since = null) {
    if ($since === null) {
        $since = date('Y-m-d', strtotime('-1 year'));
    }
    
    return fetchAllPages('search/issues', $token, [
        'q' => "author:$username type:pr created:>=$since",
        'sort' => 'created',
        'order' => 'desc'
    ]);
}

function getUserContributionStats($token, $username) {
    $commits = getUserCommits($token, $username);
    
    // Group commits by day of week
    $dayOfWeekStats = [
        'Sunday' => 0,
        'Monday' => 0,
        'Tuesday' => 0,
        'Wednesday' => 0,
        'Thursday' => 0,
        'Friday' => 0,
        'Saturday' => 0
    ];
    
    // Group commits by hour of day
    $hourOfDayStats = array_fill(0, 24, 0);
    
    // Track commit streak
    $dateCommits = [];
    $currentStreak = 0;
    $longestStreak = 0;
    
    foreach ($commits as $commit) {
        if (isset($commit['commit']['author']['date'])) {
            $date = new DateTime($commit['commit']['author']['date']);
            
            // Day of week
            $dayOfWeek = $date->format('l');
            $dayOfWeekStats[$dayOfWeek]++;
            
            // Hour of day
            $hour = (int)$date->format('G');
            $hourOfDayStats[$hour]++;
            
            // Streak calculation
            $dateStr = $date->format('Y-m-d');
            $dateCommits[$dateStr] = isset($dateCommits[$dateStr]) ? $dateCommits[$dateStr] + 1 : 1;
        }
    }
    
    // Calculate longest streak
    if (!empty($dateCommits)) {
        ksort($dateCommits);
        $dates = array_keys($dateCommits);
        $currentStreak = 1;
        $longestStreak = 1;
        
        for ($i = 1; $i < count($dates); $i++) {
            $current = new DateTime($dates[$i]);
            $previous = new DateTime($dates[$i-1]);
            $diff = $current->diff($previous);
            
            if ($diff->days == 1) {
                $currentStreak++;
                $longestStreak = max($longestStreak, $currentStreak);
            } else if ($diff->days > 1) {
                $currentStreak = 1;
            }
        }
    }
    
    return [
        'day_of_week' => $dayOfWeekStats,
        'hour_of_day' => $hourOfDayStats,
        'longest_streak' => $longestStreak,
        'current_streak' => $currentStreak,
        'total_commits' => count($commits)
    ];
}

// Simple file-based caching functions
function getCachedData($key) {
    $cacheFile = __DIR__ . '/../cache/' . $key . '.json';
    
    if (!file_exists($cacheFile)) {
        return false;
    }
    
    $cacheTime = filemtime($cacheFile);
    if (time() - $cacheTime > CACHE_DURATION) {
        return false;
    }
    
    return json_decode(file_get_contents($cacheFile), true);
}

function cacheData($key, $data) {
    $cacheDir = __DIR__ . '/../cache';
    
    if (!is_dir($cacheDir)) {
        if (!mkdir($cacheDir, 0755, true)) {
            error_log("Failed to create cache directory: $cacheDir");
            return false;
        }
    }
    
    if (!is_writable($cacheDir)) {
        error_log("Cache directory is not writable: $cacheDir");
        return false;
    }
    
    $cacheFile = $cacheDir . '/' . $key . '.json';
    return file_put_contents($cacheFile, json_encode($data)) !== false;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['access_token']) && !empty($_SESSION['access_token']);
}
?>