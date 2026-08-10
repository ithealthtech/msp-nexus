<?php
/** @var array<string, mixed> $attributes */
$count   = min(12, max(1, (int) ($attributes['count'] ?? 6)));
$columns = min(4, max(1, (int) ($attributes['columns'] ?? 3)));
$query   = new WP_Query(array('post_type' => 'msp_service', 'post_status' => 'publish', 'posts_per_page' => $count, 'orderby' => array('menu_order' => 'ASC', 'title' => 'ASC'), 'no_found_rows' => true));
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-service-grid columns-' . $columns)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if ($query->have_posts()) : ?>
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <article class="msp-nexus-service-grid__item">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <?php if (! empty($attributes['showExcerpt'])) : ?><div><?php the_excerpt(); ?></div><?php endif; ?>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
    <?php else : ?>
        <p><?php esc_html_e('Add published services to populate this section.', 'msp-nexus-core'); ?></p>
    <?php endif; ?>
</div>
