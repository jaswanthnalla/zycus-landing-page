<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Zycus_Admin' ) ) {

class Zycus_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'maybe_export_csv' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function menu() {
        add_menu_page(
            'Demo Requests',
            'Demo Requests',
            'manage_options',
            'zycus-leads',
            array( __CLASS__, 'render_leads_page' ),
            'dashicons-clipboard',
            26
        );

        add_submenu_page(
            'zycus-leads',
            'Settings',
            'Settings',
            'manage_options',
            'zycus-leads-settings',
            array( __CLASS__, 'render_settings_page' )
        );
    }

    public static function register_settings() {
        register_setting( 'zycus_landing', 'zycus_ga4_id', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ) );
    }

    public static function render_leads_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $leads = Zycus_DB::all_leads( 'created_at', 'DESC' );
        $export_url = wp_nonce_url( admin_url( 'admin.php?page=zycus-leads&zycus_export=1' ), 'zycus_export_csv' );
        ?>
        <div class="wrap">
            <h1>Demo Requests
                <a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action">Export CSV</a>
            </h1>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Job Title</th>
                        <th>Country</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ( empty( $leads ) ) : ?>
                    <tr><td colspan="7">No demo requests yet.</td></tr>
                <?php else : foreach ( $leads as $lead ) : ?>
                    <tr>
                        <td><?php echo esc_html( $lead['created_at'] ); ?></td>
                        <td><?php echo esc_html( $lead['full_name'] ); ?></td>
                        <td><a href="mailto:<?php echo esc_attr( $lead['email'] ); ?>"><?php echo esc_html( $lead['email'] ); ?></a></td>
                        <td><?php echo esc_html( $lead['company'] ); ?></td>
                        <td><?php echo esc_html( $lead['job_title'] ); ?></td>
                        <td><?php echo esc_html( $lead['country'] ); ?></td>
                        <td><?php echo esc_html( $lead['message'] ); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>Zycus Landing Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'zycus_landing' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="zycus_ga4_id">Google Analytics 4 Measurement ID</label></th>
                        <td>
                            <input type="text" id="zycus_ga4_id" name="zycus_ga4_id" value="<?php echo esc_attr( get_option( 'zycus_ga4_id', '' ) ); ?>" class="regular-text" placeholder="G-XXXXXXXXXX" />
                            <p class="description">Leave blank to disable analytics.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public static function maybe_export_csv() {
        if ( ! isset( $_GET['zycus_export'] ) || ! current_user_can( 'manage_options' ) ) {
            return;
        }
        check_admin_referer( 'zycus_export_csv' );

        $leads = Zycus_DB::all_leads( 'created_at', 'DESC' );

        nocache_headers();
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=zycus-leads-' . gmdate( 'Y-m-d' ) . '.csv' );

        $out = fopen( 'php://output', 'w' );
        fputcsv( $out, array( 'ID', 'Name', 'Email', 'Company', 'Job Title', 'Country', 'Message', 'Source', 'IP', 'Created' ) );
        foreach ( $leads as $lead ) {
            fputcsv( $out, array(
                $lead['id'], $lead['full_name'], $lead['email'], $lead['company'],
                $lead['job_title'], $lead['country'], $lead['message'], $lead['source'],
                $lead['ip_address'], $lead['created_at'],
            ) );
        }
        fclose( $out );
        exit;
    }
}

}
