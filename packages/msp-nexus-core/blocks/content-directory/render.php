<?php
/** @var array<string, mixed> $attributes */
$allowed = array('msp_resource', 'msp_case_study', 'msp_outcome', 'msp_event', 'msp_team', 'msp_location');
$post_type = sanitize_key((string) ($attributes['contentType'] ?? 'msp_resource'));
if (! in_array($post_type, $allowed, true)) {
    $post_type = 'msp_resource';
}
$count = min(24, max(1, (int) ($attributes['count'] ?? 12)));
$columns = min(4, max(1, (int) ($attributes['columns'] ?? 3)));
$keyword = sanitize_text_field(wp_unslash((string) ($_GET['msp_q'] ?? '')));
$topic = sanitize_title(wp_unslash((string) ($_GET['msp_topic'] ?? '')));
$resource_type = sanitize_title(wp_unslash((string) ($_GET['msp_resource_type'] ?? '')));
$tax_query = array();
if ($topic && is_object_in_taxonomy($post_type, 'msp_topic')) {
    $tax_query[] = array('taxonomy' => 'msp_topic', 'field' => 'slug', 'terms' => $topic);
}
if ($resource_type && is_object_in_taxonomy($post_type, 'msp_resource_type')) {
    $tax_query[] = array('taxonomy' => 'msp_resource_type', 'field' => 'slug', 'terms' => $resource_type);
}
$args = array('post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => $count, 'paged' => max(1, (int) get_query_var('paged')), 's' => $keyword);
if ($tax_query) {
    $args['tax_query'] = $tax_query;
}
$query = new WP_Query($args);
$topics = is_object_in_taxonomy($post_type, 'msp_topic') ? get_terms(array('taxonomy' => 'msp_topic', 'hide_empty' => true)) : array();
$types = is_object_in_taxonomy($post_type, 'msp_resource_type') ? get_terms(array('taxonomy' => 'msp_resource_type', 'hide_empty' => true)) : array();
?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-directory columns-' . $columns)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if (! empty($attributes['showFilters'])) : ?>
        <form class="msp-nexus-directory__filters" method="get" role="search">
            <label><?php esc_html_e('Search this directory', 'msp-nexus-core'); ?><input name="msp_q" type="search" value="<?php echo esc_attr($keyword); ?>"></label>
            <?php if (is_array($topics) && $topics) : ?><label><?php esc_html_e('Topic', 'msp-nexus-core'); ?><select name="msp_topic"><option value=""><?php esc_html_e('All topics', 'msp-nexus-core'); ?></option><?php foreach ($topics as $term) : ?><option value="<?php echo esc_attr($term->slug); ?>" <?php selected($topic, $term->slug); ?>><?php echo esc_html($term->name); ?></option><?php endforeach; ?></select></label><?php endif; ?>
            <?php if (is_array($types) && $types) : ?><label><?php esc_html_e('Format', 'msp-nexus-core'); ?><select name="msp_resource_type"><option value=""><?php esc_html_e('All formats', 'msp-nexus-core'); ?></option><?php foreach ($types as $term) : ?><option value="<?php echo esc_attr($term->slug); ?>" <?php selected($resource_type, $term->slug); ?>><?php echo esc_html($term->name); ?></option><?php endforeach; ?></select></label><?php endif; ?>
            <button type="submit"><?php esc_html_e('Apply filters', 'msp-nexus-core'); ?></button><a href="<?php echo esc_url(remove_query_arg(array('msp_q', 'msp_topic', 'msp_resource_type'))); ?>"><?php esc_html_e('Clear', 'msp-nexus-core'); ?></a>
        </form>
    <?php endif; ?>
    <div class="msp-nexus-directory__results" aria-live="polite">
        <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?><article><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><?php the_excerpt(); ?></article><?php endwhile; wp_reset_postdata(); else : ?><p><?php esc_html_e('No matching content was found. Clear a filter or try a broader search.', 'msp-nexus-core'); ?></p><?php endif; ?>
    </div>
    <?php if ($query->max_num_pages > 1) : ?><nav class="msp-nexus-directory__pagination" aria-label="<?php esc_attr_e('Directory pages', 'msp-nexus-core'); ?>"><?php echo wp_kses_post(paginate_links(array('total' => $query->max_num_pages, 'current' => max(1, (int) get_query_var('paged'))))); ?></nav><?php endif; ?>
</section>
