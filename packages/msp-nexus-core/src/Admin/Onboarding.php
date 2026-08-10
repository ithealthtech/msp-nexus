<?php
/**
 * Safe, idempotent, resumable demo importer and onboarding screen.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

final class Onboarding
{
    private const ACTION = 'msp_nexus_import_demo';
    private const CANCEL_ACTION = 'msp_nexus_cancel_demo';
    private const STATE_OPTION = 'msp_nexus_import_state';
    private const LEDGER_OPTION = 'msp_nexus_import_ledger';
    private const BATCH_SIZE = 10;

    public function register_hooks(): void
    {
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_post_' . self::ACTION, array($this, 'import'));
        add_action('admin_post_' . self::CANCEL_ACTION, array($this, 'cancel'));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('MSP Nexus setup', 'msp-nexus-core'), __('Setup', 'msp-nexus-core'), 'manage_options', 'msp-nexus-setup', array($this, 'render'));
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        $state = get_option(self::STATE_OPTION, array());
        $manifest = $this->manifest();
        $total = is_array($manifest) ? count($manifest['content']) : 0;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('MSP Nexus guided setup', 'msp-nexus-core'); ?></h1>
            <p><?php esc_html_e('The importer adds or updates only records carrying MSP Nexus demo identifiers. It never deletes customer content and can be run repeatedly.', 'msp-nexus-core'); ?></p>
            <?php if (is_array($state) && 'running' === ($state['status'] ?? '')) : ?>
                <?php $cursor = (int) ($state['cursor'] ?? 0); $percent = $total > 0 ? min(100, (int) floor(($cursor / $total) * 100)) : 0; ?>
                <div class="notice notice-info inline"><p><?php echo esc_html(sprintf(__('Import job is %1$d%% complete (%2$d of %3$d records). Created: %4$d. Updated: %5$d. You can leave this page and resume safely.', 'msp-nexus-core'), $percent, $cursor, $total, (int) ($state['created'] ?? 0), (int) ($state['updated'] ?? 0))); ?></p></div>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="<?php echo esc_attr(self::ACTION); ?>"><input type="hidden" name="resume" value="1">
                    <?php wp_nonce_field(self::ACTION); ?>
                    <?php submit_button(__('Process next safe batch', 'msp-nexus-core'), 'primary', 'submit', false); ?>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: .75rem;">
                    <input type="hidden" name="action" value="<?php echo esc_attr(self::CANCEL_ACTION); ?>">
                    <?php wp_nonce_field(self::CANCEL_ACTION); ?>
                    <?php submit_button(__('Pause and cancel this import', 'msp-nexus-core'), 'secondary', 'submit', false); ?>
                </form>
            <?php elseif (is_array($state) && ! empty($state['completed_at'])) : ?>
                <div class="notice notice-success inline"><p><?php echo esc_html(sprintf(__('Last import completed at %s. Created: %d. Updated: %d.', 'msp-nexus-core'), (string) $state['completed_at'], (int) ($state['created'] ?? 0), (int) ($state['updated'] ?? 0))); ?></p></div>
            <?php endif; ?>

            <?php $ledger = get_option(self::LEDGER_OPTION, array()); ?>
            <?php if (is_array($ledger) && ! empty($ledger)) : ?>
                <h2><?php esc_html_e('Import activity report', 'msp-nexus-core'); ?></h2>
                <p><?php echo esc_html(sprintf(__('Showing the latest %d processed records. Created objects can be recovered with the Advanced reset tool; customer-authored objects are never listed.', 'msp-nexus-core'), count($ledger))); ?></p>
                <table class="widefat striped"><thead><tr><th><?php esc_html_e('Origin key', 'msp-nexus-core'); ?></th><th><?php esc_html_e('Type', 'msp-nexus-core'); ?></th><th><?php esc_html_e('Result', 'msp-nexus-core'); ?></th><th><?php esc_html_e('Object', 'msp-nexus-core'); ?></th></tr></thead><tbody>
                <?php foreach (array_slice(array_reverse($ledger), 0, 50) as $entry) : ?>
                    <tr><td><code><?php echo esc_html((string) ($entry['key'] ?? '')); ?></code></td><td><?php echo esc_html((string) ($entry['post_type'] ?? '')); ?></td><td><?php echo esc_html((string) ($entry['result'] ?? '')); ?></td><td><?php echo esc_html((string) ($entry['post_id'] ?? ($entry['error'] ?? ''))); ?></td></tr>
                <?php endforeach; ?>
                </tbody></table>
            <?php endif; ?>

            <h2><?php esc_html_e('Operation preview', 'msp-nexus-core'); ?></h2>
            <?php if (is_array($manifest)) : ?>
                <p><?php echo esc_html(sprintf(__('Starter: %1$s. Manifest version: %2$s. Records: %3$d. Batch size: %4$d.', 'msp-nexus-core'), (string) ($manifest['brand'] ?? 'MSP Nexus'), (string) ($manifest['version'] ?? ''), $total, self::BATCH_SIZE)); ?></p>
                <ul><?php foreach ((array) ($manifest['counts'] ?? array()) as $type => $count) : ?><li><code><?php echo esc_html((string) $type); ?></code>: <?php echo esc_html((string) $count); ?></li><?php endforeach; ?></ul>
                <p><strong><?php echo esc_html((string) ($manifest['notice'] ?? '')); ?></strong></p>
            <?php else : ?>
                <div class="notice notice-error inline"><p><?php esc_html_e('The bundled demo manifest or integrity digest is invalid. Import is disabled.', 'msp-nexus-core'); ?></p></div>
            <?php endif; ?>

            <h2><?php esc_html_e('Before importing', 'msp-nexus-core'); ?></h2>
            <ol><li><?php esc_html_e('Create a current backup.', 'msp-nexus-core'); ?></li><li><?php esc_html_e('Activate the MSP Nexus theme and companion plugin.', 'msp-nexus-core'); ?></li><li><?php esc_html_e('Generic service pages publish immediately. Unverified proof, people, locations, resources, events, relationships, and legal placeholders remain drafts until you review them.', 'msp-nexus-core'); ?></li></ol>
            <?php if (is_array($manifest) && (! is_array($state) || 'running' !== ($state['status'] ?? ''))) : ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="<?php echo esc_attr(self::ACTION); ?>">
                    <?php wp_nonce_field(self::ACTION); ?>
                    <p><label><input type="checkbox" name="set_homepage" value="1"> <?php esc_html_e('Set the imported homepage as the static homepage (the prior setting is recorded).', 'msp-nexus-core'); ?></label></p>
                    <p><label><input type="checkbox" name="confirm_demo" value="1" required> <?php esc_html_e('I have a backup and understand this creates production-safe starter content plus draft placeholders that require review.', 'msp-nexus-core'); ?></label></p>
                    <?php submit_button(__('Start idempotent import', 'msp-nexus-core')); ?>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }

    public function import(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to import demo content.', 'msp-nexus-core'), '', array('response' => 403));
        }
        check_admin_referer(self::ACTION);
        $data = $this->manifest();
        if (! is_array($data)) {
            wp_die(esc_html__('The bundled demo manifest failed integrity or schema validation.', 'msp-nexus-core'), '', array('response' => 500));
        }

        $state = get_option(self::STATE_OPTION, array());
        $resuming = ! empty($_POST['resume']) && is_array($state) && 'running' === ($state['status'] ?? '');
        if (! $resuming) {
            if (empty($_POST['confirm_demo'])) {
                $this->redirect();
            }
            $state = array(
                'job_id' => wp_generate_uuid4(),
                'status' => 'running',
                'manifest_version' => sanitize_text_field((string) ($data['version'] ?? '')),
                'manifest_hash' => hash_file('sha256', MSP_NEXUS_CORE_PATH . 'demo/content.json'),
                'cursor' => 0,
                'total' => count($data['content']),
                'created' => 0,
                'updated' => 0,
                'failed' => 0,
                'set_homepage' => ! empty($_POST['set_homepage']),
                'previous_front' => get_option('show_on_front'),
                'previous_page_on_front' => (int) get_option('page_on_front'),
                'started_at' => current_time('mysql', true),
                'actor' => get_current_user_id(),
            );
            update_option(self::LEDGER_OPTION, array(), false);
        }

        $cursor = (int) ($state['cursor'] ?? 0);
        $batch = array_slice($data['content'], $cursor, self::BATCH_SIZE);
        $ledger = get_option(self::LEDGER_OPTION, array());
        if (! is_array($ledger)) {
            $ledger = array();
        }
        foreach ($batch as $item) {
            $result = $this->import_item($item);
            $ledger[] = $result;
            if ('created' === $result['result']) {
                ++$state['created'];
            } elseif ('updated' === $result['result']) {
                ++$state['updated'];
            } else {
                ++$state['failed'];
            }
            if (! empty($state['set_homepage']) && 'page-home' === ($item['key'] ?? '') && ! empty($result['post_id'])) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', (int) $result['post_id']);
            }
        }
        $state['cursor'] = $cursor + count($batch);
        $state['last_batch_at'] = current_time('mysql', true);
        if ($state['cursor'] >= count($data['content'])) {
            $state['status'] = empty($state['failed']) ? 'completed' : 'completed_with_errors';
            $state['completed_at'] = current_time('mysql', true);
        }
        update_option(self::LEDGER_OPTION, array_slice($ledger, -500), false);
        update_option(self::STATE_OPTION, $state, false);
        $this->redirect();
    }

    public function cancel(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to cancel demo imports.', 'msp-nexus-core'), '', array('response' => 403));
        }
        check_admin_referer(self::CANCEL_ACTION);
        $state = get_option(self::STATE_OPTION, array());
        if (is_array($state) && 'running' === ($state['status'] ?? '')) {
            $state['status'] = 'cancelled';
            $state['cancelled_at'] = current_time('mysql', true);
            $state['cancelled_by'] = get_current_user_id();
            update_option(self::STATE_OPTION, $state, false);
        }
        $this->redirect();
    }

    /** @return array<string, mixed>|null */
    private function manifest(): ?array
    {
        $path = MSP_NEXUS_CORE_PATH . 'demo/content.json';
        $digest_path = MSP_NEXUS_CORE_PATH . 'demo/content.sha256';
        $raw = is_readable($path) ? file_get_contents($path) : false;
        $digest_line = is_readable($digest_path) ? file_get_contents($digest_path) : false;
        if (! is_string($raw) || ! is_string($digest_line)) {
            return null;
        }
        $expected = strtolower(strtok(trim($digest_line), " \t"));
        if (! preg_match('/^[a-f0-9]{64}$/', $expected) || ! hash_equals($expected, hash('sha256', $raw))) {
            return null;
        }
        $data = json_decode($raw, true);
        if (! is_array($data) || 1 !== ($data['schema'] ?? null) || ! isset($data['content']) || ! is_array($data['content']) || count($data['content']) > 500) {
            return null;
        }
        return $data;
    }

    /** @param mixed $item
     *  @return array<string, mixed>
     */
    private function import_item($item): array
    {
        if (! is_array($item) || empty($item['key']) || empty($item['post_type']) || ! post_type_exists((string) $item['post_type'])) {
            return array('key' => sanitize_key((string) ($item['key'] ?? 'invalid')), 'result' => 'failed', 'error' => 'invalid_record');
        }
        $key = sanitize_key((string) $item['key']);
        $post_type = sanitize_key((string) $item['post_type']);
        $status = sanitize_key((string) ($item['status'] ?? 'publish'));
        if (! in_array($status, array('publish', 'draft', 'pending', 'private'), true)) {
            $status = 'draft';
        }
        $title = (string) ($item['title'] ?? 'Starter content');
        if ('page-home' === $key) {
            $site_title = trim((string) get_bloginfo('name'));
            if ('' !== $site_title) {
                $title = $site_title;
            }
        }
        $existing = get_posts(array('post_type' => $post_type, 'post_status' => 'any', 'meta_key' => '_msp_nexus_demo_key', 'meta_value' => $key, 'fields' => 'ids', 'posts_per_page' => 1, 'no_found_rows' => true));
        $prior = isset($existing[0]) ? get_post((int) $existing[0], ARRAY_A) : null;
        $post = array(
            'ID' => isset($existing[0]) ? (int) $existing[0] : 0,
            'post_type' => $post_type,
            'post_status' => $status,
            'post_name' => sanitize_title((string) ($item['slug'] ?? $key)),
            'post_title' => sanitize_text_field($title),
            'post_excerpt' => sanitize_textarea_field((string) ($item['excerpt'] ?? '')),
            'post_content' => wp_kses_post((string) ($item['content'] ?? '')),
        );
        $template = sanitize_key((string) ($item['template'] ?? ''));
        if ('page' === $post_type && in_array($template, array('page-no-title', 'page-narrow', 'blank'), true)) {
            $post['page_template'] = $template;
        }
        $post_id = wp_insert_post(wp_slash($post), true);
        if (is_wp_error($post_id)) {
            return array('key' => $key, 'result' => 'failed', 'error' => sanitize_key($post_id->get_error_code()));
        }
        update_post_meta($post_id, '_msp_nexus_demo_key', $key);
        if (! empty($item['meta']) && is_array($item['meta'])) {
            foreach ($item['meta'] as $meta_key => $value) {
                if (0 === strpos((string) $meta_key, '_msp_')) {
                    update_post_meta($post_id, sanitize_key((string) $meta_key), sanitize_text_field((string) $value));
                }
            }
        }
        return array('key' => $key, 'post_id' => $post_id, 'post_type' => $post_type, 'result' => $prior ? 'updated' : 'created', 'prior_modified_gmt' => is_array($prior) ? (string) ($prior['post_modified_gmt'] ?? '') : null, 'at' => current_time('mysql', true));
    }

    private function redirect(): void
    {
        wp_safe_redirect(admin_url('admin.php?page=msp-nexus-setup'));
        exit;
    }
}
