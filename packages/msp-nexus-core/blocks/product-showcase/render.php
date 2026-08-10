<?php
/** @var array<string, mixed> $attributes */
if (! function_exists('wc_get_product')) {
    if (is_admin()) {
        echo '<p ' . get_block_wrapper_attributes() . '>' . esc_html__('Install and activate WooCommerce to preview products.', 'msp-nexus-core') . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    return;
}
$count = min(12, max(1, (int) ($attributes['count'] ?? 4)));
$columns = min(4, max(1, (int) ($attributes['columns'] ?? 4)));
$args = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => $count, 'orderby' => 'menu_order title', 'order' => 'ASC');
$category = sanitize_title((string) ($attributes['category'] ?? ''));
if ('' !== $category) {
    $args['tax_query'] = array(array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => array($category)));
}
if (! empty($attributes['featuredOnly'])) {
    $args['tax_query'] = isset($args['tax_query']) ? $args['tax_query'] : array();
    $args['tax_query'][] = array('taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => array('featured'));
}
$query = new WP_Query($args);
?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-product-showcase columns-' . $columns)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><div class="msp-nexus-product-showcase__grid">
<?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); $product = wc_get_product(get_the_ID()); if (! $product) { continue; } ?>
    <article class="msp-nexus-product-showcase__card">
        <a href="<?php the_permalink(); ?>" class="msp-nexus-product-showcase__image"><?php echo $product->get_image('woocommerce_thumbnail', array('loading' => 'lazy')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <?php if (! empty($attributes['showRating'])) : ?><?php echo wp_kses_post(wc_get_rating_html((float) $product->get_average_rating())); ?><?php endif; ?>
        <p class="price"><?php echo wp_kses_post($product->get_price_html()); ?></p>
        <p><a class="wp-element-button" href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-quantity="1" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>"><?php echo esc_html($product->add_to_cart_text()); ?></a></p>
    </article>
<?php endwhile; wp_reset_postdata(); else : ?><p><?php esc_html_e('No products match this showcase.', 'msp-nexus-core'); ?></p><?php endif; ?>
</div></section>
