<?php
/**
 * Optional WooCommerce presentation and Studio integration.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Integrations;

final class WooCommerce
{
    private const OPTION = 'msp_nexus_woocommerce';

    public function register_hooks(): void
    {
        add_action('after_setup_theme', array($this, 'theme_support'));
        add_action('before_woocommerce_init', array($this, 'compatibility'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'menu'));
        add_action('wp_enqueue_scripts', array($this, 'assets'));
        add_action('widgets_init', array($this, 'sidebar'));
        add_action('wp', array($this, 'configure_catalog'));
        add_filter('body_class', array($this, 'body_class'));
        add_filter('loop_shop_columns', array($this, 'columns'));
        add_filter('loop_shop_per_page', array($this, 'products_per_page'));
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'cart_fragments'));
        add_shortcode('msp_nexus_wishlist', array($this, 'wishlist_shortcode'));
    }

    public function register_settings(): void
    {
        register_setting('msp_nexus_woocommerce', self::OPTION, array('type' => 'object', 'default' => array(), 'sanitize_callback' => array($this, 'sanitize')));
    }

    public function menu(): void
    {
        $capability = class_exists('WooCommerce') ? 'manage_woocommerce' : 'manage_options';
        add_submenu_page('msp-nexus', __('Nexus WooCommerce', 'msp-nexus-core'), __('WooCommerce', 'msp-nexus-core'), $capability, 'msp-nexus-woocommerce', array($this, 'render_settings'));
    }

    /** @param mixed $value
     *  @return array<string, mixed>
     */
    public function sanitize($value): array
    {
        if (! is_array($value)) {
            return array();
        }
        $gallery = sanitize_key((string) ($value['gallery_style'] ?? 'slider'));
        $checkout = sanitize_key((string) ($value['checkout_style'] ?? 'standard'));
        return array(
            'catalog_mode' => ! empty($value['catalog_mode']),
            'hide_prices' => ! empty($value['hide_prices']),
            'quick_view' => ! empty($value['quick_view']),
            'wishlist' => ! empty($value['wishlist']),
            'swatches' => ! empty($value['swatches']),
            'sticky_mobile' => ! empty($value['sticky_mobile']),
            'ajax_filters' => ! empty($value['ajax_filters']),
            'enquiry_url' => esc_url_raw((string) ($value['enquiry_url'] ?? '')),
            'products_per_page' => min(96, max(1, absint($value['products_per_page'] ?? 12))),
            'columns' => min(6, max(1, absint($value['columns'] ?? 4))),
            'gallery_style' => in_array($gallery, array('slider', 'grid', 'stack'), true) ? $gallery : 'slider',
            'checkout_style' => in_array($checkout, array('standard', 'two-column', 'distraction-free'), true) ? $checkout : 'standard',
            'free_shipping_threshold' => max(0, (float) ($value['free_shipping_threshold'] ?? 0)),
        );
    }

    public function theme_support(): void
    {
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }

    public function compatibility(): void
    {
        if (class_exists('Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', MSP_NEXUS_CORE_FILE, true);
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', MSP_NEXUS_CORE_FILE, true);
        }
    }

    public function assets(): void
    {
        if (! class_exists('WooCommerce')) {
            return;
        }
        wp_enqueue_style('msp-nexus-woocommerce');
        $settings = $this->settings();
        if (! empty($settings['quick_view']) || ! empty($settings['wishlist']) || ! empty($settings['swatches'])) {
            wp_enqueue_script('msp-nexus-woocommerce-element-view-script');
        }
    }

    public function configure_catalog(): void
    {
        if (! class_exists('WooCommerce')) {
            return;
        }
        $settings = $this->settings();
        if (! empty($settings['catalog_mode'])) {
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            add_filter('woocommerce_is_purchasable', '__return_false');
        }
        if (! empty($settings['hide_prices'])) {
            add_filter('woocommerce_get_price_html', '__return_empty_string');
        }
        if (! empty($settings['quick_view']) || ! empty($settings['wishlist'])) {
            add_action('woocommerce_after_shop_loop_item', array($this, 'product_actions'), 18);
        }
        if (! empty($settings['swatches'])) {
            add_filter('woocommerce_dropdown_variation_attribute_options_html', array($this, 'variation_swatches'), 20, 2);
        }
    }

    public function product_actions(): void
    {
        global $product;
        if (! $product instanceof \WC_Product) {
            return;
        }
        $settings = $this->settings();
        $id = $product->get_id();
        echo '<div class="msp-nexus-woo-actions">';
        if (! empty($settings['wishlist'])) {
            echo '<button type="button" class="msp-nexus-woo-wishlist" data-nexus-wishlist="' . esc_attr((string) $id) . '" aria-pressed="false">' . esc_html__('Save', 'msp-nexus-core') . '</button>';
        }
        if (! empty($settings['quick_view'])) {
            $template_id = 'msp-nexus-product-' . $id;
            echo '<button type="button" data-nexus-quick-view="' . esc_attr((string) $id) . '" aria-controls="' . esc_attr($template_id) . '">' . esc_html__('Quick view', 'msp-nexus-core') . '</button><template id="' . esc_attr($template_id) . '"><div>' . wp_kses_post($product->get_image('woocommerce_single')) . '</div><div><h2><a href="' . esc_url($product->get_permalink()) . '">' . esc_html($product->get_name()) . '</a></h2>' . wp_kses_post($product->get_price_html()) . '<div>' . wp_kses_post(wpautop($product->get_short_description())) . '</div><p><a class="button" href="' . esc_url($product->get_permalink()) . '">' . esc_html__('View product options', 'msp-nexus-core') . '</a></p></div></template>';
        }
        if (! empty($settings['catalog_mode']) && ! empty($settings['enquiry_url'])) {
            echo '<a class="button msp-nexus-product-enquiry" href="' . esc_url(add_query_arg('product', $id, (string) $settings['enquiry_url'])) . '">' . esc_html__('Enquire about this product', 'msp-nexus-core') . '</a>';
        }
        echo '</div>';
    }

    public function sidebar(): void
    {
        register_sidebar(array('name' => __('Nexus Shop Sidebar', 'msp-nexus-core'), 'id' => 'msp-nexus-shop-sidebar', 'description' => __('Blocks and widgets shown by the WooCommerce off-canvas Shop Sidebar element.', 'msp-nexus-core'), 'before_widget' => '<section id="%1$s" class="widget %2$s">', 'after_widget' => '</section>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>'));
    }

    /** @param array<string, mixed> $args */
    public function variation_swatches(string $html, array $args): string
    {
        $attribute = sanitize_title((string) ($args['attribute'] ?? ''));
        $options = is_array($args['options'] ?? null) ? $args['options'] : array();
        if ('' === $attribute || ! $options) {
            return $html;
        }
        $buttons = '<span class="msp-nexus-swatches" data-attribute="' . esc_attr($attribute) . '" role="group" aria-label="' . esc_attr(wc_attribute_label($attribute)) . '">';
        foreach ($options as $option) {
            $value = (string) $option;
            $label = $value;
            if (taxonomy_exists($attribute)) {
                $term = get_term_by('slug', $value, $attribute);
                if ($term instanceof \WP_Term) {
                    $label = $term->name;
                }
            }
            $buttons .= '<button type="button" data-swatch-value="' . esc_attr($value) . '" aria-pressed="false">' . esc_html($label) . '</button>';
        }
        return $html . $buttons . '</span>';
    }

    /** @param array<int, string> $classes
     *  @return array<int, string>
     */
    public function body_class(array $classes): array
    {
        if (! class_exists('WooCommerce')) {
            return $classes;
        }
        $settings = $this->settings();
        $classes[] = 'msp-nexus-woocommerce-active';
        $classes[] = 'msp-nexus-gallery-' . sanitize_html_class((string) ($settings['gallery_style'] ?? 'slider'));
        $classes[] = 'msp-nexus-checkout-' . sanitize_html_class((string) ($settings['checkout_style'] ?? 'standard'));
        if (! empty($settings['catalog_mode'])) {
            $classes[] = 'msp-nexus-catalog-mode';
        }
        if (! empty($settings['sticky_mobile'])) {
            $classes[] = 'msp-nexus-sticky-cart-enabled';
        }
        return $classes;
    }

    public function columns(): int
    {
        return (int) $this->settings()['columns'];
    }

    public function products_per_page(): int
    {
        return (int) $this->settings()['products_per_page'];
    }

    /** @param array<string, string> $fragments
     *  @return array<string, string>
     */
    public function cart_fragments(array $fragments): array
    {
        $count = function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
        $fragments['.msp-nexus-cart-count'] = '<span class="msp-nexus-cart-count">' . esc_html((string) $count) . '</span>';
        return $fragments;
    }

    /** @param array<string, mixed> $attributes */
    public function wishlist_shortcode(array $attributes = array()): string
    {
        if (! class_exists('WooCommerce')) {
            return '';
        }
        return (string) render_block(array('blockName' => 'msp-nexus/woocommerce-element', 'attrs' => array('element' => 'wishlist', 'columns' => absint($attributes['columns'] ?? 4)), 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array()));
    }

    public function render_settings(): void
    {
        if (! current_user_can(class_exists('WooCommerce') ? 'manage_woocommerce' : 'manage_options')) {
            return;
        }
        $value = $this->settings();
        ?>
        <div class="wrap"><h1><?php esc_html_e('Nexus WooCommerce Studio', 'msp-nexus-core'); ?></h1><p><?php esc_html_e('Configure store presentation while WooCommerce continues to own products, inventory, taxes, payments, accounts, and order security.', 'msp-nexus-core'); ?></p>
        <?php if (! class_exists('WooCommerce')) : ?><div class="notice notice-warning inline"><p><?php esc_html_e('WooCommerce is not active. Settings can be prepared now and will apply after activation.', 'msp-nexus-core'); ?></p></div><?php endif; ?>
        <form action="options.php" method="post"><?php settings_fields('msp_nexus_woocommerce'); ?><table class="form-table" role="presentation">
        <?php foreach (array('catalog_mode' => __('Catalog mode (disable purchases)', 'msp-nexus-core'), 'hide_prices' => __('Hide product prices', 'msp-nexus-core'), 'quick_view' => __('Enable accessible quick view', 'msp-nexus-core'), 'wishlist' => __('Enable browser-local wishlists', 'msp-nexus-core'), 'swatches' => __('Enhance variable-product attributes with accessible swatch buttons', 'msp-nexus-core'), 'sticky_mobile' => __('Use a sticky mobile add-to-cart form', 'msp-nexus-core'), 'ajax_filters' => __('Enhance compatible product filters without full reloads', 'msp-nexus-core')) as $key => $label) : ?>
        <tr><th scope="row"><?php echo esc_html($label); ?></th><td><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION . '[' . $key . ']'); ?>" value="1" <?php checked(! empty($value[$key])); ?>> <?php esc_html_e('Enabled', 'msp-nexus-core'); ?></label></td></tr>
        <?php endforeach; ?>
        <tr><th scope="row"><label for="msp-nexus-woo-enquiry"><?php esc_html_e('Catalog enquiry URL', 'msp-nexus-core'); ?></label></th><td><input class="regular-text" id="msp-nexus-woo-enquiry" type="url" name="<?php echo esc_attr(self::OPTION . '[enquiry_url]'); ?>" value="<?php echo esc_attr((string) $value['enquiry_url']); ?>"></td></tr>
        <tr><th scope="row"><label for="msp-nexus-woo-count"><?php esc_html_e('Products per page', 'msp-nexus-core'); ?></label></th><td><input id="msp-nexus-woo-count" type="number" min="1" max="96" name="<?php echo esc_attr(self::OPTION . '[products_per_page]'); ?>" value="<?php echo esc_attr((string) $value['products_per_page']); ?>"></td></tr>
        <tr><th scope="row"><label for="msp-nexus-woo-columns"><?php esc_html_e('Shop columns', 'msp-nexus-core'); ?></label></th><td><input id="msp-nexus-woo-columns" type="number" min="1" max="6" name="<?php echo esc_attr(self::OPTION . '[columns]'); ?>" value="<?php echo esc_attr((string) $value['columns']); ?>"></td></tr>
        <tr><th scope="row"><label for="msp-nexus-woo-shipping"><?php esc_html_e('Free-shipping progress threshold', 'msp-nexus-core'); ?></label></th><td><input id="msp-nexus-woo-shipping" type="number" min="0" step="0.01" name="<?php echo esc_attr(self::OPTION . '[free_shipping_threshold]'); ?>" value="<?php echo esc_attr((string) $value['free_shipping_threshold']); ?>"><p class="description"><?php esc_html_e('Presentation only. Configure the actual free-shipping method and minimum in WooCommerce shipping zones.', 'msp-nexus-core'); ?></p></td></tr>
        <?php $this->select_row('gallery_style', __('Product gallery', 'msp-nexus-core'), array('slider' => __('Slider', 'msp-nexus-core'), 'grid' => __('Image grid', 'msp-nexus-core'), 'stack' => __('Vertical stack', 'msp-nexus-core')), $value); ?>
        <?php $this->select_row('checkout_style', __('Checkout presentation', 'msp-nexus-core'), array('standard' => __('Standard', 'msp-nexus-core'), 'two-column' => __('Two column', 'msp-nexus-core'), 'distraction-free' => __('Distraction free', 'msp-nexus-core')), $value); ?>
        </table><?php submit_button(); ?></form></div>
        <?php
    }

    /** @param array<string, string> $choices
     *  @param array<string, mixed> $value
     */
    private function select_row(string $key, string $label, array $choices, array $value): void
    {
        ?><tr><th scope="row"><label for="msp-nexus-woo-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th><td><select id="msp-nexus-woo-<?php echo esc_attr($key); ?>" name="<?php echo esc_attr(self::OPTION . '[' . $key . ']'); ?>"><?php foreach ($choices as $choice => $choice_label) : ?><option value="<?php echo esc_attr($choice); ?>" <?php selected((string) $value[$key], $choice); ?>><?php echo esc_html($choice_label); ?></option><?php endforeach; ?></select></td></tr><?php
    }

    /** @return array<string, mixed> */
    private function settings(): array
    {
        return wp_parse_args(
            is_array(get_option(self::OPTION, array())) ? get_option(self::OPTION, array()) : array(),
            array('catalog_mode' => false, 'hide_prices' => false, 'quick_view' => true, 'wishlist' => true, 'swatches' => true, 'sticky_mobile' => true, 'ajax_filters' => false, 'enquiry_url' => '', 'products_per_page' => 12, 'columns' => 4, 'gallery_style' => 'slider', 'checkout_style' => 'standard', 'free_shipping_threshold' => 0)
        );
    }
}
