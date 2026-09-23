<?php
/**
 * Title: Service: Cloud & Microsoft 365
 * Slug: msp-nexus/service-cloud
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Cloud and Microsoft 365 service page: page hero, secure-by-default story, a four-step migration path, what is included, and a call to action.
 *
 * @package MspNexus
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-pagehero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-pagehero"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"52rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Cloud & Microsoft 365', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('Microsoft 365 and cloud, <em>secure by default.</em>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-hero__lede"} -->
<p class="nx-fs-hero__lede"><?php esc_html_e('Migrations with a rollback plan, a tenant hardened on day one, and licensing reviewed every quarter so you only pay for what people use.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-row nx-fs-row--flip","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-row nx-fs-row--flip"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-fs-row__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-fs-row__grid"><!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__media"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__media" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"nx-fs-row__photo"} -->
<figure class="wp-block-image size-full nx-fs-row__photo"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/developer-workspace.webp')); ?>" alt="<?php esc_attr_e('A person working on a laptop at a small table', 'msp-nexus'); ?>" loading="lazy" decoding="async"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__copy"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__copy" style="flex-basis:50%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Hardened from day one', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Most Microsoft 365 breaches start with a default setting.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('We apply a security baseline before anyone logs in: multi-factor sign-in, conditional access, protected admin accounts, and sharing rules that fit how your team works.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-checks"} -->
<ul class="wp-block-list nx-fs-checks"><!-- wp:list-item -->
<li><?php esc_html_e('MFA and conditional access for every account', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Teams and SharePoint sharing rules that make sense', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Backups for email, OneDrive, and SharePoint', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-tint nx-fs-process","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-tint nx-fs-process"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--center"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Migration path', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Moved over a weekend, not a quarter.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:list {"ordered":true,"align":"wide","className":"nx-fs-steps"} -->
<ol class="wp-block-list alignwide nx-fs-steps"><!-- wp:list-item -->
<li><strong><?php esc_html_e('Plan', 'msp-nexus'); ?></strong><em><?php esc_html_e('Week 1', 'msp-nexus'); ?></em><span><?php esc_html_e('Inventory mailboxes, files, and apps, and agree the cutover date.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Pilot', 'msp-nexus'); ?></strong><em><?php esc_html_e('Week 2', 'msp-nexus'); ?></em><span><?php esc_html_e('Move a small group first and fix anything they notice.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Move', 'msp-nexus'); ?></strong><em><?php esc_html_e('Cutover weekend', 'msp-nexus'); ?></em><span><?php esc_html_e('Everyone else moves while the office is closed, with a rollback plan ready.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Optimize', 'msp-nexus'); ?></strong><em><?php esc_html_e('First 30 days', 'msp-nexus'); ?></em><span><?php esc_html_e('On-site help Monday morning, then licensing trimmed to what is used.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->

<!-- wp:paragraph {"align":"wide","className":"nx-fs-sample-note"} -->
<p class="alignwide nx-fs-sample-note"><?php esc_html_e('Typical timeline for small and mid-sized organizations. Adjust to your own process.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-included","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-included"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--center"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('What ongoing management includes', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:list {"align":"wide","className":"nx-fs-included__grid"} -->
<ul class="wp-block-list alignwide nx-fs-included__grid"><!-- wp:list-item -->
<li><strong><?php esc_html_e('User lifecycle', 'msp-nexus'); ?></strong><?php esc_html_e('New hires ready on day one; leavers locked out the same hour.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Licensing review', 'msp-nexus'); ?></strong><?php esc_html_e('Quarterly check for unused seats and better-fit plans.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Security baseline', 'msp-nexus'); ?></strong><?php esc_html_e('Configuration drift caught and corrected every month.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Cloud backup', 'msp-nexus'); ?></strong><?php esc_html_e('Email, OneDrive, SharePoint, and Teams, restorable by item.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Azure & AWS', 'msp-nexus'); ?></strong><?php esc_html_e('Servers and apps right-sized, monitored, and cost-tracked.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Adoption help', 'msp-nexus'); ?></strong><?php esc_html_e('Short how-to sessions so Teams and SharePoint actually get used.', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-cta nx-fs-cta--top","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-cta nx-fs-cta--top"><!-- wp:group {"align":"wide","className":"nx-fs-cta__panel","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-cta__panel"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Get a free Microsoft 365 security check.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('We review your tenant against a security baseline and show you the top fixes, in plain language.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%","className":"nx-fs-cta__actions"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-cta__actions" style="flex-basis:40%"><!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"className":"nx-fs-btn-primary"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 nx-fs-btn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Request the check', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
