<?php
/**
 * Client-safe white-label presentation controls.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

final class Branding
{
    private const OPTION = 'msp_nexus_branding';

    public function register_hooks(): void
    {
        add_action('admin_init', array($this, 'register'));
        add_action('admin_menu', array($this, 'menu'));
        add_filter('admin_footer_text', array($this, 'footer'));
        add_action('login_head', array($this, 'login_style'));
        add_filter('login_headerurl', array($this, 'login_url'));
        add_filter('login_headertext', array($this, 'login_text'));
        add_action('admin_head', array($this, 'admin_style'));
        add_action('wp_dashboard_setup', array($this, 'dashboard'));
        add_action('admin_bar_menu', array($this, 'admin_bar'), 90);
        add_filter('wp_mail_from_name', array($this, 'mail_name'));
        add_filter('wp_mail_from', array($this, 'mail_address'));
        add_filter('all_plugins', array($this, 'plugin_identity'));
    }

    public function register(): void
    {
        register_setting('msp_nexus_branding', self::OPTION, array('type' => 'object', 'default' => array(), 'sanitize_callback' => array($this, 'sanitize')));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('Nexus client branding', 'msp-nexus-core'), __('White label', 'msp-nexus-core'), 'manage_options', 'msp-nexus-branding', array($this, 'render'));
    }

    /** @param mixed $value
     *  @return array<string, mixed>
     */
    public function sanitize($value): array
    {
        if (! is_array($value)) {
            return array();
        }
        return array(
            'client_mode' => ! empty($value['client_mode']),
            'product_name' => sanitize_text_field((string) ($value['product_name'] ?? '')),
            'partner_name' => sanitize_text_field((string) ($value['partner_name'] ?? '')),
            'support_url' => esc_url_raw((string) ($value['support_url'] ?? '')),
            'logo_url' => esc_url_raw((string) ($value['logo_url'] ?? '')),
            'admin_logo_url' => esc_url_raw((string) ($value['admin_logo_url'] ?? '')),
            'login_background_url' => esc_url_raw((string) ($value['login_background_url'] ?? '')),
            'accent_color' => sanitize_hex_color((string) ($value['accent_color'] ?? '')) ?: '#2271b1',
            'dashboard_message' => wp_kses_post((string) ($value['dashboard_message'] ?? '')),
            'email_from_name' => sanitize_text_field((string) ($value['email_from_name'] ?? '')),
            'email_from_address' => sanitize_email((string) ($value['email_from_address'] ?? '')),
            'hide_wp_news' => ! empty($value['hide_wp_news']),
            'show_support_widget' => ! empty($value['show_support_widget']),
        );
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        $value = get_option(self::OPTION, array());
        ?>
        <div class="wrap"><h1><?php esc_html_e('Nexus white-label controls', 'msp-nexus-core'); ?></h1><p><?php esc_html_e('Present a client-facing support identity without hiding WordPress security, update, privacy, or license state.', 'msp-nexus-core'); ?></p><form action="options.php" method="post"><?php settings_fields('msp_nexus_branding'); ?><table class="form-table" role="presentation">
        <tr><th scope="row"><?php esc_html_e('Client mode', 'msp-nexus-core'); ?></th><td><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[client_mode]" value="1" <?php checked(! empty($value['client_mode'])); ?>> <?php esc_html_e('Use partner branding on the login and admin footer', 'msp-nexus-core'); ?></label></td></tr>
        <?php $this->field('product_name', __('Client-facing product name', 'msp-nexus-core'), $value); $this->field('partner_name', __('Support partner name', 'msp-nexus-core'), $value); $this->field('support_url', __('Support URL', 'msp-nexus-core'), $value, 'url'); $this->field('logo_url', __('Login logo URL', 'msp-nexus-core'), $value, 'url'); $this->field('admin_logo_url', __('Admin-bar logo URL', 'msp-nexus-core'), $value, 'url'); $this->field('login_background_url', __('Login background image URL', 'msp-nexus-core'), $value, 'url'); $this->field('accent_color', __('Admin and login accent color', 'msp-nexus-core'), $value, 'color'); $this->field('email_from_name', __('Transactional email sender name', 'msp-nexus-core'), $value); $this->field('email_from_address', __('Transactional email sender address', 'msp-nexus-core'), $value, 'email'); ?>
        <tr><th scope="row"><label for="msp-brand-dashboard_message"><?php esc_html_e('Dashboard welcome message', 'msp-nexus-core'); ?></label></th><td><textarea class="large-text" rows="4" id="msp-brand-dashboard_message" name="<?php echo esc_attr(self::OPTION . '[dashboard_message]'); ?>"><?php echo esc_textarea((string) ($value['dashboard_message'] ?? '')); ?></textarea></td></tr>
        <tr><th scope="row"><?php esc_html_e('Client dashboard', 'msp-nexus-core'); ?></th><td><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[show_support_widget]" value="1" <?php checked(! array_key_exists('show_support_widget', $value) || ! empty($value['show_support_widget'])); ?>> <?php esc_html_e('Show a branded support widget', 'msp-nexus-core'); ?></label><br><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[hide_wp_news]" value="1" <?php checked(! empty($value['hide_wp_news'])); ?>> <?php esc_html_e('Hide only the WordPress Events and News widget', 'msp-nexus-core'); ?></label></td></tr>
        </table><?php submit_button(); ?></form></div>
        <?php
    }

    /** @param array<string, mixed> $value */
    private function field(string $name, string $label, array $value, string $type = 'text'): void
    {
        ?><tr><th scope="row"><label for="msp-brand-<?php echo esc_attr($name); ?>"><?php echo esc_html($label); ?></label></th><td><input class="regular-text" id="msp-brand-<?php echo esc_attr($name); ?>" name="<?php echo esc_attr(self::OPTION . '[' . $name . ']'); ?>" type="<?php echo esc_attr($type); ?>" value="<?php echo esc_attr((string) ($value[$name] ?? '')); ?>"></td></tr><?php
    }

    /** @param mixed $text
     *  @return mixed
     */
    public function footer($text)
    {
        $value = get_option(self::OPTION, array());
        if (! is_array($value) || empty($value['client_mode']) || '' === trim((string) ($value['partner_name'] ?? ''))) {
            return $text;
        }
        $label = sprintf(__('Technology support by %s', 'msp-nexus-core'), (string) $value['partner_name']);
        return ! empty($value['support_url']) ? '<a href="' . esc_url((string) $value['support_url']) . '">' . esc_html($label) . '</a>' : esc_html($label);
    }

    public function login_style(): void
    {
        $value = get_option(self::OPTION, array());
        if (! is_array($value) || empty($value['client_mode'])) {
            return;
        }
        $accent = sanitize_hex_color((string) ($value['accent_color'] ?? '')) ?: '#2271b1';
        $logo = esc_url((string) ($value['logo_url'] ?? ''));
        $background = esc_url((string) ($value['login_background_url'] ?? ''));
        echo '<style>body.login{background:#f4f6f9' . ($background ? ' url(' . $background . ') center/cover fixed' : '') . '}body.login .button-primary{background:' . esc_attr($accent) . ';border-color:' . esc_attr($accent) . '}body.login #login h1 a{' . ($logo ? 'background-image:url(' . esc_url($logo) . ');background-size:contain;width:100%;' : '') . '}body.login form{border-radius:8px;box-shadow:0 18px 55px rgba(0,0,0,.18)}</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function login_url(): string
    {
        $value = get_option(self::OPTION, array());
        return is_array($value) && ! empty($value['client_mode']) && ! empty($value['support_url']) ? (string) $value['support_url'] : home_url('/');
    }

    public function login_text(): string
    {
        $value = get_option(self::OPTION, array());
        if (is_array($value) && ! empty($value['client_mode']) && ! empty($value['product_name'])) {
            return (string) $value['product_name'];
        }
        return get_bloginfo('name');
    }

    public function admin_style(): void
    {
        $value = $this->settings();
        if (empty($value['client_mode'])) {
            return;
        }
        $accent = sanitize_hex_color((string) ($value['accent_color'] ?? '')) ?: '#2271b1';
        echo '<style>:root{--msp-client-accent:' . esc_attr($accent) . '}#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,#adminmenu .wp-menu-arrow,#adminmenu .wp-menu-arrow div,#adminmenu li.current a.menu-top,#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu{background:var(--msp-client-accent)}.wp-core-ui .button-primary{background:var(--msp-client-accent);border-color:var(--msp-client-accent)}</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function dashboard(): void
    {
        $value = $this->settings();
        if (empty($value['client_mode'])) {
            return;
        }
        if (! empty($value['hide_wp_news'])) {
            remove_meta_box('dashboard_primary', 'dashboard', 'side');
        }
        if (! empty($value['show_support_widget'])) {
            wp_add_dashboard_widget('msp_nexus_client_support', (string) ($value['product_name'] ?: __('Technology support', 'msp-nexus-core')), array($this, 'dashboard_widget'));
        }
    }

    public function dashboard_widget(): void
    {
        $value = $this->settings();
        echo wp_kses_post(wpautop((string) ($value['dashboard_message'] ?: __('Need help or planning guidance? Use the support route below to reach the responsible team.', 'msp-nexus-core'))));
        if (! empty($value['support_url'])) {
            echo '<p><a class="button button-primary" href="' . esc_url((string) $value['support_url']) . '">' . esc_html__('Open support', 'msp-nexus-core') . '</a></p>';
        }
    }

    /** @param \WP_Admin_Bar $bar */
    public function admin_bar($bar): void
    {
        $value = $this->settings();
        if (empty($value['client_mode']) || ! is_object($bar)) {
            return;
        }
        $bar->remove_node('wp-logo');
        $logo = esc_url((string) ($value['admin_logo_url'] ?? ''));
        $title = $logo ? '<img src="' . $logo . '" alt="" style="height:20px;margin-top:6px;width:auto">' : esc_html((string) ($value['product_name'] ?: get_bloginfo('name')));
        $bar->add_node(array('id' => 'msp-client-brand', 'title' => $title, 'href' => esc_url((string) ($value['support_url'] ?: home_url('/'))), 'meta' => array('title' => (string) ($value['product_name'] ?: get_bloginfo('name')))));
    }

    public function mail_name(string $name): string
    {
        $value = $this->settings();
        return ! empty($value['client_mode']) && ! empty($value['email_from_name']) ? (string) $value['email_from_name'] : $name;
    }

    public function mail_address(string $address): string
    {
        $value = $this->settings();
        return ! empty($value['client_mode']) && is_email((string) ($value['email_from_address'] ?? '')) ? (string) $value['email_from_address'] : $address;
    }

    /** @param array<string, array<string, string>> $plugins
     *  @return array<string, array<string, string>>
     */
    public function plugin_identity(array $plugins): array
    {
        $value = $this->settings();
        $key = plugin_basename(MSP_NEXUS_CORE_FILE);
        if (! empty($value['client_mode']) && ! empty($value['product_name']) && isset($plugins[$key])) {
            $plugins[$key]['Name'] = (string) $value['product_name'];
            $plugins[$key]['Title'] = (string) $value['product_name'];
            $plugins[$key]['Author'] = (string) ($value['partner_name'] ?: $plugins[$key]['Author']);
            $plugins[$key]['AuthorName'] = $plugins[$key]['Author'];
        }
        return $plugins;
    }

    /** @return array<string, mixed> */
    private function settings(): array
    {
        $value = get_option(self::OPTION, array());
        return is_array($value) ? $value : array();
    }
}
