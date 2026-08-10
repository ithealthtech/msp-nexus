<?php
/** @var array<string, mixed> $attributes */
$lines = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) ($attributes['items'] ?? '')) ?: array())));
$items = array();
foreach (array_slice($lines, 0, 12) as $line) {
    $parts = array_map('trim', explode('|', $line, 2));
    if ('' !== ($parts[0] ?? '')) {
        $items[] = array(sanitize_text_field($parts[0]), sanitize_textarea_field((string) ($parts[1] ?? '')));
    }
}
if (! $items) {
    return;
}
$id = 'msp-nexus-tabs-' . substr(hash('sha256', (string) ($attributes['items'] ?? '')), 0, 12);
$orientation = 'vertical' === ($attributes['orientation'] ?? 'horizontal') ? 'vertical' : 'horizontal';
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-tabs is-' . $orientation, 'data-nexus-tabs' => '', 'data-orientation' => $orientation)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><div class="msp-nexus-tabs__list" role="tablist" aria-orientation="<?php echo esc_attr($orientation); ?>"><?php foreach ($items as $index => $item) : ?><button id="<?php echo esc_attr($id . '-tab-' . $index); ?>" type="button" role="tab" aria-controls="<?php echo esc_attr($id . '-panel-' . $index); ?>" aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $index ? '0' : '-1'; ?>"><?php echo esc_html($item[0]); ?></button><?php endforeach; ?></div><div class="msp-nexus-tabs__panels"><?php foreach ($items as $index => $item) : ?><section id="<?php echo esc_attr($id . '-panel-' . $index); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr($id . '-tab-' . $index); ?>" <?php echo 0 === $index ? '' : 'hidden'; ?>><h3><?php echo esc_html($item[0]); ?></h3><p><?php echo esc_html($item[1]); ?></p></section><?php endforeach; ?></div></div>
