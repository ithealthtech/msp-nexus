<?php
/**
 * Site-wide design token overrides managed from Nexus settings.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Studio;

final class DesignTokens
{
    public function register_hooks(): void
    {
        add_action('wp_enqueue_scripts', array($this, 'styles'), 20);
        add_action('enqueue_block_editor_assets', array($this, 'styles'), 20);
    }

    public function styles(): void
    {
        $settings = get_option('msp_nexus_settings', array());
        if (! is_array($settings)) {
            return;
        }
        $accent = sanitize_hex_color((string) ($settings['accent_color'] ?? ''));
        $surface = sanitize_hex_color((string) ($settings['surface_color'] ?? '')) ?: '#121124';
        $text = sanitize_hex_color((string) ($settings['text_color'] ?? '')) ?: '#ffffff';
        $muted = sanitize_hex_color((string) ($settings['muted_color'] ?? '')) ?: '#d9d9e5';
        $focus = sanitize_hex_color((string) ($settings['focus_color'] ?? '')) ?: '#69bff5';
        $radius = min(48, max(0, (int) ($settings['button_radius'] ?? 3)));
        $button_x = min(64, max(8, (int) ($settings['button_padding_x'] ?? 24)));
        $button_y = min(32, max(6, (int) ($settings['button_padding_y'] ?? 14)));
        $card_radius = min(64, max(0, (int) ($settings['card_radius'] ?? 12)));
        $card_border = min(8, max(0, (int) ($settings['card_border'] ?? 1)));
        $shadow = min(5, max(0, (int) ($settings['card_shadow'] ?? 2)));
        $scale = min(125, max(85, (int) ($settings['type_scale'] ?? 100)));
        $heading_weight = min(900, max(300, (int) ($settings['heading_weight'] ?? 600)));
        $line_height = min(200, max(120, (int) ($settings['line_height'] ?? 165))) / 100;
        $content_width = min(1200, max(480, (int) ($settings['content_width'] ?? 704)));
        $wide_width = min(1920, max(720, (int) ($settings['wide_width'] ?? 1216)));
        $section_spacing = min(160, max(24, (int) ($settings['section_spacing'] ?? 80)));
        $shadows = array('none', '0 4px 12px rgba(5,11,22,.06)', '0 10px 30px rgba(5,11,22,.10)', '0 18px 48px rgba(5,11,22,.14)', '0 24px 64px rgba(5,11,22,.18)', '0 32px 80px rgba(5,11,22,.24)');
        $css = ':root{--msp-nexus-accent:' . ($accent ?: '#69bff5') . ';--msp-nexus-surface:' . $surface . ';--msp-nexus-text:' . $text . ';--msp-nexus-muted:' . $muted . ';--msp-nexus-focus:' . $focus . ';--msp-nexus-button-radius:' . $radius . 'px;--msp-nexus-card-radius:' . $card_radius . 'px;--msp-nexus-card-border:' . $card_border . 'px;--msp-nexus-card-shadow:' . $shadows[$shadow] . ';--msp-nexus-type-scale:' . ($scale / 100) . ';--msp-nexus-content-width:' . $content_width . 'px;--msp-nexus-wide-width:' . $wide_width . 'px;--msp-nexus-section-spacing:' . $section_spacing . 'px}';
        // Force a site-wide button radius only when it was changed from the default; otherwise
        // each flagship design keeps its own button shape (pills, rounded, square).
        $css .= (3 !== $radius ? '.wp-element-button,.wp-block-button__link{border-radius:var(--msp-nexus-button-radius)!important}' : '');
        $css .= '.wp-element-button,.wp-block-button__link{padding:' . $button_y . 'px ' . $button_x . 'px}.msp-nexus-card,.msp-nexus-orbit-card,.msp-nexus-icon-card{border-width:var(--msp-nexus-card-border);border-radius:var(--msp-nexus-card-radius);box-shadow:var(--msp-nexus-card-shadow)}html{font-size:calc(100% * var(--msp-nexus-type-scale))}body{line-height:' . $line_height . '}h1,h2,h3,h4,h5,h6{font-weight:' . $heading_weight . '}.msp-nexus-element{padding-block:var(--msp-nexus-section-spacing)}:where(a,button,input,select,textarea):focus-visible{outline:3px solid var(--msp-nexus-focus);outline-offset:3px}' . (! array_key_exists('link_underline', $settings) || ! empty($settings['link_underline']) ? ':where(p,li) a{text-decoration:underline}' : '');
        wp_add_inline_style('msp-nexus-studio', $css);
    }
}
