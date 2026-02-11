<?php
/**
 * Admin Dashboard for FreeDownload Plugin
 * 
 * This file is responsible for rendering the admin panel for the FreeDownload plugin.
 *
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add the admin menu
add_action('admin_menu', 'fd_admin_menu');

function fd_admin_menu() {
    add_menu_page(
        'FreeDownload Settings',
        'FreeDownload',
        'manage_options',
        'freedownload-settings',
        'fd_settings_page',
        'dashicons-download',
        100
    );
}

function fd_settings_page() {
    // Handle form submission
    if (isset($_POST['fd_update_settings'])) {
        // Verify nonce
        check_admin_referer('fd_update_settings_nonce');

        // Save settings
        update_option('fd_download_limit', intval($_POST['fd_download_limit']));
        echo '<div class="updated"><p>Settings saved!</p></div>';
    }

    // Get current settings
    $download_limit = get_option('fd_download_limit', 10);
    ?><!-- HTML content for settings page -->
    <div class="wrap">
        <h1>FreeDownload Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('fd_update_settings_nonce'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Download Limit</th>
                    <td><input type="number" name="fd_download_limit" value="<?php echo esc_attr($download_limit); ?>" /></td>
                </tr>
            </table>
            <?php submit_button('Save Settings', 'primary', 'fd_update_settings'); ?>
        </form>
    </div>
<?php
}