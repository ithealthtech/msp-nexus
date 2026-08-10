<?php
if (function_exists('yoast_breadcrumb')) {
    yoast_breadcrumb('<nav aria-label="' . esc_attr__('Breadcrumbs', 'msp-nexus-core') . '">', '</nav>');
    return;
}
if (function_exists('rank_math_the_breadcrumbs')) {
    echo '<nav aria-label="' . esc_attr__('Breadcrumbs', 'msp-nexus-core') . '">';
    rank_math_the_breadcrumbs();
    echo '</nav>';
    return;
}
if (is_front_page()) {
    return;
}
?>
<nav <?php echo get_block_wrapper_attributes(array('aria-label' => __('Breadcrumbs', 'msp-nexus-core'), 'class' => 'msp-nexus-breadcrumbs')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><ol><li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'msp-nexus-core'); ?></a></li><?php if (is_singular()) : ?><li aria-current="page"><?php echo esc_html(get_the_title()); ?></li><?php elseif (is_archive()) : ?><li aria-current="page"><?php echo esc_html(get_the_archive_title()); ?></li><?php endif; ?></ol></nav>
