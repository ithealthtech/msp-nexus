<?php
/**
 * Title: Core: Home (Northstar)
 * Slug: msp-nexus/flagship-northstar-home
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Hand-designed enterprise MSP homepage with a service-desk hero, support-model chooser, services bento, outcomes, industries, proof, onboarding timeline, plans, and conversion footer.
 *
 * @package MspNexus
 */

$msp_nexus_sample = esc_html__('Sample figures. Replace with verified numbers before launch.', 'msp-nexus');
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-hero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-hero"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-fs-hero__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-fs-hero__grid"><!-- wp:column {"verticalAlignment":"center","width":"54%","className":"nx-fs-hero__copy"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-hero__copy" style="flex-basis:54%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Managed IT & security operations', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('IT that answers the first time <em>and</em> plans for the next five years.', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-hero__lede"} -->
<p class="nx-fs-hero__lede"><?php esc_html_e('Northstar runs help desk, security, cloud, and strategy for organizations of 25 to 1,000 people, with named engineers, published response targets, and a roadmap your leadership team can actually read.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"nx-fs-hero__actions"} -->
<div class="wp-block-buttons nx-fs-hero__actions"><!-- wp:button {"className":"nx-fs-btn-primary"} -->
<div class="wp-block-button nx-fs-btn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Book a 30-minute assessment', 'msp-nexus'); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline nx-fs-btn-ghost"} -->
<div class="wp-block-button is-style-outline nx-fs-btn-ghost"><a class="wp-block-button__link wp-element-button" href="/plans/"><?php esc_html_e('See plans & pricing', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:list {"className":"nx-fs-hero__assurances"} -->
<ul class="wp-block-list nx-fs-hero__assurances"><!-- wp:list-item -->
<li><?php esc_html_e('No long-term lock-in', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('24/7 security monitoring', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('US-based service desk', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"46%","className":"nx-fs-hero__visual"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-hero__visual" style="flex-basis:46%"><!-- wp:html -->
<div class="nx-fs-console" role="img" aria-label="<?php esc_attr_e('Illustration of a service desk dashboard showing open requests, response times, and security status', 'msp-nexus'); ?>">
	<div class="nx-fs-console__bar"><span></span><span></span><span></span><b>service-desk / live</b></div>
	<div class="nx-fs-console__stats">
		<div><small>Median first response</small><strong>11<i>min</i></strong></div>
		<div><small>Resolved same day</small><strong>87<i>%</i></strong></div>
		<div><small>Endpoints protected</small><strong>2,418</strong></div>
	</div>
	<ul class="nx-fs-console__queue">
		<li><span class="is-done">Resolved</span>New-hire laptop provisioned for Finance<em>08:42</em></li>
		<li><span class="is-live">In progress</span>VPN profile update, Denver office<em>09:05</em></li>
		<li><span class="is-alert">Contained</span>Suspicious sign-in blocked, MFA enforced<em>09:11</em></li>
		<li><span class="is-done">Resolved</span>Shared mailbox permissions, Legal<em>09:26</em></li>
	</ul>
	<div class="nx-fs-console__foot"><span class="nx-fs-pulse"></span>All systems monitored</div>
</div>
<!-- /wp:html --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"nx-fs-proofbar","layout":{"type":"grid","columnCount":4,"minimumColumnWidth":"11rem"}} -->
<div class="wp-block-group alignwide nx-fs-proofbar"><!-- wp:group {"className":"nx-fs-proofbar__item","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-proofbar__item"><!-- wp:paragraph {"className":"nx-fs-proofbar__value"} -->
<p class="nx-fs-proofbar__value">15 min</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-proofbar__label"} -->
<p class="nx-fs-proofbar__label"><?php esc_html_e('Critical-issue response target', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-proofbar__item","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-proofbar__item"><!-- wp:paragraph {"className":"nx-fs-proofbar__value"} -->
<p class="nx-fs-proofbar__value">97%</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-proofbar__label"} -->
<p class="nx-fs-proofbar__label"><?php esc_html_e('Client satisfaction, trailing 12 months', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-proofbar__item","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-proofbar__item"><!-- wp:paragraph {"className":"nx-fs-proofbar__value"} -->
<p class="nx-fs-proofbar__value">3</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-proofbar__label"} -->
<p class="nx-fs-proofbar__label"><?php esc_html_e('Regional offices with on-site engineers', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-proofbar__item","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-proofbar__item"><!-- wp:paragraph {"className":"nx-fs-proofbar__value"} -->
<p class="nx-fs-proofbar__value">SOC 2</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-proofbar__label"} -->
<p class="nx-fs-proofbar__label"><?php esc_html_e('Type II audited operations', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"align":"wide","className":"nx-fs-sample-note"} -->
<p class="alignwide nx-fs-sample-note"><?php echo $msp_nexus_sample; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-models","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-models"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--center"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Choose your support model', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Hand us all of IT, or add depth to the team you already have.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"nx-fs-models__grid"} -->
<div class="wp-block-columns alignwide nx-fs-models__grid"><!-- wp:column {"className":"nx-fs-model nx-fs-model--full"} -->
<div class="wp-block-column nx-fs-model nx-fs-model--full"><!-- wp:paragraph {"className":"nx-fs-model__tag"} -->
<p class="nx-fs-model__tag"><?php esc_html_e('Fully managed IT', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"nx-fs-model__title"} -->
<h3 class="wp-block-heading nx-fs-model__title"><?php esc_html_e('We become your IT department.', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-model__fit"} -->
<p class="nx-fs-model__fit"><?php esc_html_e('Best for organizations without in-house IT, or with one person stretched too thin.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-checks"} -->
<ul class="wp-block-list nx-fs-checks"><!-- wp:list-item -->
<li><?php esc_html_e('Unlimited help desk, remote and on-site', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Security stack, patching, and backup included', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Quarterly roadmap with a dedicated vCIO', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Vendor management, licensing, and procurement', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph {"className":"nx-fs-link"} -->
<p class="nx-fs-link"><a href="/services/"><?php esc_html_e('How fully managed IT works', 'msp-nexus'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-fs-model nx-fs-model--co"} -->
<div class="wp-block-column nx-fs-model nx-fs-model--co"><!-- wp:paragraph {"className":"nx-fs-model__tag"} -->
<p class="nx-fs-model__tag"><?php esc_html_e('Co-managed IT', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"nx-fs-model__title"} -->
<h3 class="wp-block-heading nx-fs-model__title"><?php esc_html_e('We extend the team you have.', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-model__fit"} -->
<p class="nx-fs-model__fit"><?php esc_html_e('Best for internal IT teams that need after-hours coverage, specialists, or tooling.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-checks"} -->
<ul class="wp-block-list nx-fs-checks"><!-- wp:list-item -->
<li><?php esc_html_e('Tier 1 overflow and after-hours desk', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('24/7 SOC feeding your existing ticket system', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Project engineers for migrations and rollouts', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Shared RMM, documentation, and reporting', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph {"className":"nx-fs-link"} -->
<p class="nx-fs-link"><a href="/co-managed-it/"><?php esc_html_e('How co-managed IT works', 'msp-nexus'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-services","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-services"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--split","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--split"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('What we run', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('One team for every layer of your technology.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"nx-fs-link"} -->
<p class="nx-fs-link"><a href="/services/"><?php esc_html_e('All services', 'msp-nexus'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"nx-fs-bento","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-bento"><!-- wp:group {"className":"nx-fs-tile nx-fs-tile--feature","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile nx-fs-tile--feature"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-shield"} -->
<p class="nx-fs-tile__icon nx-icon-shield"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="/cybersecurity/"><?php esc_html_e('Cybersecurity & 24/7 SOC', 'msp-nexus'); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Managed detection and response, identity protection, email security, and vulnerability management, watched around the clock by analysts who can act, not just alert.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-chips"} -->
<ul class="wp-block-list nx-fs-chips"><!-- wp:list-item -->
<li>MDR</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>SIEM</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Phishing defense', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Incident response', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-tile","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-headset"} -->
<p class="nx-fs-tile__icon nx-icon-headset"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="/services/#support"><?php esc_html_e('Help desk', 'msp-nexus'); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Phone, chat, and portal support from engineers who know your environment.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-tile","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-cloud"} -->
<p class="nx-fs-tile__icon nx-icon-cloud"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="/cloud-microsoft-365/"><?php esc_html_e('Cloud & Microsoft 365', 'msp-nexus'); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Azure, AWS, and M365 designed, migrated, secured, and cost-controlled.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-tile","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-restore"} -->
<p class="nx-fs-tile__icon nx-icon-restore"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="/services/#backup"><?php esc_html_e('Backup & recovery', 'msp-nexus'); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Immutable backups with restore tests you can see, not just hope for.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-tile","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-network"} -->
<p class="nx-fs-tile__icon nx-icon-network"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="/services/#infrastructure"><?php esc_html_e('Network & infrastructure', 'msp-nexus'); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Firewalls, Wi-Fi, and servers monitored and patched on schedule.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-tile nx-fs-tile--wide","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-tile nx-fs-tile--wide"><!-- wp:paragraph {"className":"nx-fs-tile__icon nx-icon-compass"} -->
<p class="nx-fs-tile__icon nx-icon-compass"></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="/it-consulting/"><?php esc_html_e('vCIO & compliance', 'msp-nexus'); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Budgets, lifecycle plans, and audit evidence for HIPAA, CMMC, PCI, and cyber-insurance questionnaires.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-tint nx-fs-outcomes","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-tint nx-fs-outcomes"><!-- wp:columns {"align":"wide","className":"nx-fs-outcomes__grid"} -->
<div class="wp-block-columns alignwide nx-fs-outcomes__grid"><!-- wp:column {"width":"38%","className":"nx-fs-outcomes__intro"} -->
<div class="wp-block-column nx-fs-outcomes__intro" style="flex-basis:38%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('What changes in the first 90 days', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Less firefighting. More forward motion.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Every engagement starts with a baseline of your tickets, risks, and spend, so improvement is something you can measure rather than take on faith.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"62%","className":"nx-fs-outcomes__list"} -->
<div class="wp-block-column nx-fs-outcomes__list" style="flex-basis:62%"><!-- wp:group {"className":"nx-fs-outcome","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-outcome"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Fewer repeat tickets', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Root-cause reviews target the issues that keep coming back, not just the ones in front of us.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-outcome","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-outcome"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('A security score that moves', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('MFA, patching, and backup coverage tracked monthly against a baseline you sign off on.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-outcome","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-outcome"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Predictable spend', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('A 36-month hardware and licensing forecast, so budget season has no surprises.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-outcome","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-outcome"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Audit-ready evidence', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Policies, access reviews, and restore tests documented as they happen.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-people","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-people"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-fs-people__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-fs-people__grid"><!-- wp:column {"verticalAlignment":"center","width":"52%","className":"nx-fs-people__media"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-people__media" style="flex-basis:52%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"nx-fs-people__photo"} -->
<figure class="wp-block-image size-full nx-fs-people__photo"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/advisory-meeting.webp')); ?>" alt="<?php esc_attr_e('A consultant walks a leadership team through a plan on a laptop', 'msp-nexus'); ?>" width="960" height="640" loading="lazy" decoding="async"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"nx-fs-people__card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group nx-fs-people__card"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"nx-fs-people__thumb"} -->
<figure class="wp-block-image size-full nx-fs-people__thumb"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/laptop-support.webp')); ?>" alt="" width="960" height="640" loading="lazy" decoding="async"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p><strong><?php esc_html_e('Your pod', 'msp-nexus'); ?></strong><br><?php esc_html_e('2 engineers · 1 vCIO · 1 security analyst', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"48%","className":"nx-fs-people__copy"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-people__copy" style="flex-basis:48%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('People, not ticket numbers', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('A named team that already knows your environment.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Every client gets a dedicated pod. When you call, you reach someone who has seen your network diagram, knows your line-of-business apps, and remembers the last three things you asked for.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-fs-checks"} -->
<ul class="wp-block-list nx-fs-checks"><!-- wp:list-item -->
<li><?php esc_html_e('Same engineers every week, not a rotating queue', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Documentation you own and can read', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('On-site visits scheduled, not just promised', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph {"className":"nx-fs-link"} -->
<p class="nx-fs-link"><a href="/about-us/"><?php esc_html_e('Meet the team', 'msp-nexus'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-industries","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-industries"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"46rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Industries', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('We already know your regulators, software, and busy season.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:list {"align":"wide","className":"nx-fs-industry-list"} -->
<ul class="wp-block-list alignwide nx-fs-industry-list"><!-- wp:list-item -->
<li><a href="/healthcare-it/"><strong><?php esc_html_e('Healthcare', 'msp-nexus'); ?></strong><span><?php esc_html_e('HIPAA, EHR uptime, clinical devices', 'msp-nexus'); ?></span></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/legal-it/"><strong><?php esc_html_e('Legal', 'msp-nexus'); ?></strong><span><?php esc_html_e('Document management, client confidentiality', 'msp-nexus'); ?></span></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/industries/financial-services/"><strong><?php esc_html_e('Financial services', 'msp-nexus'); ?></strong><span><?php esc_html_e('GLBA, FINRA, examiner requests', 'msp-nexus'); ?></span></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/manufacturing-it/"><strong><?php esc_html_e('Manufacturing', 'msp-nexus'); ?></strong><span><?php esc_html_e('OT networks, ERP, CMMC', 'msp-nexus'); ?></span></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/industries/construction/"><strong><?php esc_html_e('Construction', 'msp-nexus'); ?></strong><span><?php esc_html_e('Job-site connectivity, field devices', 'msp-nexus'); ?></span></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/industries/nonprofit/"><strong><?php esc_html_e('Nonprofit', 'msp-nexus'); ?></strong><span><?php esc_html_e('Donor data, grant-funded budgets', 'msp-nexus'); ?></span></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/industries/professional-services/"><strong><?php esc_html_e('Professional services', 'msp-nexus'); ?></strong><span><?php esc_html_e('Billable-hour uptime, remote teams', 'msp-nexus'); ?></span></a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/industries/"><strong><?php esc_html_e('See all industries', 'msp-nexus'); ?></strong><span><?php esc_html_e('Education, government, and more', 'msp-nexus'); ?></span></a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-story","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-story"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-fs-story__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-fs-story__grid"><!-- wp:column {"verticalAlignment":"center","width":"58%","className":"nx-fs-story__quote"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-story__quote" style="flex-basis:58%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Client story · Regional accounting firm', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:quote {"className":"nx-fs-quote"} -->
<blockquote class="wp-block-quote nx-fs-quote"><!-- wp:paragraph -->
<p><?php esc_html_e('Tax season used to mean a week of outages and a server closet held together with hope. This year nobody on the floor noticed IT at all, which is exactly the point.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --><cite><?php esc_html_e('Managing Partner, sample client', 'msp-nexus'); ?></cite></blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph {"className":"nx-fs-link"} -->
<p class="nx-fs-link"><a href="/case-studies/"><?php esc_html_e('Read the full case study', 'msp-nexus'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"42%","className":"nx-fs-story__metrics"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-story__metrics" style="flex-basis:42%"><!-- wp:group {"className":"nx-fs-metric","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-metric"><!-- wp:paragraph {"className":"nx-fs-metric__value"} -->
<p class="nx-fs-metric__value">−62%</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-metric__label"} -->
<p class="nx-fs-metric__label"><?php esc_html_e('Monthly ticket volume after six months', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-metric","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-metric"><!-- wp:paragraph {"className":"nx-fs-metric__value"} -->
<p class="nx-fs-metric__value">0</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-metric__label"} -->
<p class="nx-fs-metric__label"><?php esc_html_e('Hours of unplanned downtime during filing season', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"nx-fs-metric","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-metric"><!-- wp:paragraph {"className":"nx-fs-metric__value"} -->
<p class="nx-fs-metric__value">4 wks</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-metric__label"} -->
<p class="nx-fs-metric__label"><?php esc_html_e('From signed agreement to fully onboarded', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"nx-fs-sample-note"} -->
<p class="nx-fs-sample-note"><?php echo $msp_nexus_sample; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-tint nx-fs-process","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-tint nx-fs-process"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--center"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Switching is easier than you think', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('A structured onboarding, not a leap of faith.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:list {"ordered":true,"align":"wide","className":"nx-fs-steps"} -->
<ol class="wp-block-list alignwide nx-fs-steps"><!-- wp:list-item -->
<li><strong><?php esc_html_e('Assess', 'msp-nexus'); ?></strong><em><?php esc_html_e('Week 1', 'msp-nexus'); ?></em><span><?php esc_html_e('Network discovery, security baseline, and a candid review of what is working.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Transition', 'msp-nexus'); ?></strong><em><?php esc_html_e('Weeks 2–3', 'msp-nexus'); ?></em><span><?php esc_html_e('Credentials, documentation, and vendor contacts moved over with your outgoing provider.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Stabilize', 'msp-nexus'); ?></strong><em><?php esc_html_e('Weeks 4–8', 'msp-nexus'); ?></em><span><?php esc_html_e('Security agents deployed, backups verified, and quick wins closed out.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Plan', 'msp-nexus'); ?></strong><em><?php esc_html_e('Day 90', 'msp-nexus'); ?></em><span><?php esc_html_e('Your first business review, with a prioritized 12-month roadmap and budget.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item --></ol>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-plans","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-plans"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--split","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--split"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Plans', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Flat monthly pricing, per user.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"nx-fs-link"} -->
<p class="nx-fs-link"><a href="/plans/"><?php esc_html_e('Compare every feature', 'msp-nexus'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"nx-fs-plans__grid"} -->
<div class="wp-block-columns alignwide nx-fs-plans__grid"><!-- wp:column {"className":"nx-fs-plan"} -->
<div class="wp-block-column nx-fs-plan"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Essentials', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-plan__price"} -->
<p class="nx-fs-plan__price">$95<span><?php esc_html_e('/user/mo', 'msp-nexus'); ?></span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Help desk, monitoring, patching, and endpoint security.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-fs-plan nx-fs-plan--featured"} -->
<div class="wp-block-column nx-fs-plan nx-fs-plan--featured"><!-- wp:paragraph {"className":"nx-fs-plan__badge"} -->
<p class="nx-fs-plan__badge"><?php esc_html_e('Most chosen', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Complete', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-plan__price"} -->
<p class="nx-fs-plan__price">$145<span><?php esc_html_e('/user/mo', 'msp-nexus'); ?></span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Everything in Essentials plus 24/7 SOC, backup, and a vCIO.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"nx-fs-plan"} -->
<div class="wp-block-column nx-fs-plan"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e('Co-managed', 'msp-nexus'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-plan__price"} -->
<p class="nx-fs-plan__price"><?php esc_html_e('Custom', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Pick the tools, coverage hours, and specialists your team needs.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:paragraph {"align":"wide","className":"nx-fs-sample-note"} -->
<p class="alignwide nx-fs-sample-note"><?php esc_html_e('Sample pricing. Set your own plans under Pricing Plans.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-cta","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-cta"><!-- wp:group {"align":"wide","className":"nx-fs-cta__panel","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-cta__panel"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Find out what your IT is really costing you.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('A free 30-minute assessment covers your security gaps, support pain points, and where spend is leaking. You keep the findings either way.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%","className":"nx-fs-cta__actions"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-cta__actions" style="flex-basis:40%"><!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"className":"nx-fs-btn-primary"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 nx-fs-btn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Book your assessment', 'msp-nexus'); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"width":100,"className":"is-style-outline nx-fs-btn-ghost"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline nx-fs-btn-ghost"><a class="wp-block-button__link wp-element-button" href="/support/"><?php esc_html_e('Existing client? Get support', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
