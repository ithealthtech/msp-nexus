<?php
/**
 * Title: Service: Co-managed IT
 * Slug: msp-nexus/service-co-managed
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Co-managed IT service page: page hero, "your team stays in charge" story, a who-does-what responsibility table, add-on capabilities, and a call to action.
 *
 * @package MspNexus
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-pagehero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-pagehero"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"52rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Co-managed IT', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('More hands for your IT team, <em>without giving up the keys.</em>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-hero__lede"} -->
<p class="nx-fs-hero__lede"><?php esc_html_e('Keep your internal IT staff in charge. We add after-hours coverage, specialists, and tooling exactly where you are stretched.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-row","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-row"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-fs-row__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-fs-row__grid"><!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__media"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__media" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"nx-fs-row__photo"} -->
<figure class="wp-block-image size-full nx-fs-row__photo"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/team-planning.webp')); ?>" alt="<?php esc_attr_e('An internal IT team planning work together', 'msp-nexus'); ?>" loading="lazy" decoding="async"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__copy"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__copy" style="flex-basis:50%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Your team stays in charge', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('We work in your ticket system, on your priorities.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Co-management fails when the provider takes over. We agree up front who owns what, escalate through your team, and document everything in the tools you already use.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-checks"} -->
<ul class="wp-block-list nx-fs-checks"><!-- wp:list-item -->
<li><?php esc_html_e('Your ticketing, RMM, and documentation, or ours', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Escalations go through your IT lead', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Scale coverage up or down each quarter', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-tint nx-fs-matrix-section","layout":{"type":"constrained","contentSize":"60rem"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-tint nx-fs-matrix-section"><!-- wp:group {"className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group nx-fs-head nx-fs-head--center"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Who does what', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('A typical split. Yours is agreed in writing.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:table {"className":"nx-fs-matrix"} -->
<figure class="wp-block-table nx-fs-matrix"><table><thead><tr><th><?php esc_html_e('Responsibility', 'msp-nexus'); ?></th><th><?php esc_html_e('Your IT team', 'msp-nexus'); ?></th><th><?php esc_html_e('Our team', 'msp-nexus'); ?></th></tr></thead><tbody><tr><td><?php esc_html_e('Business-hours help desk', 'msp-nexus'); ?></td><td><?php esc_html_e('Leads', 'msp-nexus'); ?></td><td><?php esc_html_e('Overflow', 'msp-nexus'); ?></td></tr><tr><td><?php esc_html_e('After-hours and weekend support', 'msp-nexus'); ?></td><td>—</td><td><?php esc_html_e('Covers', 'msp-nexus'); ?></td></tr><tr><td><?php esc_html_e('24/7 security monitoring', 'msp-nexus'); ?></td><td><?php esc_html_e('Informed', 'msp-nexus'); ?></td><td><?php esc_html_e('Covers', 'msp-nexus'); ?></td></tr><tr><td><?php esc_html_e('Patching and backups', 'msp-nexus'); ?></td><td><?php esc_html_e('Approves', 'msp-nexus'); ?></td><td><?php esc_html_e('Runs', 'msp-nexus'); ?></td></tr><tr><td><?php esc_html_e('Projects and migrations', 'msp-nexus'); ?></td><td><?php esc_html_e('Owns', 'msp-nexus'); ?></td><td><?php esc_html_e('Engineers on demand', 'msp-nexus'); ?></td></tr><tr><td><?php esc_html_e('Strategy and budget', 'msp-nexus'); ?></td><td><?php esc_html_e('Decides', 'msp-nexus'); ?></td><td><?php esc_html_e('Advises', 'msp-nexus'); ?></td></tr></tbody></table></figure>
<!-- /wp:table --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-included","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-included"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--center"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Add only what you need', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:list {"align":"wide","className":"nx-fs-included__grid"} -->
<ul class="wp-block-list alignwide nx-fs-included__grid"><!-- wp:list-item -->
<li><strong><?php esc_html_e('After-hours desk', 'msp-nexus'); ?></strong><?php esc_html_e('Nights, weekends, and holidays covered by real people.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Security operations', 'msp-nexus'); ?></strong><?php esc_html_e('24/7 monitoring that feeds alerts into your queue.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Project engineers', 'msp-nexus'); ?></strong><?php esc_html_e('Migrations, rollouts, and network refreshes on demand.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Enterprise tooling', 'msp-nexus'); ?></strong><?php esc_html_e('RMM, backup, and documentation platforms without the licensing.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Specialists', 'msp-nexus'); ?></strong><?php esc_html_e('Cloud, networking, and security experts when a ticket needs depth.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Vacation cover', 'msp-nexus'); ?></strong><?php esc_html_e('Your one-person IT department can finally take a week off.', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-cta nx-fs-cta--top","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-cta nx-fs-cta--top"><!-- wp:group {"align":"wide","className":"nx-fs-cta__panel","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-cta__panel"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Tell us where your team is stretched.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('A short call with your IT lead is enough to sketch a responsibility split and a price.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%","className":"nx-fs-cta__actions"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-cta__actions" style="flex-basis:40%"><!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"className":"nx-fs-btn-primary"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 nx-fs-btn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Talk to an engineer', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
