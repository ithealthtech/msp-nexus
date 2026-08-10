<?php
/**
 * Accessible privacy-conscious consultation form processing.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Forms;

final class ConsultationHandler
{
    public const ACTION = 'msp_nexus_consultation';

    public function register_hooks(): void
    {
        add_action('admin_post_' . self::ACTION, array($this, 'submit'));
        add_action('admin_post_nopriv_' . self::ACTION, array($this, 'submit'));
    }

    public function submit(): void
    {
        $settings = get_option('msp_nexus_settings', array());
        if (is_array($settings) && array_key_exists('enable_native_form', $settings) && empty($settings['enable_native_form'])) {
            wp_die(esc_html__('The native consultation form is disabled. Use the published contact channel.', 'msp-nexus-core'), '', array('response' => 503));
        }
        $return = $this->return_url();
        if (! isset($_POST['_msp_nexus_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['_msp_nexus_nonce'])), self::ACTION)) {
            $this->redirect($return, 'security');
        }
        if (! empty($_POST['company_website'])) {
            $this->redirect($return, 'success');
        }
        $rate_key = 'msp_nexus_form_' . substr(hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? '') . wp_salt('nonce')), 0, 32);
        if (get_transient($rate_key)) {
            $this->redirect($return, 'rate');
        }
        set_transient($rate_key, 1, MINUTE_IN_SECONDS);

        $payload = array(
            'name' => sanitize_text_field(wp_unslash((string) ($_POST['name'] ?? ''))),
            'email' => sanitize_email(wp_unslash((string) ($_POST['email'] ?? ''))),
            'company' => sanitize_text_field(wp_unslash((string) ($_POST['company'] ?? ''))),
            'phone' => sanitize_text_field(wp_unslash((string) ($_POST['phone'] ?? ''))),
            'message' => sanitize_textarea_field(wp_unslash((string) ($_POST['message'] ?? ''))),
            'consent' => ! empty($_POST['consent']),
        );
        if ('' === $payload['name'] || ! is_email($payload['email']) || '' === $payload['message'] || ! $payload['consent']) {
            $this->redirect($return, 'validation');
        }
        if (true === apply_filters('msp_nexus_form_is_spam', false, $payload)) {
            do_action('msp_nexus_form_spam_rejected', $payload);
            $this->redirect($return, 'success');
        }

        $settings = get_option('msp_nexus_settings', array());
        $recipient = is_array($settings) ? sanitize_email((string) ($settings['sales_email'] ?? '')) : '';
        $recipient = (string) apply_filters('msp_nexus_consultation_recipient', $recipient ?: get_option('admin_email'));
        $subject = sprintf(__('Consultation request from %s', 'msp-nexus-core'), $payload['name']);
        $body = sprintf("Name: %s\nEmail: %s\nCompany: %s\nPhone: %s\n\nMessage:\n%s\n", $payload['name'], $payload['email'], $payload['company'], $payload['phone'], $payload['message']);
        $sent = wp_mail($recipient, $subject, $body, array('Reply-To: ' . $payload['name'] . ' <' . $payload['email'] . '>'));
        do_action('msp_nexus_consultation_processed', $payload, $sent);
        $this->redirect($return, $sent ? 'success' : 'delivery');
    }

    private function return_url(): string
    {
        $requested = esc_url_raw(wp_unslash((string) ($_POST['return_url'] ?? '')));
        return wp_validate_redirect($requested, home_url('/'));
    }

    private function redirect(string $url, string $status): void
    {
        wp_safe_redirect(add_query_arg('msp_form', sanitize_key($status), $url) . '#msp-consultation-form');
        exit;
    }
}
