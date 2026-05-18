<?php
/**
 * Template Name: Zycus Demo Landing
 *
 * Single-page, full-width landing page for the Zycus B2B procurement demo.
 * Drop this file into your active WordPress theme (or child theme) and assign
 * the "Zycus Demo Landing" template to a new page. Requires the
 * "Zycus Landing Page" plugin to be active.
 *
 * @package Zycus_Landing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main class="zycus-page" id="zycus-page-root">

    <?php echo do_shortcode( '[zycus_sticky_cta]' ); ?>

    <!-- Hero -->
    <section class="zycus-hero" id="zycus-hero">
        <div class="zycus-container">
            <div class="zycus-hero-grid">
                <div class="zycus-hero-text">
                    <div class="zycus-hero-eyebrow">Procurement, Reinvented</div>
                    <h1>Cut Procurement Cycle Time by 40%</h1>
                    <p class="zycus-hero-sub">Automate sourcing workflows, reduce costs by 40%, and gain real-time visibility into spend across your entire organization.</p>
                    <ul class="zycus-hero-list">
                        <li>Reduce sourcing cycles from weeks to days with AI automation</li>
                        <li>Eliminate manual RFx processes with intelligent templates</li>
                        <li>Gain real-time spend visibility across all categories</li>
                        <li>Ensure compliance and manage supplier risk automatically</li>
                    </ul>
                    <div class="zycus-hero-ctas">
                        <a href="#zycus-demo-form" class="zycus-btn zycus-btn-primary" data-zycus-track="hero">Get Your Demo</a>
                        <a href="#" class="zycus-btn zycus-btn-secondary" data-zycus-track="hero">View Platform Tour</a>
                    </div>
                </div>
                <div class="zycus-hero-image" role="img" aria-label="Zycus procurement platform dashboard">
                    <?php
                    $hero_image = get_post_meta( get_the_ID(), '_zycus_hero_image', true );
                    if ( $hero_image ) {
                        echo '<img src="' . esc_url( $hero_image ) . '" alt="Zycus platform" loading="eager" width="500" height="400" />';
                    } else {
                        ?>
                        <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="width:100%;height:auto;display:block;">
                            <rect width="500" height="400" fill="#F9FAFB"/>
                            <rect x="20" y="20" width="460" height="50" rx="6" fill="#003DA5"/>
                            <rect x="40" y="40" width="120" height="10" rx="3" fill="#FFFFFF"/>
                            <circle cx="450" cy="45" r="8" fill="#1A5BEB"/>
                            <rect x="20" y="90" width="220" height="140" rx="6" fill="#FFFFFF" stroke="#DDDDDD"/>
                            <rect x="40" y="110" width="100" height="10" rx="3" fill="#003DA5"/>
                            <rect x="40" y="130" width="180" height="6" rx="3" fill="#DDDDDD"/>
                            <rect x="40" y="145" width="160" height="6" rx="3" fill="#DDDDDD"/>
                            <rect x="40" y="175" width="90" height="35" rx="4" fill="#1A5BEB"/>
                            <rect x="260" y="90" width="220" height="140" rx="6" fill="#FFFFFF" stroke="#DDDDDD"/>
                            <rect x="280" y="110" width="100" height="10" rx="3" fill="#003DA5"/>
                            <rect x="280" y="135" width="40" height="60" rx="3" fill="#003DA5"/>
                            <rect x="335" y="115" width="40" height="80" rx="3" fill="#1A5BEB"/>
                            <rect x="390" y="145" width="40" height="50" rx="3" fill="#003DA5"/>
                            <rect x="20" y="250" width="460" height="130" rx="6" fill="#FFFFFF" stroke="#DDDDDD"/>
                            <rect x="40" y="270" width="140" height="10" rx="3" fill="#003DA5"/>
                            <rect x="40" y="295" width="420" height="8" rx="3" fill="#F5F5F5"/>
                            <rect x="40" y="310" width="380" height="8" rx="3" fill="#F5F5F5"/>
                            <rect x="40" y="325" width="400" height="8" rx="3" fill="#F5F5F5"/>
                            <rect x="40" y="345" width="90" height="25" rx="4" fill="#003DA5"/>
                        </svg>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits -->
    <section class="zycus-benefits" id="zycus-benefits">
        <div class="zycus-container">
            <header class="zycus-section-head">
                <h2>Why Choose Zycus?</h2>
                <p>A unified procurement platform built for enterprise scale.</p>
                <span class="zycus-accent" aria-hidden="true"></span>
            </header>
            <div class="zycus-benefit-grid">
                <article class="zycus-benefit-card">
                    <div class="zycus-benefit-num">01</div>
                    <h3>AI-Powered Automation</h3>
                    <p>Automate RFx processes, contract generation, and supplier communication with intelligent workflows. Cut process time from weeks to days.</p>
                    <a href="#" class="zycus-benefit-link">Learn more &rarr;</a>
                </article>
                <article class="zycus-benefit-card">
                    <div class="zycus-benefit-num">02</div>
                    <h3>Real-Time Spend Analytics</h3>
                    <p>Gain complete visibility into spend across categories, suppliers, and business units. Make data-driven decisions instantly.</p>
                    <a href="#" class="zycus-benefit-link">Learn more &rarr;</a>
                </article>
                <article class="zycus-benefit-card">
                    <div class="zycus-benefit-num">03</div>
                    <h3>Risk &amp; Compliance Management</h3>
                    <p>Ensure regulatory compliance, manage supplier risk automatically, and maintain audit trails. Stay compliant effortlessly.</p>
                    <a href="#" class="zycus-benefit-link">Learn more &rarr;</a>
                </article>
            </div>
        </div>
    </section>

    <!-- Social Proof -->
    <section class="zycus-social" id="zycus-social">
        <div class="zycus-container">
            <header class="zycus-section-head">
                <h2>Trusted by 500+ Enterprises</h2>
            </header>
            <div class="zycus-logo-wall" aria-label="Customer logos">
                <?php
                $logos = array( 'GlobalCo', 'Acme Inc.', 'Northwind', 'Initech', 'Umbrella', 'Stark Ind.', 'Wayne Ent.', 'Hooli', 'Pied Piper', 'Soylent' );
                foreach ( $logos as $logo ) {
                    echo '<div class="zycus-logo">' . esc_html( $logo ) . '</div>';
                }
                ?>
            </div>

            <header class="zycus-section-head">
                <h2>Proven ROI</h2>
            </header>
            <div class="zycus-metrics">
                <div>
                    <div class="zycus-metric-value" data-target="40" data-suffix="%">0%</div>
                    <div class="zycus-metric-label">Faster Sourcing Cycles</div>
                </div>
                <div>
                    <div class="zycus-metric-value" data-target="60" data-suffix="%">0%</div>
                    <div class="zycus-metric-label">Cost Savings</div>
                </div>
                <div>
                    <div class="zycus-metric-value" data-target="90" data-suffix="%">0%</div>
                    <div class="zycus-metric-label">Process Efficiency</div>
                </div>
            </div>

            <header class="zycus-section-head">
                <h2>What Our Customers Say</h2>
            </header>
            <article class="zycus-testimonial-card">
                <div class="zycus-testimonial-stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p class="zycus-testimonial-quote">&ldquo;Zycus transformed our procurement function. We reduced sourcing cycles by 50% and saved $2M+ in supplier negotiations in year one.&rdquo;</p>
                <div class="zycus-testimonial-attr">
                    <div class="zycus-testimonial-avatar" aria-hidden="true">JS</div>
                    <div>
                        <strong>John Smith</strong><br>
                        Director of Procurement, Global Consumer Goods Company
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- Demo Form -->
    <?php echo do_shortcode( '[zycus_demo_form]' ); ?>

</main>

<?php
get_footer();
