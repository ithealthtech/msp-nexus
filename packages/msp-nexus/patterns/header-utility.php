<?php
/**
 * Title: Header with utility navigation
 * Slug: msp-nexus/header-utility
 * Categories: header
 * Block Types: core/template-part/header
 *
 * @package MspNexus
 */
?>
<!-- wp:group {"align":"full","backgroundColor":"ink","textColor":"white","style":{"spacing":{"padding":{"top":"8px","bottom":"8px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-white-color has-ink-background-color has-text-color has-background" style="padding-top:8px;padding-bottom:8px"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"},"fontSize":"xs"} -->
<div class="wp-block-group alignwide has-xs-font-size"><!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"msp-nexus/settings","args":{"field":"announcement"}}}}} -->
<p>Security-first IT operations for growing organizations</p>
<!-- /wp:paragraph --><!-- wp:paragraph -->
<p><a href="/support/">Client support</a> · <a href="/contact/">Contact</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --><!-- wp:group {"align":"full","backgroundColor":"white","style":{"spacing":{"padding":{"top":"18px","bottom":"18px"}},"position":{"type":"sticky","top":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-white-background-color has-background" style="padding-top:18px;padding-bottom:18px"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide"><!-- wp:site-logo {"width":40} /--><!-- wp:site-title {"level":0} /--><!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"center"}} /--><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"metadata":{"bindings":{"url":{"source":"msp-nexus/settings","args":{"field":"cta_url"}}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Book a consultation</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
