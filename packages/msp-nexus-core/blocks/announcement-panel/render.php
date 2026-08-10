<?php
/**
 * Accessible announcement panel render callback.
 *
 * @var array<string, mixed> $attributes
 */
$mode = 'modal' === ($attributes['mode'] ?? 'banner') ? 'modal' : 'banner';
$heading = sanitize_text_field((string) ($attributes['heading'] ?? ''));
$body = sanitize_textarea_field((string) ($attributes['body'] ?? ''));
$button_label = sanitize_text_field((string) ($attributes['buttonLabel'] ?? ''));
$button_url = esc_url((string) ($attributes['buttonUrl'] ?? ''));
$instance = substr(hash('sha256', $heading . '|' . $button_url . '|' . $mode), 0, 16);
$config = array(
    'delay' => min(60, max(0, (int) ($attributes['delaySeconds'] ?? 4))),
    'frequency' => min(365, max(0, (int) ($attributes['frequencyDays'] ?? 14))),
    'path' => sanitize_text_field((string) ($attributes['includePath'] ?? '/')),
    'excludedReferrer' => sanitize_text_field((string) ($attributes['excludedReferrer'] ?? '')),
    'requireConsent' => ! empty($attributes['requireConsent']),
    'consentKey' => sanitize_key((string) ($attributes['consentKey'] ?? 'msp_consent_marketing')),
    'storageKey' => 'msp_nexus_announcement_' . $instance,
);
$content = '<h2>' . esc_html($heading) . '</h2><p>' . esc_html($body) . '</p>';
if ('' !== $button_label && '' !== $button_url) {
    $content .= '<p><a class="wp-element-button" href="' . $button_url . '">' . esc_html($button_label) . '</a></p>';
}
$close = '<button class="msp-nexus-announcement__close" type="button" data-msp-announcement-close aria-label="' . esc_attr__('Dismiss announcement', 'msp-nexus-core') . '">×</button>';
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-announcement is-' . $mode, 'data-msp-announcement' => $mode, 'data-config' => wp_json_encode($config))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if ('modal' === $mode) : ?>
        <dialog class="msp-nexus-announcement__dialog" aria-labelledby="msp-announcement-<?php echo esc_attr($instance); ?>-title">
            <?php echo $close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <div id="msp-announcement-<?php echo esc_attr($instance); ?>-title"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        </dialog>
        <noscript><aside class="msp-nexus-announcement__fallback"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></aside></noscript>
    <?php else : ?>
        <aside class="msp-nexus-announcement__banner" aria-label="<?php esc_attr_e('Announcement', 'msp-nexus-core'); ?>"><?php echo $close . $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></aside>
    <?php endif; ?>
</div>
