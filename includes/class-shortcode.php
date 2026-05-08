<?php
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OTW_Testimonials_Shortcode {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode( 'otw_testimonials', array( $this, 'render' ) );
        add_action( 'wp_ajax_otw_load_more',        array( $this, 'ajax_load_more' ) );
        add_action( 'wp_ajax_nopriv_otw_load_more', array( $this, 'ajax_load_more' ) );
    }

    public function render( $atts ) {
        $atts = shortcode_atts( array(
            'layout'          => 'grid',
            'columns'         => 3,
            'columns_laptop'  => 3,
            'columns_tablet'  => 2,
            'columns_mobile'  => 1,
            'platform'       => 'all',
            'limit'          => 10,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'gap'            => 24,
            'related_to'     => '',
            'autoplay'       => '1',
            'autoplay_speed' => 3000,
            'loop'           => '1',
            'arrows'         => '1',
            'dots'           => '1',
            'read_more_text'  => 'Read more',
            'load_more_text'  => 'Load More',
        ), $atts, 'otw_testimonials' );

        $orderby_map = array(
            'date'       => 'created_at',
            'rating'     => 'rating',
            'random'     => 'random',
            'sort_order' => 'sort_order',
            'title'      => 'title',
        );

        $db_orderby = isset( $orderby_map[ $atts['orderby'] ] ) ? $orderby_map[ $atts['orderby'] ] : 'created_at';

        // Resolve related_to to a post ID.
        $related_post_id = 0;
        if ( ! empty( $atts['related_to'] ) ) {
            if ( $atts['related_to'] === 'current' ) {
                $related_post_id = absint( get_queried_object_id() );
            } elseif ( is_numeric( $atts['related_to'] ) ) {
                $related_post_id = absint( $atts['related_to'] );
            }
        }

        $testimonials = OTW_Testimonials_DB::get_all( array(
            'platform'        => $atts['platform'],
            'status'          => 'publish',
            'limit'           => absint( $atts['limit'] ),
            'orderby'         => $db_orderby,
            'order'           => $atts['order'],
            'related_post_id' => $related_post_id,
        ) );

        if ( empty( $testimonials ) ) {
            return '';
        }

        $this->enqueue_assets( $atts['layout'], $atts['read_more_text'] );

        $columns        = max( 1, min( 6, absint( $atts['columns'] ) ) );
        $columns_laptop = max( 1, min( 6, absint( $atts['columns_laptop'] ) ) );
        $columns_tablet = max( 1, min( 4, absint( $atts['columns_tablet'] ) ) );
        $columns_mobile = max( 1, min( 2, absint( $atts['columns_mobile'] ) ) );
        $gap            = absint( $atts['gap'] );

        ob_start();

        $wrapper_style = sprintf(
            '--otw-cols:%d;--otw-cols-laptop:%d;--otw-cols-tablet:%d;--otw-cols-mobile:%d;--otw-gap:%dpx;',
            $columns,
            $columns_laptop,
            $columns_tablet,
            $columns_mobile,
            $gap
        );

        if ( $atts['layout'] === 'carousel' ) {
            $this->render_carousel( $testimonials, $atts, $wrapper_style );
        } else {
            $limit = absint( $atts['limit'] );
            $shown = count( $testimonials );

            $has_more = $limit > 0 && $shown >= $limit && $shown < OTW_Testimonials_DB::get_count( array(
                'status'          => 'publish',
                'platform'        => $atts['platform'] !== 'all' ? $atts['platform'] : '',
                'related_post_id' => $related_post_id,
            ) );

            $more = array(
                'has_more'       => $has_more,
                'limit'          => $limit,
                'offset'         => $shown,
                'platform'       => $atts['platform'],
                'orderby'        => $db_orderby,
                'order'          => $atts['order'],
                'related'        => $related_post_id,
                'load_more_text' => sanitize_text_field( $atts['load_more_text'] ),
            );

            $this->render_grid( $testimonials, $wrapper_style, $more );
        }

        // Output JSON-LD schema markup.
        echo OTW_Testimonials_DB::build_schema_json( $testimonials, $related_post_id ); // phpcs:ignore WordPress.Security.EscapeOutput

        return ob_get_clean();
    }

    private function render_grid( $testimonials, $wrapper_style, $more = array() ) {
        ?>
        <div class="otw-testimonials-wrapper" style="<?php echo esc_attr( $wrapper_style ); ?>">
            <div class="otw-testimonials-grid">
                <?php foreach ( $testimonials as $testimonial ) : ?>
                    <?php $this->render_card( $testimonial ); ?>
                <?php endforeach; ?>
            </div>
            <?php if ( ! empty( $more['has_more'] ) ) : ?>
            <div class="otw-load-more-wrap">
                <button type="button" class="otw-load-more-btn"
                    data-limit="<?php echo esc_attr( $more['limit'] ); ?>"
                    data-offset="<?php echo esc_attr( $more['offset'] ); ?>"
                    data-platform="<?php echo esc_attr( $more['platform'] ); ?>"
                    data-orderby="<?php echo esc_attr( $more['orderby'] ); ?>"
                    data-order="<?php echo esc_attr( $more['order'] ); ?>"
                    data-related="<?php echo esc_attr( $more['related'] ); ?>"
                    data-nonce="<?php echo esc_attr( wp_create_nonce( 'otw_load_more' ) ); ?>">
                    <?php echo esc_html( ! empty( $more['load_more_text'] ) ? $more['load_more_text'] : __( 'Load More', 'otw-testimonials' ) ); ?>
                </button>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_carousel( $testimonials, $atts, $wrapper_style ) {
        $data_attrs = sprintf(
            'data-cols="%d" data-cols-laptop="%d" data-cols-tablet="%d" data-cols-mobile="%d" data-gap="%d" data-autoplay="%s" data-autoplay-speed="%d" data-loop="%s" data-arrows="%s" data-dots="%s"',
            absint( $atts['columns'] ),
            absint( $atts['columns_laptop'] ),
            absint( $atts['columns_tablet'] ),
            absint( $atts['columns_mobile'] ),
            absint( $atts['gap'] ),
            esc_attr( $atts['autoplay'] ),
            absint( $atts['autoplay_speed'] ),
            esc_attr( $atts['loop'] ),
            esc_attr( $atts['arrows'] ),
            esc_attr( $atts['dots'] )
        );
        ?>
        <div class="otw-testimonials-wrapper otw-testimonials-carousel" style="<?php echo esc_attr( $wrapper_style ); ?>" <?php echo $data_attrs; ?>>
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ( $testimonials as $testimonial ) : ?>
                        <div class="swiper-slide">
                            <?php $this->render_card( $testimonial ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if ( $atts['arrows'] === '1' ) : ?>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            <?php endif; ?>
            <?php if ( $atts['dots'] === '1' ) : ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function render_card( $testimonial ) {
        $platform = sanitize_file_name( $testimonial->platform );
        $template = OTW_TESTIMONIALS_DIR . 'templates/card-' . $platform . '.php';

        if ( file_exists( $template ) ) {
            $testimonial = (object) (array) $testimonial;
            $testimonial->description = wpautop( $testimonial->description );
            include $template;
        }
    }

    public function ajax_load_more() {
        check_ajax_referer( 'otw_load_more', 'nonce' );

        $limit           = max( 1, absint( $_POST['limit'] ?? 10 ) );
        $offset          = absint( $_POST['offset'] ?? 0 );
        $platform        = sanitize_text_field( $_POST['platform'] ?? 'all' );
        $orderby         = sanitize_text_field( $_POST['orderby'] ?? 'created_at' );
        $order           = strtoupper( sanitize_text_field( $_POST['order'] ?? 'DESC' ) );
        $related_post_id = absint( $_POST['related'] ?? 0 );

        $testimonials = OTW_Testimonials_DB::get_all( array(
            'platform'        => $platform,
            'status'          => 'publish',
            'limit'           => $limit,
            'offset'          => $offset,
            'orderby'         => $orderby,
            'order'           => $order,
            'related_post_id' => $related_post_id,
        ) );

        $total = OTW_Testimonials_DB::get_count( array(
            'status'          => 'publish',
            'platform'        => $platform !== 'all' ? $platform : '',
            'related_post_id' => $related_post_id,
        ) );

        ob_start();
        foreach ( $testimonials as $testimonial ) {
            $this->render_card( $testimonial );
        }
        $html = ob_get_clean();

        $next_offset = $offset + count( $testimonials );

        wp_send_json_success( array(
            'html'        => $html,
            'has_more'    => $next_offset < $total,
            'next_offset' => $next_offset,
        ) );
    }

    private function enqueue_assets( $layout, $read_more_text = 'Read more' ) {
        // CSS is already enqueued in wp_enqueue_scripts (must be in <head>).
        // Only enqueue footer scripts here — wp_footer fires after the_content so timing is fine.
        wp_enqueue_script( 'otw-testimonials-frontend' );
        wp_enqueue_script( 'otw-glightbox' );

        wp_localize_script( 'otw-testimonials-frontend', 'otwFrontend', array(
            'ajaxurl'      => admin_url( 'admin-ajax.php' ),
            'readMoreText' => sanitize_text_field( $read_more_text ),
        ) );

        if ( $layout === 'carousel' ) {
            // Use Elementor's (or any other plugin's) already-registered Swiper to avoid
            // CSS/JS conflicts. Only fall back to our bundle when nothing else provides it.
            $swiper_style  = wp_style_is( 'swiper', 'registered' )  ? 'swiper'     : 'otw-swiper';
            $swiper_script = wp_script_is( 'swiper', 'registered' ) ? 'swiper'     : 'otw-swiper';
            wp_enqueue_style( $swiper_style );
            wp_enqueue_script( $swiper_script );
        }
    }
}
