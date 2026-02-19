<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class OTW_Testimonials_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct( array(
            'singular' => 'testimonial',
            'plural'   => 'testimonials',
            'ajax'     => false,
        ) );
    }

    public function get_columns() {
        return array(
            'cb'          => '<input type="checkbox" />',
            'image'       => __( 'Image', 'otw-testimonials' ),
            'title'       => __( 'Title', 'otw-testimonials' ),
            'author_name' => __( 'Author', 'otw-testimonials' ),
            'rating'      => __( 'Rating', 'otw-testimonials' ),
            'platform'    => __( 'Platform', 'otw-testimonials' ),
            'status'      => __( 'Status', 'otw-testimonials' ),
            'created_at'  => __( 'Date', 'otw-testimonials' ),
        );
    }

    public function get_sortable_columns() {
        return array(
            'title'       => array( 'title', false ),
            'author_name' => array( 'author_name', false ),
            'rating'      => array( 'rating', false ),
            'platform'    => array( 'platform', false ),
            'created_at'  => array( 'created_at', true ),
        );
    }

    public function prepare_items() {
        $per_page = 20;
        $current_page = $this->get_pagenum();

        $orderby = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'created_at';
        $order   = isset( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : 'DESC';

        $args = array(
            'status'  => '',
            'orderby' => $orderby,
            'order'   => $order,
            'limit'   => $per_page,
            'offset'  => ( $current_page - 1 ) * $per_page,
        );

        $this->items = OTW_Testimonials_DB::get_all( $args );
        $total_items = OTW_Testimonials_DB::get_count( array( 'status' => '' ) );

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page ),
        ) );

        $this->_column_headers = array(
            $this->get_columns(),
            array(),
            $this->get_sortable_columns(),
        );

        $this->process_bulk_action();
    }

    public function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="testimonial_ids[]" value="%d" />', $item->id );
    }

    public function column_image( $item ) {
        if ( $item->image_id ) {
            $img = wp_get_attachment_image( $item->image_id, array( 40, 40 ), false, array( 'style' => 'border-radius:50%;object-fit:cover;' ) );
            if ( $img ) {
                return $img;
            }
        }
        return '<span class="dashicons dashicons-format-image" style="font-size:30px;width:40px;height:40px;line-height:40px;color:#ccc;"></span>';
    }

    public function column_title( $item ) {
        $edit_url   = admin_url( 'admin.php?page=otw-testimonials&action=edit&id=' . $item->id );
        $delete_url = wp_nonce_url(
            admin_url( 'admin.php?page=otw-testimonials&action=delete&id=' . $item->id ),
            'otw_testimonials_delete_' . $item->id
        );

        $actions = array(
            'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'otw-testimonials' ) ),
            'delete' => sprintf( '<a href="%s" onclick="return confirm(\'%s\')">%s</a>',
                esc_url( $delete_url ),
                esc_js( __( 'Are you sure you want to delete this testimonial?', 'otw-testimonials' ) ),
                __( 'Delete', 'otw-testimonials' )
            ),
        );

        return sprintf( '<strong><a href="%s">%s</a></strong>%s',
            esc_url( $edit_url ),
            esc_html( $item->title ),
            $this->row_actions( $actions )
        );
    }

    public function column_author_name( $item ) {
        return esc_html( $item->author_name );
    }

    public function column_rating( $item ) {
        $stars = '';
        for ( $i = 1; $i <= 5; $i++ ) {
            $stars .= $i <= $item->rating
                ? '<span style="color:#f5a623;">★</span>'
                : '<span style="color:#ccc;">☆</span>';
        }
        return $stars;
    }

    public function column_platform( $item ) {
        $platforms = array(
            'google'     => '🔍 Google',
            'facebook'   => '📘 Facebook',
            'trustpilot' => '⭐ Trustpilot',
        );
        return esc_html( $platforms[ $item->platform ] ?? $item->platform );
    }

    public function column_status( $item ) {
        if ( $item->status === 'publish' ) {
            return '<span style="color:#46b450;">● ' . esc_html__( 'Published', 'otw-testimonials' ) . '</span>';
        }
        return '<span style="color:#999;">● ' . esc_html__( 'Draft', 'otw-testimonials' ) . '</span>';
    }

    public function column_created_at( $item ) {
        return esc_html( date_i18n( get_option( 'date_format' ), strtotime( $item->created_at ) ) );
    }

    public function get_bulk_actions() {
        return array(
            'bulk_delete' => __( 'Delete', 'otw-testimonials' ),
        );
    }

    public function process_bulk_action() {
        if ( $this->current_action() === 'delete' && isset( $_GET['id'] ) ) {
            $id = absint( $_GET['id'] );
            if ( wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'otw_testimonials_delete_' . $id ) ) {
                OTW_Testimonials_DB::delete( $id );
                wp_safe_redirect( admin_url( 'admin.php?page=otw-testimonials&message=deleted' ) );
                exit;
            }
        }

        if ( $this->current_action() === 'bulk_delete' && ! empty( $_GET['testimonial_ids'] ) ) {
            if ( wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'bulk-testimonials' ) ) {
                foreach ( array_map( 'absint', $_GET['testimonial_ids'] ) as $id ) {
                    OTW_Testimonials_DB::delete( $id );
                }
                wp_safe_redirect( admin_url( 'admin.php?page=otw-testimonials&message=deleted' ) );
                exit;
            }
        }
    }

    public function no_items() {
        esc_html_e( 'No testimonials found.', 'otw-testimonials' );
    }
}
