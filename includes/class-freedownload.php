<?php
/**
 * Main plugin class
 */

class FreeDownload {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->setup_hooks();
        $this->load_textdomain();
    }
    
    private function setup_hooks() {
        // Enqueue scripts and styles
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        
        // AJAX handlers
        add_action( 'wp_ajax_freedownload_get_downloads', [ $this, 'ajax_get_downloads' ] );
        add_action( 'wp_ajax_nopriv_freedownload_get_downloads', [ $this, 'ajax_get_downloads' ] );
        add_action( 'wp_ajax_freedownload_handle_form_submission', [ $this, 'ajax_handle_form_submission' ] );
        add_action( 'wp_ajax_nopriv_freedownload_handle_form_submission', [ $this, 'ajax_handle_form_submission' ] );
    }
    
    public function enqueue_frontend_assets() {
        wp_enqueue_style( 'freedownload-frontend', FREEDOWNLOAD_URL . 'assets/css/frontend.css', [], FREEDOWNLOAD_VERSION );
        wp_enqueue_script( 'freedownload-frontend', FREEDOWNLOAD_URL . 'assets/js/frontend.js', [ 'jquery' ], FREEDOWNLOAD_VERSION, true );
        
        wp_localize_script( 'freedownload-frontend', 'freedownloadAjax', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'freedownload-nonce' ),
        ] );
    }
    
    public function enqueue_admin_assets( $hook ) {
        if ( get_post_type() !== 'freedownload' ) {
            return;
        }
        
        wp_enqueue_style( 'freedownload-admin', FREEDOWNLOAD_URL . 'assets/css/admin.css', [], FREEDOWNLOAD_VERSION );
        wp_enqueue_script( 'freedownload-admin', FREEDOWNLOAD_URL . 'assets/js/admin.js', [ 'jquery' ], FREEDOWNLOAD_VERSION, true );
        wp_enqueue_media();
    }
    
    public function ajax_get_downloads() {
        check_ajax_referer( 'freedownload-nonce' );
        
        $paged = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
        $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
        $tags = isset( $_POST['tags'] ) ? array_map( 'intval', (array) $_POST['tags'] ) : [];
        $per_page = apply_filters( 'freedownload_per_page', 12 );
        
        $args = [
            'post_type' => 'freedownload',
            'posts_per_page' => $per_page,
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        
        if ( ! empty( $search ) ) {
            $args['s'] = $search;
        }
        
        if ( ! empty( $tags ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'freedownload_tag',
                    'field' => 'term_id',
                    'terms' => $tags,
                    'operator' => 'IN',
                ],
            ];
        }
        
        $query = new WP_Query( $args );
        
        ob_start();
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                include FREEDOWNLOAD_PATH . 'templates/grid-item.php';
            }
        } else {
            echo '<p>' . esc_html__( 'No templates found.', 'freedownload' ) . '</p>';
        }
        
        wp_reset_postdata();
        $html = ob_get_clean();
        
        wp_send_json_success( [
            'html' => $html,
            'max_pages' => $query->max_num_pages,
        ] );
    }
    
    public function ajax_handle_form_submission() {
        check_ajax_referer( 'freedownload-nonce' );
        
        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $template_id = isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0;
        
        if ( empty( $email ) || empty( $name ) || empty( $template_id ) ) {
            wp_send_json_error( [
                'message' => esc_html__( 'Please fill all fields.', 'freedownload' ),
            ] );
        }
        
        if ( ! is_email( $email ) ) {
            wp_send_json_error( [
                'message' => esc_html__( 'Invalid email address.', 'freedownload' ),
            ] );
        }
        
        // Store submission data
        update_post_meta( $template_id, '_download_email', $email );
        update_post_meta( $template_id, '_download_name', $name );
        
        // Increment download count
        $download_count = intval( get_post_meta( $template_id, '_download_count', true ) );
        update_post_meta( $template_id, '_download_count', $download_count + 1 );
        
        // Get download file URL
        $file_url = get_post_meta( $template_id, '_file_url', true );
        
        wp_send_json_success( [
            'message' => esc_html__( 'Download started!', 'freedownload' ),
            'file_url' => $file_url,
        ] );
    }
    
    private function load_textdomain() {
        load_plugin_textdomain(
            'freedownload',
            false,
            dirname( FREEDOWNLOAD_BASENAME ) . '/languages'
        );
    }
}