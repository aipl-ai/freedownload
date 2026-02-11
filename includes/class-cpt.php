<?php
/**
 * Custom Post Type registration
 */

class FreeDownload_CPT {
    
    public static function register_post_type() {
        $labels = [
            'name' => esc_html__( 'Templates', 'freedownload' ),
            'singular_name' => esc_html__( 'Template', 'freedownload' ),
            'menu_name' => esc_html__( 'FreeDownload', 'freedownload' ),
            'add_new' => esc_html__( 'Add New Template', 'freedownload' ),
            'add_new_item' => esc_html__( 'Add New Template', 'freedownload' ),
            'edit_item' => esc_html__( 'Edit Template', 'freedownload' ),
            'view_item' => esc_html__( 'View Template', 'freedownload' ),
            'all_items' => esc_html__( 'All Templates', 'freedownload' ),
            'search_items' => esc_html__( 'Search Templates', 'freedownload' ),
        ];
        
        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => [ 'slug' => 'template' ],
            'supports' => [ 'title', 'editor', 'thumbnail' ],
            'menu_icon' => 'dashicons-download',
            'show_in_rest' => true,
        ];
        
        register_post_type( 'freedownload', $args );
        
        // Register taxonomy for tags
        $tax_labels = [
            'name' => esc_html__( 'Tags', 'freedownload' ),
            'singular_name' => esc_html__( 'Tag', 'freedownload' ),
            'search_items' => esc_html__( 'Search Tags', 'freedownload' ),
            'all_items' => esc_html__( 'All Tags', 'freedownload' ),
            'parent_item' => esc_html__( 'Parent Tag', 'freedownload' ),
            'edit_item' => esc_html__( 'Edit Tag', 'freedownload' ),
            'update_item' => esc_html__( 'Update Tag', 'freedownload' ),
            'add_new_item' => esc_html__( 'Add New Tag', 'freedownload' ),
            'new_item_name' => esc_html__( 'New Tag Name', 'freedownload' ),
            'menu_name' => esc_html__( 'Tags', 'freedownload' ),
        ];
        
        register_taxonomy( 'freedownload_tag', 'freedownload', [
            'labels' => $tax_labels,
            'hierarchical' => false,
            'rewrite' => [ 'slug' => 'template-tag' ],
            'show_in_rest' => true,
        ] );
    }
}

// Hook to register post type early
add_action( 'init', [ 'FreeDownload_CPT', 'register_post_type' ] );
