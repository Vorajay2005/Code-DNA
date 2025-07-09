<?php
// Simple admin authentication
session_start();

// Set a simple admin password - in a real application, use a more secure method
$admin_password = "github_wrapped_admin";

// Check if user is already logged in as admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Handle login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['is_admin'] = true;
        $is_admin = true;
    } else {
        $error = "Invalid password";
    }
}

// Old logout handling removed - now handled by logout.php

// Function to get log data
function getLogData() {
    $logFile = __DIR__ . '/logs/user_logins.log';
    if (file_exists($logFile)) {
        return file_get_contents($logFile);
    }
    return "No login data available.";
}

// Function to clear logs
function clearLogs() {
    $logFile = __DIR__ . '/logs/user_logins.log';
    if (file_exists($logFile)) {
        file_put_contents($logFile, '');
        return true;
    }
    return false;
}

// Handle clear logs action
if ($is_admin && isset($_POST['clear_logs'])) {
    clearLogs();
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | GitHub Wrapped</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #0d1117;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #30363d;
        }
        
        .login-form {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #0d1117;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #c9d1d9;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #30363d;
            border-radius: 4px;
            background-color: #0d1117;
            color: #c9d1d9;
        }
        
        .log-container {
            background-color: #161b22;
            padding: 15px;
            border-radius: 4px;
            max-height: 500px;
            overflow-y: auto;
            font-family: monospace;
            white-space: pre-wrap;
            color: #c9d1d9;
        }
        
        .error {
            color: #f85149;
            margin-bottom: 15px;
        }
        
        .admin-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <?php if (!$is_admin): ?>
    <div class="login-form">
        <h2>Admin Login</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
    <?php else: ?>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <div>
                <a href="index.php" class="btn btn-outline"><i class="fas fa-home"></i> Home</a>
                <a href="logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <h2>User Login History</h2>
        <div class="log-container">
            <?= nl2br(htmlspecialchars(getLogData())) ?>
        </div>
        
        <div class="admin-actions">
            <form method="post">
                <input type="hidden" name="clear_logs" value="1">
                <button type="submit" class="btn btn-outline" onclick="return confirm('Are you sure you want to clear all logs?');">
                    <i class="fas fa-trash"></i> Clear Logs
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>