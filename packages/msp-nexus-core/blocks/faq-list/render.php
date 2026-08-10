<?php
/** @var array<string, mixed> $attributes */
$count = min(20, max(1, (int) ($attributes['count'] ?? 8)));
$query = new WP_Query(array('post_type' => 'msp_faq', 'post_status' => 'publish', 'posts_per_page' => $count, 'orderby' => array('menu_order' => 'ASC', 'title' => 'ASC'), 'no_found_rows' => true));
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-faq-list')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if ($query->have_posts()) : $index = 0; ?>
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <details class="msp-nexus-faq-list__item" <?php echo (! empty($attributes['openFirst']) && 0 === $index) ? 'open' : ''; ?>>
                <summary><?php the_title(); ?></summary>
                <div class="msp-nexus-faq-list__answer"><?php the_content(); ?></div>
            </details>
        <?php ++$index; endwhile; wp_reset_postdata(); ?>
    <?php else : ?>
        <p><?php esc_html_e('Add published FAQs to populate this section.', 'msp-nexus-core'); ?></p>
    <?php endif; ?>
</div>
