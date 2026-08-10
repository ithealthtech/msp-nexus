<?php
/**
 * Redacted product diagnostics and narrowly scoped repair actions.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

final class Diagnostics
{
    private const EXPORT_ACTION = 'msp_nexus_export_diagnostics';
    private const REPAIR_ACTION = 'msp_nexus_repair';

    public function register_hooks(): void
    {
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_post_' . self::EXPORT_ACTION, array($this, 'export'));
        add_action('admin_post_' . self::REPAIR_ACTION, array($this, 'repair'));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('MSP Nexus diagnostics', 'msp-nexus-core'), __('Diagnostics', 'msp-nexus-core'), 'manage_options', 'msp-nexus-diagnostics', array($this, 'render'));
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        $checks = $this->checks();
        ?>
        <div class="wrap"><h1><?php esc_html_e('MSP Nexus diagnostics', 'msp-nexus-core'); ?></h1><p><?php esc_html_e('This report contains no license key, password, token, customer content, or visitor data.', 'msp-nexus-core'); ?></p>
        <table class="widefat striped"><thead><tr><th><?php esc_html_e('Check', 'msp-nexus-core'); ?></th><th><?php esc_html_e('Status', 'msp-nexus-core'); ?></th><th><?php esc_html_e('Detail', 'msp-nexus-core'); ?></th></tr></thead><tbody>
        <?php foreach ($checks as $check) : ?><tr><th scope="row"><?php echo esc_html($check['label']); ?></th><td><?php echo esc_html($check['status']); ?></td><td><code><?php echo esc_html($check['detail']); ?></code></td></tr><?php endforeach; ?>
        </tbody></table>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="<?php echo esc_attr(self::EXPORT_ACTION); ?>"><?php wp_nonce_field(self::EXPORT_ACTION); ?><?php submit_button(__('Download redacted diagnostics JSON', 'msp-nexus-core'), 'secondary'); ?></form>
        <h2><?php esc_html_e('Safe repair tools', 'msp-nexus-core'); ?></h2><p><?php esc_html_e('These actions regenerate derived state only. They do not delete posts, media, terms, users, or customer settings.', 'msp-nexus-core'); ?></p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="<?php echo esc_attr(self::REPAIR_ACTION); ?>"><?php wp_nonce_field(self::REPAIR_ACTION); ?><p><label><input type="checkbox" name="repairs[]" value="rewrite"> <?php esc_html_e('Flush rewrite rules once', 'msp-nexus-core'); ?></label></p><p><label><input type="checkbox" name="repairs[]" value="update_cache"> <?php esc_html_e('Clear MSP Nexus update-check cache', 'msp-nexus-core'); ?></label></p><?php submit_button(__('Run selected repairs', 'msp-nexus-core')); ?></form>
        <p><a href="<?php echo esc_url(admin_url('admin.php?page=msp-nexus-reset')); ?>"><?php esc_html_e('Open advanced demo reset (separate confirmation required)', 'msp-nexus-core'); ?></a></p></div>
        <?php
    }

    public function export(): void
    {
        $this->authorize(self::EXPORT_ACTION);
        $payload = array('schema' => 1, 'generated_at' => current_time('mysql', true), 'site_hash' => hash('sha256', strtolower(home_url('/'))), 'checks' => $this->checks(), 'import' => $this->redacted_import_state(), 'license' => $this->redacted_license_state(), 'recent_repairs' => array_slice((array) get_option('msp_nexus_repair_log', array()), -20));
        nocache_headers();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="msp-nexus-diagnostics-' . gmdate('Ymd-His') . '.json"');
        echo wp_json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }

    public function repair(): void
    {
        $this->authorize(self::REPAIR_ACTION);
        $requested = array_map('sanitize_key', (array) ($_POST['repairs'] ?? array()));
        $completed = array();
        if (in_array('rewrite', $requested, true)) {
            flush_rewrite_rules(false);
            $completed[] = 'rewrite';
        }
        if (in_array('update_cache', $requested, true)) {
            delete_site_transient('msp_nexus_update_msp-nexus');
            delete_site_transient('msp_nexus_update_msp-nexus-core');
            $completed[] = 'update_cache';
        }
        $log = (array) get_option('msp_nexus_repair_log', array());
        $log[] = array('actor' => get_current_user_id(), 'actions' => $completed, 'at' => current_time('mysql', true));
        update_option('msp_nexus_repair_log', array_slice($log, -100), false);
        wp_safe_redirect(admin_url('admin.php?page=msp-nexus-diagnostics&repaired=1'));
        exit;
    }

    /** @return array<int, array{label:string,status:string,detail:string}> */
    public function checks(): array
    {
        global $wpdb;
        $theme = wp_get_theme();
        $upload = wp_upload_dir(null, false);
        $cron_disabled = defined('DISABLE_WP_CRON') && DISABLE_WP_CRON;
        $license = get_site_option('msp_nexus_license_state', array());
        $settings = get_option('msp_nexus_settings', array());
        $import_state = (array) get_option('msp_nexus_import_state', array());
        $published_unverified = $this->published_unverified_count();
        $privacy_policy_url = get_privacy_policy_url();
        $integrations = array_filter(array(
            'WooCommerce' => class_exists('WooCommerce'),
            'Yoast SEO' => defined('WPSEO_VERSION'),
            'Rank Math' => defined('RANK_MATH_VERSION'),
            'WPML' => defined('ICL_SITEPRESS_VERSION'),
            'Polylang' => defined('POLYLANG_VERSION'),
            'Gravity Forms' => class_exists('GFForms'),
            'WPForms' => defined('WPFORMS_VERSION'),
            'Fluent Forms' => defined('FLUENTFORM'),
            'Contact Form 7' => defined('WPCF7_VERSION'),
        ));
        return array(
            $this->check(__('WordPress', 'msp-nexus-core'), version_compare(get_bloginfo('version'), '6.7', '>='), get_bloginfo('version')),
            $this->check(__('PHP', 'msp-nexus-core'), version_compare(PHP_VERSION, '7.4.33', '>='), PHP_VERSION),
            $this->check(__('Database', 'msp-nexus-core'), '' !== (string) $wpdb->db_version(), (string) $wpdb->db_version()),
            $this->check(__('Active theme', 'msp-nexus-core'), $theme->get_template() === 'msp-nexus', $theme->get('Name') . ' ' . $theme->get('Version'), 'notice'),
            $this->check(__('Block theme', 'msp-nexus-core'), wp_is_block_theme(), wp_is_block_theme() ? 'yes' : 'no', 'notice'),
            $this->check(__('HTTPS', 'msp-nexus-core'), is_ssl(), is_ssl() ? 'enabled' : 'not detected', 'notice'),
            $this->check(__('REST API', 'msp-nexus-core'), rest_url() !== '', wp_parse_url(rest_url(), PHP_URL_PATH) ?: 'available'),
            $this->check(__('Scheduled tasks', 'msp-nexus-core'), ! $cron_disabled, $cron_disabled ? 'WP-Cron disabled; confirm system cron' : 'WP-Cron enabled', 'notice'),
            $this->check(__('Content directory writable', 'msp-nexus-core'), wp_is_writable(WP_CONTENT_DIR), wp_is_writable(WP_CONTENT_DIR) ? 'yes' : 'no', 'notice'),
            $this->check(__('Upload directory', 'msp-nexus-core'), empty($upload['error']) && wp_is_writable($upload['basedir']), empty($upload['error']) ? 'writable' : (string) $upload['error'], 'notice'),
            $this->check(__('ZIP support', 'msp-nexus-core'), class_exists('ZipArchive'), class_exists('ZipArchive') ? 'available' : 'required for licensed updates', 'fail'),
            $this->check(__('Sodium signatures', 'msp-nexus-core'), function_exists('sodium_crypto_sign_verify_detached'), function_exists('sodium_crypto_sign_verify_detached') ? 'available' : 'required for licensed updates', 'fail'),
            $this->check(__('Image processing', 'msp-nexus-core'), extension_loaded('imagick') || extension_loaded('gd'), extension_loaded('imagick') ? 'Imagick' : (extension_loaded('gd') ? 'GD' : 'not available'), 'notice'),
            array('label' => __('Memory limit', 'msp-nexus-core'), 'status' => 'info', 'detail' => (string) ini_get('memory_limit')),
            array('label' => __('Execution time', 'msp-nexus-core'), 'status' => 'info', 'detail' => (string) ini_get('max_execution_time') . ' seconds'),
            array('label' => __('Multisite mode', 'msp-nexus-core'), 'status' => 'info', 'detail' => is_multisite() ? 'network' : 'single site'),
            $this->check(__('Plugin schema migration', 'msp-nexus-core'), 2 === (int) get_option('msp_nexus_core_schema_version', 0), 'version ' . (string) get_option('msp_nexus_core_schema_version', 0)),
            array('label' => __('Update channel', 'msp-nexus-core'), 'status' => 'info', 'detail' => is_array($settings) ? (string) ($settings['update_channel'] ?? 'stable') : 'stable'),
            array('label' => __('Automatic updates', 'msp-nexus-core'), 'status' => 'info', 'detail' => is_array($settings) && ! empty($settings['automatic_updates']) ? 'administrator enabled' : 'disabled'),
            array('label' => __('Detected integrations', 'msp-nexus-core'), 'status' => 'info', 'detail' => $integrations ? implode(', ', array_keys($integrations)) : 'none detected'),
            array('label' => __('Demo import', 'msp-nexus-core'), 'status' => 'info', 'detail' => sanitize_key((string) ($import_state['status'] ?? 'not_started'))),
            $this->check(__('Launch safety', 'msp-nexus-core'), 0 === $published_unverified, 0 === $published_unverified ? 'no unverified starter records are public' : $published_unverified . ' unverified starter records are published; rerun Setup or move them to Draft'),
            $this->check(__('Privacy policy', 'msp-nexus-core'), '' !== $privacy_policy_url, '' !== $privacy_policy_url ? (wp_parse_url($privacy_policy_url, PHP_URL_PATH) ?: 'configured') : 'not configured; publish a reviewed policy before collecting personal data', 'notice'),
            array('label' => __('Update history', 'msp-nexus-core'), 'status' => 'info', 'detail' => (string) count((array) get_site_option('msp_nexus_update_history', array())) . ' recorded actions'),
            array('label' => __('License service', 'msp-nexus-core'), 'status' => is_array($license) && ! empty($license['last_checked_at']) ? 'info' : 'notice', 'detail' => is_array($license) && ! empty($license['last_checked_at']) ? 'last checked ' . gmdate('c', (int) $license['last_checked_at']) : 'not activated or not checked'),
        );
    }

    /** @return array{label:string,status:string,detail:string} */
    private function check(string $label, bool $passed, string $detail, string $failure = 'fail'): array
    {
        return array('label' => $label, 'status' => $passed ? 'pass' : $failure, 'detail' => $detail);
    }

    private function authorize(string $action): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to run diagnostics.', 'msp-nexus-core'), '', array('response' => 403));
        }
        check_admin_referer($action);
    }

    private function published_unverified_count(): int
    {
        $ids = get_posts(array(
            'post_type' => 'any',
            'post_status' => 'publish',
            'meta_key' => '_msp_nexus_demo_key',
            'fields' => 'ids',
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ));
        $draft_only_prefixes = array('case-', 'testimonial-', 'team-', 'location-', 'event-', 'partner-', 'certification-');
        $draft_only_pages = array('page-careers', 'page-privacy', 'page-accessibility', 'page-cookies', 'page-legal');
        $reviewed_resources = array('resource-it-roadmap', 'resource-security-questions', 'resource-service-metrics');
        $count = 0;
        foreach ($ids as $post_id) {
            $key = (string) get_post_meta((int) $post_id, '_msp_nexus_demo_key', true);
            $requires_review = in_array($key, $draft_only_pages, true);
            foreach ($draft_only_prefixes as $prefix) {
                if (0 === strpos($key, $prefix)) {
                    $requires_review = true;
                    break;
                }
            }
            if (0 === strpos($key, 'resource-') && ! in_array($key, $reviewed_resources, true)) {
                $requires_review = true;
            }
            if ($requires_review) {
                ++$count;
            }
        }
        return $count;
    }

    /** @return array<string, mixed> */
    private function redacted_license_state(): array
    {
        $state = get_site_option('msp_nexus_license_state', array());
        return is_array($state) ? array_intersect_key($state, array_flip(array('status', 'plan', 'expires_at', 'grace_ends_at', 'last_checked_at', 'last_error'))) : array();
    }

    /** @return array<string, mixed> */
    private function redacted_import_state(): array
    {
        $state = get_option('msp_nexus_import_state', array());
        return is_array($state) ? array_intersect_key($state, array_flip(array('status', 'manifest_version', 'cursor', 'total', 'created', 'updated', 'failed', 'started_at', 'completed_at', 'cancelled_at'))) : array();
    }
}
