<?php
/**
 * Plugin Name: Free Download Templates
 * Description: A WordPress plugin to offer downloadable free templates.
 * Version: 1.0
 * Author: aipl-ai
 */

// Ensure WordPress is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function fd_download_template($template_name) {
    // Logic to handle the download of the template
    if ( ! empty( $template_name ) ) {
        $file_path = plugin_dir_path( __FILE__ ) . 'templates/' . $template_name;
        if ( file_exists( $file_path ) ) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file_path));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            flush();
            readfile($file_path);
            exit;
        }
    }
}

// Example usage: add a hook or shortcode to trigger the download
// add_action('init', 'fd_download_template');
?>