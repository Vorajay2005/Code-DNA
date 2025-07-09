<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$token = $_SESSION['access_token'];

try {
    // Fetch user data
    $user = fetchGitHubData('user', $token);

    if (isset($user['error'])) {
        throw new Exception("Error fetching user data: " . $user['error']);
    }

    $username = $user['login'];

    // Get user's languages
    $languages = getUserLanguages($token);
    if (empty($languages)) {
        $languages = ['No Data' => 100]; // Fallback if no languages found
    }

    // Get user's contribution stats
    $stats = getUserContributionStats($token, $username);
    
    // Get pull requests
    $pullRequests = getUserPullRequests($token, $username);
    $prCount = isset($pullRequests['total_count']) ? $pullRequests['total_count'] : 0;

    // Find most active day and hour
    $mostActiveDay = array_search(max($stats['day_of_week']), $stats['day_of_week']);
    $mostActiveHour = array_search(max($stats['hour_of_day']), $stats['hour_of_day']);

    // Format the hour in 12-hour format with AM/PM
    $formattedHour = date('g A', strtotime("$mostActiveHour:00"));

    // Get top 3 languages
    $topLanguages = array_slice($languages, 0, 3, true);

    // Prepare data for JavaScript
    $jsLanguages = json_encode(array_keys($languages));
    $jsLanguageValues = json_encode(array_values($languages));
    $jsDayStats = json_encode(array_values($stats['day_of_week']));
    $jsHourStats = json_encode($stats['hour_of_day']);
    
} catch (Exception $e) {
    error_log("Error in wrapped.php: " . $e->getMessage());
    
    // Set default values for the template
    $username = $_SESSION['github_user'] ?? 'GitHub User';
    $user = [
        'avatar_url' => 'https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png',
        'login' => $username
    ];
    
    $languages = ['No Data' => 100];
    $jsLanguages = json_encode(array_keys($languages));
    $jsLanguageValues = json_encode(array_values($languages));
    
    $stats = [
        'total_commits' => 0,
        'longest_streak' => 0,
        'day_of_week' => array_fill(0, 7, 0),
        'hour_of_day' => array_fill(0, 24, 0)
    ];
    
    $jsDayStats = json_encode(array_values($stats['day_of_week']));
    $jsHourStats = json_encode($stats['hour_of_day']);
    
    $prCount = 0;
    $mostActiveDay = 'Unknown';
    $formattedHour = 'Unknown';
    $topLanguages = [];
    
    // Display error message to the user
    $errorMessage = "There was an error fetching your GitHub data. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GitHub Wrapped | <?= htmlspecialchars($username) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php if (isset($errorMessage)): ?>
        <div class="error-message-container">
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <header class="dashboard-header">
            <div class="user-info">
                <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Profile" class="avatar">
                <div>
                    <h1>@<?= htmlspecialchars($username) ?>'s GitHub Wrapped</h1>
                    <p>Your coding year in review</p>
                </div>
            </div>
            <div class="actions">
                <button id="shareBtn" class="btn"><i class="fas fa-share-alt"></i> Share</button>
                <a href="logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php if (in_array($username, ['shehzad-epuratech', 'shehzad-epura'])): ?>
                <a href="admin.php" class="btn btn-outline"><i class="fas fa-user-shield"></i> Admin</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="dashboard-grid">
            <!-- Top Stats Cards -->
            <div class="stat-card highlight-card">
                <div class="stat-icon"><i class="fas fa-code-branch"></i></div>
                <div class="stat-content">
                    <h3>Commit Count</h3>
                    <div class="stat-value"><?= number_format($stats['total_commits']) ?></div>
                    <p>commits in the last year</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-fire"></i></div>
                <div class="stat-content">
                    <h3>Longest Streak</h3>
                    <div class="stat-value"><?= $stats['longest_streak'] ?> days</div>
                    <p>of consecutive coding</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-code-merge"></i></div>
                <div class="stat-content">
                    <h3>Pull Requests</h3>
                    <div class="stat-value"><?= $prCount ?></div>
                    <p>opened this year</p>
                </div>
            </div>

            <!-- Language Distribution -->
            <div class="chart-card">
                <h3>Language Distribution</h3>
                <div class="chart-container">
                    <canvas id="languageChart"></canvas>
                </div>
                <div class="chart-insight">
                    <?php if (!empty($topLanguages)): ?>
                        <p>Your top language is <strong><?= key($topLanguages) ?></strong> at <?= current($topLanguages) ?>%</p>
                    <?php else: ?>
                        <p>No language data available</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Coding Patterns -->
            <div class="chart-card">
                <h3>When You Code</h3>
                <div class="chart-container">
                    <canvas id="activityChart"></canvas>
                </div>
                <div class="chart-insight">
                    <p>You code most on <strong><?= $mostActiveDay ?></strong> at around <strong><?= $formattedHour ?></strong></p>
                </div>
            </div>

            <!-- Weekly Activity -->
            <div class="chart-card">
                <h3>Weekly Activity</h3>
                <div class="chart-container">
                    <canvas id="weeklyChart"></canvas>
                </div>
                <div class="chart-insight">
                    <p>Your most productive day is <strong><?= $mostActiveDay ?></strong></p>
                </div>
            </div>

            <!-- Hourly Activity -->
            <div class="chart-card">
                <h3>Daily Rhythm</h3>
                <div class="chart-container">
                    <canvas id="hourlyChart"></canvas>
                </div>
                <div class="chart-insight">
                    <p>You're a <strong><?= ($mostActiveHour >= 5 && $mostActiveHour < 12) ? 'morning person' : (($mostActiveHour >= 12 && $mostActiveHour < 18) ? 'afternoon coder' : 'night owl') ?></strong>!</p>
                </div>
            </div>
        </div>

        <div class="share-container" id="shareContainer">
            <div class="share-options">
                <button id="downloadBtn" class="btn"><i class="fas fa-download"></i> Download Image</button>
                <button id="twitterBtn" class="btn btn-twitter"><i class="fab fa-twitter"></i> Share on Twitter</button>
                <button id="closeShareBtn" class="btn btn-outline"><i class="fas fa-times"></i> Close</button>
            </div>
            <div id="sharePreview"></div>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const languageLabels = <?= $jsLanguages ?>;
        const languageData = <?= $jsLanguageValues ?>;
        const dayOfWeekData = <?= $jsDayStats ?>;
        const hourOfDayData = <?= $jsHourStats ?>;
        const username = "<?= htmlspecialchars($username) ?>";
        
        // Set up session timeout (30 minutes of inactivity)
        let sessionTimeout;
        const SESSION_TIMEOUT_DURATION = 30 * 60 * 1000; // 30 minutes in milliseconds
        
        function resetSessionTimeout() {
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(function() {
                // Redirect to logout page after timeout
                window.location.href = 'logout.php?reason=timeout';
            }, SESSION_TIMEOUT_DURATION);
        }
        
        // Reset timeout on user activity
        ['click', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(function(event) {
            document.addEventListener(event, resetSessionTimeout, false);
        });
        
        // Initialize the session timeout
        resetSessionTimeout();
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>