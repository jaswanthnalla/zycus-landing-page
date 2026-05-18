<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Zycus_DB' ) ) {

class Zycus_DB {

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . ZYCUS_LANDING_TABLE;
    }

    public static function install() {
        global $wpdb;

        $table   = self::table_name();
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            company VARCHAR(255) NOT NULL,
            job_title VARCHAR(255) NOT NULL,
            country VARCHAR(100) NOT NULL,
            message LONGTEXT NULL,
            source VARCHAR(50) DEFAULT 'landing_page',
            user_agent VARCHAR(500) NULL,
            ip_address VARCHAR(45) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_email (email),
            KEY idx_created_at (created_at),
            KEY idx_job_title (job_title)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function insert_lead( array $data ) {
        global $wpdb;

        return $wpdb->insert(
            self::table_name(),
            array(
                'full_name'  => $data['full_name'],
                'email'      => $data['email'],
                'company'    => $data['company'],
                'job_title'  => $data['job_title'],
                'country'    => $data['country'],
                'message'    => $data['message'],
                'source'     => $data['source'],
                'user_agent' => $data['user_agent'],
                'ip_address' => $data['ip_address'],
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );
    }

    public static function recent_submissions_from_ip( $ip, $window_seconds = 3600 ) {
        global $wpdb;
        $table = self::table_name();
        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE ip_address = %s AND created_at >= DATE_SUB(NOW(), INTERVAL %d SECOND)",
            $ip,
            $window_seconds
        ) );
    }

    public static function all_leads( $orderby = 'created_at', $order = 'DESC' ) {
        global $wpdb;
        $table   = self::table_name();
        $allowed = array( 'created_at', 'job_title', 'country', 'id' );
        $orderby = in_array( $orderby, $allowed, true ) ? $orderby : 'created_at';
        $order   = strtoupper( $order ) === 'ASC' ? 'ASC' : 'DESC';
        return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY {$orderby} {$order}", ARRAY_A );
    }
}

}
