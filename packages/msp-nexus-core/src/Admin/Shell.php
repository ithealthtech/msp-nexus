<?php
/**
 * Shared admin shell for every MSP Nexus screen: branded top bar, navigation, page header,
 * card styling, and a per-user light or dark scheme.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

use MspNexusCore\Content\Registrar;
use MspNexusCore\Studio\Layouts;

final class Shell
{
    private const SCHEME_META = 'msp_nexus_ui_scheme';
    private const SCHEME_ACTION = 'msp_nexus_ui_scheme';

    public function register_hooks(): void
    {
        add_action('admin_enqueue_scripts', array($this, 'assets'));
        add_filter('admin_body_class', array($this, 'body_class'));
        add_action('in_admin_header', array($this, 'render'));
        add_action('wp_ajax_' . self::SCHEME_ACTION, array($this, 'save_scheme'));
    }

    /** True on the plugin's own admin pages and the Nexus layout screens. */
    public static function is_nexus_screen(): bool
    {
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash((string) $_GET['page'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ('' !== $page) {
            return 0 === strpos($page, 'msp-nexus');
        }
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        // The layout list gets the shell; the block editor for a single layout stays full screen.
        return $screen && Layouts::POST_TYPE === $screen->post_type && 'edit' === $screen->base;
    }

    public function assets(): void
    {
        if (! self::is_nexus_screen()) {
            return;
        }
        wp_enqueue_style('msp-nexus-admin-shell', plugins_url('assets/admin-shell.css', MSP_NEXUS_CORE_FILE), array(), MSP_NEXUS_CORE_VERSION);
        wp_enqueue_script('msp-nexus-admin-shell', plugins_url('assets/admin-shell.js', MSP_NEXUS_CORE_FILE), array(), MSP_NEXUS_CORE_VERSION, true);
        wp_localize_script('msp-nexus-admin-shell', 'mspNexusShell', array(
            'ajaxUrl' => admin_url('admin-ajax.php', 'relative'),
            'action' => self::SCHEME_ACTION,
            'nonce' => wp_create_nonce(self::SCHEME_ACTION),
        ));
    }

    public function body_class(string $classes): string
    {
        if (! self::is_nexus_screen()) {
            return $classes;
        }
        return $classes . ' msp-nexus-ui msp-nexus-ui--' . self::scheme();
    }

    public function save_scheme(): void
    {
        check_ajax_referer(self::SCHEME_ACTION, 'nonce');
        if (! current_user_can('read')) {
            wp_send_json_error(null, 403);
        }
        $scheme = 'dark' === sanitize_key(wp_unslash((string) ($_POST['scheme'] ?? ''))) ? 'dark' : 'light';
        update_user_meta(get_current_user_id(), self::SCHEME_META, $scheme);
        wp_send_json_success(array('scheme' => $scheme));
    }

    /** The user's saved choice, or auto (follow the operating system) until they pick one. */
    private static function scheme(): string
    {
        $saved = (string) get_user_meta(get_current_user_id(), self::SCHEME_META, true);
        return in_array($saved, array('light', 'dark'), true) ? $saved : 'auto';
    }

    public function render(): void
    {
        if (! self::is_nexus_screen()) {
            return;
        }
        $current = $this->current_slug();
        $primary = array_filter($this->primary_items(), array($this, 'allowed'));
        $tools = array_filter($this->tool_items(), array($this, 'allowed'));
        $tools_active = in_array($current, array_column($tools, 'slug'), true);
        $brand = $this->brand();
        $title = $this->title($current);
        ?>
        <div class="msp-nexus-shell" data-scheme="<?php echo esc_attr(self::scheme()); ?>">
            <div class="msp-nexus-shell__bar">
                <a class="msp-nexus-shell__brand" href="<?php echo esc_url(admin_url('admin.php?page=msp-nexus')); ?>">
                    <?php if ('' !== $brand['logo']) : ?>
                        <img class="msp-nexus-shell__logo-img" src="<?php echo esc_url($brand['logo']); ?>" alt="">
                    <?php else : ?>
                        <span class="msp-nexus-shell__mark" aria-hidden="true"></span>
                    <?php endif; ?>
                    <span class="msp-nexus-shell__name"><?php echo esc_html($brand['name']); ?></span>
                    <span class="msp-nexus-shell__version">v<?php echo esc_html(MSP_NEXUS_CORE_VERSION); ?></span>
                </a>
                <nav class="msp-nexus-shell__nav" aria-label="<?php esc_attr_e('MSP Nexus', 'msp-nexus-core'); ?>">
                    <ul class="msp-nexus-shell__menu">
                        <?php foreach ($primary as $item) : ?>
                            <li><a class="msp-nexus-shell__tab<?php echo $item['slug'] === $current ? ' is-active' : ''; ?>" href="<?php echo esc_url($item['url']); ?>"<?php echo $item['slug'] === $current ? ' aria-current="page"' : ''; ?>><?php self::icon($item['icon']); ?><span><?php echo esc_html($item['label']); ?></span></a></li>
                        <?php endforeach; ?>
                        <?php if ($tools) : ?>
                            <li class="msp-nexus-shell__more">
                                <button type="button" class="msp-nexus-shell__tab<?php echo $tools_active ? ' is-active' : ''; ?>" aria-expanded="false" aria-controls="msp-nexus-shell-tools"><?php self::icon('tools'); ?><span><?php esc_html_e('Tools', 'msp-nexus-core'); ?></span><?php self::icon('chevron'); ?></button>
                                <ul class="msp-nexus-shell__dropdown" id="msp-nexus-shell-tools" hidden>
                                    <?php foreach ($tools as $item) : ?>
                                        <li><a class="msp-nexus-shell__tool<?php echo $item['slug'] === $current ? ' is-active' : ''; ?>" href="<?php echo esc_url($item['url']); ?>"<?php echo $item['slug'] === $current ? ' aria-current="page"' : ''; ?>><?php self::icon($item['icon']); ?><span class="msp-nexus-shell__tool-text"><span class="msp-nexus-shell__tool-label"><?php echo esc_html($item['label']); ?></span><span class="msp-nexus-shell__tool-desc"><?php echo esc_html($item['desc']); ?></span></span></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="msp-nexus-shell__actions">
                    <a class="msp-nexus-shell__icon-btn" href="<?php echo esc_url(home_url('/')); ?>" target="_blank" rel="noopener"><?php self::icon('external'); ?><span class="screen-reader-text"><?php esc_html_e('View site (opens in a new tab)', 'msp-nexus-core'); ?></span></a>
                    <button type="button" class="msp-nexus-shell__icon-btn msp-nexus-shell__scheme" aria-pressed="<?php echo 'dark' === self::scheme() ? 'true' : 'false'; ?>"><?php self::icon('moon'); ?><?php self::icon('sun'); ?><span class="screen-reader-text"><?php esc_html_e('Dark mode', 'msp-nexus-core'); ?></span></button>
                </div>
            </div>
            <div class="msp-nexus-shell__sub">
                <ol class="msp-nexus-shell__crumbs">
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=msp-nexus')); ?>"><?php echo esc_html($brand['name']); ?></a></li>
                    <?php if ('msp-nexus' !== $current && '' !== $title) : ?><li aria-current="page"><?php echo esc_html($title); ?></li><?php endif; ?>
                </ol>
            </div>
        </div>
        <?php
    }

    /** @param array{cap:string} $item */
    private function allowed(array $item): bool
    {
        return current_user_can($item['cap']);
    }

    private function current_slug(): string
    {
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash((string) $_GET['page'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return '' !== $page ? $page : 'layouts';
    }

    private function title(string $slug): string
    {
        if (Registrar::MENU_SLUG === $slug) {
            return __('Company content', 'msp-nexus-core');
        }
        foreach (array_merge($this->primary_items(), $this->tool_items()) as $item) {
            if ($item['slug'] === $slug) {
                return $item['label'];
            }
        }
        return '';
    }

    /** @return array{name:string,logo:string} */
    private function brand(): array
    {
        $branding = get_option('msp_nexus_branding', array());
        $branding = is_array($branding) ? $branding : array();
        $client = ! empty($branding['client_mode']);
        $name = $client && '' !== (string) ($branding['product_name'] ?? '') ? (string) $branding['product_name'] : __('MSP Nexus', 'msp-nexus-core');
        $logo = $client ? (string) ($branding['admin_logo_url'] ?? '') : '';
        return array('name' => $name, 'logo' => $logo);
    }

    private function page_url(string $slug): string
    {
        return admin_url('admin.php?page=' . $slug);
    }

    /** @return array<int, array{slug:string,label:string,icon:string,url:string,cap:string}> */
    private function primary_items(): array
    {
        return array(
            array('slug' => 'msp-nexus', 'label' => __('Dashboard', 'msp-nexus-core'), 'icon' => 'dashboard', 'url' => $this->page_url('msp-nexus'), 'cap' => 'manage_options'),
            array('slug' => 'msp-nexus-settings', 'label' => __('Settings', 'msp-nexus-core'), 'icon' => 'settings', 'url' => $this->page_url('msp-nexus-settings'), 'cap' => 'manage_options'),
            array('slug' => 'msp-nexus-starters', 'label' => __('Starter sites', 'msp-nexus-core'), 'icon' => 'starters', 'url' => $this->page_url('msp-nexus-starters'), 'cap' => 'manage_options'),
            array('slug' => 'msp-nexus-studio', 'label' => __('Studio', 'msp-nexus-core'), 'icon' => 'studio', 'url' => $this->page_url('msp-nexus-studio'), 'cap' => 'edit_theme_options'),
            array('slug' => 'layouts', 'label' => __('Layouts', 'msp-nexus-core'), 'icon' => 'layouts', 'url' => admin_url('edit.php?post_type=' . Layouts::POST_TYPE), 'cap' => 'edit_theme_options'),
        );
    }

    /** @return array<int, array{slug:string,label:string,desc:string,icon:string,url:string,cap:string}> */
    private function tool_items(): array
    {
        $items = array(
            array('slug' => 'msp-nexus-setup', 'label' => __('Guided setup', 'msp-nexus-core'), 'desc' => __('Import starter content safely', 'msp-nexus-core'), 'icon' => 'setup', 'cap' => 'manage_options'),
            array('slug' => 'msp-nexus-portability', 'label' => __('Import / export', 'msp-nexus-core'), 'desc' => __('Move settings between sites', 'msp-nexus-core'), 'icon' => 'transfer', 'cap' => 'manage_options'),
            array('slug' => 'msp-nexus-performance', 'label' => __('Performance', 'msp-nexus-core'), 'desc' => __('Speed checks and safe tweaks', 'msp-nexus-core'), 'icon' => 'gauge', 'cap' => 'manage_options'),
            array('slug' => 'msp-nexus-privacy', 'label' => __('Privacy & consent', 'msp-nexus-core'), 'desc' => __('Cookie choices and scripts', 'msp-nexus-core'), 'icon' => 'lock', 'cap' => 'manage_privacy_options'),
            array('slug' => 'msp-nexus-integrations', 'label' => __('Integrations', 'msp-nexus-core'), 'desc' => __('Forms, SEO, and other plugins', 'msp-nexus-core'), 'icon' => 'plug', 'cap' => 'manage_options'),
            array('slug' => 'msp-nexus-branding', 'label' => __('White label', 'msp-nexus-core'), 'desc' => __('Client-facing admin branding', 'msp-nexus-core'), 'icon' => 'tag', 'cap' => 'manage_options'),
        );
        if (class_exists('WooCommerce')) {
            $items[] = array('slug' => 'msp-nexus-woocommerce', 'label' => __('WooCommerce', 'msp-nexus-core'), 'desc' => __('Store presentation', 'msp-nexus-core'), 'icon' => 'cart', 'cap' => 'manage_woocommerce');
        }
        $items[] = array('slug' => 'msp-nexus-license', 'label' => __('License & updates', 'msp-nexus-core'), 'desc' => __('Activation and update channel', 'msp-nexus-core'), 'icon' => 'key', 'cap' => 'update_plugins');
        $items[] = array('slug' => 'msp-nexus-diagnostics', 'label' => __('Diagnostics', 'msp-nexus-core'), 'desc' => __('System status and repairs', 'msp-nexus-core'), 'icon' => 'pulse', 'cap' => 'manage_options');
        $items[] = array('slug' => 'msp-nexus-reset', 'label' => __('Advanced reset', 'msp-nexus-core'), 'desc' => __('Remove imported demo content', 'msp-nexus-core'), 'icon' => 'reset', 'cap' => 'manage_options');
        foreach ($items as &$item) {
            $item['url'] = $this->page_url($item['slug']);
        }
        unset($item);
        return $items;
    }

    /** Prints one of the shell's line icons. */
    public static function icon(string $name): void
    {
        $paths = array(
            'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
            'settings' => '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
            'starters' => '<path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 13l9 5 9-5"/>',
            'studio' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/>',
            'layouts' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
            'tools' => '<path d="M14.7 6.3a4 4 0 00-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 005.4-5.4l-2.6 2.6-2.4-.6-.6-2.4z"/>',
            'chevron' => '<path d="M6 9l6 6 6-6"/>',
            'setup' => '<path d="M5 13l4 4L19 7"/>',
            'transfer' => '<path d="M7 7h13l-4-4M17 17H4l4 4"/>',
            'gauge' => '<path d="M12 14l4-4"/><path d="M3.3 17a9 9 0 1117.4 0"/>',
            'lock' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>',
            'plug' => '<path d="M9 2v6M15 2v6M6 8h12v3a6 6 0 01-12 0zM12 17v5"/>',
            'tag' => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 01-2.8 0L3 13V3h10l7.6 7.6a2 2 0 010 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
            'cart' => '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 12.4a2 2 0 002 1.6h8.6a2 2 0 002-1.6L22 7H6"/>',
            'key' => '<circle cx="7.5" cy="15.5" r="4.5"/><path d="M10.7 12.3L21 2M17 6l3 3M14 9l2 2"/>',
            'pulse' => '<path d="M3 12h4l3-8 4 16 3-8h4"/>',
            'reset' => '<path d="M3 12a9 9 0 103-6.7L3 8"/><path d="M3 3v5h5"/>',
            'external' => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h5"/>',
            'moon' => '<path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/>',
            'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
            'check' => '<path d="M5 13l4 4L19 7"/>',
            'alert' => '<path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/>',
            'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/>',
            'content' => '<path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>',
            'book' => '<path d="M4 19.5A2.5 2.5 0 016.5 17H20V3H6.5A2.5 2.5 0 004 5.5z"/><path d="M4 19.5A2.5 2.5 0 006.5 22H20v-5"/>',
            'help' => '<circle cx="12" cy="12" r="9"/><path d="M9.1 9a3 3 0 015.8 1c0 2-3 3-3 3M12 17h.01"/>',
            'refresh' => '<path d="M21 12a9 9 0 01-15.5 6.3L3 16M3 12a9 9 0 0115.5-6.3L21 8"/><path d="M21 3v5h-5M3 21v-5h5"/>',
            'building' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2M10 21v-3h4v3"/>',
            'phone' => '<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.4 1.8.7 2.7a2 2 0 01-.5 2.1L8 9.8a16 16 0 006 6l1.3-1.3a2 2 0 012.1-.5c.9.3 1.8.6 2.7.7a2 2 0 011.7 2z"/>',
            'share' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/>',
            'megaphone' => '<path d="M3 11v3a1 1 0 001 1h3l6 4V6L7 10H4a1 1 0 00-1 1z"/><path d="M17 8a5 5 0 010 8"/>',
            'palette' => '<path d="M12 3a9 9 0 000 18c1.1 0 1.7-.8 1.7-1.7 0-.4-.2-.8-.4-1.1-.3-.3-.4-.7-.4-1.1 0-.9.8-1.7 1.7-1.7H17a4 4 0 004-4c0-4.6-4-8.4-9-8.4z"/><circle cx="7.5" cy="11.5" r="1"/><circle cx="10.5" cy="7.5" r="1"/><circle cx="15" cy="7.5" r="1"/>',
            'search' => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
        );
        if (! isset($paths[$name])) {
            return;
        }
        echo '<svg class="msp-nexus-icon msp-nexus-icon--' . esc_attr($name) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[$name] . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup above.
    }
}
