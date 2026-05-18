<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/** @var array $data */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Demo Request</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background:#F5F5F5; margin:0; padding:24px; color:#1A1A1A;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; margin:0 auto; background:#FFFFFF; border:1px solid #DDDDDD; border-radius:4px;">
        <tr>
            <td style="background:#003DA5; padding:24px; color:#FFFFFF;">
                <h1 style="margin:0; font-size:22px;">New Demo Request</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 16px;">A new demo request was submitted on the Zycus landing page.</p>
                <table role="presentation" width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse;">
                    <tr><td style="font-weight:600; width:140px;">Name</td><td><?php echo esc_html( $data['full_name'] ); ?></td></tr>
                    <tr><td style="font-weight:600;">Email</td><td><a href="mailto:<?php echo esc_attr( $data['email'] ); ?>"><?php echo esc_html( $data['email'] ); ?></a></td></tr>
                    <tr><td style="font-weight:600;">Company</td><td><?php echo esc_html( $data['company'] ); ?></td></tr>
                    <tr><td style="font-weight:600;">Job Title</td><td><?php echo esc_html( $data['job_title'] ); ?></td></tr>
                    <tr><td style="font-weight:600;">Country</td><td><?php echo esc_html( $data['country'] ); ?></td></tr>
                    <?php if ( ! empty( $data['message'] ) ) : ?>
                    <tr><td style="font-weight:600; vertical-align:top;">Message</td><td><?php echo nl2br( esc_html( $data['message'] ) ); ?></td></tr>
                    <?php endif; ?>
                    <tr><td style="font-weight:600;">Source</td><td><?php echo esc_html( $data['source'] ); ?></td></tr>
                </table>
                <p style="margin-top:24px;">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=zycus-leads' ) ); ?>" style="background:#003DA5; color:#FFFFFF; padding:12px 24px; border-radius:4px; text-decoration:none; font-weight:600;">View in Dashboard</a>
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
