# WP Rocket Cache Monitor

Monitors WP Rocket cache size and notifies via email if it exceeds a specified limit. Includes a WordPress admin interface to manage thresholds and email settings.

## Features

- Monitors the `wp-content/cache/wp-rocket` folder
- Customizable size limit in GB
- Email alerts when cache size exceeds the limit
- Fully compatible with SMTP plugins (e.g. FluentSMTP)
- Optional custom From and Reply-To headers
- Simple admin settings UI in **Settings > Rocket Monitor**
- Dashboard widget with current cache size and manual clear button
- Auto-localized interface (Czech/English) based on WordPress locale

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate it through the WordPress admin
3. Go to **Settings > Rocket Monitor** to configure

## License

MIT
