<?php
/**
 * WP-CLI license and diagnostics commands.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Cli;

use MspNexusCore\Admin\Diagnostics;
use MspNexusCore\Licensing\Client;
use MspNexusCore\Licensing\EntitlementVerifier;

final class Commands
{
    /** Show redacted product and entitlement status. */
    public function status(): void
    {
        $state = get_site_option('msp_nexus_license_state', array());
        $rows = array(
            array('field' => 'core_version', 'value' => MSP_NEXUS_CORE_VERSION),
            array('field' => 'license_status', 'value' => is_array($state) ? (string) ($state['status'] ?? 'inactive') : 'inactive'),
            array('field' => 'plan', 'value' => is_array($state) ? (string) ($state['plan'] ?? '') : ''),
            array('field' => 'expires_at', 'value' => is_array($state) ? (string) ($state['expires_at'] ?? '') : ''),
            array('field' => 'last_checked_at', 'value' => is_array($state) && ! empty($state['last_checked_at']) ? gmdate('c', (int) $state['last_checked_at']) : 'never'),
        );
        \WP_CLI\Utils\format_items('table', $rows, array('field', 'value'));
    }

    /** Refresh cached entitlement state from the configured service. */
    public function refresh(): void
    {
        (new Client())->refresh();
        \WP_CLI::success('Entitlement refresh completed. Run `wp msp-nexus status` for the redacted result.');
    }

    /** Print redacted compatibility diagnostics. */
    public function diagnostics(): void
    {
        \WP_CLI\Utils\format_items('table', (new Diagnostics())->checks(), array('label', 'status', 'detail'));
    }

    /**
     * Activate this installation. The raw key is sent once and not stored.
     *
     * ## OPTIONS
     *
     * <license-key>
     * : Customer license key. Prefer a protected environment variable to avoid shell history.
     */
    public function activate(array $args): void
    {
        $key = sanitize_text_field((string) ($args[0] ?? ''));
        if ('' === $key) {
            \WP_CLI::error('A license key is required.');
        }
        $instance = wp_generate_uuid4();
        $endpoint = apply_filters('msp_nexus_license_activation_url', 'https://licenses.example.com/v1/licenses/activate');
        $response = wp_safe_remote_post($endpoint, array('timeout' => 12, 'headers' => array('Accept' => 'application/json', 'Content-Type' => 'application/json'), 'body' => wp_json_encode(array('license_key' => $key, 'site_url' => home_url('/'), 'instance_id' => $instance, 'is_multisite' => is_multisite()))));
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            \WP_CLI::error('Activation was not accepted. The key was not stored.');
        }
        $payload = json_decode(wp_remote_retrieve_body($response), true);
        $entitlement = is_array($payload) ? (new EntitlementVerifier())->verified($payload) : null;
        if (! is_array($payload) || empty($payload['license_id']) || ! is_array($entitlement)) {
            \WP_CLI::error('The activation response was invalid.');
        }
        update_site_option('msp_nexus_license_state', array('license_id' => sanitize_text_field((string) $payload['license_id']), 'instance_id' => $instance, 'status' => sanitize_key((string) ($entitlement['status'] ?? 'unknown')), 'plan' => sanitize_key((string) ($entitlement['plan'] ?? '')), 'expires_at' => sanitize_text_field((string) ($entitlement['expires_at'] ?? '')), 'grace_ends_at' => sanitize_text_field((string) ($entitlement['grace_ends_at'] ?? '')), 'signed_entitlement' => $entitlement, 'entitlement_signature' => sanitize_text_field((string) $payload['entitlement_signature']), 'signed_cache_expires_at' => sanitize_text_field((string) ($entitlement['cache_expires_at'] ?? '')), 'last_checked_at' => time()));
        \WP_CLI::success('License activated. The raw key was not stored.');
    }

    /** Deactivate this installation without deleting public content. */
    public function deactivate(): void
    {
        $state = get_site_option('msp_nexus_license_state', array());
        if (! is_array($state) || empty($state['license_id']) || empty($state['instance_id'])) {
            \WP_CLI::success('This installation is already inactive.');
            return;
        }
        $endpoint = apply_filters('msp_nexus_license_deactivation_url', 'https://licenses.example.com/v1/licenses/deactivate');
        $response = wp_safe_remote_post($endpoint, array('timeout' => 12, 'headers' => array('Accept' => 'application/json', 'Content-Type' => 'application/json'), 'body' => wp_json_encode(array('license_id' => $state['license_id'], 'site_url' => home_url('/'), 'instance_id' => $state['instance_id']))));
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            \WP_CLI::error('Remote deactivation failed; local entitlement was preserved.');
        }
        delete_site_option('msp_nexus_license_state');
        \WP_CLI::success('License deactivated. Public content is unchanged.');
    }
}
