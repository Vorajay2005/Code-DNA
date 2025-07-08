<?php
$errorCode = isset($_GET['code']) ? intval($_GET['code']) : 404;

$errorMessages = [
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Page Not Found',
    500 => 'Internal Server Error',
    503 => 'Service Unavailable'
];

$errorMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : 'Unknown Error';
$errorDescription = '';

switch ($errorCode) {
    case 404:
        $errorDescription = "The page you're looking for doesn't exist.";
        break;
    case 403:
        $errorDescription = "You don't have permission to access this resource.";
        break;
    case 500:
        $errorDescription = "Something went wrong on our end. Please try again later.";
        break;
    default:
        $errorDescription = "An unexpected error occurred.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?= $errorCode ?> | GitHub Wrapped</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .error-container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
            padding: 40px;
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        
        .error-icon {
            font-size: 60px;
            margin-bottom: 20px;
            color: var(--danger-color);
        }
        
        .error-code {
            font-size: 3rem;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        
        .error-description {
            margin-bottom: 30px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="error-code"><?= $errorCode ?></div>
        <div class="error-message"><?= htmlspecialchars($errorMessage) ?></div>
        <div class="error-description"><?= htmlspecialchars($errorDescription) ?></div>
        <a href="index.php" class="btn">
            <i class="fas fa-home"></i> Back to Home
        </a>
    </div>
</body>
</html>