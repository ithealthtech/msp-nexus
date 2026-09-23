<?php
/**
 * Title: Core: About
 * Slug: msp-nexus/flagship-northstar-about
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: About page with a founding story beside team photography, operating principles, sample proof figures, leadership cards, and a hiring call to action.
 *
 * @package MspNexus
 */

$msp_nexus_leaders = array(
	array('initials' => 'AR', 'name' => __('Alex Rivera', 'msp-nexus'), 'role' => __('Founder & CEO', 'msp-nexus'), 'bio' => __('Ran IT for a 400-person manufacturer before starting Northstar to fix what frustrated him as a client.', 'msp-nexus')),
	array('initials' => 'JC', 'name' => __('Jordan Chen', 'msp-nexus'), 'role' => __('Director of Service Delivery', 'msp-nexus'), 'bio' => __('Owns response targets, pod staffing, and the monthly numbers every client sees.', 'msp-nexus')),
	array('initials' => 'SP', 'name' => __('Sam Patel', 'msp-nexus'), 'role' => __('Head of Security', 'msp-nexus'), 'bio' => __('Leads the SOC and incident response, and translates risk into board-ready language.', 'msp-nexus')),
	array('initials' => 'MO', 'name' => __('Morgan Okafor', 'msp-nexus'), 'role' => __('Lead vCIO', 'msp-nexus'), 'bio' => __('Builds the budgets and roadmaps behind every quarterly business review.', 'msp-nexus')),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-pagehero","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-pagehero"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"52rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('About Northstar', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"nx-fs-hero__title"} -->
<h1 class="wp-block-heading nx-fs-hero__title"><?php echo wp_kses_post(__('We started as the client <em>who was tired of waiting.</em>', 'msp-nexus')); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-hero__lede"} -->
<p class="nx-fs-hero__lede"><?php esc_html_e('Northstar was founded by people who spent years on the other side of the help desk. We built the provider we wished we could have hired.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-row","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-row"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-fs-row__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center nx-fs-row__grid"><!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__media"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__media" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"nx-fs-row__photo"} -->
<figure class="wp-block-image size-full nx-fs-row__photo"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/stock/team-planning.webp')); ?>" alt="<?php esc_attr_e('Team members taking notes together around a laptop', 'msp-nexus'); ?>" loading="lazy" decoding="async"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-fs-row__copy"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-row__copy" style="flex-basis:50%"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Our story', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Built around one question: would we want to be our client?', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('That question shaped everything: small dedicated pods instead of a ticket queue, published response targets instead of vague promises, and documentation clients own outright.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Today we support organizations across healthcare, legal, finance, manufacturing, and nonprofits, and we still measure ourselves the same way.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-principles","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-principles"><!-- wp:group {"align":"wide","className":"nx-fs-head nx-fs-head--center","layout":{"type":"constrained","contentSize":"46rem"}} -->
<div class="wp-block-group alignwide nx-fs-head nx-fs-head--center"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('How we work', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('Four principles we are held to.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:list {"align":"wide","className":"nx-fs-included__grid nx-fs-principles__grid"} -->
<ul class="wp-block-list alignwide nx-fs-included__grid nx-fs-principles__grid"><!-- wp:list-item -->
<li><strong><?php esc_html_e('Answer like it is ours', 'msp-nexus'); ?></strong><?php esc_html_e('Every ticket gets the urgency we would want for our own business.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Show the numbers', 'msp-nexus'); ?></strong><?php esc_html_e('Response times, backlog, and security scores are reported, not summarized.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Explain it plainly', 'msp-nexus'); ?></strong><?php esc_html_e('No jargon in reports, budgets, or recommendations.', 'msp-nexus'); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e('Earn the renewal', 'msp-nexus'); ?></strong><?php esc_html_e('No lock-in. If we are not worth keeping, you can leave cleanly.', 'msp-nexus'); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-light nx-fs-tint nx-fs-leaders","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-light nx-fs-tint nx-fs-leaders"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"46rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php esc_html_e('Leadership', 'msp-nexus'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('The people accountable for your service.', 'msp-nexus'); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"nx-fs-leaders__grid","layout":{"type":"grid","columnCount":4,"minimumColumnWidth":"13rem"}} -->
<div class="wp-block-group alignwide nx-fs-leaders__grid"><?php foreach ($msp_nexus_leaders as $msp_nexus_leader) : ?><!-- wp:group {"className":"nx-fs-leader","layout":{"type":"default"}} -->
<div class="wp-block-group nx-fs-leader"><!-- wp:paragraph {"className":"nx-fs-leader__avatar"} -->
<p class="nx-fs-leader__avatar" aria-hidden="true"><?php echo esc_html($msp_nexus_leader['initials']); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html($msp_nexus_leader['name']); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-leader__role"} -->
<p class="nx-fs-leader__role"><?php echo esc_html($msp_nexus_leader['role']); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html($msp_nexus_leader['bio']); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --><?php endforeach; ?></div>
<!-- /wp:group -->

<!-- wp:paragraph {"align":"wide","className":"nx-fs-sample-note"} -->
<p class="alignwide nx-fs-sample-note"><?php esc_html_e('Sample people. Replace with your team, or use the Team directory block to pull from Team Members.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-cta nx-fs-cta--top","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-cta nx-fs-cta--top"><!-- wp:group {"align":"wide","className":"nx-fs-cta__panel","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-cta__panel"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php esc_html_e('See if we are the right fit.', 'msp-nexus'); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Thirty minutes with an engineer, a written summary afterwards, and no pressure either way.', 'msp-nexus'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%","className":"nx-fs-cta__actions"} -->
<div class="wp-block-column is-vertically-aligned-center nx-fs-cta__actions" style="flex-basis:40%"><!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"className":"nx-fs-btn-primary"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 nx-fs-btn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php esc_html_e('Book your assessment', 'msp-nexus'); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"width":100,"className":"is-style-outline nx-fs-btn-ghost"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline nx-fs-btn-ghost"><a class="wp-block-button__link wp-element-button" href="/careers/"><?php esc_html_e('We are hiring', 'msp-nexus'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
