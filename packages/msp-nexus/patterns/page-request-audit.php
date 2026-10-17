<?php
/**
 * Title: Page: Request an audit
 * Slug: msp-nexus/page-request-audit
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Audit request page: hero, the detailed audit request form beside what happens next and what to have ready, and a short reassurance band.
 *
 * @package MspNexus
 */

$e = static function (string $text): string {
    return esc_html($text);
};
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-pagehero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-pagehero"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"52rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo $e(__('Request an audit', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('Get a second opinion <em>on your IT.</em>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-hero__lede"} -->
<p class="nx-fs-hero__lede"><?php echo $e(__('Is your IT company doing what they say they’re doing? We provide validation and peace of mind that your IT company is actually doing everything it claims. Tell us a little about your business and we will take it from there.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-contact","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-contact"><!-- wp:columns {"align":"wide","className":"nx-fs-contact__grid"} -->
<div class="wp-block-columns alignwide nx-fs-contact__grid"><!-- wp:column {"width":"34%"} -->
<div class="wp-block-column" style="flex-basis:34%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo $e(__('What happens next', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('From form to findings in about two weeks.', 'msp-nexus')); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"className":"nx-fs-next"} -->
<ol class="wp-block-list nx-fs-next"><!-- wp:list-item -->
<li><strong><?php echo $e(__('We reply within one business day', 'msp-nexus')); ?></strong><span><?php echo $e(__('A real person reads your request and schedules a 30-minute discovery call.', 'msp-nexus')); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php echo $e(__('You get a fixed quote', 'msp-nexus')); ?></strong><span><?php echo $e(__('Based on your users, devices, and locations. Nothing starts until you approve it.', 'msp-nexus')); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php echo $e(__('NDA and read-only access', 'msp-nexus')); ?></strong><span><?php echo $e(__('We sign a mutual NDA and set up the least access we need.', 'msp-nexus')); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php echo $e(__('Report and review meeting', 'msp-nexus')); ?></strong><span><?php echo $e(__('Risk-ranked findings, a billing reconciliation, and a 90-day roadmap, walked through with you.', 'msp-nexus')); ?></span></li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo $e(__('Helpful to have ready', 'msp-nexus')); ?></h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"nx-fs-checks"} -->
<ul class="wp-block-list nx-fs-checks"><!-- wp:list-item -->
<li><?php echo $e(__('Your current IT agreement or a recent invoice', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo $e(__('A rough count of computers, servers, and locations', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo $e(__('Any recent problems, incidents, or insurance questionnaires', 'msp-nexus')); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph {"className":"nx-fs-embed-note"} -->
<p class="nx-fs-embed-note"><?php echo $e(__('Prefer to talk first?', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline nx-fs-btn-ghost","metadata":{"bindings":{"text":{"source":"msp-nexus/settings","args":{"field":"sales_phone"}},"url":{"source":"msp-nexus/settings","args":{"field":"sales_phone"}}}}} -->
<div class="wp-block-button is-style-outline nx-fs-btn-ghost"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php echo $e(__('Call us', 'msp-nexus')); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"66%"} -->
<div class="wp-block-column" style="flex-basis:66%"><!-- wp:msp-nexus/audit-request-form /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-included","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-included"><!-- wp:list {"align":"wide","className":"nx-fs-included__grid"} -->
<ul class="wp-block-list alignwide nx-fs-included__grid"><!-- wp:list-item -->
<li><strong><?php echo $e(__('Confidential', 'msp-nexus')); ?></strong><?php echo $e(__('We never contact your current provider without your permission.', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php echo $e(__('Vendor-neutral', 'msp-nexus')); ?></strong><?php echo $e(__('Findings are backed by evidence, and we note what your provider does well, too.', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php echo $e(__('No obligation', 'msp-nexus')); ?></strong><?php echo $e(__('The report is yours to keep, whether or not you ever work with us.', 'msp-nexus')); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->
