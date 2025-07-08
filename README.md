# GitHub Wrapped

A Spotify Wrapped-style analytics dashboard for your GitHub activity. Visualize your coding habits, language preferences, and contribution patterns in a beautiful, shareable format.

## Features

- **Language Analysis**: See which programming languages you use most
- **Activity Patterns**: Discover your most productive days and hours
- **Contribution Stats**: Track your commits, PRs, and contribution streaks
- **Shareable Images**: Generate images to share your coding journey on social media

## Tech Stack

- **Frontend**: HTML, CSS, JavaScript, Chart.js
- **Backend**: PHP
- **APIs**: GitHub REST API
- **Authentication**: GitHub OAuth

## Setup Instructions

1. **Clone the repository**
   ```
   git clone https://github.com/yourusername/github-wrapped.git
   cd github-wrapped
   ```

2. **Install dependencies**
   ```
   composer install
   ```

3. **Configure GitHub OAuth**
   - Go to GitHub > Settings > Developer settings > OAuth Apps > New OAuth App
   - Set the Authorization callback URL to `http://localhost/github-wrapped/auth_callback.php` (adjust as needed)
   - Copy your Client ID and Client Secret
   - Update `includes/config.php` with your credentials

4. **Run the application**
   - Use a local server like XAMPP, WAMP, or PHP's built-in server
   - Navigate to the project URL in your browser

## How It Works

1. User authenticates with GitHub via OAuth
2. The application fetches user data from the GitHub API:
   - Repositories
   - Commits
   - Languages
   - Pull requests
3. Data is processed and visualized using Chart.js
4. Users can view their stats and generate shareable images

## Screenshots

![Dashboard Preview](assets/images/dashboard-preview.png)
![Language Analysis](assets/images/language-analysis.png)
![Activity Patterns](assets/images/activity-patterns.png)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- Inspired by Spotify Wrapped
- Uses the GitHub API
- Built with Chart.js for visualizations