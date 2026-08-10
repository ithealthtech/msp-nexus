<?php
/**
 * Commercial entitlement administration.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

final class LicenseScreen
{
    private const STATE = 'msp_nexus_license_state';
    private const ACTIVATE = 'msp_nexus_activate_license';
    private const DEACTIVATE = 'msp_nexus_deactivate_license';

    public function register_hooks(): void
    {
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_post_' . self::ACTIVATE, array($this, 'activate'));
        add_action('admin_post_' . self::DEACTIVATE, array($this, 'deactivate'));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('MSP Nexus license', 'msp-nexus-core'), __('License & updates', 'msp-nexus-core'), 'update_plugins', 'msp-nexus-license', array($this, 'render'));
    }

    public function render(): void
    {
        if (! current_user_can('update_plugins')) {
            return;
        }
        $state = get_site_option(self::STATE, array());
        $history = get_site_option('msp_nexus_update_history', array());
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('License & updates', 'msp-nexus-core'); ?></h1>
            <p><?php esc_html_e('A license controls commercial updates, cloud libraries, and support. It never controls whether your public content renders.', 'msp-nexus-core'); ?></p>
            <?php if (! empty($_GET['msp_notice'])) : ?><div class="notice notice-info inline"><p><?php echo esc_html(sanitize_text_field(wp_unslash((string) $_GET['msp_notice']))); ?></p></div><?php endif; ?>
            <?php if (is_array($state) && ! empty($state['license_id'])) : ?>
                <table class="widefat striped"><tbody>
                    <tr><th><?php esc_html_e('Status', 'msp-nexus-core'); ?></th><td><?php echo esc_html((string) ($state['status'] ?? 'unknown')); ?></td></tr>
                    <tr><th><?php esc_html_e('Plan', 'msp-nexus-core'); ?></th><td><?php echo esc_html((string) ($state['plan'] ?? '')); ?></td></tr>
                    <tr><th><?php esc_html_e('Expires', 'msp-nexus-core'); ?></th><td><?php echo esc_html((string) ($state['expires_at'] ?? '')); ?></td></tr>
                    <tr><th><?php esc_html_e('License ID', 'msp-nexus-core'); ?></th><td><code><?php echo esc_html($this->redact((string) $state['license_id'])); ?></code></td></tr>
                    <tr><th><?php esc_html_e('Activation identity', 'msp-nexus-core'); ?></th><td><code><?php echo esc_html($this->redact((string) ($state['activation_id'] ?? $state['instance_id'] ?? ''))); ?></code></td></tr>
                    <tr><th><?php esc_html_e('Production sites', 'msp-nexus-core'); ?></th><td><?php echo esc_html(sprintf('%1$d / %2$d', (int) ($state['active_production_activations'] ?? 0), (int) ($state['activation_limit'] ?? 0))); ?></td></tr>
                    <tr><th><?php esc_html_e('Staging sites', 'msp-nexus-core'); ?></th><td><?php echo esc_html((string) (int) ($state['active_staging_activations'] ?? 0)); ?></td></tr>
                    <tr><th><?php esc_html_e('Last successful check', 'msp-nexus-core'); ?></th><td><?php echo ! empty($state['last_checked_at']) ? esc_html(wp_date('Y-m-d H:i:s T', (int) $state['last_checked_at'])) : esc_html__('Never', 'msp-nexus-core'); ?></td></tr>
                </tbody></table>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="<?php echo esc_attr(self::DEACTIVATE); ?>"><?php wp_nonce_field(self::DEACTIVATE); ?><?php submit_button(__('Deactivate this site', 'msp-nexus-core'), 'secondary'); ?></form>
            <?php else : ?>
                <h2><?php esc_html_e('Activation privacy notice', 'msp-nexus-core'); ?></h2><p><?php esc_html_e('Activation transmits the entered key, this site origin, a new random installation identifier, and whether WordPress multisite is enabled. The service necessarily observes connection metadata such as IP address for security and rate limiting. It returns a signed entitlement cache. No post content, users, analytics, passwords, or visitor data are sent. Retention and verified deletion requests are documented in the vendor privacy and operations policy.', 'msp-nexus-core'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="<?php echo esc_attr(self::ACTIVATE); ?>"><?php wp_nonce_field(self::ACTIVATE); ?>
                    <table class="form-table" role="presentation"><tr><th><label for="msp-license-key"><?php esc_html_e('License key', 'msp-nexus-core'); ?></label></th><td><input class="regular-text" id="msp-license-key" name="license_key" type="password" required autocomplete="off"><p class="description"><?php esc_html_e('The raw key is sent once over HTTPS and is not stored in WordPress.', 'msp-nexus-core'); ?></p></td></tr></table>
                    <?php submit_button(__('Activate license', 'msp-nexus-core')); ?>
                </form>
            <?php endif; ?>
            <h2><?php esc_html_e('Recent update history', 'msp-nexus-core'); ?></h2>
            <?php if (empty($history)) : ?><p><?php esc_html_e('No MSP Nexus updates have been recorded on this site.', 'msp-nexus-core'); ?></p><?php else : ?><ul><?php foreach (array_slice((array) $history, 0, 10) as $event) : ?><li><code><?php echo esc_html((string) ($event['completed_at'] ?? '')); ?></code> <?php echo esc_html(implode(', ', (array) ($event['items'] ?? array()))); ?></li><?php endforeach; ?></ul><?php endif; ?>
            <h2><?php esc_html_e('Verified rollback', 'msp-nexus-core'); ?></h2><p><?php esc_html_e('Use only after a current backup. Rollback replaces product files with the previous signed and hash-verified package; it does not reverse customer content or unrelated database changes.', 'msp-nexus-core'); ?></p>
            <?php foreach (array('msp-nexus' => __('Theme', 'msp-nexus-core'), 'msp-nexus-core' => __('Core plugin', 'msp-nexus-core')) as $product => $label) : $manifest = get_site_transient('msp_nexus_update_' . $product); ?>
                <?php if (is_array($manifest) && ! empty($manifest['rollback_package_url'])) : ?><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="msp_nexus_rollback"><input type="hidden" name="product" value="<?php echo esc_attr($product); ?>"><?php wp_nonce_field('msp_nexus_rollback_' . $product); ?><?php submit_button(sprintf(__('Roll back %1$s to %2$s', 'msp-nexus-core'), $label, (string) ($manifest['rollback_version'] ?? 'previous version')), 'secondary', 'submit', false); ?></form><?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
    }

    public function activate(): void
    {
        $this->authorize(self::ACTIVATE);
        $key = sanitize_text_field(wp_unslash((string) ($_POST['license_key'] ?? '')));
        if ('' === $key) {
            $this->redirect(__('Enter a license key.', 'msp-nexus-core'));
        }
        $instance = wp_generate_uuid4();
        $endpoint = apply_filters('msp_nexus_license_activation_url', 'https://licenses.example.com/v1/licenses/activate');
        $response = wp_safe_remote_post($endpoint, array('timeout' => 12, 'headers' => array('Accept' => 'application/json', 'Content-Type' => 'application/json'), 'body' => wp_json_encode(array('license_key' => $key, 'site_url' => home_url('/'), 'instance_id' => $instance, 'is_multisite' => is_multisite()))));
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            $this->redirect(__('Activation was not accepted. Check the key, domain allowance, and service connection.', 'msp-nexus-core'));
        }
        $payload = json_decode(wp_remote_retrieve_body($response), true);
        $entitlement = is_array($payload) ? (new \MspNexusCore\Licensing\EntitlementVerifier())->verified($payload) : null;
        if (! is_array($payload) || empty($payload['license_id']) || ! is_array($entitlement)) {
            $this->redirect(__('The activation response was invalid.', 'msp-nexus-core'));
        }
        update_site_option(self::STATE, array('license_id' => sanitize_text_field((string) $payload['license_id']), 'instance_id' => $instance, 'activation_id' => sanitize_text_field((string) ($entitlement['activation_id'] ?? '')), 'status' => sanitize_key((string) ($entitlement['status'] ?? 'unknown')), 'plan' => sanitize_key((string) ($entitlement['plan'] ?? '')), 'expires_at' => sanitize_text_field((string) ($entitlement['expires_at'] ?? '')), 'grace_ends_at' => sanitize_text_field((string) ($entitlement['grace_ends_at'] ?? '')), 'activation_limit' => (int) ($entitlement['activation_limit'] ?? 0), 'active_production_activations' => (int) ($entitlement['active_production_activations'] ?? 0), 'active_staging_activations' => (int) ($entitlement['active_staging_activations'] ?? 0), 'is_staging' => ! empty($entitlement['is_staging']), 'allow_multisite' => ! empty($entitlement['allow_multisite']), 'signed_entitlement' => $entitlement, 'entitlement_signature' => sanitize_text_field((string) $payload['entitlement_signature']), 'signed_cache_expires_at' => sanitize_text_field((string) ($entitlement['cache_expires_at'] ?? '')), 'last_checked_at' => time()));
        $this->redirect(__('License activated. The raw key was not stored.', 'msp-nexus-core'));
    }

    public function deactivate(): void
    {
        $this->authorize(self::DEACTIVATE);
        $state = get_site_option(self::STATE, array());
        if (is_array($state) && ! empty($state['license_id']) && ! empty($state['instance_id'])) {
            $endpoint = apply_filters('msp_nexus_license_deactivation_url', 'https://licenses.example.com/v1/licenses/deactivate');
            $response = wp_safe_remote_post($endpoint, array('timeout' => 12, 'headers' => array('Accept' => 'application/json', 'Content-Type' => 'application/json'), 'body' => wp_json_encode(array('license_id' => $state['license_id'], 'site_url' => home_url('/'), 'instance_id' => $state['instance_id']))));
            if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
                $this->redirect(__('Remote deactivation failed; the local entitlement remains intact.', 'msp-nexus-core'));
            }
        }
        delete_site_option(self::STATE);
        delete_site_transient('msp_nexus_update_msp-nexus');
        delete_site_transient('msp_nexus_update_msp-nexus-core');
        $this->redirect(__('This site was deactivated. Public content is unchanged.', 'msp-nexus-core'));
    }

    private function authorize(string $action): void
    {
        if (! current_user_can('update_plugins')) {
            wp_die(esc_html__('You are not allowed to manage product licenses.', 'msp-nexus-core'), '', array('response' => 403));
        }
        check_admin_referer($action);
    }

    private function redirect(string $notice): void
    {
        wp_safe_redirect(add_query_arg('msp_notice', $notice, admin_url('admin.php?page=msp-nexus-license')));
        exit;
    }

    private function redact(string $value): string
    {
        return strlen($value) <= 8 ? '••••••••' : substr($value, 0, 4) . '…' . substr($value, -4);
    }
}
