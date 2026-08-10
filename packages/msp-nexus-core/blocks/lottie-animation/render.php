<?php
/** @var array<string, mixed> $attributes */
$url = esc_url((string) ($attributes['jsonUrl'] ?? ''));
$label = sanitize_text_field((string) ($attributes['label'] ?? __('Animated illustration', 'msp-nexus-core')));
$config = array(
    'url' => $url,
    'loop' => ! empty($attributes['loop']),
    'autoPlay' => ! empty($attributes['autoPlay']),
    'playOnHover' => ! empty($attributes['playOnHover']),
    'startWhenVisible' => ! empty($attributes['startWhenVisible']),
    'speed' => min(4, max(0.1, (float) ($attributes['speed'] ?? 1))),
);
if ('' === $url) {
    if (is_admin()) {
        echo '<p ' . get_block_wrapper_attributes(array('class' => 'msp-nexus-lottie is-placeholder')) . '>' . esc_html__('Choose a Lottie JSON URL.', 'msp-nexus-core') . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    return;
}
echo '<div ' . get_block_wrapper_attributes(array('class' => 'msp-nexus-lottie', 'role' => 'img', 'aria-label' => $label, 'data-lottie-config' => wp_json_encode($config))) . '><noscript><p>' . esc_html($label) . '</p></noscript></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
