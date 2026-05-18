<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Zycus_Shortcode' ) ) {

class Zycus_Shortcode {

    public static function init() {
        add_shortcode( 'zycus_demo_form', array( __CLASS__, 'render_form' ) );
        add_shortcode( 'zycus_sticky_cta', array( __CLASS__, 'render_sticky' ) );
    }

    public static function job_titles() {
        return array(
            'Procurement Manager',
            'Director of Procurement',
            'Chief Procurement Officer',
            'Sourcing Manager',
            'Supply Chain Manager',
            'CFO',
            'Other (please specify)',
        );
    }

    public static function countries() {
        return array(
            'United States', 'United Kingdom', 'Canada', 'Australia', 'India', 'Germany', 'France',
            'Netherlands', 'Singapore', 'United Arab Emirates', 'Japan', 'Brazil', 'Mexico',
            'South Africa', 'Spain', 'Italy', 'Sweden', 'Switzerland', 'Ireland', 'Belgium',
            'Denmark', 'Norway', 'Finland', 'Poland', 'Saudi Arabia', 'Hong Kong', 'New Zealand',
            'Malaysia', 'Indonesia', 'Philippines', 'Thailand', 'Vietnam', 'South Korea', 'China',
            'Other',
        );
    }

    public static function render_form() {
        ob_start();
        ?>
        <section class="zycus-form-section" id="zycus-demo-form">
            <div class="zycus-container">
                <header class="zycus-form-header">
                    <h2>Ready to Transform Procurement?</h2>
                    <p>Book a personalized demo to see how Zycus can transform your procurement process.</p>
                </header>
                <div class="zycus-form-card">
                    <form id="zycus-demo" class="zycus-form" novalidate>
                        <div class="zycus-form-row">
                            <div class="zycus-field">
                                <label for="zycus-full-name">Full Name <span aria-hidden="true">*</span></label>
                                <input type="text" id="zycus-full-name" name="full_name" placeholder="John Smith" required minlength="2" maxlength="50" pattern="[A-Za-z\s\-']+" aria-describedby="err-full-name" />
                                <span class="zycus-error" id="err-full-name" role="alert"></span>
                            </div>
                            <div class="zycus-field">
                                <label for="zycus-email">Work Email <span aria-hidden="true">*</span></label>
                                <input type="email" id="zycus-email" name="email" placeholder="john@company.com" required aria-describedby="err-email" />
                                <span class="zycus-error" id="err-email" role="alert"></span>
                            </div>
                        </div>
                        <div class="zycus-form-row">
                            <div class="zycus-field">
                                <label for="zycus-company">Company <span aria-hidden="true">*</span></label>
                                <input type="text" id="zycus-company" name="company" placeholder="Acme Corporation" required minlength="2" maxlength="100" aria-describedby="err-company" />
                                <span class="zycus-error" id="err-company" role="alert"></span>
                            </div>
                            <div class="zycus-field">
                                <label for="zycus-job-title">Job Title <span aria-hidden="true">*</span></label>
                                <select id="zycus-job-title" name="job_title" required aria-describedby="err-job-title">
                                    <option value="">Select your job title</option>
                                    <?php foreach ( self::job_titles() as $title ) : ?>
                                        <option value="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="zycus-error" id="err-job-title" role="alert"></span>
                            </div>
                        </div>
                        <div class="zycus-form-row">
                            <div class="zycus-field">
                                <label for="zycus-country">Country <span aria-hidden="true">*</span></label>
                                <select id="zycus-country" name="country" required aria-describedby="err-country">
                                    <option value="">Select Country</option>
                                    <?php foreach ( self::countries() as $country ) : ?>
                                        <option value="<?php echo esc_attr( $country ); ?>"><?php echo esc_html( $country ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="zycus-error" id="err-country" role="alert"></span>
                            </div>
                            <div class="zycus-field">
                                <label for="zycus-message">Message</label>
                                <textarea id="zycus-message" name="message" rows="4" maxlength="500" placeholder="Tell us about your procurement challenges..." aria-describedby="err-message zycus-message-count"></textarea>
                                <div class="zycus-field-meta">
                                    <span class="zycus-error" id="err-message" role="alert"></span>
                                    <span class="zycus-counter" id="zycus-message-count" aria-live="polite">0/500</span>
                                </div>
                            </div>
                        </div>
                        <div class="zycus-form-row zycus-honeypot" aria-hidden="true">
                            <label for="zycus-website">Website</label>
                            <input type="text" id="zycus-website" name="website" tabindex="-1" autocomplete="off" />
                        </div>
                        <button type="submit" class="zycus-btn zycus-btn-primary zycus-submit" disabled>
                            <span class="zycus-btn-label">Get Your Demo Now</span>
                        </button>
                        <p class="zycus-privacy">We respect your privacy. By submitting this form, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</p>
                        <div class="zycus-form-alert" role="status" aria-live="polite"></div>
                    </form>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    public static function render_sticky() {
        ob_start();
        ?>
        <div class="zycus-sticky" id="zycus-sticky" hidden>
            <div class="zycus-container zycus-sticky-inner">
                <span class="zycus-sticky-icon" aria-hidden="true">&#10003;</span>
                <span class="zycus-sticky-text">Ready to transform procurement?</span>
                <a href="#zycus-demo-form" class="zycus-btn zycus-btn-sticky" data-zycus-track="sticky">Book Your Demo</a>
                <button type="button" class="zycus-sticky-close" aria-label="Dismiss">&times;</button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

}
