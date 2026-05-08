<?php
declare(strict_types=1);
/**
 * Plugin Name: OTW Testimonials
 * Description: Testimonials manager with custom DB table, admin CRUD, shortcode, and Elementor widget. Supports Google, Facebook, Trustpilot, and Instagram card designs with carousel and grid layouts.
 * Version: 1.0.1
 * Author: OTW
 * Text Domain: otw-testimonials
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OTW_TESTIMONIALS_VERSION', '1.0.1' );
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
        require_once OTW_TESTIMONIALS_DIR . 'includes/class-css-generator.php';
        require_once OTW_TESTIMONIALS_DIR . 'includes/class-settings.php';
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
            OTW_Testimonials_Settings::get_instance();
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
        // Bundled Swiper — always use our own copy for predictable behaviour.
        wp_register_style( 'otw-swiper', OTW_TESTIMONIALS_URL . 'assets/css/vendor/swiper.min.css', array(), '11.0.0' );
        wp_register_script( 'otw-swiper', OTW_TESTIMONIALS_URL . 'assets/js/vendor/swiper.min.js', array(), '11.0.0', true );

        // Bundled GLightbox — own copy, no CDN dependency, no conflicts with page builders.
        wp_register_style( 'otw-glightbox', OTW_TESTIMONIALS_URL . 'assets/css/vendor/glightbox.min.css', array(), '3.3.0' );
        wp_register_script( 'otw-glightbox', OTW_TESTIMONIALS_URL . 'assets/js/vendor/glightbox.min.js', array(), '3.3.0', true );

        // Register core frontend CSS/JS — enqueueing happens below (shortcode pages) or via
        // Elementor's get_style_depends() / get_script_depends() (widget pages).
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

        // Enqueue CSS in <head> only on singular pages that contain the shortcode.
        // Elementor widget pages are handled by get_style_depends() — no global load needed.
        global $post;
        if ( is_singular() && isset( $post->post_content ) && has_shortcode( $post->post_content, 'otw_testimonials' ) ) {
            wp_enqueue_style( 'otw-testimonials-frontend' );
            wp_enqueue_style( 'otw-glightbox' );
            // Inject design settings CSS as inline style on the frontend stylesheet handle.
            $design_css = OTW_Testimonials_Settings::get_design_css();
            if ( $design_css ) {
                wp_add_inline_style( 'otw-testimonials-frontend', $design_css );
            }
        }
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
