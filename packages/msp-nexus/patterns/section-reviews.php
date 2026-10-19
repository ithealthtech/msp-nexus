<?php
/**
 * Title: Client reviews
 * Slug: msp-nexus/section-reviews
 * Categories: msp-nexus-trust
 * Keywords: reviews, testimonials, quotes, ratings
 * Description: Review summary, a featured client quote, and a grid of review cards. Replace the samples with real reviews and their source.
 *
 * @package MspNexus
 */

$e = static function (string $text): string {
    return esc_html($text);
};
$card = static function (string $text, string $name, string $source) use ($e): string {
    return '<!-- wp:group {"tagName":"article","className":"nx-fs-review","layout":{"type":"default"}} -->
<article class="wp-block-group nx-fs-review"><!-- wp:paragraph {"className":"nx-fs-review__stars"} -->
<p class="nx-fs-review__stars"><span aria-hidden="true">★★★★★</span><span class="screen-reader-text">' . $e(__('5 out of 5 stars', 'msp-nexus')) . '</span></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-review__text"} -->
<p class="nx-fs-review__text">' . $e($text) . '</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"nx-fs-review__who"} -->
<p class="nx-fs-review__who"><strong>' . $e($name) . '</strong>' . $e($source) . '</p>
<!-- /wp:paragraph --></article>
<!-- /wp:group -->';
};
$sample = __('Sample review: replace this with a real client review, word for word, and keep the reviewer’s first name and initial.', 'msp-nexus');
?>
<!-- wp:group {"tagName":"section","align":"full","className":"nx-fs nx-fs-story nx-fs-reviews","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull nx-fs nx-fs-story nx-fs-reviews"><!-- wp:group {"align":"wide","className":"nx-fs-head","layout":{"type":"constrained","contentSize":"46rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide nx-fs-head"><!-- wp:paragraph {"className":"nx-fs-kicker"} -->
<p class="nx-fs-kicker"><?php echo $e(__('What clients say', 'msp-nexus')); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"nx-fs-title"} -->
<h2 class="wp-block-heading nx-fs-title"><?php echo $e(__('Real reviews from real clients.', 'msp-nexus')); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"nx-fs-reviews__summary"} -->
<p class="nx-fs-reviews__summary"><strong>5.0 ★</strong> <?php echo $e(__('average from your review profile (replace with your real rating and count)', 'msp-nexus')); ?> <a href="#"><?php echo $e(__('Read them all', 'msp-nexus')); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"nx-fs-reviews__feature","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-reviews__feature"><!-- wp:quote {"className":"nx-fs-quote"} -->
<blockquote class="wp-block-quote nx-fs-quote"><!-- wp:paragraph -->
<p><?php echo $e(__('Sample featured quote: use your strongest review from a business client here.', 'msp-nexus')); ?></p>
<!-- /wp:paragraph --><cite><?php echo $e(__('Client company name', 'msp-nexus')); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"nx-fs-reviews__grid","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide nx-fs-reviews__grid"><?php
echo $card($sample, __('Client A.', 'msp-nexus'), __('Google review · year', 'msp-nexus')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in $card.
echo "\n\n" . $card($sample, __('Client B.', 'msp-nexus'), __('Google review · year', 'msp-nexus')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo "\n\n" . $card($sample, __('Client C.', 'msp-nexus'), __('Review site · year', 'msp-nexus')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
