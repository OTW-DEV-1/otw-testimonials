<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OTW_Testimonials_Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'otw-testimonials';
    }

    public function get_title() {
        return __( 'OTW Testimonials', 'otw-testimonials' );
    }

    public function get_icon() {
        return 'eicon-testimonial';
    }

    public function get_categories() {
        return array( 'otw-widgets' );
    }

    public function get_keywords() {
        return array( 'testimonials', 'reviews', 'google', 'facebook', 'trustpilot', 'carousel', 'otw' );
    }

    public function get_style_depends() {
        return array( 'otw-testimonials-frontend' );
    }

    public function get_script_depends() {
        return array( 'otw-testimonials-frontend', 'swiper' );
    }

    protected function register_controls() {
        $this->register_query_controls();
        $this->register_layout_controls();
        $this->register_carousel_controls();
        $this->register_style_card_controls();
        $this->register_style_content_controls();
        $this->register_style_rating_controls();
    }

    private function register_query_controls() {
        $this->start_controls_section( 'section_query', array(
            'label' => __( 'Query', 'otw-testimonials' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'platform', array(
            'label'   => __( 'Platform', 'otw-testimonials' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'all',
            'options' => array(
                'all'        => __( 'All', 'otw-testimonials' ),
                'google'     => __( 'Google', 'otw-testimonials' ),
                'facebook'   => __( 'Facebook', 'otw-testimonials' ),
                'trustpilot' => __( 'Trustpilot', 'otw-testimonials' ),
            ),
        ) );

        $this->add_control( 'limit', array(
            'label'   => __( 'Limit', 'otw-testimonials' ),
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'default' => 10,
            'min'     => 1,
            'max'     => 100,
        ) );

        $this->add_control( 'orderby', array(
            'label'   => __( 'Order By', 'otw-testimonials' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'date',
            'options' => array(
                'date'       => __( 'Date', 'otw-testimonials' ),
                'rating'     => __( 'Rating', 'otw-testimonials' ),
                'random'     => __( 'Random', 'otw-testimonials' ),
                'sort_order' => __( 'Sort Order', 'otw-testimonials' ),
                'title'      => __( 'Title', 'otw-testimonials' ),
            ),
        ) );

        $this->add_control( 'order', array(
            'label'   => __( 'Order', 'otw-testimonials' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'DESC',
            'options' => array(
                'DESC' => __( 'Descending', 'otw-testimonials' ),
                'ASC'  => __( 'Ascending', 'otw-testimonials' ),
            ),
        ) );

        $this->add_control( 'related_to', array(
            'label'       => __( 'Related To', 'otw-testimonials' ),
            'type'        => \Elementor\Controls_Manager::SELECT,
            'default'     => 'none',
            'options'     => array(
                'none'     => __( 'No filter', 'otw-testimonials' ),
                'current'  => __( 'Current page / post / product', 'otw-testimonials' ),
                'specific' => __( 'Specific post ID', 'otw-testimonials' ),
            ),
            'separator'   => 'before',
            'description' => __( '"Current" auto-detects the page being viewed. Useful on single product or post pages.', 'otw-testimonials' ),
        ) );

        $this->add_control( 'related_post_id', array(
            'label'     => __( 'Post / Product ID', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::NUMBER,
            'default'   => 0,
            'min'       => 1,
            'condition' => array( 'related_to' => 'specific' ),
        ) );

        $this->end_controls_section();
    }

    private function register_layout_controls() {
        $this->start_controls_section( 'section_layout', array(
            'label' => __( 'Layout', 'otw-testimonials' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'layout', array(
            'label'   => __( 'Layout', 'otw-testimonials' ),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'grid',
            'options' => array(
                'grid'     => __( 'Grid', 'otw-testimonials' ),
                'carousel' => __( 'Carousel', 'otw-testimonials' ),
            ),
        ) );

        $this->add_responsive_control( 'columns', array(
            'label'          => __( 'Columns', 'otw-testimonials' ),
            'type'           => \Elementor\Controls_Manager::SELECT,
            'default'        => '3',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'options'        => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
            ),
        ) );

        $this->add_responsive_control( 'gap', array(
            'label'      => __( 'Gap', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'default'    => array( 'size' => 24 ),
            'range'      => array(
                'px' => array( 'min' => 0, 'max' => 80 ),
            ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-testimonials-wrapper' => '--otw-gap: {{SIZE}}px;',
            ),
        ) );

        $this->end_controls_section();
    }

    private function register_carousel_controls() {
        $this->start_controls_section( 'section_carousel', array(
            'label'     => __( 'Carousel', 'otw-testimonials' ),
            'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
            'condition' => array( 'layout' => 'carousel' ),
        ) );

        $this->add_control( 'autoplay', array(
            'label'        => __( 'Autoplay', 'otw-testimonials' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ) );

        $this->add_control( 'autoplay_speed', array(
            'label'     => __( 'Autoplay Speed (ms)', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::NUMBER,
            'default'   => 3000,
            'min'       => 500,
            'max'       => 10000,
            'step'      => 100,
            'condition' => array( 'autoplay' => 'yes' ),
        ) );

        $this->add_control( 'loop', array(
            'label'        => __( 'Loop', 'otw-testimonials' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ) );

        $this->add_control( 'arrows', array(
            'label'        => __( 'Arrows', 'otw-testimonials' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ) );

        $this->add_control( 'dots', array(
            'label'        => __( 'Pagination Dots', 'otw-testimonials' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ) );

        $this->end_controls_section();
    }

    private function register_style_card_controls() {
        $this->start_controls_section( 'section_style_card', array(
            'label' => __( 'Card', 'otw-testimonials' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'card_bg_color', array(
            'label'     => __( 'Background Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .otw-testimonial-card' => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
            'name'     => 'card_border',
            'selector' => '{{WRAPPER}} .otw-testimonial-card',
        ) );

        $this->add_responsive_control( 'card_border_radius', array(
            'label'      => __( 'Border Radius', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', '%' ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-testimonial-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );

        $this->add_responsive_control( 'card_padding', array(
            'label'      => __( 'Padding', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', 'em' ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-testimonial-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
            'name'     => 'card_shadow',
            'selector' => '{{WRAPPER}} .otw-testimonial-card',
        ) );

        $this->end_controls_section();
    }

    private function register_style_content_controls() {
        $this->start_controls_section( 'section_style_content', array(
            'label' => __( 'Content', 'otw-testimonials' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'heading_title_style', array(
            'label'     => __( 'Title', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
        ) );

        $this->add_control( 'title_color', array(
            'label'     => __( 'Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .otw-card__title' => 'color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .otw-card__title',
        ) );

        $this->add_control( 'heading_desc_style', array(
            'label'     => __( 'Description', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'desc_color', array(
            'label'     => __( 'Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .otw-card__content' => 'color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name'     => 'desc_typography',
            'selector' => '{{WRAPPER}} .otw-card__content',
        ) );

        $this->add_control( 'heading_name_style', array(
            'label'     => __( 'Author Name', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'name_color', array(
            'label'     => __( 'Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .otw-card__author-name' => 'color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name'     => 'name_typography',
            'selector' => '{{WRAPPER}} .otw-card__author-name',
        ) );

        $this->end_controls_section();
    }

    private function register_style_rating_controls() {
        $this->start_controls_section( 'section_style_rating', array(
            'label' => __( 'Rating', 'otw-testimonials' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'star_color', array(
            'label'     => __( 'Star Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .otw-star--filled path' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .otw-tp-star--filled'   => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_control( 'star_empty_color', array(
            'label'     => __( 'Empty Star Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .otw-star--empty path' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .otw-tp-star--empty'   => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_responsive_control( 'star_size', array(
            'label'      => __( 'Star Size', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'range'      => array(
                'px' => array( 'min' => 10, 'max' => 40 ),
            ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-star'     => 'width: {{SIZE}}px; height: {{SIZE}}px;',
                '{{WRAPPER}} .otw-tp-star'  => 'width: calc({{SIZE}}px + 8px); height: calc({{SIZE}}px + 8px);',
                '{{WRAPPER}} .otw-tp-star svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
            ),
        ) );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $orderby_map = array(
            'date'       => 'created_at',
            'rating'     => 'rating',
            'random'     => 'random',
            'sort_order' => 'sort_order',
            'title'      => 'title',
        );

        $db_orderby = isset( $orderby_map[ $settings['orderby'] ] ) ? $orderby_map[ $settings['orderby'] ] : 'created_at';

        // Resolve related_to to a post ID.
        $related_post_id = 0;
        $related_to      = $settings['related_to'] ?? 'none';
        if ( $related_to === 'current' ) {
            $related_post_id = absint( get_queried_object_id() );
        } elseif ( $related_to === 'specific' && ! empty( $settings['related_post_id'] ) ) {
            $related_post_id = absint( $settings['related_post_id'] );
        }

        $testimonials = OTW_Testimonials_DB::get_all( array(
            'platform'        => $settings['platform'],
            'status'          => 'publish',
            'limit'           => absint( $settings['limit'] ),
            'orderby'         => $db_orderby,
            'order'           => $settings['order'],
            'related_post_id' => $related_post_id,
        ) );

        if ( empty( $testimonials ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<p style="text-align:center;padding:40px;color:#999;">' . esc_html__( 'No testimonials found. Add some from the Testimonials admin page.', 'otw-testimonials' ) . '</p>';
            }
            return;
        }

        $this->enqueue_frontend_assets( $settings['layout'] );

        $columns        = ! empty( $settings['columns'] ) ? $settings['columns'] : 3;
        $columns_tablet = isset( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : ( isset( $settings['columns__tablet'] ) ? $settings['columns__tablet'] : 2 );
        $columns_mobile = isset( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : ( isset( $settings['columns__mobile'] ) ? $settings['columns__mobile'] : 1 );
        $gap            = isset( $settings['gap']['size'] ) ? $settings['gap']['size'] : 24;

        $wrapper_style = sprintf(
            '--otw-cols:%d;--otw-cols-tablet:%d;--otw-cols-mobile:%d;--otw-gap:%dpx;',
            $columns,
            $columns_tablet,
            $columns_mobile,
            $gap
        );

        $layout = $settings['layout'];

        if ( $layout === 'carousel' ) {
            $data_attrs = sprintf(
                'data-cols="%d" data-cols-tablet="%d" data-cols-mobile="%d" data-gap="%d" data-autoplay="%s" data-autoplay-speed="%d" data-loop="%s" data-arrows="%s" data-dots="%s"',
                $columns,
                $columns_tablet,
                $columns_mobile,
                $gap,
                $settings['autoplay'] === 'yes' ? '1' : '0',
                absint( $settings['autoplay_speed'] ?: 3000 ),
                $settings['loop'] === 'yes' ? '1' : '0',
                $settings['arrows'] === 'yes' ? '1' : '0',
                $settings['dots'] === 'yes' ? '1' : '0'
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
                <?php if ( $settings['arrows'] === 'yes' ) : ?>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                <?php endif; ?>
                <?php if ( $settings['dots'] === 'yes' ) : ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>
            </div>
            <?php
        } else {
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

        // Output JSON-LD schema markup.
        echo OTW_Testimonials_DB::build_schema_json( $testimonials, $related_post_id ); // phpcs:ignore WordPress.Security.EscapeOutput
    }

    private function render_card( $testimonial ) {
        $platform = sanitize_file_name( $testimonial->platform );
        $template = OTW_TESTIMONIALS_DIR . 'templates/card-' . $platform . '.php';

        if ( file_exists( $template ) ) {
            include $template;
        }
    }

    private function enqueue_frontend_assets( $layout ) {
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
                wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );
                wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );
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
