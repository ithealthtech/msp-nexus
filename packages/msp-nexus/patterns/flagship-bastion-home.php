<?php
/**
 * Title: Service: Cybersecurity (Bastion)
 * Slug: msp-nexus/flagship-bastion-home
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Security-first homepage for an MSSP: incident hotline strip, editorial hero, detect-contain-recover timeline, coverage console, compliance frameworks, numbered services, and a risk-assessment offer.
 *
 * @package MspNexus
 */

$msp_nexus_sample = esc_html__('Sample figures. Replace with your measured numbers before launch.', 'msp-nexus');
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-bs nx-bs-alert","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-bs nx-bs-alert"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"nx-bs-alert__text"} -->
<p class="nx-bs-alert__text"><?php esc_html_e('Under attack right now? Our incident response line is staffed 24/7.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"nx-bs-alert__btn","metadata":{"bindings":{"text":{"source":"msp-nexus/settings","args":{"field":"support_phone"}},"url":{"source":"msp-nexus/settings","args":{"field":"support_phone"}}}}} -->
<div class="wp-block-button nx-bs-alert__btn"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Call the IR hotline', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-bs nx-bs-hero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-bs nx-bs-hero"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"nx-bs-mono"} -->
<p class="nx-bs-mono"><?php esc_html_e('// managed detection & response', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-bs-hero__title"} -->
<h1 class="wp-block-heading nx-bs-hero__title"><?php echo wp_kses_post(__('Attackers work nights.<br><mark>So do we.</mark>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:columns {"className":"nx-bs-hero__below"} -->
<div class="wp-block-columns nx-bs-hero__below"><!-- wp:column {"width":"55%"} -->
<div class="wp-block-column" style="flex-basis:55%"><!-- wp:paragraph {"className":"nx-bs-hero__lede"} -->
<p class="nx-bs-hero__lede"><?php esc_html_e('A security operations center that watches your endpoints, identities, email, and cloud around the clock, and has the authority to contain a threat the moment it is confirmed.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom","width":"45%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:45%"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"nx-bs-btn"} -->
<div class="wp-block-button nx-bs-btn"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Get a risk assessment', 'msp-nexus'); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"nx-bs-btn-line"} -->
<div class="wp-block-button nx-bs-btn-line"><a class="wp-block-button__link wp-element-button" href="#coverage"><?php esc_html_e('What we monitor', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-bs nx-bs-timeline","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-bs nx-bs-timeline"><!-- wp:paragraph {"align":"wide","className":"nx-bs-mono"} -->
<p class="alignwide nx-bs-mono"><?php esc_html_e('// anatomy of a contained incident', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"ordered":true,"align":"wide","className":"nx-bs-phases"} -->
<ol class="wp-block-list alignwide nx-bs-phases"><!-- wp:list-item -->
<li><em>00:00</em><strong><?php esc_html_e('Detect', 'msp-nexus'); ?></strong><span><?php esc_html_e('An impossible-travel sign-in and a new mailbox rule trigger a correlated alert.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><em>00:04</em><strong><?php esc_html_e('Confirm', 'msp-nexus'); ?></strong><span><?php esc_html_e('An analyst reviews the session, the device, and the user’s normal pattern.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><em>00:09</em><strong><?php esc_html_e('Contain', 'msp-nexus'); ?></strong><span><?php esc_html_e('Sessions revoked, password reset, rule removed, device isolated if needed.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><em>00:30</em><strong><?php esc_html_e('Report', 'msp-nexus'); ?></strong><span><?php esc_html_e('You get a plain-language summary: what happened, what we did, what to change.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->

<!-- wp:paragraph {"align":"wide","className":"nx-bs-note"} -->
<p class="alignwide nx-bs-note"><?php echo $msp_nexus_sample; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","anchor":"coverage","className":"nx-bs nx-bs-coverage","layout":{"type":"constrained"}} -->
<section id="coverage" class="wp-block-group alignfull nx-bs nx-bs-coverage"><!-- wp:columns {"align":"wide","className":"nx-bs-coverage__grid"} -->
<div class="wp-block-columns alignwide nx-bs-coverage__grid"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:heading {"className":"nx-bs-title"} -->
<h2 class="wp-block-heading nx-bs-title"><?php esc_html_e('Every door, watched.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Most breaches start with a stolen password or a phishing email, not a movie-style hack. Our coverage follows where attackers actually get in.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:list {"className":"nx-bs-console"} -->
<ul class="wp-block-list nx-bs-console"><!-- wp:list-item -->
<li><b>endpoints</b><?php esc_html_e('EDR on every laptop and server, with remote isolation', 'msp-nexus'); ?><i>on</i></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><b>identity</b><?php esc_html_e('Risky sign-ins, MFA fatigue, and privilege changes', 'msp-nexus'); ?><i>on</i></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><b>email</b><?php esc_html_e('Phishing, business email compromise, and malicious rules', 'msp-nexus'); ?><i>on</i></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><b>cloud</b><?php esc_html_e('Microsoft 365 and Google Workspace audit logs', 'msp-nexus'); ?><i>on</i></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><b>network</b><?php esc_html_e('Firewall and VPN logs correlated in the SIEM', 'msp-nexus'); ?><i>on</i></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><b>people</b><?php esc_html_e('Monthly phishing simulations and short training', 'msp-nexus'); ?><i>on</i></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-bs nx-bs-frameworks","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-bs nx-bs-frameworks"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"nx-bs-frameworks__lead"} -->
<p class="nx-bs-frameworks__lead"><?php esc_html_e('Evidence mapped to the frameworks your auditors and insurers ask about', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"nx-bs-tags"} -->
<ul class="wp-block-list nx-bs-tags"><!-- wp:list-item -->
<li>HIPAA</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>CMMC</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>PCI DSS</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>SOC 2</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>NIST CSF</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php esc_html_e('Cyber insurance', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-bs nx-bs-services","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-bs nx-bs-services"><!-- wp:heading {"align":"wide","className":"nx-bs-title alignwide"} -->
<h2 class="wp-block-heading alignwide nx-bs-title"><?php esc_html_e('Security services', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"align":"wide","className":"nx-bs-numbered"} -->
<ol class="wp-block-list alignwide nx-bs-numbered"><!-- wp:list-item -->
<li><strong><?php esc_html_e('Managed detection & response', 'msp-nexus'); ?></strong><span><?php esc_html_e('24/7 SOC monitoring with containment authority, not just alerts.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Incident response retainer', 'msp-nexus'); ?></strong><span><?php esc_html_e('Pre-agreed responders, runbooks, and a guaranteed call-back window.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Vulnerability management', 'msp-nexus'); ?></strong><span><?php esc_html_e('Continuous scanning, prioritized by what is actually exploitable.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Security awareness', 'msp-nexus'); ?></strong><span><?php esc_html_e('Phishing simulations and bite-size training people finish.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Compliance readiness', 'msp-nexus'); ?></strong><span><?php esc_html_e('Policies, control evidence, and audit support in one place.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('vCISO', 'msp-nexus'); ?></strong><span><?php esc_html_e('A security leader for board updates, budgets, and insurance renewals.', 'msp-nexus'); ?></span></li>
<!-- /wp:list-item --></ol>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-bs nx-bs-offer","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-bs nx-bs-offer"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-bs-offer__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-bs-offer__grid"><!-- wp:column {"verticalAlignment":"center","width":"45%","className":"nx-bs-offer__media"} -->
<div class="wp-block-column is-vertically-aligned-center nx-bs-offer__media" style="flex-basis:45%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/patch-panel.webp')); ?>" alt="<?php esc_attr_e('Close-up of labeled network patch panel ports', 'msp-nexus'); ?>" loading="lazy" decoding="async"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%"><!-- wp:paragraph {"className":"nx-bs-mono"} -->
<p class="nx-bs-mono"><?php esc_html_e('// free for qualifying organizations', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-bs-title"} -->
<h2 class="wp-block-heading nx-bs-title"><?php esc_html_e('Find out what an attacker would find first.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Our risk assessment reviews your external exposure, Microsoft 365 configuration, backups, and MFA coverage, then ranks the fixes by impact. You keep the report.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"nx-bs-btn"} -->
<div class="wp-block-button nx-bs-btn"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Request the assessment', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
