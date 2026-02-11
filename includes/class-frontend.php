<?php
/**
 * Frontend functionality
 */

class FreeDownload_Frontend {
    
    public function __construct() {
        add_shortcode( 'freedownload_grid', [ $this, 'render_grid' ] );
        add_action( 'template_redirect', [ $this, 'handle_single_template' ] );
    }
    
    public function render_grid( $atts ) {
        $atts = shortcode_atts( [
            'columns' => 3,
            'per_page' => 12,
        ], $atts );
        
        ob_start();
        ?>
        <div class="freedownload-wrapper">
            <div class="freedownload-header">
                <div class="freedownload-filters">
                    <div class="filters-list">
                        <?php $this->render_filters(); ?>
                    </div>
                </div>
                <div class="freedownload-search">
                    <input 
                        type="text" 
                        id="freedownload-search" 
                        placeholder="<?php esc_attr_e( 'Search templates...', 'freedownload' ); ?>" 
                        class="search-input"
                    >
                </div>
            </div>
            
            <div class="freedownload-grid" data-columns="<?php echo intval( $atts['columns'] ); ?>" data-per-page="<?php echo intval( $atts['per_page'] ); ?>">
                <?php $this->render_grid_items( intval( $atts['per_page'] ) ); ?>
            </div>
            
            <div class="freedownload-pagination"></div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function render_filters() {
        $tags = get_terms( [
            'taxonomy' => 'freedownload_tag',
            'hide_empty' => true,
        ]);
        
        if ( empty( $tags ) || is_wp_error( $tags ) ) {
            return;
        }
        ?>
        <div class="filter-group">
            <h3><?php esc_html_e( 'Filters', 'freedownload' ); ?></h3>
            <div class="filter-tags">
                <?php foreach ( $tags as $tag ) : ?>
                    <label class="filter-checkbox">
                        <input type="checkbox" class="freedownload-filter-tag" data-tag-id="<?php echo intval( $tag->term_id ); ?>" value="<?php echo intval( $tag->term_id ); ?>">
                        <span><?php echo esc_html( $tag->name ); ?> (<?php echo intval( $tag->count ); ?>)</span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    private function render_grid_items( $per_page ) {
        $args = [
            'post_type' => 'freedownload',
            'posts_per_page' => $per_page,
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        
        $query = new WP_Query( $args );
        
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                include FREEDOWNLOAD_PATH . 'templates/grid-item.php';
            }
        } else {
            echo '<p>' . esc_html__( 'No templates found.', 'freedownload' ) . '</p>';
        }
        
        wp_reset_postdata();
    }
    
    public function handle_single_template() {
        if ( is_singular( 'freedownload' ) ) {
            include FREEDOWNLOAD_PATH . 'templates/single-template.php';
            exit;
        }
    }
}

// Initialize frontend class
add_action( 'wp_loaded', function() {
    new FreeDownload_Frontend();
} );