<?php
/**
 * Signed commercial update integration.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Licensing;

final class UpdateClient
{
    private const HISTORY = 'msp_nexus_update_history';

    public function register_hooks(): void
    {
        add_filter('pre_set_site_transient_update_plugins', array($this, 'plugin_update'));
        add_filter('pre_set_site_transient_update_themes', array($this, 'theme_update'));
        add_action('upgrader_process_complete', array($this, 'record_update'), 10, 2);
        add_filter('upgrader_pre_download', array($this, 'verify_download'), 10, 4);
        add_filter('upgrader_pre_install', array($this, 'preflight'), 10, 2);
        add_filter('auto_update_plugin', array($this, 'allow_plugin_auto_update'), 10, 2);
        add_filter('auto_update_theme', array($this, 'allow_theme_auto_update'), 10, 2);
        add_action('admin_post_msp_nexus_rollback', array($this, 'rollback'));
    }

    /** @param mixed $transient
     *  @return mixed
     */
    public function plugin_update($transient)
    {
        if (! is_object($transient) || ! isset($transient->checked)) {
            return $transient;
        }
        $result = $this->check('msp-nexus-core');
        if (! $result) {
            return $transient;
        }
        $slug = 'msp-nexus-core/msp-nexus-core.php';
        if (version_compare(MSP_NEXUS_CORE_VERSION, (string) $result['version'], '<')) {
            $transient->response[$slug] = (object) array('slug' => 'msp-nexus-core', 'plugin' => $slug, 'new_version' => $result['version'], 'package' => $result['package_url'], 'url' => $result['changelog_url'], 'requires_php' => $result['requires_php'], 'tested' => $result['tested_wordpress']);
        }
        return $transient;
    }

    /** @param mixed $transient
     *  @return mixed
     */
    public function theme_update($transient)
    {
        if (! is_object($transient) || ! isset($transient->checked['msp-nexus'])) {
            return $transient;
        }
        $result = $this->check('msp-nexus');
        if ($result && version_compare((string) $transient->checked['msp-nexus'], (string) $result['version'], '<')) {
            $transient->response['msp-nexus'] = array('theme' => 'msp-nexus', 'new_version' => $result['version'], 'package' => $result['package_url'], 'url' => $result['changelog_url'], 'requires' => $result['requires_wordpress'], 'requires_php' => $result['requires_php']);
        }
        return $transient;
    }

    /** @return array<string, mixed>|null */
    private function check(string $product): ?array
    {
        $state = get_site_option('msp_nexus_license_state', array());
        if (! is_array($state) || empty($state['license_id']) || empty($state['instance_id'])) {
            return null;
        }
        $cache_key = 'msp_nexus_update_' . sanitize_key($product);
        $cached = get_site_transient($cache_key);
        if (is_array($cached)) {
            return $cached;
        }
        $endpoint = apply_filters('msp_nexus_license_api_url', 'https://licenses.example.com/v1/updates/check');
        $settings = get_option('msp_nexus_settings', array());
        $channel = is_array($settings) ? sanitize_key((string) ($settings['update_channel'] ?? 'stable')) : 'stable';
        if (! in_array($channel, array('stable', 'beta', 'development'), true)) {
            $channel = 'stable';
        }
        $response = wp_safe_remote_post($endpoint, array('timeout' => 8, 'headers' => array('Accept' => 'application/json', 'Content-Type' => 'application/json'), 'body' => wp_json_encode(array('license_id' => $state['license_id'], 'instance_id' => $state['instance_id'], 'site_url' => home_url('/'), 'product' => $product, 'channel' => $channel))));
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            return null;
        }
        $payload = json_decode(wp_remote_retrieve_body($response), true);
        if (! is_array($payload) || ! is_array($payload['manifest'] ?? null) || empty($payload['signature'])) {
            return null;
        }
        $default_key = defined('MSP_NEXUS_MANIFEST_PUBLIC_KEY') ? (string) constant('MSP_NEXUS_MANIFEST_PUBLIC_KEY') : '';
        $public_key = (string) apply_filters('msp_nexus_manifest_public_key', $default_key);
        if (! (new ManifestVerifier())->verify($payload['manifest'], (string) $payload['signature'], $public_key)) {
            return null;
        }
        if (($payload['manifest']['product'] ?? '') !== $product || strtotime((string) ($payload['manifest']['expires_at'] ?? '')) < time()) {
            return null;
        }
        set_site_transient($cache_key, $payload['manifest'], 5 * MINUTE_IN_SECONDS);
        return $payload['manifest'];
    }

    /** @param mixed $update @param mixed $item @return mixed */
    public function allow_plugin_auto_update($update, $item)
    {
        $plugin = is_object($item) ? (string) ($item->plugin ?? '') : '';
        return 'msp-nexus-core/msp-nexus-core.php' === $plugin && $this->automatic_updates_enabled() ? true : $update;
    }

    /** @param mixed $update @param mixed $item @return mixed */
    public function allow_theme_auto_update($update, $item)
    {
        $theme = is_object($item) ? (string) ($item->theme ?? '') : '';
        return 'msp-nexus' === $theme && $this->automatic_updates_enabled() ? true : $update;
    }

    private function automatic_updates_enabled(): bool
    {
        $settings = get_option('msp_nexus_settings', array());
        $state = get_site_option('msp_nexus_license_state', array());
        return is_array($settings) && ! empty($settings['automatic_updates']) && is_array($state) && in_array((string) ($state['status'] ?? ''), array('active', 'grace'), true);
    }

    /** @param array<string, mixed> $options */
    public function record_update($upgrader, array $options): void
    {
        unset($upgrader);
        if ('update' !== ($options['action'] ?? '') || ! in_array(($options['type'] ?? ''), array('plugin', 'theme'), true)) {
            return;
        }
        $history = get_site_option(self::HISTORY, array());
        if (! is_array($history)) {
            $history = array();
        }
        array_unshift($history, array('type' => sanitize_key((string) $options['type']), 'items' => array_map('sanitize_text_field', (array) ($options['plugins'] ?? $options['themes'] ?? array())), 'completed_at' => current_time('mysql', true), 'user_id' => get_current_user_id()));
        update_site_option(self::HISTORY, array_slice($history, 0, 25));
        delete_site_transient('msp_nexus_update_msp-nexus');
        delete_site_transient('msp_nexus_update_msp-nexus-core');
    }

    /**
     * Fail early for known MSP Nexus packages when the environment cannot
     * safely verify or extract the release.
     *
     * @param mixed $response Existing response.
     * @param array<string, mixed> $hook_extra Update context.
     * @return mixed
     */
    public function preflight($response, array $hook_extra)
    {
        if (is_wp_error($response)) {
            return $response;
        }
        $targets = array_merge((array) ($hook_extra['plugins'] ?? array()), (array) ($hook_extra['themes'] ?? array()));
        $is_nexus = in_array('msp-nexus-core/msp-nexus-core.php', $targets, true) || in_array('msp-nexus', $targets, true)
            || 'msp-nexus-core/msp-nexus-core.php' === ($hook_extra['plugin'] ?? '') || 'msp-nexus' === ($hook_extra['theme'] ?? '');
        if (! $is_nexus) {
            return $response;
        }
        if (version_compare(PHP_VERSION, '7.4.33', '<') || version_compare(get_bloginfo('version'), '6.7', '<')) {
            return new \WP_Error('msp_nexus_incompatible_runtime', __('MSP Nexus requires WordPress 6.7 or newer and PHP 7.4.33 or newer.', 'msp-nexus-core'));
        }
        if (! class_exists('ZipArchive') || ! function_exists('sodium_crypto_sign_verify_detached')) {
            return new \WP_Error('msp_nexus_update_extensions', __('Verified MSP Nexus updates require the PHP ZIP and Sodium extensions.', 'msp-nexus-core'));
        }
        $free = function_exists('disk_free_space') ? disk_free_space(WP_CONTENT_DIR) : false;
        if (is_numeric($free) && (float) $free < 50 * MB_IN_BYTES) {
            return new \WP_Error('msp_nexus_update_disk_space', __('At least 50 MB of free content-directory space is required before updating MSP Nexus.', 'msp-nexus-core'));
        }
        update_site_option('msp_nexus_last_update_preflight', array('checked_at' => current_time('mysql', true), 'user_id' => get_current_user_id(), 'backup_guidance_acknowledged' => false));
        return $response;
    }

    /**
     * Download a known MSP Nexus package and verify its signed hash and identity
     * before WordPress extracts it.
     *
     * @param mixed  $reply Existing result.
     * @param string $package Package URL.
     * @param mixed  $upgrader Upgrader instance.
     * @param array<string, mixed> $hook_extra Update context.
     * @return mixed
     */
    public function verify_download($reply, string $package, $upgrader, array $hook_extra)
    {
        unset($upgrader, $hook_extra);
        if (false !== $reply) {
            return $reply;
        }
        $manifest = null;
        foreach (array('msp-nexus', 'msp-nexus-core') as $product) {
            $candidate = get_site_transient('msp_nexus_update_' . $product);
            if (is_array($candidate) && (hash_equals((string) ($candidate['package_url'] ?? ''), $package) || hash_equals((string) ($candidate['rollback_package_url'] ?? ''), $package))) {
                $manifest = $candidate;
                break;
            }
        }
        if (! is_array($manifest)) {
            return false;
        }
        $file = download_url($package, 60);
        if (is_wp_error($file)) {
            return $file;
        }
        $is_rollback = hash_equals((string) ($manifest['rollback_package_url'] ?? ''), $package);
        $expected = strtolower((string) ($is_rollback ? ($manifest['rollback_package_sha256'] ?? '') : ($manifest['package_sha256'] ?? '')));
        $actual = hash_file('sha256', $file);
        if (! is_string($actual) || ! preg_match('/^[a-f0-9]{64}$/', $expected) || ! hash_equals($expected, strtolower($actual))) {
            wp_delete_file($file);
            return new \WP_Error('msp_nexus_hash_mismatch', __('The MSP Nexus update package failed cryptographic hash verification.', 'msp-nexus-core'));
        }
        if (! $this->archive_identity_is_valid($file, (string) ($manifest['product'] ?? ''))) {
            wp_delete_file($file);
            return new \WP_Error('msp_nexus_identity_mismatch', __('The MSP Nexus update package identity is invalid.', 'msp-nexus-core'));
        }
        return $file;
    }

    public function rollback(): void
    {
        $product = sanitize_key(wp_unslash((string) ($_POST['product'] ?? '')));
        if (! in_array($product, array('msp-nexus', 'msp-nexus-core'), true)) {
            wp_die(esc_html__('Invalid rollback product.', 'msp-nexus-core'), '', array('response' => 400));
        }
        $capability = 'msp-nexus' === $product ? 'update_themes' : 'update_plugins';
        if (! current_user_can($capability)) {
            wp_die(esc_html__('You are not allowed to roll back this product.', 'msp-nexus-core'), '', array('response' => 403));
        }
        check_admin_referer('msp_nexus_rollback_' . $product);
        $manifest = get_site_transient('msp_nexus_update_' . $product);
        if (! is_array($manifest) || empty($manifest['rollback_package_url']) || empty($manifest['rollback_package_sha256']) || ! class_exists('ZipArchive') || ! function_exists('sodium_crypto_sign_verify_detached')) {
            wp_die(esc_html__('A verified rollback package or required cryptographic extension is unavailable.', 'msp-nexus-core'), '', array('response' => 409));
        }

        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        $skin = new \Automatic_Upgrader_Skin();
        if ('msp-nexus' === $product) {
            $upgrader = new \Theme_Upgrader($skin);
        } else {
            $upgrader = new \Plugin_Upgrader($skin);
        }
        $result = $upgrader->install((string) $manifest['rollback_package_url'], array('overwrite_package' => true, 'clear_update_cache' => true));
        $history = get_site_option(self::HISTORY, array());
        if (! is_array($history)) {
            $history = array();
        }
        array_unshift($history, array('type' => 'rollback', 'items' => array($product), 'target_version' => sanitize_text_field((string) ($manifest['rollback_version'] ?? '')), 'completed_at' => current_time('mysql', true), 'user_id' => get_current_user_id(), 'result' => is_wp_error($result) || ! $result ? 'failed' : 'success'));
        update_site_option(self::HISTORY, array_slice($history, 0, 25));
        if (is_wp_error($result) || ! $result) {
            wp_die(esc_html__('Rollback failed. Review filesystem access, the update log, and the previous verified backup.', 'msp-nexus-core'), '', array('response' => 500));
        }
        delete_site_transient('msp_nexus_update_' . $product);
        wp_safe_redirect(admin_url('admin.php?page=msp-nexus-license&rollback=success'));
        exit;
    }

    private function archive_identity_is_valid(string $file, string $product): bool
    {
        if (! class_exists('ZipArchive')) {
            return false;
        }
        $zip = new \ZipArchive();
        if (true !== $zip->open($file)) {
            return false;
        }
        $required = 'msp-nexus' === $product ? 'msp-nexus/style.css' : 'msp-nexus-core/msp-nexus-core.php';
        $valid = false !== $zip->locateName($required, \ZipArchive::FL_NOCASE);
        $zip->close();
        return $valid;
    }
}
