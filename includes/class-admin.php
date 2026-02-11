<?php
/**
 * Admin functionality
 */

class FreeDownload_Admin {
    
    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post_freedownload', [ $this, 'save_meta_boxes' ] );
        add_filter( 'manage_freedownload_posts_columns', [ $this, 'custom_columns' ] );
        add_action( 'manage_freedownload_posts_custom_column', [ $this, 'render_custom_columns' ], 10, 2 );
    }
    
    public function add_meta_boxes() {
        add_meta_box(
            'freedownload_file',
            esc_html__( 'Download File', 'freedownload' ),
            [ $this, 'render_file_metabox' ],
            'freedownload',
            'normal',
            'high'
        );
        
        add_meta_box(
            'freedownload_stats',
            esc_html__( 'Download Statistics', 'freedownload' ),
            [ $this, 'render_stats_metabox' ],
            'freedownload',
            'side',
            'default'
        );
    }
    
    public function render_file_metabox( $post ) {
        wp_nonce_field( 'freedownload_nonce', 'freedownload_nonce' );
        
        $file_id = get_post_meta( $post->ID, '_file_id', true );
        $file_url = get_post_meta( $post->ID, '_file_url', true );
        ?>
        <div class="freedownload-file-upload">
            <p>
                <label for="freedownload_file_upload"><?php esc_html_e( 'Upload File:', 'freedownload' ); ?></label>
            </p>
            <input type="hidden" id="freedownload_file_id" name="freedownload_file_id" value="<?php echo esc_attr( $file_id ); ?>">
            <input type="hidden" id="freedownload_file_url" name="freedownload_file_url" value="<?php echo esc_url( $file_url ); ?>">
            
            <button type="button" class="button button-primary" id="freedownload_upload_btn"><?php esc_html_e( 'Choose File', 'freedownload' ); ?></button>
            
            <?php if ( ! empty( $file_url ) ) : ?>
                <div class="freedownload-file-info" style="margin-top: 10px;">
                    <p><strong><?php esc_html_e( 'File:', 'freedownload' ); ?></strong></p>
                    <p><code><?php echo esc_html( basename( $file_url ) ); ?></code></p>
                    <a href="<?php echo esc_url( $file_url ); ?>" class="button button-small" target="_blank"><?php esc_html_e( 'View', 'freedownload' ); ?></a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    public function render_stats_metabox( $post ) {
        $download_count = intval( get_post_meta( $post->ID, '_download_count', true ) );
        ?>
        <div class="freedownload-stats">
            <p>
                <strong><?php esc_html_e( 'Total Downloads:', 'freedownload' ); ?></strong><br>
                <span style="font-size: 24px; font-weight: bold; color: #0073aa;">
                    <?php echo intval( $download_count ); ?>
                </span>
            </p>
        </div>
        <?php
    }
    
    public function save_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['freedownload_nonce'] ) || ! wp_verify_nonce( $_POST['freedownload_nonce'], 'freedownload_nonce' ) ) {
            return;
        }
        
        if ( isset( $_POST['freedownload_file_id'] ) ) {
            update_post_meta( $post_id, '_file_id', intval( $_POST['freedownload_file_id'] ) );
        }
        
        if ( isset( $_POST['freedownload_file_url'] ) ) {
            update_post_meta( $post_id, '_file_url', esc_url_raw( $_POST['freedownload_file_url'] ) );
        }
    }
    
    public function custom_columns( $columns ) {
        return [
            'cb' => $columns['cb'],
            'title' => esc_html__( 'Template', 'freedownload' ),
            'thumbnail' => esc_html__( 'Image', 'freedownload' ),
            'tags' => esc_html__( 'Tags', 'freedownload' ),
            'downloads' => esc_html__( 'Downloads', 'freedownload' ),
            'date' => $columns['date'],
        ];
    }
    
    public function render_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'thumbnail':
                if ( has_post_thumbnail( $post_id ) ) {
                    echo get_the_post_thumbnail( $post_id, [ 50, 50 ] );
                } else {
                    echo '<em>' . esc_html__( 'No image', 'freedownload' ) . '</em>';
                }
                break;
            
            case 'tags':
                $tags = get_the_terms( $post_id, 'freedownload_tag' );
                if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
                    $tag_names = wp_list_pluck( $tags, 'name' );
                    echo esc_html( implode( ', ', $tag_names ) );
                } else {
                    echo '<em>' . esc_html__( 'No tags', 'freedownload' ) . '</em>';
                }
                break;
            
            case 'downloads':
                $count = intval( get_post_meta( $post_id, '_download_count', true ) );
                echo intval( $count );
                break;
        }
    }
}

// Initialize admin class
add_action( 'admin_init', function() {
    new FreeDownload_Admin();
} );