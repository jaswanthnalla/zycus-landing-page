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
    <title>Thank you for requesting a Zycus demo</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background:#F5F5F5; margin:0; padding:24px; color:#1A1A1A;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; margin:0 auto; background:#FFFFFF; border:1px solid #DDDDDD; border-radius:4px;">
        <tr>
            <td style="background:#003DA5; padding:32px; color:#FFFFFF; text-align:center;">
                <h1 style="margin:0; font-size:24px;">Thank you, <?php echo esc_html( $data['full_name'] ); ?>!</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:32px; line-height:1.6;">
                <p>Thanks for requesting a personalized Zycus demo. We've received your request and a member of our team will reach out within <strong>24 hours</strong> to schedule your demo.</p>
                <h3 style="color:#003DA5; margin-top:24px;">What happens next?</h3>
                <ul style="padding-left:20px;">
                    <li>Our procurement specialist will review your requirements.</li>
                    <li>We'll schedule a 30-minute demo tailored to <?php echo esc_html( $data['company'] ); ?>.</li>
                    <li>You'll see how Zycus reduces sourcing cycles by 40% and cuts costs by up to 60%.</li>
                </ul>
                <p style="margin-top:24px;">In the meantime, feel free to reply to this email with any questions.</p>
                <p style="margin-top:32px;">Best regards,<br><strong>The Zycus Team</strong></p>
            </td>
        </tr>
        <tr>
            <td style="background:#F5F5F5; padding:16px; text-align:center; font-size:12px; color:#666666;">
                &copy; Zycus &middot; Procurement Reinvented
            </td>
        </tr>
    </table>
</body>
</html>
