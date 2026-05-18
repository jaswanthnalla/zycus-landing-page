<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Zycus_Analytics' ) ) {

class Zycus_Analytics {

    public static function init() {
        add_action( 'wp_head', array( __CLASS__, 'inject_gtag' ), 5 );
    }

    public static function inject_gtag() {
        $id = trim( (string) get_option( 'zycus_ga4_id', '' ) );
        if ( $id === '' ) {
            return;
        }
        ?>
        <!-- Google Analytics 4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_js( $id ); ?>');
        </script>
        <?php
    }
}

}
