<?php
/**
 * Title: Blog call to action
 * Slug: msp-nexus/blog-cta
 * Categories: msp-nexus-conversion
 * Inserter: false
 * Description: Closing panel for the blog index and articles: free booklet, security self-assessment, and a consultation.
 *
 * @package MspNexus
 */

$e = static function (string $text): string {
    return esc_html($text);
};
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-cta nx-fs-cta--top nx-blog-cta","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-cta nx-fs-cta--top nx-blog-cta"><!-- wp:group {"align":"wide","className":"nx-fs-cta__panel","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-cta__panel"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo $e(__('Free for business owners', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('Want to know where your business stands?', 'msp-nexus')); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Get our 40-page Cybersecurity Essentials booklet, or talk with an engineer about your setup. No obligation.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%","className":"nx-fs-cta__actions"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-cta__actions" style="flex-basis:40%"><!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"className":"nx-fs-btn-primary"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 nx-fs-btn-primary"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(home_url('/cybersecurity-essentials/')); ?>"><?php echo $e(__('Get the free booklet', 'msp-nexus')); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"width":100,"className":"is-style-outline nx-fs-btn-ghost"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline nx-fs-btn-ghost"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php echo $e(__('Talk to an engineer', 'msp-nexus')); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
