<?php
/**
 * Title: Core: Contact
 * Slug: msp-nexus/flagship-northstar-contact
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Contact page that splits new-client and existing-client paths, pairs the consultation form with what-happens-next steps, and lists offices.
 *
 * @package MspNexus
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-pagehero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-pagehero"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"52rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Contact', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('Talk to an engineer, <em>not a sales script.</em>', 'msp-nexus')); ?></h1>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"nx-fs-paths"} -->
<div class="wp-block-columns alignwide nx-fs-paths"><!-- wp:column {"className":"nx-fs-path nx-fs-path--new"} -->
<div class="wp-block-column nx-fs-path nx-fs-path--new"><!-- wp:heading {"level":2,"className":"nx-fs-path__title"} -->
<h2 class="wp-block-heading nx-fs-path__title"><?php esc_html_e('Exploring a new partner?', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Book a 30-minute assessment or send the form below. A solutions engineer replies within one business day.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-link"} -->
<p class="nx-fs-link"><a href="#consultation"><?php esc_html_e('Start the conversation', 'msp-nexus'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-fs-path nx-fs-path--client"} -->
<div class="wp-block-column nx-fs-path nx-fs-path--client"><!-- wp:heading {"level":2,"className":"nx-fs-path__title"} -->
<h2 class="wp-block-heading nx-fs-path__title"><?php esc_html_e('Already a client and need help?', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Skip the form. Open a ticket in the portal or call the service desk for anything urgent.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-link"} -->
<p class="nx-fs-link"><a href="/support/"><?php esc_html_e('Go to client support', 'msp-nexus'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","anchor":"consultation","className":"nx-fs nx-fs-light nx-fs-contact","layout":{"type":"constrained"}} -->
<section id="consultation" class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-contact"><!-- wp:columns {"align":"wide","className":"nx-fs-contact__grid"} -->
<div class="wp-block-columns alignwide nx-fs-contact__grid"><!-- wp:column {"width":"58%","className":"nx-fs-contact__form"} -->
<div class="wp-block-column nx-fs-contact__form" style="flex-basis:58%"><!-- wp:msp-nexus/consultation-form {"heading":"<?php echo esc_attr__('Tell us what is going on', 'msp-nexus'); ?>"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"42%","className":"nx-fs-contact__aside"} -->
<div class="wp-block-column nx-fs-contact__aside" style="flex-basis:42%"><!-- wp:heading {"level":2,"className":"nx-fs-contact__aside-title"} -->
<h2 class="wp-block-heading nx-fs-contact__aside-title"><?php esc_html_e('What happens next', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"className":"nx-fs-next"} -->
<ol class="wp-block-list nx-fs-next"><!-- wp:list-item -->
<li><strong><?php esc_html_e('We reply within one business day', 'msp-nexus'); ?></strong><span><?php esc_html_e('From an engineer, with a few questions and times to talk.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('A 30-minute assessment call', 'msp-nexus'); ?></strong><span><?php esc_html_e('Your environment, your pain points, and what good looks like.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('A written findings summary', 'msp-nexus'); ?></strong><span><?php esc_html_e('Yours to keep, whether or not we work together.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->

<!-- wp:group {"className":"nx-fs-direct","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-direct"><!-- wp:paragraph {"className":"nx-fs-direct__label"} -->
<p class="nx-fs-direct__label"><?php esc_html_e('Prefer to reach out directly?', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"nx-fs-direct__links","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-buttons nx-fs-direct__links"><!-- wp:button {"className":"nx-fs-direct__link","metadata":{"bindings":{"text":{"source":"msp-nexus/settings","args":{"field":"sales_phone"}},"url":{"source":"msp-nexus/settings","args":{"field":"sales_phone"}}}}} -->
<div class="wp-block-button nx-fs-direct__link"><a class="wp-block-button__link wp-element-button" href="tel:+15555550100">(555) 555-0100</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"nx-fs-direct__link","metadata":{"bindings":{"text":{"source":"msp-nexus/settings","args":{"field":"sales_email"}},"url":{"source":"msp-nexus/settings","args":{"field":"sales_email"}}}}} -->
<div class="wp-block-button nx-fs-direct__link"><a class="wp-block-button__link wp-element-button" href="mailto:hello@example.com">hello@example.com</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:paragraph {"className":"nx-fs-sample-note"} -->
<p class="nx-fs-sample-note"><?php esc_html_e('Shows your sales phone and email from MSP Nexus > Settings once they are filled in.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-offices","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-offices"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"46rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Offices', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('On-site engineers within an hour of every client.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"nx-fs-offices__grid"} -->
<div class="wp-block-columns alignwide nx-fs-offices__grid"><!-- wp:column {"className":"nx-fs-office"} -->
<div class="wp-block-column nx-fs-office"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Headquarters', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>100 Example Street, Suite 200<br>Your City, ST 00000</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-office__hours"} -->
<p class="nx-fs-office__hours"><?php esc_html_e('Mon–Fri, 7am–7pm · Service desk 24/7', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-fs-office"} -->
<div class="wp-block-column nx-fs-office"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Regional office', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>200 Example Avenue<br>Second City, ST 00000</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-office__hours"} -->
<p class="nx-fs-office__hours"><?php esc_html_e('Mon–Fri, 8am–6pm', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-fs-office"} -->
<div class="wp-block-column nx-fs-office"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Regional office', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>300 Example Boulevard<br>Third City, ST 00000</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-office__hours"} -->
<p class="nx-fs-office__hours"><?php esc_html_e('By appointment', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
