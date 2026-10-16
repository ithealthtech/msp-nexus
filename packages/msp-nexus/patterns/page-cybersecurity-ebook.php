<?php
/**
 * Title: Page: Cybersecurity ebook download
 * Slug: msp-nexus/page-cybersecurity-ebook
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Lead-magnet landing page for a cybersecurity ebook: hero beside the email-gated download, what is inside, who it is for, a short FAQ, and a consultation follow-up.
 *
 * @package MspNexus
 */

$e = static function (string $text): string {
    return esc_html($text);
};
$inside = array(
    __('How attacks really start', 'msp-nexus') => __('Phishing, stolen passwords, and unpatched software: the handful of ways most small businesses are actually breached.', 'msp-nexus'),
    __('The essential controls', 'msp-nexus') => __('Multi-factor sign-in, patching, endpoint protection, and email filtering, explained without jargon.', 'msp-nexus'),
    __('Backups that actually restore', 'msp-nexus') => __('What to back up, how often, where to keep copies, and how to prove a restore works.', 'msp-nexus'),
    __('Your people are the firewall', 'msp-nexus') => __('Simple habits and training that stop the scams that get past technology.', 'msp-nexus'),
    __('If the worst happens', 'msp-nexus') => __('A one-page incident checklist: who to call, what to unplug, and what not to do.', 'msp-nexus'),
    __('Cyber insurance, decoded', 'msp-nexus') => __('What insurers now require, and how to answer their questionnaires honestly.', 'msp-nexus'),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-pagehero nx-fs-contact","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-pagehero nx-fs-contact"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-fs-contact__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-fs-contact__grid"><!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo $e(__('Free cybersecurity ebook', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('Protect your business <em>before</em> the breach.', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-hero__lede"} -->
<p class="nx-fs-hero__lede"><?php echo $e(__('A practical, plain-English guide to the security steps that matter most for small and mid-sized businesses. Read it in an afternoon, act on it in a week.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-hero__assurances"} -->
<ul class="wp-block-list nx-fs-hero__assurances"><!-- wp:list-item -->
<li><?php echo $e(__('Written for owners, not IT staff', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo $e(__('Checklists you can use today', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo $e(__('Free PDF, instant download', 'msp-nexus')); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%"><!-- wp:msp-nexus/gated-download {"heading":"Get your free copy","buttonLabel":"Send me the ebook","downloadLabel":"Download the ebook (PDF)"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-included","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-included"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--center"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo $e(__('What is inside', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('Everything a busy owner needs to know about staying secure.', 'msp-nexus')); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:list {"align":"wide","className":"nx-fs-included__grid"} -->
<ul class="wp-block-list alignwide nx-fs-included__grid"><?php foreach ($inside as $title => $text) : ?><!-- wp:list-item -->
<li><strong><?php echo $e($title); ?></strong><?php echo $e($text); ?></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-row","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-row"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-fs-row__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-fs-row__grid"><!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__copy"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__copy" style="flex-basis:50%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo $e(__('Who it is for', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('Small businesses are the main target, not the exception.', 'msp-nexus')); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Attackers go where defenses are weakest. This guide is for owners, office managers, and practice administrators who are responsible for security without a full-time security team.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-checks"} -->
<ul class="wp-block-list nx-fs-checks"><!-- wp:list-item -->
<li><?php echo $e(__('Offices and practices with 5 to 250 people', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo $e(__('Businesses answering a cyber-insurance questionnaire', 'msp-nexus')); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo $e(__('Anyone unsure whether their IT provider has the basics covered', 'msp-nexus')); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__media"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__media" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"nx-fs-row__photo"} -->
<figure class="wp-block-image size-full nx-fs-row__photo"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/laptop-support.webp')); ?>" alt="<?php echo esc_attr__('A business owner working securely on a laptop', 'msp-nexus'); ?>" loading="lazy" decoding="async"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-qa","layout":{"type":"constrained","contentSize":"52rem"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-qa"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('About the ebook', 'msp-nexus')); ?></h2>
<!-- /wp:heading -->

<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo $e(__('Is it really free?', 'msp-nexus')); ?></summary><!-- wp:paragraph -->
<p><?php echo $e(__('Yes. Enter your name and email and the download unlocks immediately. There is no cost and no obligation.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo $e(__('Will you spam me?', 'msp-nexus')); ?></summary><!-- wp:paragraph -->
<p><?php echo $e(__('No. We may follow up once to ask if you have questions, and you can opt out at any time. We never sell or share your details.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo $e(__('Is it technical?', 'msp-nexus')); ?></summary><!-- wp:paragraph -->
<p><?php echo $e(__('No. It is written for business owners and managers. Where a technical step is needed, it explains what to ask your IT provider to do.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo $e(__('What if I want help putting it into practice?', 'msp-nexus')); ?></summary><!-- wp:paragraph -->
<p><?php echo $e(__('Tick the box for a free 15-minute call when you download, or request a third-party audit to see exactly where your business stands today.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-cta nx-fs-cta--top","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-cta nx-fs-cta--top"><!-- wp:group {"align":"wide","className":"nx-fs-cta__panel","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-cta__panel"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('Want to know where you stand today?', 'msp-nexus')); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo $e(__('Our third-party audit checks your security, backups, and IT provider against the same controls the ebook covers.', 'msp-nexus')); ?></p>
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
