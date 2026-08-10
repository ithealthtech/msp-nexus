<?php
/** @var array<string, mixed> $attributes */
$slug = sanitize_title((string) ($attributes['layoutSlug'] ?? ''));
$label = sanitize_text_field((string) ($attributes['label'] ?? __('Explore', 'msp-nexus-core')));
if ('' === $slug) {
    return;
}
$layouts = new MspNexusCore\Studio\Layouts();
$conditions = new MspNexusCore\Studio\TemplateConditions($layouts);
$content = $conditions->render_region($slug, 'mega_menu');
if ('' === $content) {
    return;
}
$width = trim((string) ($attributes['width'] ?? '720px'));
if (! preg_match('/^(?:[0-9]+(?:\.[0-9]+)?(?:px|rem|vw|%))$/', $width)) {
    $width = '720px';
}
$alignment = sanitize_key((string) ($attributes['alignment'] ?? 'start'));
if (! in_array($alignment, array('start', 'center', 'end'), true)) {
    $alignment = 'start';
}
$config = array('hoverOpen' => ! empty($attributes['hoverOpen']), 'closeOutside' => ! empty($attributes['closeOutside']));
?>
<details <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-menu-panel is-' . $alignment, 'data-nexus-menu-panel' => '', 'data-config' => wp_json_encode($config), 'style' => '--msp-menu-panel-width:' . $width)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><summary><?php echo esc_html($label); ?> <span aria-hidden="true">+</span></summary><div class="msp-nexus-menu-panel__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></details>
