<?php
/**
 * Title: Page: Security self-assessment
 * Slug: msp-nexus/page-security-assessment
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: "How secure is your business?" page: hero, the ten-question self-assessment with instant score and tailored fixes, what the score means, and an audit call to action.
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
<p class="nx-fs-kicker"><?php echo $e(__('Free 2-minute self-assessment', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('How secure is <em>your business?</em>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-hero__lede"} -->
<p class="nx-fs-hero__lede"><?php echo $e(__('Answer ten yes-or-no questions based on the CIS Controls, the security baseline used by businesses and governments worldwide. You get an instant score and a prioritized list of what to fix first.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-hero__assurances"} -->
<ul class="wp-block-list nx-fs-hero__assurances"><!-- wp:list-item -->
<li><?php echo $e(__('Instant results on screen', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo $e(__('No email required', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo $e(__('Plain-English fixes for every gap', 'msp-nexus')); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-assessment","layout":{"type":"constrained","contentSize":"52rem"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-assessment"><!-- wp:msp-nexus/security-assessment /--></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-included","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-included"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--center"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo $e(__('What your score means', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('A quick checkup, not a full exam.', 'msp-nexus')); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('A self-assessment shows what you believe is in place. An audit shows what is actually configured and working, which is often different.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:list {"align":"wide","className":"nx-fs-included__grid"} -->
<ul class="wp-block-list alignwide nx-fs-included__grid"><!-- wp:list-item -->
<li><strong><?php echo $e(__('80–100: Strong foundation', 'msp-nexus')); ?></strong><?php echo $e(__('The essentials are in place. Verify they work as intended with an independent audit.', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php echo $e(__('50–79: Important gaps', 'msp-nexus')); ?></strong><?php echo $e(__('A good start with holes attackers look for. Most can be closed within weeks.', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php echo $e(__('Under 50: At risk', 'msp-nexus')); ?></strong><?php echo $e(__('Basic protections are missing. The fixes are well understood and affordable.', 'msp-nexus')); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-cta nx-fs-cta--top","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-cta nx-fs-cta--top"><!-- wp:group {"align":"wide","className":"nx-fs-cta__panel","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-cta__panel"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('Know for sure, not just in theory.', 'msp-nexus')); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Our third-party audit checks every one of these controls against evidence, and gives you a 90-day plan to close the gaps.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%","className":"nx-fs-cta__actions"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-cta__actions" style="flex-basis:40%"><!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"className":"nx-fs-btn-primary"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 nx-fs-btn-primary"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(home_url('/request-an-audit/')); ?>"><?php echo $e(__('Request an audit', 'msp-nexus')); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
