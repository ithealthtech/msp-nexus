<?php
/**
 * Advanced, explicitly confirmed demo-content reset.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

final class Reset
{
    private const ACTION = 'msp_nexus_reset_demo';
    private const CONFIRMATION = 'TRASH NORTHSTAR DEMO';

    public function register_hooks(): void
    {
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_post_' . self::ACTION, array($this, 'execute'));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('MSP Nexus advanced reset', 'msp-nexus-core'), __('Advanced reset', 'msp-nexus-core'), 'manage_options', 'msp-nexus-reset', array($this, 'render'));
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        $targets = $this->targets();
        ?>
        <div class="wrap"><h1><?php esc_html_e('Advanced demo reset', 'msp-nexus-core'); ?></h1><div class="notice notice-warning inline"><p><?php esc_html_e('Create a current backup first. This tool targets only posts carrying an MSP Nexus demo key and the exact import options listed below. Posts are moved to Trash for recovery; media, users, terms, tables, and unrelated options are never touched.', 'msp-nexus-core'); ?></p></div>
        <h2><?php esc_html_e('Dry-run target report', 'msp-nexus-core'); ?></h2><p><?php echo esc_html(sprintf(__('Demo-keyed posts: %d', 'msp-nexus-core'), count($targets))); ?></p><ul><?php foreach ($targets as $target) : ?><li>#<?php echo esc_html((string) $target->ID); ?> — <?php echo esc_html($target->post_type . ': ' . $target->post_title); ?></li><?php endforeach; ?></ul><p><?php esc_html_e('Options: msp_nexus_import_state, msp_nexus_import_ledger. The prior homepage settings recorded by the importer will be restored when available.', 'msp-nexus-core'); ?></p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="<?php echo esc_attr(self::ACTION); ?>"><?php wp_nonce_field(self::ACTION); ?><p><label><input type="checkbox" name="backup_ack" value="1" required> <?php esc_html_e('I acknowledge that a current backup is recommended.', 'msp-nexus-core'); ?></label></p><p><label><?php echo esc_html(sprintf(__('Type %s to enable the reset:', 'msp-nexus-core'), self::CONFIRMATION)); ?> <input name="typed_confirmation" type="text" autocomplete="off" required></label></p><?php submit_button(__('Move listed demo posts to Trash', 'msp-nexus-core'), 'delete'); ?></form></div>
        <?php
    }

    public function execute(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to reset demo content.', 'msp-nexus-core'), '', array('response' => 403));
        }
        check_admin_referer(self::ACTION);
        $typed = sanitize_text_field(wp_unslash((string) ($_POST['typed_confirmation'] ?? '')));
        if (empty($_POST['backup_ack']) || ! hash_equals(self::CONFIRMATION, $typed)) {
            wp_die(esc_html__('Backup acknowledgment and exact typed confirmation are required.', 'msp-nexus-core'), '', array('response' => 400));
        }
        $targets = $this->targets();
        $trashed = array();
        foreach ($targets as $target) {
            if (wp_trash_post((int) $target->ID)) {
                $trashed[] = (int) $target->ID;
            }
        }
        $state = get_option('msp_nexus_import_state', array());
        if (is_array($state) && isset($state['previous_front'], $state['previous_page_on_front'])) {
            update_option('show_on_front', sanitize_key((string) $state['previous_front']));
            update_option('page_on_front', (int) $state['previous_page_on_front']);
        }
        delete_option('msp_nexus_import_state');
        delete_option('msp_nexus_import_ledger');
        $log = (array) get_option('msp_nexus_reset_log', array());
        $log[] = array('actor' => get_current_user_id(), 'trashed_post_ids' => $trashed, 'at' => current_time('mysql', true));
        update_option('msp_nexus_reset_log', array_slice($log, -25), false);
        wp_safe_redirect(admin_url('admin.php?page=msp-nexus-reset&reset=1'));
        exit;
    }

    /** @return array<int, \WP_Post> */
    private function targets(): array
    {
        return get_posts(array('post_type' => 'any', 'post_status' => array('publish', 'draft', 'pending', 'private', 'future'), 'meta_key' => '_msp_nexus_demo_key', 'meta_compare' => 'EXISTS', 'posts_per_page' => 500, 'orderby' => 'ID', 'order' => 'ASC'));
    }
}
