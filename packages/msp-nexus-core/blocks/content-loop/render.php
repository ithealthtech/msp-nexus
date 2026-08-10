<?php
/**
 * Advanced visual content loop renderer.
 *
 * @var array<string, mixed> $attributes
 */
$post_type = sanitize_key((string) ($attributes['postType'] ?? 'msp_service'));
$object = get_post_type_object($post_type);
if (! $object || ! $object->public || 'attachment' === $post_type) {
    $post_type = 'post';
}
$count = min(24, max(1, (int) ($attributes['count'] ?? 6)));
$columns = min(4, max(1, (int) ($attributes['columns'] ?? 3)));
$order_by = sanitize_key((string) ($attributes['orderBy'] ?? 'menu_order'));
if (! in_array($order_by, array('date', 'title', 'menu_order', 'modified', 'rand', 'comment_count', 'meta_value', 'meta_value_num'), true)) {
    $order_by = 'menu_order';
}
$order = 'DESC' === strtoupper((string) ($attributes['order'] ?? 'ASC')) ? 'DESC' : 'ASC';
$paged = ! empty($attributes['showPagination']) ? max(1, (int) get_query_var('paged')) : 1;
$args = array(
    'post_type' => $post_type,
    'post_status' => 'publish',
    'posts_per_page' => $count,
    'paged' => $paged,
    'offset' => min(500, max(0, (int) ($attributes['offset'] ?? 0))),
    'orderby' => $order_by,
    'order' => $order,
    'ignore_sticky_posts' => true,
);
$ids = static function (string $value): array {
    return array_values(array_filter(array_map('absint', explode(',', $value))));
};
$include_ids = $ids((string) ($attributes['includeIds'] ?? ''));
$exclude_ids = $ids((string) ($attributes['excludeIds'] ?? ''));
$authors = $ids((string) ($attributes['authors'] ?? ''));
if ($include_ids) {
    $args['post__in'] = $include_ids;
}
if ($exclude_ids) {
    $args['post__not_in'] = $exclude_ids;
}
if (! empty($attributes['excludeCurrent']) && is_singular()) {
    $args['post__not_in'] = array_values(array_unique(array_merge((array) ($args['post__not_in'] ?? array()), array(get_queried_object_id()))));
}
if ($authors) {
    $args['author__in'] = $authors;
}
$tax_query = array();
foreach (array(array('taxonomy', 'terms'), array('taxonomy2', 'terms2')) as $pair) {
    $taxonomy = sanitize_key((string) ($attributes[$pair[0]] ?? ''));
    $terms = array_values(array_filter(array_map('sanitize_title', explode(',', (string) ($attributes[$pair[1]] ?? '')))));
    if ($taxonomy && $terms && is_object_in_taxonomy($post_type, $taxonomy)) {
        $tax_query[] = array('taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $terms);
    }
}
$related_taxonomy = sanitize_key((string) ($attributes['relatedTaxonomy'] ?? ''));
if ($related_taxonomy && is_singular() && is_object_in_taxonomy($post_type, $related_taxonomy)) {
    $related_terms = wp_get_post_terms(get_queried_object_id(), $related_taxonomy, array('fields' => 'ids'));
    if (is_array($related_terms) && $related_terms) {
        $tax_query[] = array('taxonomy' => $related_taxonomy, 'field' => 'term_id', 'terms' => $related_terms);
    }
}
if ($tax_query) {
    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'OR' === strtoupper((string) ($attributes['taxRelation'] ?? 'AND')) ? 'OR' : 'AND';
    }
    $args['tax_query'] = $tax_query;
}
$meta_key = sanitize_key((string) ($attributes['metaKey'] ?? ''));
$allowed_meta = (new MspNexusCore\Content\DynamicData())->allowed_meta($post_type);
if ($meta_key && array_key_exists($meta_key, $allowed_meta)) {
    $compare = strtoupper((string) ($attributes['metaCompare'] ?? '='));
    if (! in_array($compare, array('=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS'), true)) {
        $compare = '=';
    }
    $args['meta_key'] = $meta_key;
    $args['meta_query'] = array(array('key' => $meta_key, 'value' => sanitize_text_field((string) ($attributes['metaValue'] ?? '')), 'compare' => $compare));
} elseif (in_array($order_by, array('meta_value', 'meta_value_num'), true)) {
    $args['orderby'] = 'date';
}
$query = new WP_Query($args);
$template_slug = sanitize_title((string) ($attributes['templateSlug'] ?? ''));
$template = '' !== $template_slug ? (new MspNexusCore\Studio\Layouts())->by_slug($template_slug, 'loop') : null;
$layout = sanitize_key((string) ($attributes['layout'] ?? 'grid'));
if (! in_array($layout, array('grid', 'list', 'masonry'), true)) {
    $layout = 'grid';
}
$depth = (int) ($GLOBALS['msp_nexus_loop_template_depth'] ?? 0);
?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-content-loop columns-' . $columns . ' is-' . $layout)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="msp-nexus-content-loop__grid">
        <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
            <?php if ($template instanceof WP_Post && $depth < 3) : $GLOBALS['msp_nexus_loop_template_depth'] = $depth + 1; ?>
                <article class="msp-nexus-content-loop__item" data-post-id="<?php echo esc_attr((string) get_the_ID()); ?>"><?php echo do_blocks($template->post_content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></article>
                <?php $GLOBALS['msp_nexus_loop_template_depth'] = $depth; ?>
            <?php else : ?>
                <article class="msp-nexus-content-loop__card">
                    <?php if (! empty($attributes['showImage']) && has_post_thumbnail()) : ?><a class="msp-nexus-content-loop__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail('large', array('loading' => 'lazy', 'decoding' => 'async', 'alt' => '')); ?></a><?php endif; ?>
                    <?php if (! empty($attributes['showMeta'])) : ?><p class="msp-nexus-content-loop__meta"><?php echo esc_html(get_the_date()); ?></p><?php endif; ?>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php if (! empty($attributes['showExcerpt'])) : ?><div class="msp-nexus-content-loop__excerpt"><?php the_excerpt(); ?></div><?php endif; ?>
                    <?php if ('' !== trim((string) ($attributes['buttonLabel'] ?? ''))) : ?><p><a class="msp-nexus-content-loop__link" href="<?php the_permalink(); ?>"><?php echo esc_html((string) $attributes['buttonLabel']); ?> <span aria-hidden="true">&rarr;</span></a></p><?php endif; ?>
                </article>
            <?php endif; ?>
        <?php endwhile; wp_reset_postdata(); else : ?><p><?php echo esc_html((string) ($attributes['noResultsText'] ?? __('No published content matches this loop.', 'msp-nexus-core'))); ?></p><?php endif; ?>
    </div>
    <?php if (! empty($attributes['showPagination']) && $query->max_num_pages > 1) : ?><nav class="msp-nexus-content-loop__pagination" aria-label="<?php esc_attr_e('Content pages', 'msp-nexus-core'); ?>"><?php echo wp_kses_post(paginate_links(array('total' => $query->max_num_pages, 'current' => $paged))); ?></nav><?php endif; ?>
</section>
