<?php
/** @var array<string, mixed> $attributes */
$slug = sanitize_title((string) ($attributes['layoutSlug'] ?? ''));
$area = sanitize_key((string) ($attributes['area'] ?? 'loop'));
if (! in_array($area, array('template', 'header', 'footer', 'mega_menu', 'popup', 'loop'), true) || '' === $slug) {
    return;
}
$layouts = new MspNexusCore\Studio\Layouts();
$conditions = new MspNexusCore\Studio\TemplateConditions($layouts);
$content = $conditions->render_region($slug, $area);
if ('' === $content) {
    return;
}
echo '<div ' . get_block_wrapper_attributes(array('class' => 'msp-nexus-layout-region msp-nexus-layout-region--' . $area, 'data-layout' => $slug)) . '>' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
