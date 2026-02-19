<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OTW_Testimonials_DB {

    private static function table_name() {
        global $wpdb;
        return $wpdb->prefix . 'otw_testimonials';
    }

    public static function get_all( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'platform'        => '',
            'status'          => 'publish',
            'limit'           => 0,
            'offset'          => 0,
            'orderby'         => 'created_at',
            'order'           => 'DESC',
            'related_post_id' => 0,
        );

        $args  = wp_parse_args( $args, $defaults );
        $table = self::table_name();

        $allowed_orderby = array( 'id', 'title', 'author_name', 'rating', 'platform', 'sort_order', 'created_at' );
        $orderby         = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'created_at';
        $order           = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

        $where  = array();
        $values = array();

        if ( ! empty( $args['status'] ) ) {
            $where[]  = 'status = %s';
            $values[] = $args['status'];
        }

        if ( ! empty( $args['platform'] ) && $args['platform'] !== 'all' ) {
            $where[]  = 'platform = %s';
            $values[] = $args['platform'];
        }

        if ( ! empty( $args['related_post_id'] ) ) {
            $where[]  = 'related_post_id = %d';
            $values[] = absint( $args['related_post_id'] );
        }

        $where_sql = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';

        if ( $args['orderby'] === 'random' ) {
            $order_sql = 'ORDER BY RAND()';
        } else {
            $order_sql = "ORDER BY {$orderby} {$order}";
        }

        $limit_sql = '';
        if ( $args['limit'] > 0 ) {
            $limit_sql = $wpdb->prepare( 'LIMIT %d OFFSET %d', absint( $args['limit'] ), absint( $args['offset'] ) );
        }

        $sql = "SELECT * FROM {$table} {$where_sql} {$order_sql} {$limit_sql}";

        if ( ! empty( $values ) ) {
            $sql = $wpdb->prepare( $sql, $values );
        }

        return $wpdb->get_results( $sql );
    }

    public static function get_by_id( $id ) {
        global $wpdb;
        $table = self::table_name();

        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", absint( $id ) )
        );
    }

    public static function insert( $data ) {
        global $wpdb;

        $sanitized = self::sanitize_data( $data );
        $sanitized['created_at'] = current_time( 'mysql' );
        $sanitized['updated_at'] = current_time( 'mysql' );

        $wpdb->insert( self::table_name(), $sanitized, self::get_formats( $sanitized ) );

        return $wpdb->insert_id;
    }

    public static function update( $id, $data ) {
        global $wpdb;

        $sanitized = self::sanitize_data( $data );
        $sanitized['updated_at'] = current_time( 'mysql' );

        return $wpdb->update(
            self::table_name(),
            $sanitized,
            array( 'id' => absint( $id ) ),
            self::get_formats( $sanitized ),
            array( '%d' )
        );
    }

    public static function delete( $id ) {
        global $wpdb;

        return $wpdb->delete(
            self::table_name(),
            array( 'id' => absint( $id ) ),
            array( '%d' )
        );
    }

    public static function get_count( $args = array() ) {
        global $wpdb;

        $table  = self::table_name();
        $where  = array();
        $values = array();

        if ( ! empty( $args['status'] ) ) {
            $where[]  = 'status = %s';
            $values[] = $args['status'];
        }

        if ( ! empty( $args['platform'] ) && $args['platform'] !== 'all' ) {
            $where[]  = 'platform = %s';
            $values[] = $args['platform'];
        }

        $where_sql = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';

        $sql = "SELECT COUNT(*) FROM {$table} {$where_sql}";

        if ( ! empty( $values ) ) {
            $sql = $wpdb->prepare( $sql, $values );
        }

        return (int) $wpdb->get_var( $sql );
    }

    /**
     * Build JSON-LD schema markup for a set of testimonials.
     *
     * @param array $testimonials
     * @param int   $related_post_id  0 = no related post.
     * @return string  One or more <script type="application/ld+json"> tags.
     */
    public static function build_schema_json( $testimonials, $related_post_id = 0 ) {
        if ( empty( $testimonials ) ) {
            return '';
        }

        $reviews = array();
        foreach ( $testimonials as $t ) {
            $review = array(
                '@type'        => 'Review',
                'reviewRating' => array(
                    '@type'       => 'Rating',
                    'ratingValue' => (string) $t->rating,
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ),
                'author'       => array(
                    '@type' => 'Person',
                    'name'  => $t->title,
                ),
                'reviewBody'    => wp_strip_all_tags( $t->description ),
                'datePublished' => date( 'Y-m-d', strtotime( $t->created_at ) ),
            );

            $reviews[] = $review;
        }

        // With a related post: wrap reviews inside the item being reviewed.
        if ( $related_post_id ) {
            $post = get_post( absint( $related_post_id ) );

            if ( $post ) {
                $ratings      = array_column( (array) $testimonials, 'rating' );
                $avg_rating   = count( $ratings ) > 0 ? round( array_sum( $ratings ) / count( $ratings ), 1 ) : 0;
                $item_type    = get_post_type( $post ) === 'product' ? 'Product' : 'LocalBusiness';

                $schema = array(
                    '@context'        => 'https://schema.org',
                    '@type'           => $item_type,
                    'name'            => get_the_title( $post ),
                    'url'             => get_permalink( $post ),
                    'aggregateRating' => array(
                        '@type'       => 'AggregateRating',
                        'ratingValue' => (string) $avg_rating,
                        'reviewCount' => (string) count( $testimonials ),
                        'bestRating'  => '5',
                        'worstRating' => '1',
                    ),
                    'review'          => $reviews,
                );

                return '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
            }
        }

        // No related post: one Review schema per testimonial.
        $output = '';
        foreach ( $reviews as $review ) {
            $schema = array_merge( array( '@context' => 'https://schema.org' ), $review );
            $output .= '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        }

        return $output;
    }

    private static function sanitize_data( $data ) {
        $sanitized = array();

        if ( isset( $data['title'] ) ) {
            $sanitized['title'] = sanitize_text_field( $data['title'] );
        }
        if ( isset( $data['description'] ) ) {
            $sanitized['description'] = wp_kses_post( $data['description'] );
        }
        if ( isset( $data['image_id'] ) ) {
            $sanitized['image_id'] = absint( $data['image_id'] );
        }
        if ( isset( $data['author_name'] ) ) {
            $sanitized['author_name'] = sanitize_text_field( $data['author_name'] );
        }
        if ( isset( $data['rating'] ) ) {
            $sanitized['rating'] = max( 1, min( 5, absint( $data['rating'] ) ) );
        }
        if ( isset( $data['platform'] ) ) {
            $allowed = array( 'google', 'facebook', 'trustpilot', 'blank' );
            $sanitized['platform'] = in_array( $data['platform'], $allowed, true ) ? $data['platform'] : 'google';
        }
        if ( isset( $data['sort_order'] ) ) {
            $sanitized['sort_order'] = intval( $data['sort_order'] );
        }
        if ( isset( $data['status'] ) ) {
            $allowed_status = array( 'publish', 'draft' );
            $sanitized['status'] = in_array( $data['status'], $allowed_status, true ) ? $data['status'] : 'publish';
        }
        if ( isset( $data['related_post_id'] ) ) {
            $sanitized['related_post_id'] = absint( $data['related_post_id'] );
        }

        return $sanitized;
    }

    private static function get_formats( $data ) {
        $formats    = array();
        $int_fields = array( 'image_id', 'rating', 'sort_order', 'related_post_id' );

        foreach ( array_keys( $data ) as $key ) {
            $formats[] = in_array( $key, $int_fields, true ) ? '%d' : '%s';
        }

        return $formats;
    }
}
