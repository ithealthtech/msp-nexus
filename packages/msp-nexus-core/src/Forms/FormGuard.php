<?php
/**
 * Shared checks for public lead forms: nonce, honeypot, per-visitor rate limit, recipient, redirects.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Forms;

final class FormGuard
{
    /** Keep a page that renders a nonce-bearing form out of page caches; the nonce expires within a day. */
    public static function no_cache(string $reason): void
    {
        if (! defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
        do_action('litespeed_control_set_nocache', $reason);
        if (! headers_sent()) {
            nocache_headers();
        }
    }

    /**
     * Verify the nonce, honeypot, and rate limit. Redirects with a status and exits on failure; a filled
     * honeypot is answered with a fake success so bots learn nothing.
     */
    public static function check(string $action, string $anchor, string $success_status = 'success'): string
    {
        $return = self::return_url();
        if (! isset($_POST['_msp_nexus_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['_msp_nexus_nonce'])), $action)) {
            self::redirect($return, 'security', $anchor);
        }
        if (! empty($_POST['company_website'])) {
            self::redirect($return, $success_status, $anchor);
        }
        $rate_key = 'msp_nexus_form_' . substr(hash('sha256', $action . '|' . (string) ($_SERVER['REMOTE_ADDR'] ?? '') . wp_salt('nonce')), 0, 32);
        if (get_transient($rate_key)) {
            self::redirect($return, 'rate', $anchor);
        }
        set_transient($rate_key, 1, MINUTE_IN_SECONDS);
        return $return;
    }

    public static function text(string $key): string
    {
        return sanitize_text_field(wp_unslash((string) ($_POST[$key] ?? '')));
    }

    public static function textarea(string $key): string
    {
        return sanitize_textarea_field(wp_unslash((string) ($_POST[$key] ?? '')));
    }

    /**
     * Only values from the allowed list survive.
     *
     * @param string[] $allowed
     * @return string[]
     */
    public static function choices(string $key, array $allowed): array
    {
        $raw = isset($_POST[$key]) ? (array) wp_unslash($_POST[$key]) : array();
        return array_values(array_intersect($allowed, array_map('sanitize_text_field', array_map('strval', $raw))));
    }

    /** @param string[] $allowed */
    public static function choice(string $key, array $allowed): string
    {
        $value = self::text($key);
        return in_array($value, $allowed, true) ? $value : '';
    }

    public static function recipient(): string
    {
        $settings = get_option('msp_nexus_settings', array());
        $recipient = is_array($settings) ? sanitize_email((string) ($settings['sales_email'] ?? '')) : '';
        return (string) apply_filters('msp_nexus_consultation_recipient', $recipient ?: get_option('admin_email'));
    }

    public static function return_url(): string
    {
        $requested = esc_url_raw(wp_unslash((string) ($_POST['return_url'] ?? '')));
        return wp_validate_redirect($requested, home_url('/'));
    }

    /** @param array<string, string> $extra */
    public static function redirect(string $url, string $status, string $anchor, array $extra = array()): void
    {
        wp_safe_redirect(add_query_arg(array_merge(array('msp_form' => sanitize_key($status)), $extra), $url) . '#' . $anchor);
        exit;
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function messages(string $success): array
    {
        return array(
            'success' => array('success', $success),
            'validation' => array('error', __('Complete every required field and provide a valid email address.', 'msp-nexus-core')),
            'rate' => array('error', __('Please wait a moment before submitting another request.', 'msp-nexus-core')),
            'delivery' => array('error', __('The request could not be delivered. Please call us or use the published support channel.', 'msp-nexus-core')),
            'security' => array('error', __('The form session expired. Refresh the page and try again.', 'msp-nexus-core')),
        );
    }

    public static function honeypot(): string
    {
        $id = 'msp-company-website-' . wp_unique_id();
        return '<div class="msp-nexus-form__honeypot" aria-hidden="true"><label for="' . esc_attr($id) . '">' . esc_html__('Leave this field empty', 'msp-nexus-core') . '</label><input id="' . esc_attr($id) . '" name="company_website" type="text" tabindex="-1" autocomplete="off"></div>';
    }
}
