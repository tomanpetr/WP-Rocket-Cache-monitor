<?php
/**
 * Plugin Name: WP Rocket Cache Monitor
 * Description: Monitors the size of WP Rocket cache and sends an email alert if it exceeds a defined limit. Provides a simple admin UI for configuration.
 * Version: 1.3
 * Author: Toman Petr
 * License: GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_menu', function() {
    add_options_page('WP Rocket Cache Monitor', 'Rocket Monitor', 'manage_options', 'rocket-cache-monitor', 'wprcm_render_settings_page');
});

add_action('admin_init', function() {
    register_setting('wprcm_settings', 'wprcm_email');
    register_setting('wprcm_settings', 'wprcm_limit');
    register_setting('wprcm_settings', 'wprcm_custom_headers');
    register_setting('wprcm_settings', 'wprcm_custom_from');
    register_setting('wprcm_settings', 'wprcm_custom_reply');
});

function wprcm_clear_cache() {
    $cache_path = WP_CONTENT_DIR . '/cache/wp-rocket';
    if (is_dir($cache_path)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cache_path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            $file->isDir() ? rmdir($file) : unlink($file);
        }
    }
}

add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget('wprcm_dashboard_widget', 'WP Rocket Cache Monitor', 'wprcm_dashboard_widget_display');
});

function wprcm_dashboard_widget_display() {
    $limit = (float) get_option('wprcm_limit', 10);
    $path = WP_CONTENT_DIR . '/cache/wp-rocket';
    $locale = get_locale();
    $is_czech = strpos($locale, 'cs_') === 0;

    if (!is_dir($path)) {
        echo $is_czech ? '<p>Složka cache nebyla nalezena.</p>' : '<p>Cache folder not found.</p>';
        return;
    }
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    $size_gb = round($size / 1024 / 1024 / 1024, 2);

    echo "<p><strong>" . ($is_czech ? 'Aktuální velikost cache:' : 'Current cache size:') . "</strong> {$size_gb} GB</p>";
    echo "<p><strong>" . ($is_czech ? 'Limit:' : 'Limit:') . "</strong> {$limit} GB</p>";
    echo "<p><strong>" . ($is_czech ? 'Cesta:' : 'Path:') . "</strong> {$path}</p>";
    echo '<form method="post"><input type="submit" name="wprcm_clear_cache" class="button" value="' . ($is_czech ? 'Vymazat cache' : 'Clear Cache') . '"></form>';
    if (isset($_POST['wprcm_clear_cache']) && current_user_can('manage_options')) {
        wprcm_clear_cache();
        echo '<p>' . ($is_czech ? 'Cache byla úspěšně vymazána.' : 'Cache successfully cleared.') . '</p>';
    }
}

function wprcm_render_settings_page() {
    $custom_headers = get_option('wprcm_custom_headers', '0');
    $locale = get_locale();
    $is_czech = strpos($locale, 'cs_') === 0;
    ?>
    <div class="wrap">
        <h1><?php echo $is_czech ? 'Monitorování cache WP Rocket' : 'WP Rocket Cache Monitor'; ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('wprcm_settings'); ?>
            <?php do_settings_sections('wprcm_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo $is_czech ? 'E-mail pro upozornění' : 'Notification Email'; ?></th>
                    <td><input type="email" name="wprcm_email" value="<?php echo esc_attr( get_option('wprcm_email', get_bloginfo('admin_email')) ); ?>" size="40" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo $is_czech ? 'Limit velikosti cache (GB)' : 'Cache Limit (GB)'; ?></th>
                    <td><input type="number" step="0.1" name="wprcm_limit" value="<?php echo esc_attr( get_option('wprcm_limit', 10) ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo $is_czech ? 'Hlavičky e-mailu' : 'Email Headers'; ?></th>
                    <td>
                        <fieldset>
                            <label><input type="radio" name="wprcm_custom_headers" value="0" <?php checked('0', $custom_headers); ?> /> <?php echo $is_czech ? 'Použít výchozí hlavičky (SMTP plugin nebo WP)' : 'Use default headers (SMTP plugin or WP)'; ?></label><br>
                            <label><input type="radio" name="wprcm_custom_headers" value="1" <?php checked('1', $custom_headers); ?> /> <?php echo $is_czech ? 'Použít vlastní e-mailové hlavičky' : 'Use custom email headers'; ?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top" class="wprcm-custom-header-fields">
                    <th scope="row"><?php echo $is_czech ? 'Vlastní adresa odesílatele (From)' : 'Custom From Address'; ?></th>
                    <td><input type="email" name="wprcm_custom_from" value="<?php echo esc_attr( get_option('wprcm_custom_from', '') ); ?>" size="40" /></td>
                </tr>
                <tr valign="top" class="wprcm-custom-header-fields">
                    <th scope="row"><?php echo $is_czech ? 'Vlastní adresa pro odpověď (Reply-To)' : 'Custom Reply-To Address'; ?></th>
                    <td><input type="email" name="wprcm_custom_reply" value="<?php echo esc_attr( get_option('wprcm_custom_reply', '') ); ?>" size="40" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <form method="post">
            <?php submit_button($is_czech ? 'Vymazat WP Rocket cache' : 'Clear WP Rocket Cache', 'delete', 'wprcm_clear_cache'); ?>
        </form>
        <script>
            (function() {
                function toggleFields() {
                    const selected = document.querySelector('input[name="wprcm_custom_headers"]:checked').value;
                    const customFields = document.querySelectorAll('.wprcm-custom-header-fields');
                    customFields.forEach(el => el.style.display = selected === '1' ? '' : 'none');
                }
                document.querySelectorAll('input[name="wprcm_custom_headers"]').forEach(el => {
                    el.addEventListener('change', toggleFields);
                });
                document.addEventListener('DOMContentLoaded', toggleFields);
            })();
        </script>
    </div>
    <?php
    if (isset($_POST['wprcm_clear_cache']) && current_user_can('manage_options')) {
        wprcm_clear_cache();
        echo '<div class="updated"><p>' . ($is_czech ? 'Cache byla úspěšně vymazána.' : 'Cache has been successfully cleared.') . '</p></div>';
    }
}
