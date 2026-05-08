<?php
declare(strict_types=1);
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
        return array( 'testimonials', 'reviews', 'google', 'facebook', 'trustpilot', 'instagram', 'carousel', 'otw' );
    }

    public function get_style_depends() {
        return array( 'otw-glightbox', 'otw-testimonials-frontend' );
    }

    public function get_script_depends() {
        return array( 'otw-glightbox', 'otw-testimonials-frontend' );
    }

    protected function register_controls() {
        $this->register_query_controls();
        $this->register_layout_controls();
        $this->register_carousel_controls();
        $this->register_style_card_controls();
        $this->register_style_avatar_controls();
        $this->register_style_content_controls();
        $this->register_style_rating_controls();
        $this->register_style_loadmore_controls();
        $this->register_style_carousel_nav_controls();
        $this->register_style_gallery_controls();
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
                'instagram'  => __( 'Instagram', 'otw-testimonials' ),
                'blank'      => __( 'Other (no platform)', 'otw-testimonials' ),
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
            'widescreen_default' => '3',
            'laptop_default' => '3',
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

        $this->add_control( 'read_more_text', array(
            'label'       => __( 'Read More Text', 'otw-testimonials' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => __( 'Read more', 'otw-testimonials' ),
            'placeholder' => __( 'Read more', 'otw-testimonials' ),
            'separator'   => 'before',
        ) );

        $this->add_control( 'show_load_more', array(
            'label'        => __( 'Load More Button', 'otw-testimonials' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => '',
            'return_value' => 'yes',
            'condition'    => array( 'layout' => 'grid' ),
        ) );

        $this->add_control( 'load_more_text', array(
            'label'       => __( 'Load More Text', 'otw-testimonials' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => __( 'Load More', 'otw-testimonials' ),
            'placeholder' => __( 'Load More', 'otw-testimonials' ),
            'condition'   => array( 'layout' => 'grid', 'show_load_more' => 'yes' ),
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

        $this->add_responsive_control( 'card_margin', array(
            'label'      => __( 'Margin', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', 'em', '%' ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-testimonial-card' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
            'separator'  => 'before',
        ) );

        $this->add_control( 'heading_card_hover', array(
            'label'     => __( 'Hover State', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'card_hover_bg_color', array(
            'label'     => __( 'Hover Background', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .otw-testimonial-card:hover' => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
            'name'     => 'card_hover_shadow',
            'selector' => '{{WRAPPER}} .otw-testimonial-card:hover',
        ) );

        $this->add_control( 'card_hover_transform', array(
            'label'       => __( 'Hover Transform', 'otw-testimonials' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'translateY(-4px)',
            'selectors'   => array(
                '{{WRAPPER}} .otw-testimonial-card:hover' => 'transform: {{VALUE}};',
            ),
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

        $this->add_control( 'heading_position_style', array(
            'label'     => __( 'Position / Job Title', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'position_color', array(
            'label'     => __( 'Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .otw-card__position' => 'color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name'     => 'position_typography',
            'selector' => '{{WRAPPER}} .otw-card__position',
        ) );

        $this->add_control( 'heading_date_style', array(
            'label'     => __( 'Date', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'date_show', array(
            'label'        => __( 'Show Date', 'otw-testimonials' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
            'selectors'    => array(
                '{{WRAPPER}} .otw-card__date' => 'display: {{VALUE}};',
            ),
            'selectors_dictionary' => array(
                ''    => 'none',
                'yes' => 'inline',
            ),
        ) );

        $this->add_control( 'date_color', array(
            'label'     => __( 'Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .otw-card__date' => 'color: {{VALUE}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name'     => 'date_typography',
            'selector' => '{{WRAPPER}} .otw-card__date',
        ) );

        $this->add_control( 'heading_platform_icon', array(
            'label'     => __( 'Platform / Quote Icon', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'platform_icon_show', array(
            'label'        => __( 'Show Platform Icon', 'otw-testimonials' ),
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
            'selectors'    => array(
                '{{WRAPPER}} .otw-card__platform-icon' => 'display: {{VALUE}};',
                '{{WRAPPER}} .otw-card__quote-icon'    => 'display: {{VALUE}};',
            ),
            'selectors_dictionary' => array(
                ''    => 'none',
                'yes' => 'block',
            ),
        ) );

        $this->add_responsive_control( 'platform_icon_size', array(
            'label'      => __( 'Icon Size (px)', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'range'      => array( 'px' => array( 'min' => 12, 'max' => 48 ) ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-card__platform-icon svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
                '{{WRAPPER}} .otw-card__quote-icon svg'    => 'width: {{SIZE}}px; height: auto;',
            ),
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

    private function register_style_avatar_controls() {
        $this->start_controls_section( 'section_style_avatar', array(
            'label' => __( 'Avatar', 'otw-testimonials' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_responsive_control( 'avatar_size', array(
            'label'      => __( 'Size', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'range'      => array( 'px' => array( 'min' => 24, 'max' => 120 ) ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-card__avatar' => 'width: {{SIZE}}px; height: {{SIZE}}px; min-width: {{SIZE}}px;',
            ),
        ) );

        $this->add_responsive_control( 'avatar_border_radius', array(
            'label'      => __( 'Border Radius', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', '%' ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-card__avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
            'name'     => 'avatar_border',
            'selector' => '{{WRAPPER}} .otw-card__avatar',
        ) );

        $this->end_controls_section();
    }

    private function register_style_loadmore_controls() {
        $this->start_controls_section( 'section_style_loadmore', array(
            'label'     => __( 'Load More Button', 'otw-testimonials' ),
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'layout' => 'grid', 'show_load_more' => 'yes' ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
            'name'     => 'loadmore_typography',
            'selector' => '{{WRAPPER}} .otw-load-more-btn',
        ) );

        $this->start_controls_tabs( 'loadmore_tabs' );

        $this->start_controls_tab( 'loadmore_normal', array( 'label' => __( 'Normal', 'otw-testimonials' ) ) );
        $this->add_control( 'loadmore_color', array(
            'label'     => __( 'Text Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array( '{{WRAPPER}} .otw-load-more-btn' => 'color: {{VALUE}};' ),
        ) );
        $this->add_control( 'loadmore_bg_color', array(
            'label'     => __( 'Background', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array( '{{WRAPPER}} .otw-load-more-btn' => 'background-color: {{VALUE}};' ),
        ) );
        $this->end_controls_tab();

        $this->start_controls_tab( 'loadmore_hover', array( 'label' => __( 'Hover', 'otw-testimonials' ) ) );
        $this->add_control( 'loadmore_hover_color', array(
            'label'     => __( 'Text Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array( '{{WRAPPER}} .otw-load-more-btn:hover' => 'color: {{VALUE}};' ),
        ) );
        $this->add_control( 'loadmore_hover_bg_color', array(
            'label'     => __( 'Background', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array( '{{WRAPPER}} .otw-load-more-btn:hover' => 'background-color: {{VALUE}};' ),
        ) );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
            'name'      => 'loadmore_border',
            'selector'  => '{{WRAPPER}} .otw-load-more-btn',
            'separator' => 'before',
        ) );

        $this->add_responsive_control( 'loadmore_border_radius', array(
            'label'      => __( 'Border Radius', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', '%' ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-load-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );

        $this->add_responsive_control( 'loadmore_padding', array(
            'label'      => __( 'Padding', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', 'em' ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );

        $this->end_controls_section();
    }

    private function register_style_carousel_nav_controls() {
        $this->start_controls_section( 'section_style_carousel_nav', array(
            'label'     => __( 'Carousel Navigation', 'otw-testimonials' ),
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'layout' => 'carousel' ),
        ) );

        $this->add_control( 'heading_arrows', array(
            'label' => __( 'Arrows', 'otw-testimonials' ),
            'type'  => \Elementor\Controls_Manager::HEADING,
        ) );

        $this->add_control( 'arrow_color', array(
            'label'     => __( 'Arrow Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'color: {{VALUE}}; --swiper-navigation-color: {{VALUE}};',
            ),
        ) );

        $this->add_control( 'arrow_bg_color', array(
            'label'     => __( 'Arrow Background', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_responsive_control( 'arrow_size', array(
            'label'      => __( 'Arrow Icon Size', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'range'      => array( 'px' => array( 'min' => 8, 'max' => 40 ) ),
            'selectors'  => array(
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => '--swiper-navigation-size: {{SIZE}}px;',
            ),
        ) );

        $this->add_responsive_control( 'arrow_button_size', array(
            'label'      => __( 'Button Size', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'range'      => array( 'px' => array( 'min' => 24, 'max' => 80 ) ),
            'selectors'  => array(
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
            ),
        ) );

        $this->add_responsive_control( 'arrow_border_radius', array(
            'label'      => __( 'Border Radius', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', '%' ),
            'selectors'  => array(
                '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
            'name'     => 'arrow_shadow',
            'selector' => '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next',
        ) );

        $this->add_control( 'heading_dots', array(
            'label'     => __( 'Pagination Dots', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ) );

        $this->add_control( 'dot_color', array(
            'label'     => __( 'Dot Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_control( 'dot_active_color', array(
            'label'     => __( 'Active Dot Color', 'otw-testimonials' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
            ),
        ) );

        $this->add_responsive_control( 'dot_size', array(
            'label'      => __( 'Dot Size', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'range'      => array( 'px' => array( 'min' => 4, 'max' => 20 ) ),
            'selectors'  => array(
                '{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
            ),
        ) );

        $this->end_controls_section();
    }

    private function register_style_gallery_controls() {
        $this->start_controls_section( 'section_style_gallery', array(
            'label' => __( 'Gallery Images', 'otw-testimonials' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ) );

        $this->add_responsive_control( 'gallery_border_radius', array(
            'label'      => __( 'Border Radius', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => array( 'px', '%' ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-gallery__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ),
        ) );

        $this->add_responsive_control( 'gallery_gap', array(
            'label'      => __( 'Gap Between Images', 'otw-testimonials' ),
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
            'selectors'  => array(
                '{{WRAPPER}} .otw-card__gallery' => 'gap: {{SIZE}}px;',
            ),
        ) );

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
            'name'     => 'gallery_border',
            'selector' => '{{WRAPPER}} .otw-gallery__item',
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

        $read_more_text = ! empty( $settings['read_more_text'] ) ? $settings['read_more_text'] : __( 'Read more', 'otw-testimonials' );
        $this->enqueue_frontend_assets( $settings['layout'], $read_more_text );

        $columns        = ! empty( $settings['columns'] ) ? $settings['columns'] : 3;
        $columns_laptop = isset( $settings['columns_laptop'] ) ? $settings['columns_laptop'] : ( isset( $settings['columns__laptop'] ) ? $settings['columns__laptop'] : $columns );
        $columns_tablet = isset( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : ( isset( $settings['columns__tablet'] ) ? $settings['columns__tablet'] : 2 );
        $columns_mobile = isset( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : ( isset( $settings['columns__mobile'] ) ? $settings['columns__mobile'] : 1 );
        $gap            = isset( $settings['gap']['size'] ) ? $settings['gap']['size'] : 24;

        $wrapper_style = sprintf(
            '--otw-cols:%d;--otw-cols-laptop:%d;--otw-cols-tablet:%d;--otw-cols-mobile:%d;--otw-gap:%dpx;',
            $columns,
            $columns_laptop,
            $columns_tablet,
            $columns_mobile,
            $gap
        );

        $layout = $settings['layout'];

        if ( $layout === 'carousel' ) {
            $data_attrs = sprintf(
                'data-cols="%d" data-cols-laptop="%d" data-cols-tablet="%d" data-cols-mobile="%d" data-gap="%d" data-autoplay="%s" data-autoplay-speed="%d" data-loop="%s" data-arrows="%s" data-dots="%s"',
                $columns,
                $columns_laptop,
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
            $limit          = absint( $settings['limit'] );
            $shown          = count( $testimonials );
            $show_load_more = ! empty( $settings['show_load_more'] ) && $settings['show_load_more'] === 'yes';
            $load_more_text = ! empty( $settings['load_more_text'] ) ? sanitize_text_field( $settings['load_more_text'] ) : __( 'Load More', 'otw-testimonials' );

            $has_more = false;
            if ( $show_load_more && $limit > 0 && $shown >= $limit ) {
                $has_more = $shown < OTW_Testimonials_DB::get_count( array(
                    'status'          => 'publish',
                    'platform'        => $settings['platform'] !== 'all' ? $settings['platform'] : '',
                    'related_post_id' => $related_post_id,
                ) );
            }
            ?>
            <div class="otw-testimonials-wrapper" style="<?php echo esc_attr( $wrapper_style ); ?>">
                <div class="otw-testimonials-grid">
                    <?php foreach ( $testimonials as $testimonial ) : ?>
                        <?php $this->render_card( $testimonial ); ?>
                    <?php endforeach; ?>
                </div>
                <?php if ( $has_more ) : ?>
                <div class="otw-load-more-wrap">
                    <button type="button" class="otw-load-more-btn"
                        data-limit="<?php echo esc_attr( $limit ); ?>"
                        data-offset="<?php echo esc_attr( $shown ); ?>"
                        data-platform="<?php echo esc_attr( $settings['platform'] ); ?>"
                        data-orderby="<?php echo esc_attr( $db_orderby ); ?>"
                        data-order="<?php echo esc_attr( $settings['order'] ); ?>"
                        data-related="<?php echo esc_attr( $related_post_id ); ?>"
                        data-nonce="<?php echo esc_attr( wp_create_nonce( 'otw_load_more' ) ); ?>">
                        <?php echo esc_html( $load_more_text ); ?>
                    </button>
                </div>
                <?php endif; ?>
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
            $testimonial = (object) (array) $testimonial;
            $testimonial->description = wpautop( $testimonial->description );
            include $template;
        }
    }

    private function enqueue_frontend_assets( $layout, $read_more_text = 'Read more' ) {
        wp_enqueue_style( 'otw-testimonials-frontend' );
        wp_enqueue_style( 'otw-glightbox' );
        wp_enqueue_script( 'otw-glightbox' );
        wp_enqueue_script( 'otw-testimonials-frontend' );

        wp_localize_script( 'otw-testimonials-frontend', 'otwFrontend', array(
            'ajaxurl'      => admin_url( 'admin-ajax.php' ),
            'readMoreText' => sanitize_text_field( $read_more_text ),
        ) );

        if ( $layout === 'carousel' ) {
            // Elementor always has its own Swiper registered — use it to avoid conflicts.
            // Fall back to our bundle only if nothing else provides Swiper.
            $swiper_style  = wp_style_is( 'swiper', 'registered' )  ? 'swiper'     : 'otw-swiper';
            $swiper_script = wp_script_is( 'swiper', 'registered' ) ? 'swiper'     : 'otw-swiper';
            wp_enqueue_style( $swiper_style );
            wp_enqueue_script( $swiper_script );
        }
    }
}
