<?php
/**
 * Plugin Name: FreeDownload
 * Plugin URI: https://github.com/aipl-ai/freedownload
 * Description: Display downloadable free templates with gated forms and customizable grid layout
 * Version: 1.0.0
 * Author: AIPL AI
 * License: GPL v2 or later
 * Text Domain: freedownload
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'FREEDOWNLOAD_VERSION', '1.0.0' );
define( 'FREEDOWNLOAD_PATH', plugin_dir_path( __FILE__ ) );
define( 'FREEDOWNLOAD_URL', plugin_dir_url( __FILE__ ) );
define( 'FREEDOWNLOAD_BASENAME', plugin_basename( __FILE__ ) );

// Include core plugin files
require_once FREEDOWNLOAD_PATH . 'includes/class-freedownload.php';
require_once FREEDOWNLOAD_PATH . 'includes/class-cpt.php';
require_once FREEDOWNLOAD_PATH . 'includes/class-admin.php';
require_once FREEDOWNLOAD_PATH . 'includes/class-frontend.php';

/**
 * Initialize the plugin
 */
function freedownload_init() {
    FreeDownload::get_instance();
}
add_action( 'plugins_loaded', 'freedownload_init' );

// Activation and deactivation hooks
register_activation_hook( __FILE__, 'freedownload_activate' );
register_deactivation_hook( __FILE__, 'freedownload_deactivate' );

function freedownload_activate() {
    // Create custom post type on activation
    FreeDownload_CPT::register_post_type();
    flush_rewrite_rules();
}

function freedownload_deactivate() {
    flush_rewrite_rules();
}