<?php
/** @var array<string, mixed> $attributes */
$value = (float) ($attributes['value'] ?? 100);
$decimals = min(3, max(0, (int) ($attributes['decimals'] ?? 0)));
$config = array('value' => $value, 'decimals' => $decimals, 'duration' => min(5000, max(0, (int) ($attributes['duration'] ?? 1200))));
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-metric-counter', 'data-nexus-counter' => wp_json_encode($config))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><p class="msp-nexus-metric-counter__value"><span><?php echo esc_html((string) ($attributes['prefix'] ?? '')); ?></span><strong data-counter-value><?php echo esc_html(number_format_i18n($value, $decimals)); ?></strong><span><?php echo esc_html((string) ($attributes['suffix'] ?? '')); ?></span></p><p class="msp-nexus-metric-counter__label"><?php echo esc_html((string) ($attributes['label'] ?? '')); ?></p></div>
