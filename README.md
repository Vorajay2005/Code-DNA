# GitHub Wrapped

A PHP-based web application that creates a personalized "year in review" for GitHub users, similar to Spotify Wrapped. This application analyzes your GitHub activity and presents it in a visually appealing format with charts and statistics.

## Features

### 📊 **Analytics Dashboard**

- **Language Distribution**: Visual breakdown of programming languages you've used
- **Contribution Statistics**: Total commits, issues, and pull requests
- **Activity Patterns**: Weekly and hourly activity heatmaps
- **Top Repositories**: Most active repositories with commit counts
- **Coding Habits**: Most active day of the week and hour of the day

### 🔐 **Authentication & Security**

- **GitHub OAuth Integration**: Secure login with GitHub
- **Session Management**: Proper session handling with automatic logout
- **Enhanced Logout**: Complete logout from both app and GitHub
- **Admin Panel**: Administrative access for monitoring user activity

### 🎨 **User Interface**

- **Responsive Design**: Works on desktop and mobile devices
- **Dark Theme**: GitHub-inspired dark theme
- **Interactive Charts**: Built with Chart.js for dynamic visualizations
- **Share Functionality**: Easy sharing of your wrapped results

### 📈 **Data Visualization**

- **Doughnut Charts**: Language distribution
- **Bar Charts**: Weekly activity patterns
- **Line Charts**: Hourly activity trends
- **Heatmaps**: Combined day/hour activity visualization

## Requirements

- **PHP 7.4+**
- **Apache/Nginx Web Server**
- **Composer** (for dependency management)
- **GitHub OAuth App** (for authentication)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Code-DNA
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Create GitHub OAuth App

1. Go to GitHub → Settings → Developer settings → OAuth Apps
2. Click "New OAuth App"
3. Fill in the application details:
   - **Application name**: GitHub Wrapped
   - **Homepage URL**: `http://localhost/projects/code-dna/`
   - **Authorization callback URL**: `http://localhost/projects/code-dna/auth_callback.php`
4. Note down the `Client ID` and `Client Secret`

### 4. Configure the Application

Update `includes/config.php` with your GitHub OAuth credentials:

```php
define('GITHUB_CLIENT_ID', 'your_client_id_here');
define('GITHUB_CLIENT_SECRET', 'your_client_secret_here');
define('GITHUB_REDIRECT_URI', 'http://localhost/projects/code-dna/auth_callback.php');
```

### 5. Set Up Directory Structure

Ensure the following directories exist and are writable:

```
/logs/              # For application logs
/cache/             # For API response caching (optional)
```

### 6. Web Server Configuration

Place the project in your web server's document root:

- **XAMPP**: `/xampp/htdocs/projects/code-dna/`
- **WAMP**: `/wamp/www/projects/code-dna/`
- **LAMP**: `/var/www/html/projects/code-dna/`

## Usage

### For Users

1. **Access the Application**: Navigate to `http://localhost/projects/code-dna/`
2. **Login with GitHub**: Click "Connect with GitHub" and authorize the application
3. **View Your Wrapped**: The application will analyze your GitHub data and display your personalized wrapped
4. **Share Results**: Use the share button to share your coding year in review
5. **Logout**: Click logout to securely sign out from both the app and GitHub

### For Administrators

1. **Access Admin Panel**: Navigate to `http://localhost/projects/code-dna/admin.php`
2. **Admin Login**: Use the admin password (default: `github_wrapped_admin`)
3. **Monitor Activity**: View user login logs and application usage

## File Structure

```
Code-DNA/
├── index.php              # Landing page and main entry point
├── wrapped.php            # Main dashboard with user analytics
├── auth_callback.php      # GitHub OAuth callback handler
├── logout.php             # Enhanced logout functionality
├── admin.php              # Administrative panel
├── api.php                # API endpoints for data fetching
├── auto_logout.php        # Automatic logout handler
├── includes/
│   ├── auth.php          # Authentication functions
│   └── config.php        # Configuration settings
├── assets/
│   ├── css/
│   │   └── style.css     # Main stylesheet
│   └── js/
│       └── script.js     # JavaScript for charts and interactivity
├── logs/                 # Application logs
├── composer.json         # PHP dependencies
└── README.md            # This file
```

## Key Functions

### Authentication (includes/auth.php)

- `isLoggedIn()`: Check if user is authenticated
- `getGitHubAuthUrl()`: Generate GitHub OAuth URL
- `fetchGitHubData()`: Make authenticated API calls
- `getUserLanguages()`: Analyze language usage
- `getUserContributionStats()`: Calculate contribution statistics

### Data Analysis

- **Language Analysis**: Scans all repositories for language usage
- **Contribution Tracking**: Analyzes commits, issues, and pull requests
- **Activity Patterns**: Identifies peak coding hours and days
- **Repository Insights**: Finds most active repositories

## Security Features

- **OAuth 2.0**: Secure authentication with GitHub
- **Session Management**: Proper session handling and cleanup
- **CSRF Protection**: State parameter validation
- **Rate Limiting**: API call caching to prevent rate limits
- **Secure Logout**: Complete session termination

## Configuration Options

### Cache Settings

```php
define('CACHE_ENABLED', true);
define('CACHE_DURATION', 3600); // 1 hour
```

### Database Configuration (Optional)

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'code_dna');
```

## API Endpoints

- `GET /api.php?action=user` - Get user profile data
- `GET /api.php?action=repos` - Get user repositories
- `GET /api.php?action=languages` - Get language statistics
- `GET /api.php?action=contributions` - Get contribution data

## Troubleshooting

### Common Issues

1. **"Invalid client_id or client_secret"**

   - Check your GitHub OAuth credentials in `config.php`
   - Ensure the redirect URI matches your GitHub app settings

2. **"Permission denied" errors**

   - Ensure `/logs/` directory is writable
   - Check file permissions on the web server

3. **"API rate limit exceeded"**

   - Enable caching in `config.php`
   - Reduce API calls by implementing data persistence

4. **Charts not displaying**
   - Ensure JavaScript is enabled in the browser
   - Check browser console for errors
   - Verify Chart.js is loading correctly

### Debug Mode

Enable debug mode by adding to the top of `index.php`:

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the [MIT License](LICENSE).

## Credits

- **Chart.js**: For data visualization
- **GitHub API**: For user data access
- **Font Awesome**: For icons
- **PHP**: Server-side functionality

## Support

For issues and questions:

1. Check the troubleshooting section
2. Review the logs in `/logs/` directory
3. Create an issue in the repository

---

**Note**: This application only requests read access to your public repositories and does not store any personal data permanently. All analysis is performed in real-time using the GitHub API.
