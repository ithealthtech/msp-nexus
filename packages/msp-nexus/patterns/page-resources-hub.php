<?php
/**
 * Title: Page: Resources hub
 * Slug: msp-nexus/page-resources-hub
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Resources hub: the free security booklet, the security self-assessment, articles, FAQ, and third-party audits, plus the latest articles and a call to action.
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
<p class="nx-fs-kicker"><?php echo $e(__('Resources', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('Free tools to help you <em>protect your business.</em>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-hero__lede"} -->
<p class="nx-fs-hero__lede"><?php echo $e(__('Plain-English guides, a quick security checkup, and answers to the questions business owners ask us most. No jargon, and no sales pitch required.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-resources","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-resources"><!-- wp:group {"align":"wide","className":"nx-fs-bento","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-bento"><!-- wp:group {"className":"nx-fs-tile nx-fs-tile--feature","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile nx-fs-tile--feature"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-book"} -->
<p class="nx-fs-tile__icon nx-icon-book"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo esc_url(home_url('/cybersecurity-essentials/')); ?>"><?php echo $e(__('The Cybersecurity Essentials booklet', 'msp-nexus')); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Our free 40-page guide for business owners: today’s threats, the NIST framework, and all 18 CIS Controls explained in plain English, with where a business your size should start.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-chips"} -->
<ul class="wp-block-list nx-fs-chips"><!-- wp:list-item -->
<li><?php echo $e(__('Free download', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo $e(__('40 pages', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>PDF</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-tile","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-checklist"} -->
<p class="nx-fs-tile__icon nx-icon-checklist"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo esc_url(home_url('/security-assessment/')); ?>"><?php echo $e(__('Security self-assessment', 'msp-nexus')); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Ten yes-or-no questions, an instant score, and a prioritized list of what to fix first.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-tile","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-article"} -->
<p class="nx-fs-tile__icon nx-icon-article"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo esc_url(home_url('/insights/')); ?>"><?php echo $e(__('Insights & articles', 'msp-nexus')); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Practical articles on security, Microsoft 365, the cloud, and getting more from your technology.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-tile","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-question"} -->
<p class="nx-fs-tile__icon nx-icon-question"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo esc_url(home_url('/faq/')); ?>"><?php echo $e(__('FAQ', 'msp-nexus')); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Straight answers about managed IT, what an MSP does, and how our support works.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-tile","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-shield"} -->
<p class="nx-fs-tile__icon nx-icon-shield"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo esc_url(home_url('/msp-audits/')); ?>"><?php echo $e(__('3rd-party MSP audits', 'msp-nexus')); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Already have an IT provider? Get an independent check of what is actually configured and working.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-blog-list nx-blog-more","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-blog-list nx-blog-more"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"46rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo $e(__('From the blog', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('Latest insights', 'msp-nexus')); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":7,"query":{"perPage":3,"postType":"post","order":"desc","orderBy":"date","inherit":false},"align":"wide"} -->
<div class="wp-block-query alignwide"><!-- wp:post-template {"className":"nx-blog-grid","layout":{"type":"grid","columnCount":3}} -->
<!-- wp:group {"tagName":"article","className":"nx-blog-card","layout":{"type":"default"}} -->
<article class="wp-block-group nx-blog-card"><!-- wp:post-terms {"term":"category","className":"nx-blog-card__cat"} /-->

<!-- wp:post-title {"level":3,"isLink":true,"className":"nx-blog-card__title"} /-->

<!-- wp:group {"className":"nx-blog-card__meta","layout":{"type":"flex","justifyContent":"space-between"}} -->
<div class="wp-block-group nx-blog-card__meta"><!-- wp:post-date {"format":"M j, Y"} /-->

<!-- wp:read-more {"content":"Read article →","className":"nx-blog-card__more"} /--></div>
<!-- /wp:group --></article>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-cta nx-fs-cta--top","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-cta nx-fs-cta--top"><!-- wp:group {"align":"wide","className":"nx-fs-cta__panel","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-cta__panel"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('Rather talk it through?', 'msp-nexus')); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Tell us what you are working on and an engineer will give you a straight answer. No obligation.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%","className":"nx-fs-cta__actions"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-cta__actions" style="flex-basis:40%"><!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"className":"nx-fs-btn-primary"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 nx-fs-btn-primary"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php echo $e(__('Talk to an engineer', 'msp-nexus')); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"width":100,"className":"is-style-outline nx-fs-btn-ghost"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline nx-fs-btn-ghost"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(home_url('/request-an-audit/')); ?>"><?php echo $e(__('Request an audit', 'msp-nexus')); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
