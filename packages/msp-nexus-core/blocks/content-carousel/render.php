<?php
/** @var array<string, mixed> $attributes */
$lines = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) ($attributes['items'] ?? '')) ?: array())));
$items = array();
foreach (array_slice($lines, 0, 12) as $line) {
    $parts = array_map('trim', explode('|', $line, 3));
    if ('' !== ($parts[0] ?? '')) {
        $items[] = array(sanitize_text_field($parts[0]), sanitize_textarea_field((string) ($parts[1] ?? '')), esc_url((string) ($parts[2] ?? '')));
    }
}
if (! $items) {
    return;
}
$id = 'msp-nexus-carousel-' . substr(hash('sha256', (string) ($attributes['items'] ?? '')), 0, 12);
$config = array('autoPlay' => ! empty($attributes['autoPlay']), 'interval' => min(30000, max(3000, (int) ($attributes['interval'] ?? 7000))));
?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-carousel', 'data-nexus-carousel' => wp_json_encode($config))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-roledescription="carousel" aria-label="<?php echo esc_attr((string) ($attributes['heading'] ?? '')); ?>"><div class="msp-nexus-carousel__header"><h2><?php echo esc_html((string) ($attributes['heading'] ?? '')); ?></h2><div><button type="button" data-carousel-previous aria-controls="<?php echo esc_attr($id); ?>"><span aria-hidden="true">&larr;</span><span class="screen-reader-text"><?php esc_html_e('Previous slide', 'msp-nexus-core'); ?></span></button><button type="button" data-carousel-next aria-controls="<?php echo esc_attr($id); ?>"><span aria-hidden="true">&rarr;</span><span class="screen-reader-text"><?php esc_html_e('Next slide', 'msp-nexus-core'); ?></span></button><?php if (! empty($attributes['autoPlay'])) : ?><button type="button" data-carousel-pause aria-pressed="false"><?php esc_html_e('Pause', 'msp-nexus-core'); ?></button><?php endif; ?></div></div><div id="<?php echo esc_attr($id); ?>" class="msp-nexus-carousel__track" aria-live="polite"><?php foreach ($items as $index => $item) : ?><article aria-roledescription="slide" aria-label="<?php echo esc_attr(sprintf(__('%1$d of %2$d', 'msp-nexus-core'), $index + 1, count($items))); ?>" <?php echo 0 === $index ? '' : 'hidden'; ?>><h3><?php echo esc_html($item[0]); ?></h3><p><?php echo esc_html($item[1]); ?></p><?php if ($item[2]) : ?><p><a href="<?php echo esc_url($item[2]); ?>"><?php esc_html_e('Learn more', 'msp-nexus-core'); ?> <span aria-hidden="true">&rarr;</span></a></p><?php endif; ?></article><?php endforeach; ?></div><div class="msp-nexus-carousel__dots" aria-label="<?php esc_attr_e('Choose slide', 'msp-nexus-core'); ?>"><?php foreach ($items as $index => $item) : ?><button type="button" data-carousel-index="<?php echo esc_attr((string) $index); ?>" aria-current="<?php echo 0 === $index ? 'true' : 'false'; ?>"><span class="screen-reader-text"><?php echo esc_html(sprintf(__('Show slide %d', 'msp-nexus-core'), $index + 1)); ?></span></button><?php endforeach; ?></div></section>
