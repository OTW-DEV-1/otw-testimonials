<?php
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
    }

    public function render( $atts ) {
        $atts = shortcode_atts( array(
            'layout'         => 'grid',
            'columns'        => 3,
            'columns_tablet' => 2,
            'columns_mobile' => 1,
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

        $this->enqueue_assets( $atts['layout'] );

        $columns        = max( 1, min( 6, absint( $atts['columns'] ) ) );
        $columns_tablet = max( 1, min( 4, absint( $atts['columns_tablet'] ) ) );
        $columns_mobile = max( 1, min( 2, absint( $atts['columns_mobile'] ) ) );
        $gap            = absint( $atts['gap'] );

        ob_start();

        $wrapper_style = sprintf(
            '--otw-cols:%d;--otw-cols-tablet:%d;--otw-cols-mobile:%d;--otw-gap:%dpx;',
            $columns,
            $columns_tablet,
            $columns_mobile,
            $gap
        );

        if ( $atts['layout'] === 'carousel' ) {
            $this->render_carousel( $testimonials, $atts, $wrapper_style );
        } else {
            $this->render_grid( $testimonials, $wrapper_style );
        }

        // Output JSON-LD schema markup.
        echo OTW_Testimonials_DB::build_schema_json( $testimonials, $related_post_id ); // phpcs:ignore WordPress.Security.EscapeOutput

        return ob_get_clean();
    }

    private function render_grid( $testimonials, $wrapper_style ) {
        ?>
        <div class="otw-testimonials-wrapper" style="<?php echo esc_attr( $wrapper_style ); ?>">
            <div class="otw-testimonials-grid">
                <?php foreach ( $testimonials as $testimonial ) : ?>
                    <?php $this->render_card( $testimonial ); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function render_carousel( $testimonials, $atts, $wrapper_style ) {
        $data_attrs = sprintf(
            'data-cols="%d" data-cols-tablet="%d" data-cols-mobile="%d" data-gap="%d" data-autoplay="%s" data-autoplay-speed="%d" data-loop="%s" data-arrows="%s" data-dots="%s"',
            absint( $atts['columns'] ),
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

    private function render_card( $testimonial ) {
        $platform = sanitize_file_name( $testimonial->platform );
        $template = OTW_TESTIMONIALS_DIR . 'templates/card-' . $platform . '.php';

        if ( file_exists( $template ) ) {
            include $template;
        }
    }

    private function enqueue_assets( $layout ) {
        wp_enqueue_style(
            'otw-testimonials-frontend',
            OTW_TESTIMONIALS_URL . 'assets/css/frontend.css',
            array(),
            filemtime( OTW_TESTIMONIALS_DIR . 'assets/css/frontend.css' )
        );

        if ( $layout === 'carousel' ) {
            if ( wp_script_is( 'swiper', 'registered' ) ) {
                wp_enqueue_script( 'swiper' );
            } else {
                wp_enqueue_style(
                    'swiper',
                    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
                    array(),
                    '11.0.0'
                );
                wp_enqueue_script(
                    'swiper',
                    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
                    array(),
                    '11.0.0',
                    true
                );
            }
        }

        wp_enqueue_script(
            'otw-testimonials-frontend',
            OTW_TESTIMONIALS_URL . 'assets/js/frontend.js',
            $layout === 'carousel' ? array( 'swiper' ) : array(),
            filemtime( OTW_TESTIMONIALS_DIR . 'assets/js/frontend.js' ),
            true
        );
    }
}
