<?php
/**
 * Plugin Name: Zycus Landing Page
 * Plugin URI:  https://www.zycus.com/
 * Description: Custom plugin for the Zycus B2B procurement landing page. Provides the demo-request form shortcode, custom leads table, AJAX submission with validation, admin + user email notifications, admin lead viewer with CSV export, and GA4 event tracking.
 * Version:     1.0.1
 * Author:      Zycus
 * License:     GPL-2.0+
 * Text Domain: zycus-landing
 * Requires PHP: 7.4
 * Requires at least: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ZYCUS_LANDING_VERSION', '1.0.1' );
define( 'ZYCUS_LANDING_DB_VERSION', '1.0.0' );
define( 'ZYCUS_LANDING_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZYCUS_LANDING_URL', plugin_dir_url( __FILE__ ) );
define( 'ZYCUS_LANDING_TABLE', 'zycus_leads' );

// Defensive bootstrap: any failure here gets caught so activation can't fatal.
function zycus_landing_bootstrap() {
    static $booted = false;
    if ( $booted ) {
        return;
    }
    $booted = true;

    $files = array(
        'includes/class-zycus-db.php',
        'includes/class-zycus-form-handler.php',
        'includes/class-zycus-emails.php',
        'includes/class-zycus-admin.php',
        'includes/class-zycus-shortcode.php',
        'includes/class-zycus-analytics.php',
    );

    foreach ( $files as $rel ) {
        $path = ZYCUS_LANDING_PATH . $rel;
        if ( file_exists( $path ) ) {
            require_once $path;
        }
    }

    try {
        if ( class_exists( 'Zycus_Form_Handler' ) ) {
            Zycus_Form_Handler::init();
        }
        if ( class_exists( 'Zycus_Admin' ) ) {
            Zycus_Admin::init();
        }
        if ( class_exists( 'Zycus_Shortcode' ) ) {
            Zycus_Shortcode::init();
        }
        if ( class_exists( 'Zycus_Analytics' ) ) {
            Zycus_Analytics::init();
        }
    } catch ( Throwable $e ) {
        error_log( 'Zycus Landing bootstrap error: ' . $e->getMessage() );
    }

    // Lazy DB install — only runs if version doesn't match, never blocks activation.
    try {
        $current = get_option( 'zycus_landing_db_version', '' );
        if ( $current !== ZYCUS_LANDING_DB_VERSION && class_exists( 'Zycus_DB' ) ) {
            Zycus_DB::install();
            update_option( 'zycus_landing_db_version', ZYCUS_LANDING_DB_VERSION );
        }
    } catch ( Throwable $e ) {
        error_log( 'Zycus Landing DB install error: ' . $e->getMessage() );
    }
}

add_action( 'plugins_loaded', 'zycus_landing_bootstrap', 5 );

add_action( 'wp_enqueue_scripts', function () {
    if ( ! class_exists( 'Zycus_Shortcode' ) ) {
        zycus_landing_bootstrap();
    }
    if ( ! class_exists( 'Zycus_Shortcode' ) ) {
        return; // boot still failed — bail silently
    }

    wp_enqueue_style(
        'zycus-inter-font',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        array(),
        ZYCUS_LANDING_VERSION
    );

    wp_enqueue_style(
        'zycus-landing',
        ZYCUS_LANDING_URL . 'assets/css/zycus-landing.css',
        array( 'zycus-inter-font' ),
        ZYCUS_LANDING_VERSION
    );

    wp_enqueue_script(
        'zycus-landing',
        ZYCUS_LANDING_URL . 'assets/js/zycus-landing.js',
        array(),
        ZYCUS_LANDING_VERSION,
        true
    );

    wp_localize_script( 'zycus-landing', 'ZycusLanding', array(
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'zycus_demo_submit' ),
        'gaId'      => get_option( 'zycus_ga4_id', '' ),
        'countries' => Zycus_Shortcode::countries(),
        'titles'    => Zycus_Shortcode::job_titles(),
    ) );
} );
