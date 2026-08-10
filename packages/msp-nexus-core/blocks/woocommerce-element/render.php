<?php
/**
 * Context-aware WooCommerce template element.
 *
 * @var array<string, mixed> $attributes
 */
$element = sanitize_key((string) ($attributes['element'] ?? 'product-title'));
$transactional = array('cart', 'cart-totals', 'cross-sells', 'checkout', 'order-overview', 'order-details', 'account', 'mini-cart', 'side-cart', 'wishlist', 'login-dropdown', 'free-shipping-progress');
if (! class_exists('WooCommerce')) {
    if (is_admin()) {
        echo '<p ' . get_block_wrapper_attributes() . '>' . esc_html__('Activate WooCommerce to use this element.', 'msp-nexus-core') . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    return;
}
if (is_admin() && in_array($element, $transactional, true)) {
    echo '<div ' . get_block_wrapper_attributes(array('class' => 'msp-nexus-woo-element is-placeholder')) . '><strong>' . esc_html(ucwords(str_replace('-', ' ', $element))) . '</strong><p>' . esc_html__('Transactional content renders on the public WooCommerce route.', 'msp-nexus-core') . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    return;
}
$product = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : false;
$count = min(24, max(1, (int) ($attributes['count'] ?? 4)));
$columns = min(6, max(1, (int) ($attributes['columns'] ?? 4)));
ob_start();
if ('' !== trim((string) ($attributes['heading'] ?? ''))) {
    echo '<h2>' . esc_html((string) $attributes['heading']) . '</h2>';
}
switch ($element) {
    case 'shop-title':
        echo '<h1 class="woocommerce-products-header__title page-title">' . esc_html(woocommerce_page_title(false)) . '</h1>';
        break;
    case 'category-description':
        do_action('woocommerce_archive_description');
        break;
    case 'shop-products':
        echo do_shortcode('[products limit="' . $count . '" columns="' . $columns . '"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        break;
    case 'product-title':
        if ($product) {
            woocommerce_template_single_title();
        }
        break;
    case 'product-images':
        if ($product) {
            woocommerce_show_product_images();
        }
        break;
    case 'product-price':
        if ($product) {
            woocommerce_template_single_price();
        }
        break;
    case 'add-to-cart':
        if ($product) {
            woocommerce_template_single_add_to_cart();
        }
        break;
    case 'breadcrumbs':
        woocommerce_breadcrumb();
        break;
    case 'product-reviews':
        if ($product && ! is_admin()) {
            comments_template();
        } elseif (is_admin()) {
            echo '<p>' . esc_html__('Product reviews render on a public product page.', 'msp-nexus-core') . '</p>';
        }
        break;
    case 'product-stock':
        if ($product) {
            echo wp_kses_post(wc_get_stock_html($product));
        }
        break;
    case 'product-meta':
        if ($product) {
            woocommerce_template_single_meta();
        }
        break;
    case 'product-rating':
        if ($product) {
            woocommerce_template_single_rating();
        }
        break;
    case 'product-brands':
        if ($product && taxonomy_exists('product_brand')) {
            echo wp_kses_post((string) get_the_term_list($product->get_id(), 'product_brand', '<p class="product-brands">', ', ', '</p>'));
        }
        break;
    case 'short-description':
        if ($product) {
            woocommerce_template_single_excerpt();
        }
        break;
    case 'product-tabs':
        if ($product) {
            woocommerce_output_product_data_tabs();
        }
        break;
    case 'product-content':
        echo wp_kses_post(apply_filters('the_content', (string) get_post_field('post_content', get_the_ID())));
        break;
    case 'additional-information':
        if ($product) {
            wc_display_product_attributes($product);
        }
        break;
    case 'upsells':
        woocommerce_upsell_display($count, $columns);
        break;
    case 'related-products':
        woocommerce_output_related_products();
        break;
    case 'notices':
        wc_print_notices();
        break;
    case 'shop-filters':
        $selected_category = sanitize_title((string) ($_GET['product_cat'] ?? ''));
        $selected_orderby = sanitize_key((string) ($_GET['orderby'] ?? 'menu_order'));
        $categories = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => true));
        $woo_settings = get_option('msp_nexus_woocommerce', array());
        $ajax_filters = is_array($woo_settings) && ! empty($woo_settings['ajax_filters']);
        echo '<form class="msp-nexus-shop-filters" method="get" data-instant="' . (! empty($attributes['instantFilters']) ? 'true' : 'false') . '" data-ajax="' . ($ajax_filters ? 'true' : 'false') . '"><label>' . esc_html__('Product category', 'msp-nexus-core') . '<select name="product_cat"><option value="">' . esc_html__('All products', 'msp-nexus-core') . '</option>';
        if (is_array($categories)) {
            foreach ($categories as $category) {
                if (! $category instanceof \WP_Term) {
                    continue;
                }
                $label = $category->name . (! empty($attributes['showCounts']) ? ' (' . $category->count . ')' : '');
                echo '<option value="' . esc_attr($category->slug) . '" ' . selected($selected_category, $category->slug, false) . '>' . esc_html($label) . '</option>';
            }
        }
        echo '</select></label><label>' . esc_html__('Sort products', 'msp-nexus-core') . '<select name="orderby">';
        foreach (wc_get_catalog_ordering_options() as $value => $label) {
            echo '<option value="' . esc_attr((string) $value) . '" ' . selected($selected_orderby, (string) $value, false) . '>' . esc_html((string) $label) . '</option>';
        }
        echo '</select></label><button type="submit">' . esc_html__('Apply filters', 'msp-nexus-core') . '</button></form>';
        break;
    case 'order-steps':
        echo '<ol class="msp-nexus-order-steps"><li>' . esc_html__('Cart', 'msp-nexus-core') . '</li><li>' . esc_html__('Details and payment', 'msp-nexus-core') . '</li><li>' . esc_html__('Confirmation', 'msp-nexus-core') . '</li></ol>';
        break;
    case 'cart':
        echo do_shortcode('[woocommerce_cart]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        break;
    case 'cart-totals':
        if (function_exists('woocommerce_cart_totals') && WC()->cart) {
            woocommerce_cart_totals();
        }
        break;
    case 'cross-sells':
        if (function_exists('woocommerce_cross_sell_display')) {
            woocommerce_cross_sell_display($count, $columns);
        }
        break;
    case 'checkout':
        echo do_shortcode('[woocommerce_checkout]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        break;
    case 'order-overview':
    case 'order-details':
        $order_id = absint(get_query_var('order-received'));
        $order = $order_id ? wc_get_order($order_id) : false;
        $order_key = wc_clean((string) ($_GET['key'] ?? ''));
        $owns_order = $order && ((int) $order->get_user_id() === get_current_user_id() || (0 === (int) $order->get_user_id() && hash_equals((string) $order->get_order_key(), $order_key)));
        if ($owns_order && 'order-overview' === $element) {
            wc_get_template('checkout/order-receipt.php', array('order' => $order));
        } elseif ($owns_order) {
            woocommerce_order_details_table($order_id);
        }
        break;
    case 'account':
        echo do_shortcode('[woocommerce_my_account]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        break;
    case 'mini-cart':
        woocommerce_mini_cart();
        break;
    case 'side-cart':
        echo '<button class="msp-nexus-side-cart__trigger" type="button" data-side-cart-open aria-haspopup="dialog">' . esc_html__('View cart', 'msp-nexus-core') . ' <span class="msp-nexus-cart-count">' . esc_html((string) (WC()->cart ? WC()->cart->get_cart_contents_count() : 0)) . '</span></button><dialog class="msp-nexus-side-cart__dialog"><button type="button" class="msp-nexus-side-cart__close" data-side-cart-close aria-label="' . esc_attr__('Close cart', 'msp-nexus-core') . '">&times;</button><h2>' . esc_html__('Your cart', 'msp-nexus-core') . '</h2><div class="widget_shopping_cart_content">';
        woocommerce_mini_cart();
        echo '</div></dialog>';
        break;
    case 'login-dropdown':
        if (is_user_logged_in()) {
            echo '<a class="msp-nexus-account-link" href="' . esc_url(wc_get_page_permalink('myaccount')) . '">' . esc_html__('My account', 'msp-nexus-core') . '</a>';
        } else {
            echo '<button type="button" data-account-open aria-haspopup="dialog">' . esc_html__('Sign in', 'msp-nexus-core') . '</button><dialog class="msp-nexus-account-dialog"><button type="button" data-account-close aria-label="' . esc_attr__('Close sign-in form', 'msp-nexus-core') . '">&times;</button><h2>' . esc_html__('Sign in', 'msp-nexus-core') . '</h2>';
            woocommerce_login_form(array('redirect' => wc_get_page_permalink('myaccount')));
            echo '</dialog>';
        }
        break;
    case 'shop-sidebar':
        echo '<button type="button" data-shop-sidebar-open aria-haspopup="dialog">' . esc_html__('Shop filters', 'msp-nexus-core') . '</button><dialog class="msp-nexus-shop-sidebar"><button type="button" data-shop-sidebar-close aria-label="' . esc_attr__('Close shop filters', 'msp-nexus-core') . '">&times;</button><h2>' . esc_html__('Filter products', 'msp-nexus-core') . '</h2>';
        if (is_active_sidebar('msp-nexus-shop-sidebar')) {
            dynamic_sidebar('msp-nexus-shop-sidebar');
        } else {
            echo '<p>' . esc_html__('Add WooCommerce filter blocks or widgets to the Nexus Shop Sidebar area.', 'msp-nexus-core') . '</p>';
        }
        echo '</dialog>';
        break;
    case 'view-options':
        echo '<div class="msp-nexus-shop-view" role="group" aria-label="' . esc_attr__('Product view options', 'msp-nexus-core') . '"><button type="button" data-shop-view="grid" aria-pressed="true">' . esc_html__('Grid', 'msp-nexus-core') . '</button><button type="button" data-shop-view="list" aria-pressed="false">' . esc_html__('List', 'msp-nexus-core') . '</button><label>' . esc_html__('Columns', 'msp-nexus-core') . '<select data-shop-columns>'; for ($column = 2; $column <= 6; ++$column) { echo '<option value="' . esc_attr((string) $column) . '" ' . selected($columns, $column, false) . '>' . esc_html((string) $column) . '</option>'; } echo '</select></label></div>';
        break;
    case 'free-shipping-progress':
        $threshold = max(0, (float) ($attributes['shippingThreshold'] ?? 0));
        if ($threshold <= 0) {
            $woo_settings = get_option('msp_nexus_woocommerce', array());
            $threshold = is_array($woo_settings) ? max(0, (float) ($woo_settings['free_shipping_threshold'] ?? 0)) : 0;
        }
        $subtotal = WC()->cart ? (float) WC()->cart->get_displayed_subtotal() : 0;
        $percent = $threshold > 0 ? min(100, ($subtotal / $threshold) * 100) : 0;
        $remaining = max(0, $threshold - $subtotal);
        $message = $threshold <= 0 ? __('Set a free-shipping threshold in Nexus WooCommerce settings.', 'msp-nexus-core') : ($remaining > 0 ? sprintf(__('Add %s more to reach free shipping.', 'msp-nexus-core'), wc_price($remaining)) : __('Your order qualifies for free shipping.', 'msp-nexus-core'));
        echo '<div class="msp-nexus-shipping-progress" style="--msp-shipping-progress:' . esc_attr((string) $percent) . '%"><p>' . wp_kses_post($message) . '</p><span role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . esc_attr((string) round($percent)) . '"><i></i></span></div>';
        break;
    case 'wishlist':
        $wishlist_products = wc_get_products(array('status' => 'publish', 'limit' => 100, 'return' => 'objects'));
        echo '<div class="msp-nexus-wishlist" data-empty-message="' . esc_attr__('Your wishlist is empty.', 'msp-nexus-core') . '"><p class="msp-nexus-wishlist__empty">' . esc_html__('Your wishlist is empty.', 'msp-nexus-core') . '</p><ul class="products columns-' . esc_attr((string) $columns) . '">';
        foreach ($wishlist_products as $wishlist_product) {
            if (! $wishlist_product instanceof \WC_Product) {
                continue;
            }
            echo '<li class="product" data-wishlist-product="' . esc_attr((string) $wishlist_product->get_id()) . '" hidden><a href="' . esc_url($wishlist_product->get_permalink()) . '">' . wp_kses_post($wishlist_product->get_image('woocommerce_thumbnail')) . '<h3>' . esc_html($wishlist_product->get_name()) . '</h3>' . wp_kses_post($wishlist_product->get_price_html()) . '</a><button type="button" data-wishlist-remove="' . esc_attr((string) $wishlist_product->get_id()) . '">' . esc_html__('Remove', 'msp-nexus-core') . '</button></li>';
        }
        echo '</ul></div>';
        break;
    default:
        echo '<p>' . esc_html__('Choose a WooCommerce template element.', 'msp-nexus-core') . '</p>';
}
$content = (string) ob_get_clean();
if ('' === trim($content) && is_admin()) {
    $content = '<p>' . esc_html__('This element needs the matching WooCommerce page or product context.', 'msp-nexus-core') . '</p>';
}
echo '<div ' . get_block_wrapper_attributes(array('class' => 'msp-nexus-woo-element is-' . $element)) . '>' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
