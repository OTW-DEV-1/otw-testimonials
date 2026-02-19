<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OTW_Testimonials_Admin {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_init', array( $this, 'handle_form_submission' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        add_action( 'wp_ajax_otw_search_posts', array( $this, 'ajax_search_posts' ) );
    }

    public function add_menu_pages() {
        add_menu_page(
            __( 'Testimonials', 'otw-testimonials' ),
            __( 'Testimonials', 'otw-testimonials' ),
            'manage_options',
            'otw-testimonials',
            array( $this, 'render_page' ),
            'dashicons-testimonial',
            26
        );
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'otw-testimonials' ) === false ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style(
            'otw-testimonials-admin',
            OTW_TESTIMONIALS_URL . 'assets/css/admin.css',
            array(),
            filemtime( OTW_TESTIMONIALS_DIR . 'assets/css/admin.css' )
        );
        wp_enqueue_script(
            'otw-testimonials-admin',
            OTW_TESTIMONIALS_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            filemtime( OTW_TESTIMONIALS_DIR . 'assets/js/admin.js' ),
            true
        );
        wp_localize_script( 'otw-testimonials-admin', 'otwAdmin', array(
            'ajaxurl'     => admin_url( 'admin-ajax.php' ),
            'searchNonce' => wp_create_nonce( 'otw_search_posts' ),
            'searching'   => __( 'Searching...', 'otw-testimonials' ),
            'noResults'   => __( 'No results found.', 'otw-testimonials' ),
        ) );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';

        switch ( $action ) {
            case 'add':
            case 'edit':
                $this->render_form();
                break;
            default:
                $this->render_list();
                break;
        }
    }

    private function render_list() {
        $list_table = new OTW_Testimonials_List_Table();
        $list_table->prepare_items();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'Testimonials', 'otw-testimonials' ); ?></h1>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=otw-testimonials&action=add' ) ); ?>" class="page-title-action">
                <?php esc_html_e( 'Add New', 'otw-testimonials' ); ?>
            </a>
            <hr class="wp-header-end">
            <form method="get">
                <input type="hidden" name="page" value="otw-testimonials">
                <?php $list_table->display(); ?>
            </form>

            <div class="otw-shortcode-help">
                <h2><?php esc_html_e( 'Shortcode Usage', 'otw-testimonials' ); ?></h2>
                <p><?php esc_html_e( 'Use the following shortcode to display testimonials on any page or post:', 'otw-testimonials' ); ?></p>
                <code class="otw-shortcode-example">[otw_testimonials]</code>

                <h3><?php esc_html_e( 'Parameters', 'otw-testimonials' ); ?></h3>
                <table class="widefat fixed otw-params-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Parameter', 'otw-testimonials' ); ?></th>
                            <th><?php esc_html_e( 'Default', 'otw-testimonials' ); ?></th>
                            <th><?php esc_html_e( 'Options', 'otw-testimonials' ); ?></th>
                            <th><?php esc_html_e( 'Description', 'otw-testimonials' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><code>layout</code></td><td>grid</td><td>grid, carousel</td><td><?php esc_html_e( 'Display layout type', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>columns</code></td><td>3</td><td>1–6</td><td><?php esc_html_e( 'Columns on desktop', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>columns_tablet</code></td><td>2</td><td>1–4</td><td><?php esc_html_e( 'Columns on tablet', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>columns_mobile</code></td><td>1</td><td>1–2</td><td><?php esc_html_e( 'Columns on mobile', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>platform</code></td><td>all</td><td>all, google, facebook, trustpilot</td><td><?php esc_html_e( 'Filter by platform', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>limit</code></td><td>10</td><td><?php esc_html_e( 'Any number', 'otw-testimonials' ); ?></td><td><?php esc_html_e( 'Max testimonials to show', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>orderby</code></td><td>date</td><td>date, rating, random, sort_order, title</td><td><?php esc_html_e( 'Order testimonials by', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>order</code></td><td>DESC</td><td>ASC, DESC</td><td><?php esc_html_e( 'Sort direction', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>gap</code></td><td>24</td><td><?php esc_html_e( 'Pixels', 'otw-testimonials' ); ?></td><td><?php esc_html_e( 'Space between cards', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>related_to</code></td><td><em><?php esc_html_e( 'empty', 'otw-testimonials' ); ?></em></td><td>current, <?php esc_html_e( 'post ID', 'otw-testimonials' ); ?></td><td><?php esc_html_e( 'Filter by related post. Use "current" to auto-detect from page context, or a specific post ID.', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>autoplay</code></td><td>1</td><td>0, 1</td><td><?php esc_html_e( 'Carousel autoplay (carousel only)', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>autoplay_speed</code></td><td>3000</td><td><?php esc_html_e( 'Milliseconds', 'otw-testimonials' ); ?></td><td><?php esc_html_e( 'Autoplay interval (carousel only)', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>loop</code></td><td>1</td><td>0, 1</td><td><?php esc_html_e( 'Infinite loop (carousel only)', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>arrows</code></td><td>1</td><td>0, 1</td><td><?php esc_html_e( 'Show nav arrows (carousel only)', 'otw-testimonials' ); ?></td></tr>
                        <tr><td><code>dots</code></td><td>1</td><td>0, 1</td><td><?php esc_html_e( 'Show pagination dots (carousel only)', 'otw-testimonials' ); ?></td></tr>
                    </tbody>
                </table>

                <h3><?php esc_html_e( 'Examples', 'otw-testimonials' ); ?></h3>
                <p><code>[otw_testimonials layout="carousel" columns="3" columns_tablet="2" columns_mobile="1" platform="google" limit="6"]</code></p>
                <p><code>[otw_testimonials layout="grid" columns="2" orderby="rating" order="DESC" limit="4"]</code></p>
                <p><code>[otw_testimonials related_to="current"]</code> &mdash; <?php esc_html_e( 'shows only testimonials linked to the current page/post/product', 'otw-testimonials' ); ?></p>

                <p style="margin-top:12px;color:#666;">
                    <strong><?php esc_html_e( 'Elementor:', 'otw-testimonials' ); ?></strong>
                    <?php esc_html_e( 'You can also use the "OTW Testimonials" widget in Elementor with full visual controls.', 'otw-testimonials' ); ?>
                </p>
            </div>
        </div>
        <?php
    }

    private function render_form() {
        $id              = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
        $testimonial     = $id ? OTW_Testimonials_DB::get_by_id( $id ) : null;
        $is_edit         = ! empty( $testimonial );

        $title           = $is_edit ? $testimonial->title : '';
        $description     = $is_edit ? $testimonial->description : '';
        $image_id        = $is_edit ? $testimonial->image_id : 0;
        $author_name     = $is_edit ? $testimonial->author_name : '';
        $rating          = $is_edit ? $testimonial->rating : 5;
        $platform        = $is_edit ? $testimonial->platform : 'google';
        $sort_order      = $is_edit ? $testimonial->sort_order : 0;
        $status          = $is_edit ? $testimonial->status : 'publish';
        $related_post_id = $is_edit ? absint( $testimonial->related_post_id ) : 0;

        $image_url           = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
        $related_post_title  = $related_post_id ? get_the_title( $related_post_id ) : '';
        ?>
        <div class="wrap">
            <h1><?php echo $is_edit ? esc_html__( 'Edit Testimonial', 'otw-testimonials' ) : esc_html__( 'Add New Testimonial', 'otw-testimonials' ); ?></h1>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=otw-testimonials' ) ); ?>" class="otw-testimonials-form">
                <?php wp_nonce_field( 'otw_testimonials_save', 'otw_testimonials_nonce' ); ?>
                <input type="hidden" name="otw_testimonial_id" value="<?php echo esc_attr( $id ); ?>">
                <input type="hidden" name="otw_testimonial_action" value="save">

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="otw_title"><?php esc_html_e( 'Author Name', 'otw-testimonials' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="otw_title" name="otw_title" value="<?php echo esc_attr( $title ); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="otw_description"><?php esc_html_e( 'Description', 'otw-testimonials' ); ?></label>
                        </th>
                        <td>
                            <?php
                            wp_editor( $description, 'otw_description', array(
                                'textarea_name' => 'otw_description',
                                'media_buttons' => false,
                                'textarea_rows' => 8,
                                'teeny'         => true,
                            ) );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e( 'Image', 'otw-testimonials' ); ?></label>
                        </th>
                        <td>
                            <div class="otw-image-upload">
                                <input type="hidden" id="otw_image_id" name="otw_image_id" value="<?php echo esc_attr( $image_id ); ?>">
                                <div id="otw-image-preview" class="otw-image-preview" <?php echo $image_url ? '' : 'style="display:none;"'; ?>>
                                    <?php if ( $image_url ) : ?>
                                        <img src="<?php echo esc_url( $image_url ); ?>" alt="">
                                    <?php endif; ?>
                                </div>
                                <button type="button" id="otw-upload-btn" class="button"><?php esc_html_e( 'Select Image', 'otw-testimonials' ); ?></button>
                                <button type="button" id="otw-remove-btn" class="button" <?php echo $image_url ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove Image', 'otw-testimonials' ); ?></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="otw_author_name"><?php esc_html_e( 'Position', 'otw-testimonials' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="otw_author_name" name="otw_author_name" value="<?php echo esc_attr( $author_name ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( 'Optional. e.g. CEO at Acme Corp', 'otw-testimonials' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="otw_rating"><?php esc_html_e( 'Rating', 'otw-testimonials' ); ?></label>
                        </th>
                        <td>
                            <select id="otw_rating" name="otw_rating">
                                <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                    <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $rating, $i ); ?>>
                                        <?php echo esc_html( str_repeat( '★', $i ) . str_repeat( '☆', 5 - $i ) ); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="otw_platform"><?php esc_html_e( 'Platform', 'otw-testimonials' ); ?></label>
                        </th>
                        <td>
                            <select id="otw_platform" name="otw_platform">
                                <option value="google" <?php selected( $platform, 'google' ); ?>><?php esc_html_e( 'Google', 'otw-testimonials' ); ?></option>
                                <option value="facebook" <?php selected( $platform, 'facebook' ); ?>><?php esc_html_e( 'Facebook', 'otw-testimonials' ); ?></option>
                                <option value="trustpilot" <?php selected( $platform, 'trustpilot' ); ?>><?php esc_html_e( 'Trustpilot', 'otw-testimonials' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="otw_related_post_search"><?php esc_html_e( 'Related Post / Page / Product', 'otw-testimonials' ); ?></label>
                        </th>
                        <td>
                            <div class="otw-post-search-wrap">
                                <input type="hidden" id="otw_related_post_id" name="otw_related_post_id" value="<?php echo esc_attr( $related_post_id ); ?>">
                                <input type="text" id="otw_related_post_search" class="regular-text" autocomplete="off"
                                    placeholder="<?php esc_attr_e( 'Search for a post, page or product…', 'otw-testimonials' ); ?>"
                                    value="<?php echo esc_attr( $related_post_title ); ?>">
                                <div id="otw-post-results" class="otw-post-results" style="display:none;"></div>
                                <?php if ( $related_post_id ) : ?>
                                    <button type="button" id="otw-post-clear" class="button"><?php esc_html_e( 'Clear', 'otw-testimonials' ); ?></button>
                                <?php else : ?>
                                    <button type="button" id="otw-post-clear" class="button" style="display:none;"><?php esc_html_e( 'Clear', 'otw-testimonials' ); ?></button>
                                <?php endif; ?>
                                <p class="description"><?php esc_html_e( 'Optional. Links this testimonial to a specific post, page, product, or any other content. Used for schema markup and filtering.', 'otw-testimonials' ); ?></p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="otw_sort_order"><?php esc_html_e( 'Sort Order', 'otw-testimonials' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="otw_sort_order" name="otw_sort_order" value="<?php echo esc_attr( $sort_order ); ?>" class="small-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="otw_status"><?php esc_html_e( 'Status', 'otw-testimonials' ); ?></label>
                        </th>
                        <td>
                            <select id="otw_status" name="otw_status">
                                <option value="publish" <?php selected( $status, 'publish' ); ?>><?php esc_html_e( 'Published', 'otw-testimonials' ); ?></option>
                                <option value="draft" <?php selected( $status, 'draft' ); ?>><?php esc_html_e( 'Draft', 'otw-testimonials' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php submit_button( $is_edit ? __( 'Update Testimonial', 'otw-testimonials' ) : __( 'Add Testimonial', 'otw-testimonials' ) ); ?>
            </form>
        </div>
        <?php
    }

    public function handle_form_submission() {
        if ( ! isset( $_POST['otw_testimonial_action'] ) || $_POST['otw_testimonial_action'] !== 'save' ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['otw_testimonials_nonce'] ?? '', 'otw_testimonials_save' ) ) {
            wp_die( __( 'Security check failed.', 'otw-testimonials' ) );
        }

        $id   = absint( $_POST['otw_testimonial_id'] ?? 0 );
        $data = array(
            'title'           => $_POST['otw_title'] ?? '',
            'description'     => $_POST['otw_description'] ?? '',
            'image_id'        => $_POST['otw_image_id'] ?? 0,
            'author_name'     => $_POST['otw_author_name'] ?? '',
            'rating'          => $_POST['otw_rating'] ?? 5,
            'platform'        => $_POST['otw_platform'] ?? 'google',
            'sort_order'      => $_POST['otw_sort_order'] ?? 0,
            'status'          => $_POST['otw_status'] ?? 'publish',
            'related_post_id' => $_POST['otw_related_post_id'] ?? 0,
        );

        if ( $id ) {
            OTW_Testimonials_DB::update( $id, $data );
        } else {
            $id = OTW_Testimonials_DB::insert( $data );
        }

        wp_safe_redirect( admin_url( 'admin.php?page=otw-testimonials&message=saved' ) );
        exit;
    }

    public function ajax_search_posts() {
        check_ajax_referer( 'otw_search_posts', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $search = sanitize_text_field( $_GET['search'] ?? '' );

        // Build list of all public post types.
        $post_types = array_values( get_post_types( array( 'public' => true ), 'names' ) );

        $posts = get_posts( array(
            's'              => $search,
            'post_type'      => $post_types,
            'post_status'    => 'publish',
            'posts_per_page' => 20,
            'fields'         => 'ids',
        ) );

        $results = array();
        foreach ( $posts as $post_id ) {
            $type      = get_post_type( $post_id );
            $type_obj  = get_post_type_object( $type );
            $type_label = $type_obj ? $type_obj->labels->singular_name : $type;

            $results[] = array(
                'id'   => $post_id,
                'text' => get_the_title( $post_id ) . ' — ' . $type_label . ' #' . $post_id,
            );
        }

        wp_send_json_success( $results );
    }

    public function admin_notices() {
        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'otw-testimonials' ) === false ) {
            return;
        }

        if ( isset( $_GET['message'] ) ) {
            $message = sanitize_text_field( $_GET['message'] );
            if ( $message === 'saved' ) {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Testimonial saved successfully.', 'otw-testimonials' ) . '</p></div>';
            } elseif ( $message === 'deleted' ) {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Testimonial deleted successfully.', 'otw-testimonials' ) . '</p></div>';
            }
        }
    }
}
