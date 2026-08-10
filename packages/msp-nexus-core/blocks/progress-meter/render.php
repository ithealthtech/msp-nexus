<?php
/** @var array<string, mixed> $attributes */
$value = min(100, max(0, (int) ($attributes['value'] ?? 65)));
$color = sanitize_hex_color((string) ($attributes['color'] ?? '')) ?: '#69bff5';
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-progress-meter', 'style' => '--msp-progress:' . $value . '%;--msp-progress-color:' . $color)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><p><strong><?php echo esc_html((string) ($attributes['label'] ?? '')); ?></strong><?php if (! empty($attributes['showValue'])) : ?><span><?php echo esc_html((string) $value); ?>%</span><?php endif; ?></p><div class="msp-nexus-progress-meter__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr((string) $value); ?>" aria-label="<?php echo esc_attr((string) ($attributes['label'] ?? '')); ?>"><i></i></div></div>
