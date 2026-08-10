<?php
/**
 * Searchable, composable starter-site catalog and selective application.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

use MspNexusCore\Starter\Catalog;

final class StarterSites
{
    private const OPTION = 'msp_nexus_starter_site';
    private const ACTION = 'msp_nexus_apply_starter';

    public function register_hooks(): void
    {
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_enqueue_scripts', array($this, 'assets'));
        add_action('admin_post_' . self::ACTION, array($this, 'apply'));
        add_action('rest_api_init', array($this, 'rest_routes'));
        add_filter('wp_theme_json_data_theme', array($this, 'theme_json'));
        add_filter('body_class', array($this, 'body_class'));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('Nexus starter sites', 'msp-nexus-core'), __('Starter sites', 'msp-nexus-core'), 'manage_options', 'msp-nexus-starters', array($this, 'render'));
    }

    public function assets(): void
    {
        if ('msp-nexus-starters' !== sanitize_key((string) ($_GET['page'] ?? ''))) {
            return;
        }
        wp_enqueue_script('msp-nexus-starter-library', plugins_url('assets/starter-sites.js', MSP_NEXUS_CORE_FILE), array('wp-api-fetch', 'wp-i18n'), MSP_NEXUS_CORE_VERSION, true);
        wp_localize_script('msp-nexus-starter-library', 'mspNexusStarterLibrary', array(
            'path' => '/msp-nexus/v1/starter-sites',
            'action' => admin_url('admin-post.php'),
            'nonce' => wp_create_nonce(self::ACTION),
            'selected' => (string) get_option(self::OPTION, self::default_id()),
            'labels' => array('apply' => __('Review and apply', 'msp-nexus-core'), 'selected' => __('Currently active', 'msp-nexus-core'), 'empty' => __('No starter kits match those filters.', 'msp-nexus-core')),
        ));
    }

    /** @return array<string, array<string, string>> */
    public static function definitions(): array
    {
        $definitions = array();
        foreach (Catalog::all() as $item) {
            $definitions[$item['id']] = $item;
        }
        return $definitions;
    }

    public static function default_id(): string
    {
        return 'general-msp--managed-operations--enterprise-dark';
    }

    public function rest_routes(): void
    {
        register_rest_route('msp-nexus/v1', '/starter-sites', array(
            'methods' => 'GET',
            'callback' => array($this, 'catalog'),
            'permission_callback' => static function (): bool { return current_user_can('manage_options'); },
            'args' => array(
                'search' => array('sanitize_callback' => 'sanitize_text_field'),
                'vertical' => array('sanitize_callback' => 'sanitize_key'),
                'focus' => array('sanitize_callback' => 'sanitize_key'),
                'style' => array('sanitize_callback' => 'sanitize_key'),
                'page' => array('sanitize_callback' => 'absint', 'default' => 1),
                'per_page' => array('sanitize_callback' => 'absint', 'default' => 24),
            ),
        ));
    }

    public function catalog(\WP_REST_Request $request): \WP_REST_Response
    {
        $search = strtolower((string) $request->get_param('search'));
        $vertical = sanitize_key((string) $request->get_param('vertical'));
        $focus = sanitize_key((string) $request->get_param('focus'));
        $style = sanitize_key((string) $request->get_param('style'));
        $items = array_values(array_filter(Catalog::all(), static function (array $item) use ($search, $vertical, $focus, $style): bool {
            if ('' !== $vertical && $item['vertical'] !== $vertical) {
                return false;
            }
            if ('' !== $focus && $item['focus'] !== $focus) {
                return false;
            }
            if ('' !== $style && $item['style'] !== $style) {
                return false;
            }
            return '' === $search || false !== strpos(strtolower($item['name'] . ' ' . $item['description']), $search);
        }));
        $page = max(1, absint($request->get_param('page')));
        $per_page = min(60, max(1, absint($request->get_param('per_page'))));
        return new \WP_REST_Response(array(
            'items' => array_slice($items, ($page - 1) * $per_page, $per_page),
            'total' => count($items),
            'pages' => (int) ceil(count($items) / $per_page),
            'page' => $page,
            'facets' => array('verticals' => Catalog::verticals(), 'focuses' => array_map(static function (array $item): string { return $item['name']; }, Catalog::focuses()), 'styles' => array_map(static function (array $item): string { return $item['name']; }, Catalog::styles())),
        ));
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap msp-nexus-starters"><h1><?php esc_html_e('Nexus adaptive starter library', 'msp-nexus-core'); ?></h1>
        <p><?php printf(esc_html__('%s complete MSP starter configurations combine market context, service positioning, and visual systems. Search, preview, then apply only the parts you want.', 'msp-nexus-core'), esc_html(number_format_i18n(count(Catalog::all())))); ?></p>
        <?php if (isset($_GET['starter-applied'])) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('The selected starter components were applied. Existing non-Nexus pages were preserved.', 'msp-nexus-core'); ?></p></div><?php endif; ?>
        <div class="msp-nexus-starter-controls"><label><?php esc_html_e('Search', 'msp-nexus-core'); ?><input type="search" data-starter-search placeholder="<?php esc_attr_e('Healthcare security, cloud, legal…', 'msp-nexus-core'); ?>"></label><label><?php esc_html_e('Industry', 'msp-nexus-core'); ?><select data-starter-vertical><option value=""><?php esc_html_e('All industries', 'msp-nexus-core'); ?></option></select></label><label><?php esc_html_e('Focus', 'msp-nexus-core'); ?><select data-starter-focus><option value=""><?php esc_html_e('All service focuses', 'msp-nexus-core'); ?></option></select></label><label><?php esc_html_e('Visual system', 'msp-nexus-core'); ?><select data-starter-style><option value=""><?php esc_html_e('All visual systems', 'msp-nexus-core'); ?></option></select></label></div>
        <p><strong data-starter-count><?php esc_html_e('Loading starter catalog…', 'msp-nexus-core'); ?></strong></p><div class="msp-nexus-starter-grid" data-starter-grid aria-live="polite"></div><nav class="msp-nexus-starter-pages" data-starter-pages aria-label="<?php esc_attr_e('Starter library pages', 'msp-nexus-core'); ?>"></nav></div>
        <style>.msp-nexus-starter-controls{align-items:end;display:grid;gap:12px;grid-template-columns:2fr repeat(3,1fr);max-width:1200px}.msp-nexus-starter-controls label{display:grid;font-weight:600;gap:5px}.msp-nexus-starter-controls input,.msp-nexus-starter-controls select{max-width:none;width:100%}.msp-nexus-starter-grid{display:grid;gap:16px;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));max-width:1200px}.msp-nexus-starter-card{background:#fff;border:1px solid #c3c4c7;border-radius:4px;display:flex;flex-direction:column;overflow:hidden}.msp-nexus-starter-card.is-selected{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1}.msp-nexus-starter-card__swatch{background:linear-gradient(120deg,#101d33,var(--starter-accent));height:100px}.msp-nexus-starter-card__body{display:flex;flex:1;flex-direction:column;padding:16px}.msp-nexus-starter-card__meta{font-size:12px;text-transform:uppercase}.msp-nexus-starter-card form{border-top:1px solid #dcdcde;margin-top:auto;padding-top:12px}.msp-nexus-starter-card form label{display:block;margin-block:5px}.msp-nexus-starter-pages{display:flex;gap:6px;margin-block:20px}.msp-nexus-starter-pages button[aria-current="page"]{font-weight:800}@media(max-width:900px){.msp-nexus-starter-controls{grid-template-columns:1fr 1fr}}@media(max-width:600px){.msp-nexus-starter-controls{grid-template-columns:1fr}}</style>
        <?php
    }

    public function apply(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to apply starter sites.', 'msp-nexus-core'));
        }
        check_admin_referer(self::ACTION);
        $key = sanitize_text_field((string) ($_POST['starter'] ?? ''));
        $starter = Catalog::get($key);
        if (null === $starter) {
            wp_die(esc_html__('Unknown starter configuration.', 'msp-nexus-core'));
        }
        update_option(self::OPTION, $key, false);
        if (! empty($_POST['apply_settings'])) {
            $settings = get_option('msp_nexus_settings', array());
            $settings = is_array($settings) ? $settings : array();
            $settings['accent_color'] = $starter['accent'];
            update_option('msp_nexus_settings', $settings, false);
        }
        if (! empty($_POST['create_pages'])) {
            $this->create_pages($starter, ! empty($_POST['make_home']));
        }
        if (! empty($_POST['replace_home'])) {
            $this->replace_owned_home($starter);
        }
        if (! empty($_POST['create_navigation'])) {
            $this->create_navigation($starter);
        }
        wp_safe_redirect(add_query_arg('starter-applied', '1', admin_url('admin.php?page=msp-nexus-starters')));
        exit;
    }

    /** @param array<string, string> $starter */
    private function create_pages(array $starter, bool $make_home): void
    {
        $pages = array(
            'home' => array(__('Home', 'msp-nexus-core'), $starter['pattern']),
            'services' => array(__('Services', 'msp-nexus-core'), 'page-managed-it'),
            'cybersecurity' => array(__('Cybersecurity', 'msp-nexus-core'), 'page-cybersecurity'),
            'industries' => array($starter['vertical_name'], 'page-industry-detail'),
            'about-us' => array(__('About Us', 'msp-nexus-core'), 'page-about'),
            'contact' => array(__('Contact', 'msp-nexus-core'), 'page-contact'),
            'resources' => array(__('Resources', 'msp-nexus-core'), 'page-resources'),
        );
        foreach ($pages as $slug => $page) {
            if (get_page_by_path($slug) instanceof \WP_Post) {
                continue;
            }
            $content = '<!-- wp:pattern {"slug":"msp-nexus/' . esc_attr($page[1]) . '"} /-->';
            $id = wp_insert_post(array('post_type' => 'page', 'post_status' => 'draft', 'post_name' => $slug, 'post_title' => $page[0], 'post_content' => $content), true);
            if (! is_wp_error($id)) {
                update_post_meta((int) $id, '_msp_nexus_demo_key', 'page-' . $slug);
                update_post_meta((int) $id, '_msp_nexus_starter_kit', $starter['id']);
                if ('home' === $slug && $make_home) {
                    wp_update_post(array('ID' => (int) $id, 'post_status' => 'publish'));
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', (int) $id);
                }
            }
        }
    }

    /** @param array<string, string> $starter */
    private function replace_owned_home(array $starter): void
    {
        $home = get_page_by_path('home');
        if (! $home instanceof \WP_Post || '' === (string) get_post_meta($home->ID, '_msp_nexus_demo_key', true)) {
            return;
        }
        wp_update_post(array('ID' => $home->ID, 'post_content' => '<!-- wp:pattern {"slug":"msp-nexus/' . esc_attr($starter['pattern']) . '"} /-->'));
        update_post_meta($home->ID, '_msp_nexus_starter_kit', $starter['id']);
    }

    /** @param array<string, string> $starter */
    private function create_navigation(array $starter): void
    {
        if (! post_type_exists('wp_navigation')) {
            return;
        }
        $name = 'Nexus ' . $starter['vertical_name'] . ' ' . $starter['focus_name'];
        $existing = get_page_by_title($name, OBJECT, 'wp_navigation');
        if ($existing instanceof \WP_Post) {
            return;
        }
        $links = array('Home' => '/', 'Services' => '/services/', $starter['vertical_name'] => '/industries/', 'Resources' => '/resources/', 'About' => '/about-us/', 'Contact' => '/contact/');
        $content = '';
        foreach ($links as $label => $url) {
            $content .= '<!-- wp:navigation-link {"label":"' . esc_attr($label) . '","url":"' . esc_url($url) . '","kind":"custom"} /-->';
        }
        $id = wp_insert_post(array('post_type' => 'wp_navigation', 'post_status' => 'publish', 'post_title' => $name, 'post_content' => $content), true);
        if (! is_wp_error($id)) {
            update_post_meta((int) $id, '_msp_nexus_starter_kit', $starter['id']);
        }
    }

    /** @param mixed $theme_json
     *  @return mixed
     */
    public function theme_json($theme_json)
    {
        $starter = Catalog::get((string) get_option(self::OPTION, self::default_id()));
        if (null === $starter || ! is_object($theme_json) || ! method_exists($theme_json, 'update_with')) {
            return $theme_json;
        }
        $file = get_theme_file_path('styles/' . $starter['style'] . '.json');
        if (! is_readable($file)) {
            return $theme_json;
        }
        $data = json_decode((string) file_get_contents($file), true);
        if (is_array($data)) {
            $theme_json->update_with($data);
        }
        return $theme_json;
    }

    /** @param array<int, string> $classes
     *  @return array<int, string>
     */
    public function body_class(array $classes): array
    {
        $starter = Catalog::get((string) get_option(self::OPTION, self::default_id()));
        if (null !== $starter) {
            $classes[] = 'msp-nexus-starter-' . sanitize_html_class($starter['vertical']);
            $classes[] = 'msp-nexus-focus-' . sanitize_html_class($starter['focus']);
        }
        return $classes;
    }
}
