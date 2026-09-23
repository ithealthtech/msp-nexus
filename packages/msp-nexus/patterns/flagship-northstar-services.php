<?php
/**
 * Title: Core: Services overview
 * Slug: msp-nexus/flagship-northstar-services
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Services overview with a jump-link hero, alternating photo rows for each service line, an included-in-every-plan grid, and a closing call to action.
 *
 * @package MspNexus
 */

$msp_nexus_rows = array(
	array(
		'id'    => 'support',
		'kicker'=> __('Help desk & end-user support', 'msp-nexus'),
		'title' => __('Answers in minutes from people who know your setup.', 'msp-nexus'),
		'body'  => __('Phone, chat, email, and portal support from a US-based team. Your pod keeps notes on your people, devices, and quirks, so nobody has to explain the same problem twice.', 'msp-nexus'),
		'items' => array(__('Unlimited remote and on-site support', 'msp-nexus'), __('New-hire and offboarding checklists', 'msp-nexus'), __('Device procurement and setup', 'msp-nexus')),
		'image' => 'laptop-support',
		'alt'   => __('Hands typing on a laptop at a shared worktable', 'msp-nexus'),
		'link'  => '',
	),
	array(
		'id'    => 'security',
		'kicker'=> __('Cybersecurity', 'msp-nexus'),
		'title' => __('Layered protection with analysts watching around the clock.', 'msp-nexus'),
		'body'  => __('Endpoint detection, email filtering, identity protection, and a 24/7 SOC that can isolate a device at 3 a.m. instead of opening a ticket for the morning.', 'msp-nexus'),
		'items' => array(__('Managed detection and response', 'msp-nexus'), __('Phishing simulation and training', 'msp-nexus'), __('Incident response retainer', 'msp-nexus')),
		'image' => 'code-review',
		'alt'   => __('Source code on a laptop screen in an office', 'msp-nexus'),
		'link'  => '/cybersecurity/',
	),
	array(
		'id'    => 'infrastructure',
		'kicker'=> __('Network & infrastructure', 'msp-nexus'),
		'title' => __('Networks that are documented, patched, and boring.', 'msp-nexus'),
		'body'  => __('Firewalls, switches, Wi-Fi, and servers monitored continuously and patched on a published schedule, with diagrams and credentials kept where you can see them.', 'msp-nexus'),
		'items' => array(__('24/7 monitoring and alerting', 'msp-nexus'), __('Firmware and patch management', 'msp-nexus'), __('Lifecycle and warranty tracking', 'msp-nexus')),
		'image' => 'server-racks',
		'alt'   => __('Network racks with neatly routed patch cables', 'msp-nexus'),
		'link'  => '',
	),
	array(
		'id'    => 'cloud',
		'kicker'=> __('Cloud & Microsoft 365', 'msp-nexus'),
		'title' => __('Cloud that is secure by default and priced on purpose.', 'msp-nexus'),
		'body'  => __('Microsoft 365, Azure, and AWS designed, migrated, and governed. We right-size licensing every quarter so you stop paying for seats nobody uses.', 'msp-nexus'),
		'items' => array(__('Tenant hardening and conditional access', 'msp-nexus'), __('Migrations with a rollback plan', 'msp-nexus'), __('Quarterly license and cost review', 'msp-nexus')),
		'image' => 'developer-workspace',
		'alt'   => __('A developer reviewing code on a laptop at a small table', 'msp-nexus'),
		'link'  => '/cloud-microsoft-365/',
	),
	array(
		'id'    => 'backup',
		'kicker'=> __('Backup & disaster recovery', 'msp-nexus'),
		'title' => __('Backups you have actually watched restore.', 'msp-nexus'),
		'body'  => __('Immutable, off-site backups for servers, SaaS, and endpoints, with monthly restore tests and a recovery runbook your leadership team has read.', 'msp-nexus'),
		'items' => array(__('Immutable and air-gapped copies', 'msp-nexus'), __('Microsoft 365 and Google backup', 'msp-nexus'), __('Tested recovery-time targets', 'msp-nexus')),
		'image' => 'datacenter-aisle',
		'alt'   => __('A long aisle of enclosed server racks lit in blue', 'msp-nexus'),
		'link'  => '',
	),
	array(
		'id'    => 'strategy',
		'kicker'=> __('vCIO & compliance', 'msp-nexus'),
		'title' => __('A technology plan your board can read.', 'msp-nexus'),
		'body'  => __('Quarterly business reviews, a three-year budget, and audit-ready evidence for HIPAA, CMMC, PCI, SOC 2, and cyber-insurance questionnaires.', 'msp-nexus'),
		'items' => array(__('Quarterly business reviews', 'msp-nexus'), __('36-month budget and roadmap', 'msp-nexus'), __('Policy and evidence library', 'msp-nexus')),
		'image' => 'advisory-meeting',
		'alt'   => __('A consultant walking a team through a plan', 'msp-nexus'),
		'link'  => '/it-consulting/',
	),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-pagehero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-pagehero"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"52rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Services', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('Everything IT, <em>run by one accountable team.</em>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-hero__lede"} -->
<p class="nx-fs-hero__lede"><?php esc_html_e('Pick a single service or hand us the whole stack. Every engagement shares the same pod, the same documentation, and the same response targets.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:list {"align":"wide","className":"nx-fs-jump"} -->
<ul class="wp-block-list alignwide nx-fs-jump"><?php foreach ($msp_nexus_rows as $msp_nexus_row) : ?><!-- wp:list-item -->
<li><a href="#<?php echo esc_attr($msp_nexus_row['id']); ?>"><?php echo esc_html($msp_nexus_row['kicker']); ?></a></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<?php foreach ($msp_nexus_rows as $msp_nexus_index => $msp_nexus_row) : ?>
<?php $msp_nexus_class = 'nx-fs nx-fs-row' . (0 === $msp_nexus_index % 2 ? ' nx-fs-light' : '') . (1 === $msp_nexus_index % 2 ? ' nx-fs-row--flip' : ''); ?>
<!-- wp:group {"tagName":"section","align":"full","anchor":"<?php echo esc_attr($msp_nexus_row['id']); ?>","className":"<?php echo esc_attr($msp_nexus_class); ?>","layout":{"type":"constrained"}} -->
<section id="<?php echo esc_attr($msp_nexus_row['id']); ?>" class="wp-block-group alignfull <?php echo esc_attr($msp_nexus_class); ?>"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-fs-row__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-fs-row__grid"><!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__media"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__media" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"nx-fs-row__photo"} -->
<figure class="wp-block-image size-full nx-fs-row__photo"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/' . $msp_nexus_row['image'] . '.webp')); ?>" alt="<?php echo esc_attr($msp_nexus_row['alt']); ?>" loading="lazy" decoding="async"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__copy"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__copy" style="flex-basis:50%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo esc_html($msp_nexus_row['kicker']); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo esc_html($msp_nexus_row['title']); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html($msp_nexus_row['body']); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-checks"} -->
<ul class="wp-block-list nx-fs-checks"><?php foreach ($msp_nexus_row['items'] as $msp_nexus_item) : ?><!-- wp:list-item -->
<li><?php echo esc_html($msp_nexus_item); ?></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list --><?php if ('' !== $msp_nexus_row['link']) : ?>

<!-- wp:paragraph {"className":"nx-fs-link"} -->
<p class="nx-fs-link"><a href="<?php echo esc_url($msp_nexus_row['link']); ?>"><?php esc_html_e('Service details', 'msp-nexus'); ?></a></p>
<!-- /wp:paragraph --><?php endif; ?></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
<?php endforeach; ?>

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-tint nx-fs-included","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-tint nx-fs-included"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--center"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Included with every service', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('The basics nobody should have to negotiate.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:list {"align":"wide","className":"nx-fs-included__grid"} -->
<ul class="wp-block-list alignwide nx-fs-included__grid"><!-- wp:list-item -->
<li><strong><?php esc_html_e('Named pod', 'msp-nexus'); ?></strong><?php esc_html_e('The same engineers and vCIO every month.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Published SLAs', 'msp-nexus'); ?></strong><?php esc_html_e('Response targets in writing, reported monthly.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Your documentation', 'msp-nexus'); ?></strong><?php esc_html_e('Diagrams, passwords, and runbooks you own.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Client portal', 'msp-nexus'); ?></strong><?php esc_html_e('Tickets, invoices, and reports in one place.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('No lock-in', 'msp-nexus'); ?></strong><?php esc_html_e('Month-to-month after the first term, with an exit plan.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Security baseline', 'msp-nexus'); ?></strong><?php esc_html_e('MFA, patching, and backups on day one.', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-cta nx-fs-cta--top","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-cta nx-fs-cta--top"><!-- wp:group {"align":"wide","className":"nx-fs-cta__panel","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-cta__panel"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Not sure which services you need?', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Start with a free assessment. We will map what you have, what is at risk, and the smallest set of services that closes the gaps.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%","className":"nx-fs-cta__actions"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-cta__actions" style="flex-basis:40%"><!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"className":"nx-fs-btn-primary"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 nx-fs-btn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Book your assessment', 'msp-nexus'); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"width":100,"className":"is-style-outline nx-fs-btn-ghost"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline nx-fs-btn-ghost"><a class="wp-block-button__link wp-element-button" href="/plans/"><?php esc_html_e('Compare plans', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
