<?php
/**
 * Title: Service: Small business IT (Harbor)
 * Slug: msp-nexus/flagship-harbor-home
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Warm, light one-page site for a local small-business IT provider: photo hero with a call-us card, three service cards, how-it-works steps, flat-fee pricing, a review, service area, FAQs, and contact.
 *
 * @package MspNexus
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-hb nx-hb-hero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-hb nx-hb-hero"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-hb-hero__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-hb-hero__grid"><!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%"><!-- wp:paragraph {"className":"nx-hb-badge"} -->
<p class="nx-hb-badge"><?php esc_html_e('Local IT support for small businesses', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-hb-hero__title"} -->
<h1 class="wp-block-heading nx-hb-hero__title"><?php echo wp_kses_post(__('Your computers, network, and email, <span>looked after by neighbors.</span>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-hb-hero__lede"} -->
<p class="nx-hb-hero__lede"><?php esc_html_e('One flat monthly fee covers help-desk support, security, and backups for your whole team. When something breaks, you call a person who already knows your office.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"nx-hb-actions"} -->
<div class="wp-block-buttons nx-hb-actions"><!-- wp:button {"className":"nx-hb-btn"} -->
<div class="wp-block-button nx-hb-btn"><a class="wp-block-button__link wp-element-button" href="#contact"><?php esc_html_e('Get a free IT check-up', 'msp-nexus'); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"nx-hb-btn-quiet","metadata":{"bindings":{"url":{"source":"msp-nexus/settings","args":{"field":"sales_phone"}}}}} -->
<div class="wp-block-button nx-hb-btn-quiet"><a class="wp-block-button__link wp-element-button" href="#contact"><?php esc_html_e('Or call us', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"48%","className":"nx-hb-hero__media"} -->
<div class="wp-block-column is-vertically-aligned-center nx-hb-hero__media" style="flex-basis:48%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"nx-hb-hero__photo"} -->
<figure class="wp-block-image size-full nx-hb-hero__photo"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/team-workshop.webp')); ?>" alt="<?php esc_attr_e('A small team working together around a table with a laptop', 'msp-nexus'); ?>" width="960" height="640"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"nx-hb-callcard","layout":{"type":"default"}} -->
<div class="wp-block-group nx-hb-callcard"><!-- wp:paragraph {"className":"nx-hb-callcard__label"} -->
<p class="nx-hb-callcard__label"><?php esc_html_e('Need help right now?', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-hb-callcard__number","metadata":{"bindings":{"content":{"source":"msp-nexus/settings","args":{"field":"support_phone"}}}}} -->
<p class="nx-hb-callcard__number">(555) 555-0199</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-hb-callcard__hours","metadata":{"bindings":{"content":{"source":"msp-nexus/settings","args":{"field":"business_hours"}}}}} -->
<p class="nx-hb-callcard__hours"><?php esc_html_e('Mon–Fri 8am–6pm, after-hours emergencies', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","anchor":"services","className":"nx-hb nx-hb-services","layout":{"type":"constrained"}} -->
<section id="services" class="wp-block-group alignfull nx-hb nx-hb-services"><!-- wp:heading {"textAlign":"center","className":"nx-hb-title"} -->
<h2 class="wp-block-heading has-text-align-center nx-hb-title"><?php esc_html_e('Everything your office runs on, covered.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"align":"wide","className":"nx-hb-cards"} -->
<div class="wp-block-columns alignwide nx-hb-cards"><!-- wp:column {"className":"nx-hb-card"} -->
<div class="wp-block-column nx-hb-card"><!-- wp:paragraph {"className":"nx-hb-card__icon nx-icon-headset"} -->
<p class="nx-hb-card__icon nx-icon-headset"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Help desk', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Unlimited remote help and on-site visits for printers, passwords, new laptops, and everything in between.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-hb-card"} -->
<div class="wp-block-column nx-hb-card"><!-- wp:paragraph {"className":"nx-hb-card__icon nx-icon-shield"} -->
<p class="nx-hb-card__icon nx-icon-shield"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Security', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Antivirus, patching, email filtering, and a firewall we keep up to date, so a bad click stays a small problem.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-hb-card"} -->
<div class="wp-block-column nx-hb-card"><!-- wp:paragraph {"className":"nx-hb-card__icon nx-icon-restore"} -->
<p class="nx-hb-card__icon nx-icon-restore"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Backups', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Your files and email copied off-site every night, and restore-tested, so a dead drive is an inconvenience, not a disaster.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-hb nx-hb-steps-band","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-hb nx-hb-steps-band"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-hb-steps-grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-hb-steps-grid"><!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%"><!-- wp:heading {"className":"nx-hb-title"} -->
<h2 class="wp-block-heading nx-hb-title"><?php esc_html_e('Getting started takes one afternoon.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('No big migration project. We visit, take stock, and start looking after things the same week.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:list {"ordered":true,"className":"nx-hb-steps"} -->
<ol class="wp-block-list nx-hb-steps"><!-- wp:list-item -->
<li><strong><?php esc_html_e('Free check-up', 'msp-nexus'); ?></strong><?php esc_html_e('We look at your computers, network, and backups, and tell you plainly what needs attention.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('One flat price', 'msp-nexus'); ?></strong><?php esc_html_e('You get a single monthly number for your whole office. No hourly surprises.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('We take it from here', 'msp-nexus'); ?></strong><?php esc_html_e('Monitoring, updates, and backups start right away, and you have one number to call.', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ol>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","anchor":"pricing","className":"nx-hb nx-hb-pricing","layout":{"type":"constrained","contentSize":"56rem"}} -->
<section id="pricing" class="wp-block-group alignfull nx-hb nx-hb-pricing"><!-- wp:group {"className":"nx-hb-price","layout":{"type":"default"}} -->
<div class="wp-block-group nx-hb-price"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"45%","className":"nx-hb-price__figure"} -->
<div class="wp-block-column is-vertically-aligned-center nx-hb-price__figure" style="flex-basis:45%"><!-- wp:paragraph {"className":"nx-hb-price__from"} -->
<p class="nx-hb-price__from"><?php esc_html_e('Plans from', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-hb-price__amount"} -->
<p class="nx-hb-price__amount">$75<span><?php esc_html_e('per computer / month', 'msp-nexus'); ?></span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-hb-note"} -->
<p class="nx-hb-note"><?php esc_html_e('Sample price. Set your own before launch.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%"><!-- wp:heading {"level":2,"className":"nx-hb-price__title"} -->
<h2 class="wp-block-heading nx-hb-price__title"><?php esc_html_e('Everything included. Really.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"nx-hb-ticks"} -->
<ul class="wp-block-list nx-hb-ticks"><!-- wp:list-item -->
<li><?php esc_html_e('Unlimited remote and on-site help', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Security software and updates', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Nightly off-site backups', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Month-to-month, cancel any time', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-hb nx-hb-review","layout":{"type":"constrained","contentSize":"48rem"}} -->
<section class="wp-block-group alignfull nx-hb nx-hb-review"><!-- wp:paragraph {"align":"center","className":"nx-hb-stars"} -->
<p class="has-text-align-center nx-hb-stars" aria-hidden="true">★★★★★</p>
<!-- /wp:paragraph -->

<!-- wp:quote {"className":"nx-hb-quote"} -->
<blockquote class="wp-block-quote nx-hb-quote"><!-- wp:paragraph -->
<p><?php esc_html_e('We used to lose half a day every time the server acted up. Now we send a quick message, and it is usually fixed before we finish our coffee.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --><cite><?php esc_html_e('Office manager, sample review', 'msp-nexus'); ?></cite></blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph {"align":"center","className":"nx-hb-note"} -->
<p class="has-text-align-center nx-hb-note"><?php esc_html_e('Sample review. Replace with a real one from your clients.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-hb nx-hb-area","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-hb nx-hb-area"><!-- wp:columns {"align":"wide","verticalAlignment":"center"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:heading {"className":"nx-hb-title"} -->
<h2 class="wp-block-heading nx-hb-title"><?php esc_html_e('Close enough to stop by.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('We work with offices across the area and can be on-site the same day for anything that cannot be fixed remotely.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:list {"className":"nx-hb-towns"} -->
<ul class="wp-block-list nx-hb-towns"><!-- wp:list-item -->
<li><?php esc_html_e('Downtown', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Harbor District', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Northside', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Westgate', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Lakeview', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Surrounding towns', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","anchor":"faq","className":"nx-hb nx-hb-faq","layout":{"type":"constrained","contentSize":"48rem"}} -->
<section id="faq" class="wp-block-group alignfull nx-hb nx-hb-faq"><!-- wp:heading {"textAlign":"center","className":"nx-hb-title"} -->
<h2 class="wp-block-heading has-text-align-center nx-hb-title"><?php esc_html_e('Questions we hear a lot', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:msp-nexus/faq-list {"count":6} /--></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","anchor":"contact","className":"nx-hb nx-hb-contact","layout":{"type":"constrained"}} -->
<section id="contact" class="wp-block-group alignfull nx-hb nx-hb-contact"><!-- wp:columns {"align":"wide","className":"nx-hb-contact__grid"} -->
<div class="wp-block-columns alignwide nx-hb-contact__grid"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:heading {"className":"nx-hb-title"} -->
<h2 class="wp-block-heading nx-hb-title"><?php esc_html_e('Book your free IT check-up.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Tell us a little about your office and we will set up a time that suits you. No pressure and no jargon.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-hb-contact__line","metadata":{"bindings":{"content":{"source":"msp-nexus/settings","args":{"field":"company_address"}}}}} -->
<p class="nx-hb-contact__line"><?php esc_html_e('Add your address under MSP Nexus > Settings.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"60%","className":"nx-hb-contact__form"} -->
<div class="wp-block-column nx-hb-contact__form" style="flex-basis:60%"><!-- wp:msp-nexus/consultation-form {"heading":"<?php echo esc_attr__('Send us a message', 'msp-nexus'); ?>"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
