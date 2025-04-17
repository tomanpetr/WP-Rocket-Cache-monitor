# WP Rocket Cache Monitor

A simple utility plugin for WordPress that monitors the size of the WP Rocket cache and sends an email notification when the cache size exceeds a specified limit.

## ✅ Features

- Monitors the `wp-content/cache/wp-rocket` folder
- Customizable cache size limit in GB
- Email notifications when the limit is exceeded
- Compatible with WordPress `wp_mail()` and SMTP plugins (e.g. FluentSMTP)
- Option to define custom From and Reply-To headers
- Admin UI in **Settings > Rocket Monitor**
- Dashboard widget with current cache size and one-click cache clear
- Localized interface (Czech or English) based on WordPress locale

## 🔧 Requirements

- WordPress 5.5 or later
- Active WP Rocket plugin
- Optional: SMTP plugin for better email delivery

## 🚀 Installation

1. Upload the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the WordPress admin menu
3. Navigate to **Settings > Rocket Monitor**
4. Configure your email address and cache size limit

## 📄 License

This plugin is licensed under the **GNU General Public License v2.0 or later**.  
See the [LICENSE](LICENSE) file for full terms.
