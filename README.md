# GitHub Wrapped

A PHP-based web application that creates a personalized "year in review" for GitHub users, similar to Spotify Wrapped. This application analyzes your GitHub activity and presents it in a visually appealing format with charts and statistics.

🌐 **Live Demo**: [https://code-dna.onrender.com](https://code-dna.onrender.com)

## 🚀 Quick Start

1. **Visit the live app**: [https://code-dna.onrender.com](https://code-dna.onrender.com)
2. **Click "Connect with GitHub"** to authenticate with your GitHub account
3. **Explore your coding year** with beautiful visualizations and statistics
4. **Share your results** with friends and colleagues

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

## 🌟 Deployment

This application is deployed on **Render** with the following features:

- ✅ **Automatic deployments** from GitHub
- ✅ **HTTPS enabled** by default
- ✅ **Environment variables** for secure configuration
- ✅ **Docker containerization** for consistent deployment

## 🛠️ Technology Stack

- **Backend**: PHP 8.1+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Charts**: Chart.js for data visualization
- **Authentication**: GitHub OAuth 2.0
- **HTTP Client**: Guzzle HTTP
- **Container**: Docker with Apache
- **Deployment**: Render (Cloud Platform)

## 📋 Requirements

- **PHP 8.1+**
- **Apache/Nginx Web Server**
- **Composer** (for dependency management)
- **GitHub OAuth App** (for authentication)

## 🚀 Local Development Setup

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/Code-DNA.git
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

## 🌐 Production Deployment (Render)

### Environment Variables

The following environment variables are configured in production:

```bash
APP_URL=https://code-dna.onrender.com
GITHUB_CLIENT_ID=your_production_client_id
GITHUB_CLIENT_SECRET=your_production_client_secret
GITHUB_REDIRECT_URI=https://code-dna.onrender.com/auth_callback.php
```

### Deployment Process

1. **Push to GitHub**: Changes are automatically deployed
2. **Docker Build**: Application is containerized using the Dockerfile
3. **Environment Setup**: Environment variables are injected
4. **Service Start**: Apache server starts and serves the application

## 📖 Usage

### For Users

1. **Access the Application**:
   - **Production**: Visit [https://code-dna.onrender.com](https://code-dna.onrender.com)
   - **Local**: Navigate to `http://localhost/projects/code-dna/`
2. **Login with GitHub**: Click "Connect with GitHub" and authorize the application
3. **View Your Wrapped**: The application will analyze your GitHub data and display your personalized wrapped
4. **Share Results**: Use the share button to share your coding year in review
5. **Logout**: Click logout to securely sign out from both the app and GitHub

### For Administrators

1. **Access Admin Panel**:
   - **Production**: Visit `https://code-dna.onrender.com/admin.php`
   - **Local**: Navigate to `http://localhost/projects/code-dna/admin.php`
2. **Admin Login**: Use the admin password (default: `github_wrapped_admin`)
3. **Monitor Activity**: View user login logs and application usage

## 🎯 Key Features in Production

- **🔒 Secure HTTPS**: All communication is encrypted
- **⚡ Fast Performance**: Optimized for quick data loading
- **📱 Mobile Responsive**: Works perfectly on all devices
- **🔄 Auto-Updates**: Automatic deployments from GitHub
- **📊 Real-time Analytics**: Live GitHub data analysis
- **🎨 Beautiful UI**: GitHub-inspired dark theme

## 📁 File Structure

```
Code-DNA/
├── index.php              # Landing page and main entry point
├── wrapped.php            # Main dashboard with user analytics
├── auth_callback.php      # GitHub OAuth callback handler
├── logout.php             # Enhanced logout functionality
├── admin.php              # Administrative panel
├── api.php                # API endpoints for data fetching
├── auto_logout.php        # Automatic logout handler
├── error.php              # Error handling page
├── includes/
│   ├── auth.php          # Authentication functions
│   └── config.php        # Configuration settings
├── assets/
│   ├── css/
│   │   └── style.css     # Main stylesheet
│   └── js/
│       └── script.js     # JavaScript for charts and interactivity
├── logs/                 # Application logs
├── cache/                # API response cache
├── vendor/               # Composer dependencies
├── composer.json         # PHP dependencies
├── Dockerfile           # Docker configuration
└── README.md            # This file

# Development/Debug Files
├── test.php             # Development testing script
├── debug.php            # Debug utilities
├── github_test.php      # GitHub API testing
├── phpinfo.php          # PHP information
└── clear_session.php    # Session management utility
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

## 🔧 Troubleshooting

### Common Issues

1. **"Invalid client_id or client_secret"**

   - Check your GitHub OAuth credentials in `config.php`
   - Ensure the redirect URI matches your GitHub app settings
   - Verify environment variables are set correctly in production

2. **"Permission denied" errors**

   - Ensure `/logs/` directory is writable
   - Check file permissions on the web server
   - In production, Render handles file permissions automatically

3. **"API rate limit exceeded"**

   - Enable caching in `config.php`
   - Reduce API calls by implementing data persistence
   - Consider using personal access tokens for higher rate limits

4. **Charts not displaying**

   - Ensure JavaScript is enabled in the browser
   - Check browser console for errors
   - Verify Chart.js is loading correctly
   - Check network connectivity

5. **"Headers already sent" errors**

   - Check for whitespace before `<?php` tags
   - Ensure no output before header() calls
   - Use output buffering if necessary

6. **"Shows wrong username in GitHub authorization"**
   - User is logged into GitHub with a different account
   - **Solution**: Log out of GitHub first at `https://github.com/logout`
   - Or use incognito/private browsing mode
   - The app now forces fresh login for better security

### Debug Mode

Enable debug mode by adding to the top of `index.php`:

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

### Development Tools

- **`test.php`**: Basic functionality testing
- **`debug.php`**: Advanced debugging with GitHub API testing
- **`github_test.php`**: GitHub API endpoint testing
- **`phpinfo.php`**: PHP configuration information

## 🤝 Contributing

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Make your changes** and test thoroughly
4. **Commit your changes**: `git commit -m 'Add amazing feature'`
5. **Push to the branch**: `git push origin feature/amazing-feature`
6. **Open a Pull Request**

### Development Guidelines

- Follow PSR-4 autoloading standards
- Write clear, documented code
- Test on both local and production environments
- Ensure mobile responsiveness
- Follow security best practices

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🙏 Credits

- **Chart.js**: For beautiful data visualizations
- **GitHub API**: For accessing user data
- **Guzzle HTTP**: For reliable API requests
- **Render**: For hosting and deployment
- **Font Awesome**: For icons and UI elements
- **PHP**: Server-side functionality

## 📞 Support

For issues and questions:

1. **Check the troubleshooting section** above
2. **Review the logs** in `/logs/` directory
3. **Use development tools** (`test.php`, `debug.php`) for local testing
4. **Create an issue** in the repository with detailed information

### Getting Help

- 🐛 **Bug Reports**: Use GitHub Issues
- 💡 **Feature Requests**: Use GitHub Issues with "enhancement" label
- 📧 **General Questions**: Create a GitHub Discussion

---

## 🔐 Privacy & Security

- ✅ **Read-only access**: Only requests read access to public repositories
- ✅ **No data storage**: No personal data is stored permanently
- ✅ **Real-time analysis**: All analysis is performed in real-time using the GitHub API
- ✅ **Secure authentication**: Uses GitHub OAuth 2.0 with proper security measures
- ✅ **HTTPS encryption**: All communication is encrypted in production

## 🚀 What's Next?

- **🎨 Custom themes**: Choose from multiple color schemes
- **📊 Advanced analytics**: More detailed code analysis
- **🔄 Historical data**: Track changes over time
- **🎯 Goal setting**: Set and track coding goals
- **🏆 Achievements**: Unlock coding achievements

---

**⭐ Star this repository if you found it useful!**
