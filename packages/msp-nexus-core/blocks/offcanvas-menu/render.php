<?php
/** @var array<string, mixed> $attributes */
$slug = sanitize_title((string) ($attributes['layoutSlug'] ?? ''));
$label = sanitize_text_field((string) ($attributes['buttonLabel'] ?? __('Menu', 'msp-nexus-core')));
if ('' === $slug) {
    return;
}
$layouts = new MspNexusCore\Studio\Layouts();
$conditions = new MspNexusCore\Studio\TemplateConditions($layouts);
$content = $conditions->render_region($slug, 'mega_menu');
if ('' === $content) {
    return;
}
$position = 'left' === ($attributes['position'] ?? 'right') ? 'left' : 'right';
$width = trim((string) ($attributes['width'] ?? '420px'));
if (! preg_match('/^(?:[0-9]+(?:\.[0-9]+)?(?:px|rem|vw|%))$/', $width)) {
    $width = '420px';
}
$id = 'msp-nexus-offcanvas-' . substr(hash('sha256', $slug . '|' . $label), 0, 12);
$config = array('closeOnNavigate' => ! empty($attributes['closeOnNavigate']));
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-offcanvas is-' . $position, 'data-nexus-offcanvas' => $id, 'data-config' => wp_json_encode($config))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <button class="msp-nexus-offcanvas__trigger" type="button" aria-controls="<?php echo esc_attr($id); ?>" aria-expanded="false"><span class="msp-nexus-offcanvas__icon" aria-hidden="true"><i></i><i></i><i></i></span><?php if (! empty($attributes['showLabel'])) : ?><span><?php echo esc_html($label); ?></span><?php else : ?><span class="screen-reader-text"><?php echo esc_html($label); ?></span><?php endif; ?></button>
    <dialog id="<?php echo esc_attr($id); ?>" class="msp-nexus-offcanvas__dialog" style="--msp-offcanvas-width:<?php echo esc_attr($width); ?>">
        <button class="msp-nexus-offcanvas__close" type="button" data-offcanvas-close aria-label="<?php esc_attr_e('Close menu', 'msp-nexus-core'); ?>">&times;</button>
        <div class="msp-nexus-offcanvas__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
    </dialog>
</div>
