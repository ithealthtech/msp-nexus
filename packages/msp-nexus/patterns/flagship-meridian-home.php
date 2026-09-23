<?php
/**
 * Title: Service: IT consulting & vCIO (Meridian)
 * Slug: msp-nexus/flagship-meridian-home
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Editorial, executive-facing homepage for IT consulting and vCIO firms: serif hero with photography, advisory practices, sample engagements, a principal's note, insights, and a consultation band.
 *
 * @package MspNexus
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-md nx-md-hero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-md nx-md-hero"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-md-hero__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-md-hero__grid"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:paragraph {"className":"nx-md-eyebrow"} -->
<p class="nx-md-eyebrow"><?php esc_html_e('Technology advisory for growing organizations', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-md-hero__title"} -->
<h1 class="wp-block-heading nx-md-hero__title"><?php echo wp_kses_post(__('Technology decisions, <em>made with the whole business in view.</em>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-md-hero__lede"} -->
<p class="nx-md-hero__lede"><?php esc_html_e('We work alongside owners, finance leaders, and boards as a fractional CIO: setting direction, sizing budgets, managing vendors, and making sure the plan actually happens.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"nx-md-actions"} -->
<div class="wp-block-buttons nx-md-actions"><!-- wp:button {"className":"nx-md-btn"} -->
<div class="wp-block-button nx-md-btn"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Schedule a briefing', 'msp-nexus'); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"nx-md-link-btn"} -->
<div class="wp-block-button nx-md-link-btn"><a class="wp-block-button__link wp-element-button" href="#practices"><?php esc_html_e('Our practices', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-md-hero__media"} -->
<div class="wp-block-column is-vertically-aligned-center nx-md-hero__media" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/advisory-meeting.webp')); ?>" alt="<?php esc_attr_e('An advisor walking a leadership team through a plan', 'msp-nexus'); ?>" width="960" height="640"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:list {"align":"wide","className":"nx-md-facts"} -->
<ul class="wp-block-list alignwide nx-md-facts"><!-- wp:list-item -->
<li><strong><?php esc_html_e('Fractional CIO', 'msp-nexus'); ?></strong><?php esc_html_e('Senior leadership without a full-time hire', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Vendor-neutral', 'msp-nexus'); ?></strong><?php esc_html_e('Advice not tied to reseller margins', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Board-ready', 'msp-nexus'); ?></strong><?php esc_html_e('Plans written for decision-makers', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","anchor":"practices","className":"nx-md nx-md-practices","layout":{"type":"constrained"}} -->
<section id="practices" class="wp-block-group alignfull nx-md nx-md-practices"><!-- wp:columns {"align":"wide","className":"nx-md-split"} -->
<div class="wp-block-columns alignwide nx-md-split"><!-- wp:column {"width":"33%"} -->
<div class="wp-block-column" style="flex-basis:33%"><!-- wp:paragraph {"className":"nx-md-eyebrow"} -->
<p class="nx-md-eyebrow"><?php esc_html_e('Practices', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-md-title"} -->
<h2 class="wp-block-heading nx-md-title"><?php esc_html_e('Advisory first. Implementation when it helps.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"67%"} -->
<div class="wp-block-column" style="flex-basis:67%"><!-- wp:list {"className":"nx-md-practice-list"} -->
<ul class="wp-block-list nx-md-practice-list"><!-- wp:list-item -->
<li><strong><?php esc_html_e('Strategy & roadmap', 'msp-nexus'); ?></strong><span><?php esc_html_e('A three-year technology plan tied to your business goals, with priorities, owners, and costs.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Budget & vendor management', 'msp-nexus'); ?></strong><span><?php esc_html_e('Contract reviews, renewals, and competitive bids so you pay for what you use.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Security governance', 'msp-nexus'); ?></strong><span><?php esc_html_e('Risk registers, policies, and board reporting that satisfy auditors and insurers.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Mergers & growth', 'msp-nexus'); ?></strong><span><?php esc_html_e('Technology due diligence, integration planning, and new-office launches.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-md nx-md-work","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-md nx-md-work"><!-- wp:paragraph {"align":"wide","className":"nx-md-eyebrow alignwide"} -->
<p class="alignwide nx-md-eyebrow"><?php esc_html_e('Selected engagements', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:columns {"align":"wide","className":"nx-md-cases"} -->
<div class="wp-block-columns alignwide nx-md-cases"><!-- wp:column {"className":"nx-md-case"} -->
<div class="wp-block-column nx-md-case"><!-- wp:paragraph {"className":"nx-md-case__sector"} -->
<p class="nx-md-case__sector"><?php esc_html_e('Professional services · 140 staff', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Consolidated nine vendors into three and cut annual IT spend.', 'msp-nexus'); ?></h3>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-md-case"} -->
<div class="wp-block-column nx-md-case"><!-- wp:paragraph {"className":"nx-md-case__sector"} -->
<p class="nx-md-case__sector"><?php esc_html_e('Healthcare group · 6 clinics', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Passed a HIPAA risk assessment on the first attempt.', 'msp-nexus'); ?></h3>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-md-case"} -->
<div class="wp-block-column nx-md-case"><!-- wp:paragraph {"className":"nx-md-case__sector"} -->
<p class="nx-md-case__sector"><?php esc_html_e('Manufacturer · acquisition', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Integrated an acquired plant’s systems in one quarter.', 'msp-nexus'); ?></h3>
<!-- /wp:heading --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:paragraph {"align":"wide","className":"nx-md-note alignwide"} -->
<p class="alignwide nx-md-note"><?php esc_html_e('Sample engagements. Replace with your own client outcomes before launch.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-md nx-md-letter","layout":{"type":"constrained","contentSize":"44rem"}} -->
<section class="wp-block-group alignfull nx-md nx-md-letter"><!-- wp:paragraph {"className":"nx-md-eyebrow"} -->
<p class="nx-md-eyebrow"><?php esc_html_e('A note from our principal', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-md-letter__body"} -->
<p class="nx-md-letter__body"><?php esc_html_e('Most organizations do not have a technology problem. They have a decision problem: too many options, too little context, and nobody whose job it is to connect IT to the business. That is the job we do.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-md-letter__sign"} -->
<p class="nx-md-letter__sign"><?php esc_html_e('Principal and founder (sample)', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-md nx-md-insights","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-md nx-md-insights"><!-- wp:heading {"align":"wide","className":"nx-md-title alignwide"} -->
<h2 class="wp-block-heading alignwide nx-md-title"><?php esc_html_e('Insights', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"align":"wide","className":"nx-md-articles"} -->
<ul class="wp-block-list alignwide nx-md-articles"><!-- wp:list-item -->
<li><span><?php esc_html_e('Budgeting', 'msp-nexus'); ?></span><a href="/resources/"><?php esc_html_e('How to build an IT budget your CFO will sign', 'msp-nexus'); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><span><?php esc_html_e('Governance', 'msp-nexus'); ?></span><a href="/resources/"><?php esc_html_e('Five questions every board should ask about cyber risk', 'msp-nexus'); ?></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><span><?php esc_html_e('Vendors', 'msp-nexus'); ?></span><a href="/resources/"><?php esc_html_e('When to renegotiate a managed services contract', 'msp-nexus'); ?></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-md nx-md-close","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-md nx-md-close"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:heading {"className":"nx-md-close__title"} -->
<h2 class="wp-block-heading nx-md-close__title"><?php esc_html_e('A 45-minute briefing, with no sales pitch.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"nx-md-btn nx-md-btn--light"} -->
<div class="wp-block-button nx-md-btn nx-md-btn--light"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Schedule a briefing', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
