<?php
/**
 * Performance diagnostics and conservative feature toggles.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

final class Performance
{
    private const OPTION = 'msp_nexus_performance';

    public function register_hooks(): void
    {
        add_action('admin_init', array($this, 'register'));
        add_action('admin_menu', array($this, 'menu'));
        add_action('init', array($this, 'apply'), 20);
    }

    public function register(): void
    {
        register_setting('msp_nexus_performance', self::OPTION, array('type' => 'object', 'default' => array(), 'sanitize_callback' => array($this, 'sanitize')));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('Nexus performance', 'msp-nexus-core'), __('Performance', 'msp-nexus-core'), 'manage_options', 'msp-nexus-performance', array($this, 'render'));
    }

    /** @param mixed $value
     *  @return array<string, mixed>
     */
    public function sanitize($value): array
    {
        if (! is_array($value)) {
            return array();
        }
        $format = sanitize_key((string) ($value['image_format'] ?? 'preserve'));
        return array(
            'disable_emojis' => ! empty($value['disable_emojis']),
            'disable_embeds' => ! empty($value['disable_embeds']),
            'lazy_iframes' => ! empty($value['lazy_iframes']),
            'lazy_images' => ! empty($value['lazy_images']),
            'preload_featured' => ! empty($value['preload_featured']),
            'disable_dashicons_guests' => ! empty($value['disable_dashicons_guests']),
            'image_format' => in_array($format, array('preserve', 'webp', 'avif'), true) ? $format : 'preserve',
            'image_quality' => min(95, max(60, absint($value['image_quality'] ?? 82))),
            'heartbeat' => min(120, max(15, absint($value['heartbeat'] ?? 60))),
            'preconnect' => $this->sanitize_lines((string) ($value['preconnect'] ?? ''), true),
            'unload_styles' => $this->sanitize_lines((string) ($value['unload_styles'] ?? ''), false),
            'unload_scripts' => $this->sanitize_lines((string) ($value['unload_scripts'] ?? ''), false),
        );
    }

    public function apply(): void
    {
        $settings = get_option(self::OPTION, array());
        if (! is_array($settings)) {
            return;
        }
        if (! empty($settings['disable_emojis'])) {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
        }
        if (! empty($settings['disable_embeds'])) {
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
        }
        if (! empty($settings['lazy_iframes'])) {
            add_filter('wp_lazy_loading_enabled', '__return_true');
        }
        if (! empty($settings['lazy_images'])) {
            add_filter('wp_get_attachment_image_attributes', array($this, 'lazy_image_attributes'), 20, 3);
        }
        if ('preserve' !== ($settings['image_format'] ?? 'preserve')) {
            add_filter('image_editor_output_format', array($this, 'image_formats'));
            add_filter('wp_editor_set_quality', array($this, 'image_quality'), 10, 2);
        }
        if (! empty($settings['disable_dashicons_guests'])) {
            add_action('wp_enqueue_scripts', array($this, 'dashicons'), 100);
        }
        if (! empty($settings['preload_featured'])) {
            add_action('wp_head', array($this, 'preload_featured'), 2);
        }
        if (! empty($settings['preconnect'])) {
            add_filter('wp_resource_hints', array($this, 'resource_hints'), 10, 2);
        }
        if (! empty($settings['unload_styles']) || ! empty($settings['unload_scripts'])) {
            add_action('wp_enqueue_scripts', array($this, 'unload_assets'), 999);
        }
        add_filter('heartbeat_settings', array($this, 'heartbeat'));
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        global $wpdb;
        $settings = get_option(self::OPTION, array());
        $autoload = (int) $wpdb->get_var("SELECT SUM(LENGTH(option_value)) FROM {$wpdb->options} WHERE autoload IN ('yes','on','auto-on')"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $checks = array(
            array(__('Page cache constant', 'msp-nexus-core'), defined('WP_CACHE') && WP_CACHE, __('Enable a reviewed full-page cache at the host or caching plugin.', 'msp-nexus-core')),
            array(__('Persistent object cache', 'msp-nexus-core'), wp_using_ext_object_cache(), __('Useful for dynamic or high-traffic sites when supported by the host.', 'msp-nexus-core')),
            array(__('WebP generation', 'msp-nexus-core'), function_exists('wp_image_editor_supports') && wp_image_editor_supports(array('mime_type' => 'image/webp')), __('Ask the host to enable a compatible GD or Imagick build.', 'msp-nexus-core')),
            array(__('Autoloaded options under 1 MB', 'msp-nexus-core'), $autoload < MB_IN_BYTES, sprintf(__('Current estimated autoload payload: %s.', 'msp-nexus-core'), size_format($autoload))),
            array(__('Production debug display disabled', 'msp-nexus-core'), ! (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY), __('Disable visible PHP debug output on production.', 'msp-nexus-core')),
        );
        ?>
        <div class="wrap"><h1><?php esc_html_e('Nexus performance console', 'msp-nexus-core'); ?></h1><p><?php esc_html_e('Read-only environment checks plus conservative front-end toggles. Image conversion, cache purges, and database cleanup remain with your host or dedicated performance tooling.', 'msp-nexus-core'); ?></p>
        <table class="widefat striped" style="max-width:900px"><thead><tr><th><?php esc_html_e('Check', 'msp-nexus-core'); ?></th><th><?php esc_html_e('Status', 'msp-nexus-core'); ?></th><th><?php esc_html_e('Guidance', 'msp-nexus-core'); ?></th></tr></thead><tbody><?php foreach ($checks as $check) : ?><tr><th><?php echo esc_html($check[0]); ?></th><td><strong style="color:<?php echo $check[1] ? '#16833b' : '#a54800'; ?>"><?php echo $check[1] ? esc_html__('Pass', 'msp-nexus-core') : esc_html__('Review', 'msp-nexus-core'); ?></strong></td><td><?php echo esc_html($check[2]); ?></td></tr><?php endforeach; ?></tbody></table>
        <h2><?php esc_html_e('Conservative optimizations', 'msp-nexus-core'); ?></h2><form action="options.php" method="post"><?php settings_fields('msp_nexus_performance'); ?>
        <?php foreach (array('disable_emojis' => __('Disable legacy WordPress emoji scripts and styles', 'msp-nexus-core'), 'disable_embeds' => __('Disable oEmbed discovery and host scripts', 'msp-nexus-core'), 'lazy_iframes' => __('Allow native lazy loading for supported iframes', 'msp-nexus-core'), 'lazy_images' => __('Enforce native lazy loading on non-priority Media Library images', 'msp-nexus-core'), 'preload_featured' => __('Preload the singular-page featured image', 'msp-nexus-core'), 'disable_dashicons_guests' => __('Remove Dashicons for signed-out visitors', 'msp-nexus-core')) as $key => $label) : ?><p><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION . '[' . $key . ']'); ?>" value="1" <?php checked(! empty($settings[$key])); ?>> <?php echo esc_html($label); ?></label></p><?php endforeach; ?>
        <table class="form-table" role="presentation"><tr><th scope="row"><label for="msp-performance-format"><?php esc_html_e('Generated image format', 'msp-nexus-core'); ?></label></th><td><select id="msp-performance-format" name="<?php echo esc_attr(self::OPTION); ?>[image_format]"><option value="preserve" <?php selected($settings['image_format'] ?? 'preserve', 'preserve'); ?>><?php esc_html_e('Preserve source format', 'msp-nexus-core'); ?></option><option value="webp" <?php selected($settings['image_format'] ?? '', 'webp'); ?>>WebP</option><option value="avif" <?php selected($settings['image_format'] ?? '', 'avif'); ?>>AVIF</option></select><p class="description"><?php esc_html_e('Applies to newly generated image sizes when the server image editor supports the selected format.', 'msp-nexus-core'); ?></p></td></tr>
        <tr><th scope="row"><label for="msp-performance-quality"><?php esc_html_e('Generated image quality', 'msp-nexus-core'); ?></label></th><td><input id="msp-performance-quality" min="60" max="95" type="number" name="<?php echo esc_attr(self::OPTION); ?>[image_quality]" value="<?php echo esc_attr((string) ($settings['image_quality'] ?? 82)); ?>"></td></tr>
        <tr><th scope="row"><label for="msp-performance-heartbeat"><?php esc_html_e('Editor heartbeat interval', 'msp-nexus-core'); ?></label></th><td><input id="msp-performance-heartbeat" min="15" max="120" type="number" name="<?php echo esc_attr(self::OPTION); ?>[heartbeat]" value="<?php echo esc_attr((string) ($settings['heartbeat'] ?? 60)); ?>"> <?php esc_html_e('seconds', 'msp-nexus-core'); ?></td></tr>
        <?php $this->textarea('preconnect', __('Preconnect origins', 'msp-nexus-core'), $settings, __('One full HTTPS origin per line.', 'msp-nexus-core')); $this->textarea('unload_styles', __('Front-end style handles to unload', 'msp-nexus-core'), $settings, __('Advanced: one registered handle per line. Test every affected page.', 'msp-nexus-core')); $this->textarea('unload_scripts', __('Front-end script handles to unload', 'msp-nexus-core'), $settings, __('Advanced: one registered handle per line. Never unload security, consent, cart, or form dependencies without testing.', 'msp-nexus-core')); ?>
        </table><?php submit_button(); ?></form></div>
        <?php
    }

    /** @param array<string, string> $attributes
     *  @param \WP_Post $attachment
     *  @param string|array<int, int> $size
     *  @return array<string, string>
     */
    public function lazy_image_attributes(array $attributes, $attachment, $size): array
    {
        unset($attachment, $size);
        if (($attributes['fetchpriority'] ?? '') !== 'high') {
            $attributes['loading'] = 'lazy';
            $attributes['decoding'] = 'async';
        }
        return $attributes;
    }

    /** @param array<string, string> $formats
     *  @return array<string, string>
     */
    public function image_formats(array $formats): array
    {
        $settings = get_option(self::OPTION, array());
        $format = (string) (is_array($settings) ? ($settings['image_format'] ?? 'preserve') : 'preserve');
        $mime = 'avif' === $format ? 'image/avif' : 'image/webp';
        if (function_exists('wp_image_editor_supports') && wp_image_editor_supports(array('mime_type' => $mime))) {
            $formats['image/jpeg'] = $mime;
            $formats['image/png'] = $mime;
        }
        return $formats;
    }

    public function image_quality(int $quality, string $mime_type): int
    {
        unset($quality, $mime_type);
        $settings = get_option(self::OPTION, array());
        return min(95, max(60, absint(is_array($settings) ? ($settings['image_quality'] ?? 82) : 82)));
    }

    public function dashicons(): void
    {
        if (! is_user_logged_in()) {
            wp_dequeue_style('dashicons');
        }
    }

    public function preload_featured(): void
    {
        if (! is_singular() || ! has_post_thumbnail()) {
            return;
        }
        $url = wp_get_attachment_image_url((int) get_post_thumbnail_id(), 'large');
        if ($url) {
            echo '<link rel="preload" as="image" href="' . esc_url($url) . '" fetchpriority="high">' . "\n";
        }
    }

    /** @param array<int, string|array<string, string>> $urls
     *  @return array<int, string|array<string, string>>
     */
    public function resource_hints(array $urls, string $relation_type): array
    {
        if ('preconnect' !== $relation_type) {
            return $urls;
        }
        $settings = get_option(self::OPTION, array());
        $origins = is_array($settings) ? preg_split('/\r\n|\r|\n/', (string) ($settings['preconnect'] ?? '')) : array();
        return array_values(array_unique(array_merge($urls, is_array($origins) ? array_filter($origins) : array())));
    }

    public function unload_assets(): void
    {
        $settings = get_option(self::OPTION, array());
        if (! is_array($settings)) {
            return;
        }
        foreach (preg_split('/\r\n|\r|\n/', (string) ($settings['unload_styles'] ?? '')) ?: array() as $handle) {
            wp_dequeue_style(sanitize_key($handle));
        }
        foreach (preg_split('/\r\n|\r|\n/', (string) ($settings['unload_scripts'] ?? '')) ?: array() as $handle) {
            wp_dequeue_script(sanitize_key($handle));
        }
    }

    /** @param array<string, mixed> $settings
     *  @return array<string, mixed>
     */
    public function heartbeat(array $settings): array
    {
        $option = get_option(self::OPTION, array());
        $settings['interval'] = min(120, max(15, absint(is_array($option) ? ($option['heartbeat'] ?? 60) : 60)));
        return $settings;
    }

    /** @param array<string, mixed> $settings */
    private function textarea(string $key, string $label, array $settings, string $help): void
    {
        ?><tr><th scope="row"><label for="msp-performance-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th><td><textarea class="large-text code" id="msp-performance-<?php echo esc_attr($key); ?>" rows="4" name="<?php echo esc_attr(self::OPTION . '[' . $key . ']'); ?>"><?php echo esc_textarea((string) ($settings[$key] ?? '')); ?></textarea><p class="description"><?php echo esc_html($help); ?></p></td></tr><?php
    }

    private function sanitize_lines(string $value, bool $urls): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $value) ?: array();
        $clean = array();
        foreach ($lines as $line) {
            $line = $urls ? esc_url_raw(trim($line)) : sanitize_key(trim($line));
            if ('' !== $line) {
                $clean[] = $line;
            }
        }
        return implode("\n", array_unique($clean));
    }
}
