<?php
/**
 * Reviewed JSON import/export for Nexus settings, layouts, and theme templates.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

use MspNexusCore\Studio\Layouts;

final class Portability
{
    private const EXPORT_ACTION = 'msp_nexus_export_bundle';
    private const IMPORT_ACTION = 'msp_nexus_import_bundle';

    public function register_hooks(): void
    {
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_post_' . self::EXPORT_ACTION, array($this, 'export'));
        add_action('admin_post_' . self::IMPORT_ACTION, array($this, 'import'));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('Nexus portability', 'msp-nexus-core'), __('Import / export', 'msp-nexus-core'), 'manage_options', 'msp-nexus-portability', array($this, 'render'));
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap"><h1><?php esc_html_e('Nexus import and export', 'msp-nexus-core'); ?></h1>
        <?php if (isset($_GET['imported'])) : ?><div class="notice notice-success is-dismissible"><p><?php echo esc_html(sprintf(__('Imported %d portable records.', 'msp-nexus-core'), absint($_GET['imported']))); ?></p></div><?php endif; ?>
        <h2><?php esc_html_e('Export reviewed bundle', 'msp-nexus-core'); ?></h2><p><?php esc_html_e('Downloads organization settings, design/branding/performance presets, Nexus layouts, and current-theme block templates and parts. License credentials and transient state are excluded.', 'msp-nexus-core'); ?></p>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post"><input type="hidden" name="action" value="<?php echo esc_attr(self::EXPORT_ACTION); ?>"><input type="hidden" name="scope" value="bundle"><?php wp_nonce_field(self::EXPORT_ACTION); ?><?php submit_button(__('Download JSON bundle', 'msp-nexus-core'), 'primary', 'submit', false); ?></form>
        <h2><?php esc_html_e('Export one page or content item', 'msp-nexus-core'); ?></h2><p><?php esc_html_e('Creates a focused portable document containing one block document and its safe public metadata.', 'msp-nexus-core'); ?></p><form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post"><input type="hidden" name="action" value="<?php echo esc_attr(self::EXPORT_ACTION); ?>"><input type="hidden" name="scope" value="content"><?php wp_nonce_field(self::EXPORT_ACTION); ?><select name="post_id" required><option value=""><?php esc_html_e('Choose content…', 'msp-nexus-core'); ?></option><?php foreach ($this->portable_posts() as $portable_post) : ?><option value="<?php echo esc_attr((string) $portable_post->ID); ?>"><?php echo esc_html($portable_post->post_title . ' · ' . $portable_post->post_type); ?></option><?php endforeach; ?></select> <?php submit_button(__('Download content JSON', 'msp-nexus-core'), 'secondary', 'submit', false); ?></form>
        <h2><?php esc_html_e('Import reviewed bundle', 'msp-nexus-core'); ?></h2><p><?php esc_html_e('Import into staging first. Existing Nexus layouts and theme templates with the same slug are updated; customer pages and posts are never deleted.', 'msp-nexus-core'); ?></p>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="<?php echo esc_attr(self::IMPORT_ACTION); ?>"><?php wp_nonce_field(self::IMPORT_ACTION); ?><p><input required type="file" name="bundle" accept="application/json,.json"></p><p><label><input type="checkbox" name="import_content" value="1" checked> <?php esc_html_e('Import a focused page/content export as a new draft when its slug already exists', 'msp-nexus-core'); ?></label></p><p><label><input type="checkbox" name="import_settings" value="1" checked> <?php esc_html_e('Import settings and presets', 'msp-nexus-core'); ?></label></p><p><label><input type="checkbox" name="import_templates" value="1"> <?php esc_html_e('Import current-theme templates and template parts', 'msp-nexus-core'); ?></label></p><p><label><input type="checkbox" name="import_pages" value="1"> <?php esc_html_e('Import Nexus-owned starter pages (never overwrite an unmarked customer page)', 'msp-nexus-core'); ?></label></p><?php submit_button(__('Validate and import', 'msp-nexus-core'), 'secondary', 'submit', false); ?></form>
        </div>
        <?php
    }

    public function export(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to export Nexus data.', 'msp-nexus-core'));
        }
        check_admin_referer(self::EXPORT_ACTION);
        $scope = sanitize_key((string) ($_POST['scope'] ?? 'bundle'));
        if ('content' === $scope) {
            $post_id = absint($_POST['post_id'] ?? 0);
            $post = get_post($post_id);
            if (! $post instanceof \WP_Post || ! current_user_can('edit_post', $post_id) || ! in_array($post->post_type, get_post_types(array('public' => true)), true)) {
                wp_die(esc_html__('Choose content you are allowed to export.', 'msp-nexus-core'));
            }
            $bundle = array('schema' => 1, 'product' => 'msp-nexus', 'kind' => 'content', 'exported_at' => gmdate('c'), 'content' => $this->record($post));
            $this->download($bundle, 'msp-nexus-content-' . $post->post_name . '-' . gmdate('Ymd-His') . '.json');
        }
        $bundle = array(
            'schema' => 1,
            'product' => 'msp-nexus',
            'exported_at' => gmdate('c'),
            'source' => array('home' => home_url('/'), 'theme' => get_stylesheet(), 'wordpress' => get_bloginfo('version'), 'plugin' => MSP_NEXUS_CORE_VERSION),
            'options' => array(
                'msp_nexus_settings' => get_option('msp_nexus_settings', array()),
                'msp_nexus_branding' => get_option('msp_nexus_branding', array()),
                'msp_nexus_performance' => get_option('msp_nexus_performance', array()),
                'msp_nexus_privacy' => get_option('msp_nexus_privacy', array()),
                'msp_nexus_woocommerce' => get_option('msp_nexus_woocommerce', array()),
                'msp_nexus_starter_site' => get_option('msp_nexus_starter_site', StarterSites::default_id()),
            ),
            'layouts' => $this->records(Layouts::POST_TYPE),
            'templates' => array_merge($this->records('wp_template'), $this->records('wp_template_part')),
            'starter_pages' => $this->records('page', true),
        );
        $this->download($bundle, 'msp-nexus-bundle-' . gmdate('Ymd-His') . '.json');
    }

    public function import(): void
    {
        if (! current_user_can('manage_options') || ! current_user_can('unfiltered_html')) {
            wp_die(esc_html__('You are not allowed to import block templates.', 'msp-nexus-core'));
        }
        check_admin_referer(self::IMPORT_ACTION);
        if (! isset($_FILES['bundle']) || ! is_array($_FILES['bundle']) || UPLOAD_ERR_OK !== (int) ($_FILES['bundle']['error'] ?? UPLOAD_ERR_NO_FILE)) {
            wp_die(esc_html__('Choose a readable JSON bundle.', 'msp-nexus-core'));
        }
        $size = (int) ($_FILES['bundle']['size'] ?? 0);
        $tmp = (string) ($_FILES['bundle']['tmp_name'] ?? '');
        $name = sanitize_file_name((string) ($_FILES['bundle']['name'] ?? ''));
        if ($size < 1 || $size > 2 * MB_IN_BYTES || 'json' !== strtolower((string) pathinfo($name, PATHINFO_EXTENSION)) || ! is_uploaded_file($tmp)) {
            wp_die(esc_html__('The bundle must be a JSON file no larger than 2 MB.', 'msp-nexus-core'));
        }
        $bundle = json_decode((string) file_get_contents($tmp), true);
        if (! is_array($bundle) || 1 !== (int) ($bundle['schema'] ?? 0) || 'msp-nexus' !== ($bundle['product'] ?? '')) {
            wp_die(esc_html__('This is not a supported Nexus bundle.', 'msp-nexus-core'));
        }
        $count = 0;
        if (! empty($_POST['import_settings']) && isset($bundle['options']) && is_array($bundle['options'])) {
            $options = $bundle['options'];
            if (isset($options['msp_nexus_settings'])) {
                update_option('msp_nexus_settings', (new Settings())->sanitize($options['msp_nexus_settings']), false);
            }
            if (isset($options['msp_nexus_branding'])) {
                update_option('msp_nexus_branding', (new Branding())->sanitize($options['msp_nexus_branding']), false);
            }
            if (isset($options['msp_nexus_performance'])) {
                update_option('msp_nexus_performance', (new Performance())->sanitize($options['msp_nexus_performance']), false);
            }
            if (isset($options['msp_nexus_privacy'])) {
                update_option('msp_nexus_privacy', (new Privacy())->sanitize($options['msp_nexus_privacy']), false);
            }
            if (isset($options['msp_nexus_woocommerce'])) {
                update_option('msp_nexus_woocommerce', (new \MspNexusCore\Integrations\WooCommerce())->sanitize($options['msp_nexus_woocommerce']), false);
            }
            $starters = StarterSites::definitions();
            $starter = sanitize_key((string) ($options['msp_nexus_starter_site'] ?? ''));
            if (isset($starters[$starter])) {
                update_option('msp_nexus_starter_site', $starter, false);
            }
        }
        if (! empty($_POST['import_content']) && isset($bundle['content']) && is_array($bundle['content'])) {
            $post_type = sanitize_key((string) ($bundle['content']['post_type'] ?? ''));
            if (in_array($post_type, get_post_types(array('public' => true)), true) && $this->import_record($bundle['content'], $post_type, false, false)) {
                ++$count;
            }
        }
        foreach ((array) ($bundle['layouts'] ?? array()) as $record) {
            if (is_array($record) && $this->import_record($record, Layouts::POST_TYPE)) {
                ++$count;
            }
        }
        if (! empty($_POST['import_templates'])) {
            foreach ((array) ($bundle['templates'] ?? array()) as $record) {
                if (is_array($record) && in_array(($record['post_type'] ?? ''), array('wp_template', 'wp_template_part'), true) && $this->import_record($record, (string) $record['post_type'])) {
                    ++$count;
                }
            }
        }
        if (! empty($_POST['import_pages'])) {
            foreach ((array) ($bundle['starter_pages'] ?? array()) as $record) {
                if (is_array($record) && $this->import_record($record, 'page', true)) {
                    ++$count;
                }
            }
        }
        wp_safe_redirect(add_query_arg('imported', (string) $count, admin_url('admin.php?page=msp-nexus-portability')));
        exit;
    }

    /** @return array<int, array<string, mixed>> */
    private function records(string $post_type, bool $starter_only = false): array
    {
        $args = array('post_type' => $post_type, 'post_status' => array('publish', 'draft', 'private'), 'posts_per_page' => 200, 'orderby' => 'ID', 'order' => 'ASC');
        if ($starter_only) {
            $args['meta_query'] = array(array('key' => '_msp_nexus_demo_key', 'compare' => 'EXISTS'));
        }
        $posts = get_posts($args);
        $records = array();
        foreach ($posts as $post) {
            $record = $this->record($post);
            if (Layouts::POST_TYPE === $post_type) {
                foreach (array(
                    '_msp_nexus_layout_area', '_msp_nexus_condition_context', '_msp_nexus_condition_post_type', '_msp_nexus_condition_priority',
                    '_msp_nexus_condition_include_ids', '_msp_nexus_condition_exclude_ids', '_msp_nexus_condition_taxonomy',
                    '_msp_nexus_condition_include_terms', '_msp_nexus_condition_exclude_terms', '_msp_nexus_condition_user_state',
                    '_msp_nexus_condition_roles', '_msp_nexus_condition_languages', '_msp_nexus_condition_date_start',
                    '_msp_nexus_condition_date_end', '_msp_nexus_condition_weekdays', '_msp_nexus_condition_query_key',
                    '_msp_nexus_condition_query_value', '_msp_nexus_popup_trigger', '_msp_nexus_popup_delay',
                    '_msp_nexus_popup_scroll', '_msp_nexus_popup_cookie_days', '_msp_nexus_popup_selector', '_msp_nexus_popup_animation',
                    '_msp_nexus_popup_position', '_msp_nexus_popup_close_overlay', '_msp_nexus_popup_require_consent',
                    '_msp_nexus_popup_consent_key', '_msp_nexus_popup_group', '_msp_nexus_popup_weight',
                    '_msp_nexus_header_sticky', '_msp_nexus_header_overlay', '_msp_nexus_header_shrink',
                    '_msp_nexus_header_hide_scroll', '_msp_nexus_header_offset',
                ) as $key) {
                    $record['meta'][$key] = get_post_meta($post->ID, $key, true);
                }
            } elseif ('page' === $post_type) {
                $record['meta']['_msp_nexus_demo_key'] = get_post_meta($post->ID, '_msp_nexus_demo_key', true);
                $record['meta']['_wp_page_template'] = get_post_meta($post->ID, '_wp_page_template', true);
            }
            $records[] = $record;
        }
        return $records;
    }

    /** @param array<string, mixed> $record */
    private function import_record(array $record, string $post_type, bool $starter_page = false, bool $allow_update = true): bool
    {
        $slug = sanitize_title((string) ($record['slug'] ?? ''));
        $title = sanitize_text_field((string) ($record['title'] ?? ''));
        $content = (string) ($record['content'] ?? '');
        if ('' === $slug || '' === $title || strlen($content) > 500000 || preg_match('/<\?(?:php|=)|<script\b|\son[a-z]+\s*=/i', $content)) {
            return false;
        }
        $existing = get_page_by_path($slug, OBJECT, $post_type);
        if ($starter_page && $existing instanceof \WP_Post && '' === (string) get_post_meta($existing->ID, '_msp_nexus_demo_key', true)) {
            return false;
        }
        if (! $allow_update && $existing instanceof \WP_Post) {
            $slug = wp_unique_post_slug($slug . '-imported', 0, 'draft', $post_type, 0);
            $existing = null;
        }
        $status = in_array(($record['status'] ?? ''), array('publish', 'draft', 'private'), true) ? (string) $record['status'] : 'draft';
        $data = array('post_type' => $post_type, 'post_name' => $slug, 'post_title' => $title, 'post_status' => $status, 'post_content' => $content, 'post_excerpt' => sanitize_textarea_field((string) ($record['excerpt'] ?? '')));
        if ($existing instanceof \WP_Post) {
            $data['ID'] = $existing->ID;
        }
        $post_id = wp_insert_post(wp_slash($data), true);
        if (is_wp_error($post_id)) {
            return false;
        }
        if (Layouts::POST_TYPE === $post_type && isset($record['meta']) && is_array($record['meta'])) {
            foreach ($record['meta'] as $key => $value) {
                if (0 === strpos((string) $key, '_msp_nexus_')) {
                    update_post_meta((int) $post_id, sanitize_key((string) $key), sanitize_text_field((string) $value));
                }
            }
        } elseif ($starter_page && isset($record['meta']) && is_array($record['meta'])) {
            update_post_meta((int) $post_id, '_msp_nexus_demo_key', sanitize_key((string) ($record['meta']['_msp_nexus_demo_key'] ?? '')));
            update_post_meta((int) $post_id, '_wp_page_template', sanitize_file_name((string) ($record['meta']['_wp_page_template'] ?? 'default')));
        } elseif (taxonomy_exists('wp_theme')) {
            wp_set_object_terms((int) $post_id, get_stylesheet(), 'wp_theme');
        }
        return true;
    }

    /** @return array<int, \WP_Post> */
    private function portable_posts(): array
    {
        $posts = get_posts(array('post_type' => get_post_types(array('public' => true)), 'post_status' => array('publish', 'draft', 'private'), 'posts_per_page' => 200, 'orderby' => 'modified', 'order' => 'DESC'));
        return is_array($posts) ? $posts : array();
    }

    /** @return array<string, mixed> */
    private function record(\WP_Post $post): array
    {
        $record = array('post_type' => $post->post_type, 'slug' => $post->post_name, 'title' => $post->post_title, 'status' => $post->post_status, 'content' => $post->post_content, 'excerpt' => $post->post_excerpt, 'meta' => array());
        if ('page' === $post->post_type) {
            $record['meta']['_wp_page_template'] = get_post_meta($post->ID, '_wp_page_template', true);
        }
        return $record;
    }

    /** @param array<string, mixed> $bundle */
    private function download(array $bundle, string $filename): void
    {
        nocache_headers();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . sanitize_file_name($filename) . '"');
        echo wp_json_encode($bundle, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }
}
