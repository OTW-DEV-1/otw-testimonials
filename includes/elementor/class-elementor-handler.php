<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OTW_Testimonials_Elementor_Handler {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        add_action( 'elementor/elements/categories_registered', array( $this, 'register_categories' ) );
    }

    public function register_categories( $elements_manager ) {
        $elements_manager->add_category( 'otw-widgets', array(
            'title' => __( 'OTW Widgets', 'otw-testimonials' ),
            'icon'  => 'eicon-apps',
        ) );
    }

    public function register_widgets( $widgets_manager ) {
        require_once OTW_TESTIMONIALS_DIR . 'includes/elementor/class-testimonials-widget.php';
        $widgets_manager->register( new OTW_Testimonials_Elementor_Widget() );
    }
}
