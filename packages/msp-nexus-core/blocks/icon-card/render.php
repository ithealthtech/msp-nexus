<?php
/** @var array<string, mixed> $attributes */
$icons = array(
    'shield' => '<path d="M12 3 5 6v5c0 4.4 2.9 8.4 7 10 4.1-1.6 7-5.6 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-5"/>',
    'cloud' => '<path d="M7 18h10a4 4 0 0 0 .7-7.9A6 6 0 0 0 6.3 8.4 4.8 4.8 0 0 0 7 18Z"/>',
    'support' => '<path d="M4 13v-2a8 8 0 0 1 16 0v2"/><path d="M4 13h3v6H5a2 2 0 0 1-2-2v-2a2 2 0 0 1 1-2Zm16 0h-3v6h1a2 2 0 0 0 2-2v-4Zm-3 6c-1 2-3 2-5 2"/>',
    'network' => '<rect x="3" y="4" width="7" height="5" rx="1"/><rect x="14" y="15" width="7" height="5" rx="1"/><path d="M6.5 9v4h11v2M12 6.5h2"/>',
    'strategy' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><path d="m12 12 6-6"/>',
    'identity' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
    'continuity' => '<path d="M20 7v5h-5M4 17v-5h5"/><path d="M6.1 9a7 7 0 0 1 11.8-2L20 9M4 15l2.1 2A7 7 0 0 0 18 15"/>',
    'data' => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
);
$icon = sanitize_key((string) ($attributes['icon'] ?? 'shield'));
$icon_markup = $icons[$icon] ?? $icons['shield'];
?>
<article <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-icon-card')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg><h3><?php echo esc_html((string) ($attributes['heading'] ?? '')); ?></h3><p><?php echo esc_html((string) ($attributes['body'] ?? '')); ?></p><?php if ('' !== trim((string) ($attributes['linkLabel'] ?? '')) && '' !== trim((string) ($attributes['linkUrl'] ?? ''))) : ?><p><a href="<?php echo esc_url((string) $attributes['linkUrl']); ?>"><?php echo esc_html((string) $attributes['linkLabel']); ?> <span aria-hidden="true">&rarr;</span></a></p><?php endif; ?></article>
