<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OTW_Testimonials_Activator {

    const DB_VERSION = '1.1.0';

    public static function activate() {
        self::create_table();
        update_option( 'otw_testimonials_db_version', self::DB_VERSION );
    }

    private static function create_table() {
        global $wpdb;

        $table_name      = $wpdb->prefix . 'otw_testimonials';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL DEFAULT '',
            description TEXT NOT NULL,
            image_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            author_name VARCHAR(255) NOT NULL DEFAULT '',
            rating TINYINT(1) UNSIGNED NOT NULL DEFAULT 5,
            platform VARCHAR(20) NOT NULL DEFAULT 'google',
            sort_order INT(11) NOT NULL DEFAULT 0,
            related_post_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT 'publish',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY platform (platform),
            KEY sort_order (sort_order),
            KEY related_post_id (related_post_id)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }
}
