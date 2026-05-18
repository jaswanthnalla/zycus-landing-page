<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Zycus_Emails' ) ) {

class Zycus_Emails {

    public static function send_admin_notification( array $data ) {
        $to      = get_option( 'admin_email' );
        $subject = sprintf( 'New Demo Request - %s', $data['full_name'] );
        $body    = self::render( 'email-admin.php', $data );
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        return wp_mail( $to, $subject, $body, $headers );
    }

    public static function send_user_confirmation( array $data ) {
        $to      = $data['email'];
        $subject = 'Thank you for requesting a Zycus demo';
        $body    = self::render( 'email-user.php', $data );
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Zycus <' . get_option( 'admin_email' ) . '>',
        );
        return wp_mail( $to, $subject, $body, $headers );
    }

    private static function render( $template, array $data ) {
        $path = ZYCUS_LANDING_PATH . 'templates/' . $template;
        if ( ! file_exists( $path ) ) {
            return '';
        }
        ob_start();
        include $path;
        return ob_get_clean();
    }
}

}
