<?php
/**
 * Plugin Name: OTW Testimonials
 * Description: Testimonials manager with custom DB table, admin CRUD, shortcode, and Elementor widget. Supports Google, Facebook, and Trustpilot card designs with carousel and grid layouts.
 * Version: 1.0.0
 * Author: OTW
 * Text Domain: otw-testimonials
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OTW_TESTIMONIALS_VERSION', '1.0.0' );
define( 'OTW_TESTIMONIALS_DIR', plugin_dir_path( __FILE__ ) );
define( 'OTW_TESTIMONIALS_URL', plugin_dir_url( __FILE__ ) );
define( 'OTW_TESTIMONIALS_BASENAME', plugin_basename( __FILE__ ) );

final class OTW_Testimonials {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    private function load_dependencies() {
        require_once OTW_TESTIMONIALS_DIR . 'includes/class-activator.php';
        require_once OTW_TESTIMONIALS_DIR . 'includes/class-db.php';
        require_once OTW_TESTIMONIALS_DIR . 'includes/class-admin.php';
        require_once OTW_TESTIMONIALS_DIR . 'includes/class-list-table.php';
        require_once OTW_TESTIMONIALS_DIR . 'includes/class-shortcode.php';
    }

    private function init_hooks() {
        register_activation_hook( __FILE__, array( 'OTW_Testimonials_Activator', 'activate' ) );

        // Auto-create/update DB table if version changed or table missing.
        $this->maybe_create_table();

        if ( is_admin() ) {
            OTW_Testimonials_Admin::get_instance();
        }

        OTW_Testimonials_Shortcode::get_instance();

        add_action( 'elementor/init', array( $this, 'init_elementor' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ), 5 );
    }

    /**
     * Register (but don't enqueue) frontend assets early so Elementor's
     * get_style_depends() / get_script_depends() can reference them before wp_head() fires.
     */
    public function register_frontend_assets() {
        // Register Swiper from CDN only if not already registered (e.g. by Elementor).
        if ( ! wp_style_is( 'swiper', 'registered' ) ) {
            wp_register_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );
        }
        if ( ! wp_script_is( 'swiper', 'registered' ) ) {
            wp_register_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );
        }

        wp_register_style(
            'otw-testimonials-frontend',
            OTW_TESTIMONIALS_URL . 'assets/css/frontend.css',
            array(),
            filemtime( OTW_TESTIMONIALS_DIR . 'assets/css/frontend.css' )
        );

        wp_register_script(
            'otw-testimonials-frontend',
            OTW_TESTIMONIALS_URL . 'assets/js/frontend.js',
            array(),
            filemtime( OTW_TESTIMONIALS_DIR . 'assets/js/frontend.js' ),
            true
        );
    }

    private function maybe_create_table() {
        $installed_version = get_option( 'otw_testimonials_db_version', '' );
        if ( $installed_version !== OTW_Testimonials_Activator::DB_VERSION ) {
            OTW_Testimonials_Activator::activate();
        }
    }

    public function init_elementor() {
        require_once OTW_TESTIMONIALS_DIR . 'includes/elementor/class-elementor-handler.php';
        OTW_Testimonials_Elementor_Handler::get_instance();
    }
}

add_action( 'plugins_loaded', function () {
    OTW_Testimonials::get_instance();
} );
