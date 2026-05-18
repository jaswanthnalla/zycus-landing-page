<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Zycus_Form_Handler' ) ) {

class Zycus_Form_Handler {

    const PERSONAL_DOMAINS = array( 'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'aol.com' );
    const RATE_LIMIT       = 5;
    const RATE_WINDOW      = 3600;

    public static function init() {
        add_action( 'wp_ajax_zycus_demo_submit', array( __CLASS__, 'handle' ) );
        add_action( 'wp_ajax_nopriv_zycus_demo_submit', array( __CLASS__, 'handle' ) );
    }

    public static function handle() {
        if ( ! check_ajax_referer( 'zycus_demo_submit', 'nonce', false ) ) {
            wp_send_json( array( 'success' => false, 'message' => 'Invalid request token.' ), 400 );
        }

        $honeypot = isset( $_POST['website'] ) ? trim( wp_unslash( $_POST['website'] ) ) : '';
        if ( $honeypot !== '' ) {
            wp_send_json( array( 'success' => true, 'message' => 'Thanks!' ) );
        }

        $ip = self::client_ip();
        if ( Zycus_DB::recent_submissions_from_ip( $ip, self::RATE_WINDOW ) >= self::RATE_LIMIT ) {
            wp_send_json( array( 'success' => false, 'message' => 'Too many submissions. Please try again later.' ), 429 );
        }

        $data = array(
            'full_name'  => isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '',
            'email'      => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
            'company'    => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
            'job_title'  => isset( $_POST['job_title'] ) ? sanitize_text_field( wp_unslash( $_POST['job_title'] ) ) : '',
            'country'    => isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '',
            'message'    => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
            'source'     => isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : 'landing_page',
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 500 ) : '',
            'ip_address' => $ip,
        );

        $errors = self::validate( $data );
        if ( ! empty( $errors ) ) {
            wp_send_json( array( 'success' => false, 'message' => 'Please fix the errors above.', 'errors' => $errors ), 422 );
        }

        $inserted = Zycus_DB::insert_lead( $data );
        if ( ! $inserted ) {
            wp_send_json( array( 'success' => false, 'message' => 'Something went wrong. Please try again.' ), 500 );
        }

        Zycus_Emails::send_admin_notification( $data );
        Zycus_Emails::send_user_confirmation( $data );

        wp_send_json( array(
            'success' => true,
            'message' => "We'll be in touch within 24 hours! Check your email for next steps.",
        ) );
    }

    private static function validate( array $data ) {
        $errors = array();

        if ( strlen( $data['full_name'] ) < 2 || strlen( $data['full_name'] ) > 50 ) {
            $errors['full_name'] = 'Please enter your full name';
        } elseif ( ! preg_match( "/^[a-zA-Z\s\-']+$/", $data['full_name'] ) ) {
            $errors['full_name'] = 'Please enter your full name';
        }

        if ( ! is_email( $data['email'] ) ) {
            $errors['email'] = 'Please enter a valid work email';
        } else {
            $domain = strtolower( substr( strrchr( $data['email'], '@' ), 1 ) );
            if ( in_array( $domain, self::PERSONAL_DOMAINS, true ) ) {
                $errors['email'] = 'Please use your work email address';
            }
        }

        if ( strlen( $data['company'] ) < 2 || strlen( $data['company'] ) > 100 ) {
            $errors['company'] = 'Please enter your company name';
        }

        if ( ! in_array( $data['job_title'], Zycus_Shortcode::job_titles(), true ) ) {
            $errors['job_title'] = 'Please select your job title';
        }

        if ( ! in_array( $data['country'], Zycus_Shortcode::countries(), true ) ) {
            $errors['country'] = 'Please select your country';
        }

        if ( strlen( $data['message'] ) > 500 ) {
            $errors['message'] = 'Message must be 500 characters or fewer';
        }

        return $errors;
    }

    private static function client_ip() {
        $keys = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
        foreach ( $keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ) )[0];
                $ip = trim( $ip );
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }
}

}
