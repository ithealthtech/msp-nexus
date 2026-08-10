<?php
/**
 * Purchased-content gate.
 *
 * @var array<string, mixed> $attributes
 * @var string $content
 */
if (is_admin()) {
    echo '<div ' . get_block_wrapper_attributes(array('class' => 'msp-nexus-purchase-gate is-editor')) . '>' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    return;
}
if (! defined('DONOTCACHEPAGE')) {
    define('DONOTCACHEPAGE', true);
}
$ids = array_values(array_filter(array_map('absint', explode(',', (string) ($attributes['productIds'] ?? '')))));
$fallback = sanitize_text_field((string) ($attributes['fallback'] ?? __('This content is available after purchase.', 'msp-nexus-core')));
$allowed = false;
if (class_exists('WooCommerce') && is_user_logged_in() && $ids) {
    $user = wp_get_current_user();
    $purchases = array_map(static function (int $product_id) use ($user): bool {
        return wc_customer_bought_product($user->user_email, $user->ID, $product_id);
    }, $ids);
    $allowed = 'all' === ($attributes['match'] ?? 'any') ? ! in_array(false, $purchases, true) : in_array(true, $purchases, true);
}
if ($allowed) {
    echo '<div ' . get_block_wrapper_attributes(array('class' => 'msp-nexus-purchase-gate is-unlocked')) . '>' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    return;
}
$login = class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : wp_login_url((string) get_permalink());
echo '<div ' . get_block_wrapper_attributes(array('class' => 'msp-nexus-purchase-gate is-locked')) . '><p>' . esc_html($fallback) . '</p><p><a class="wp-element-button" href="' . esc_url($login) . '">' . esc_html__('Sign in to verify access', 'msp-nexus-core') . '</a></p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
