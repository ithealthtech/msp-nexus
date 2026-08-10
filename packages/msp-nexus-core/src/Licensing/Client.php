<?php
/**
 * WordPress-side entitlement cache and privacy boundary.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Licensing;

final class Client
{
    private const STATE_OPTION = 'msp_nexus_license_state';

    public function register_hooks(): void
    {
        add_action('msp_nexus_validate_license', array($this, 'refresh'));
        add_action('init', array($this, 'schedule'));
        add_action('admin_notices', array($this, 'notice'));
    }

    public function schedule(): void
    {
        if (! wp_next_scheduled('msp_nexus_validate_license')) {
            wp_schedule_event(time() + HOUR_IN_SECONDS, 'twicedaily', 'msp_nexus_validate_license');
        }
    }

    public function refresh(): void
    {
        $state = get_site_option(self::STATE_OPTION, array());
        if (! is_array($state) || empty($state['license_id'])) {
            return;
        }

        $endpoint = apply_filters('msp_nexus_license_api_url', 'https://licenses.example.com/v1/licenses/heartbeat');
        $response = wp_safe_remote_post(
            $endpoint,
            array(
                'timeout' => 8,
                'headers' => array('Accept' => 'application/json', 'Content-Type' => 'application/json'),
                'body' => wp_json_encode(array('license_id' => sanitize_text_field((string) $state['license_id']), 'instance_id' => sanitize_text_field((string) ($state['instance_id'] ?? '')), 'site_url' => home_url('/'))),
            )
        );

        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            update_site_option(self::STATE_OPTION, array_merge($state, array('last_checked_at' => time(), 'last_error' => 'validation_unavailable')));
            return;
        }

        $payload = json_decode(wp_remote_retrieve_body($response), true);
        if (! is_array($payload)) {
            return;
        }
        $entitlement = (new EntitlementVerifier())->verified($payload);
        if (! is_array($entitlement)) {
            update_site_option(self::STATE_OPTION, array_merge($state, array('last_checked_at' => time(), 'last_error' => 'invalid_entitlement_signature')));
            return;
        }

        update_site_option(
            self::STATE_OPTION,
            array(
                'license_id'      => sanitize_text_field((string) $state['license_id']),
                'instance_id'     => sanitize_text_field((string) ($state['instance_id'] ?? '')),
                'status'          => sanitize_key((string) ($entitlement['status'] ?? 'unknown')),
                'plan'            => sanitize_key((string) ($entitlement['plan'] ?? '')),
                'expires_at'      => sanitize_text_field((string) ($entitlement['expires_at'] ?? '')),
                'grace_ends_at'   => sanitize_text_field((string) ($entitlement['grace_ends_at'] ?? '')),
                'signed_entitlement' => $entitlement,
                'entitlement_signature' => sanitize_text_field((string) $payload['entitlement_signature']),
                'signed_cache_expires_at' => sanitize_text_field((string) ($entitlement['cache_expires_at'] ?? '')),
                'activation_id' => sanitize_text_field((string) ($entitlement['activation_id'] ?? '')),
                'activation_limit' => (int) ($entitlement['activation_limit'] ?? 0),
                'active_production_activations' => (int) ($entitlement['active_production_activations'] ?? 0),
                'active_staging_activations' => (int) ($entitlement['active_staging_activations'] ?? 0),
                'is_staging' => ! empty($entitlement['is_staging']),
                'allow_multisite' => ! empty($entitlement['allow_multisite']),
                'last_checked_at' => time(),
            )
        );
    }

    public function notice(): void
    {
        if (! current_user_can('update_plugins')) {
            return;
        }
        $state = get_site_option(self::STATE_OPTION, array());
        if (! is_array($state) || empty($state['license_id'])) {
            return;
        }
        $cache_expired = strtotime((string) ($state['signed_cache_expires_at'] ?? '')) < time();
        if ($cache_expired || ! empty($state['last_error'])) {
            echo '<div class="notice notice-warning"><p>' . esc_html__('MSP Nexus could not confirm a current signed entitlement. The public site remains unchanged; commercial updates and cloud downloads stay unavailable until validation succeeds.', 'msp-nexus-core') . '</p></div>';
        } elseif ('grace' === ($state['status'] ?? '')) {
            echo '<div class="notice notice-warning"><p>' . esc_html(sprintf(__('MSP Nexus is in its offline or renewal grace period until %s. Public content remains unchanged.', 'msp-nexus-core'), (string) ($state['grace_ends_at'] ?? ''))) . '</p></div>';
        }
    }
}
