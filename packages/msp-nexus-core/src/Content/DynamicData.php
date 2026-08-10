<?php
/**
 * Dynamic data discovery and safe value resolution.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Content;

final class DynamicData
{
    /** @var array<int, string> */
    private const SITE_FIELDS = array('organization_name', 'sales_phone', 'support_phone', 'support_url', 'sales_email', 'cta_url', 'announcement', 'linkedin_url', 'facebook_url', 'youtube_url', 'company_address', 'business_hours', 'emergency_message');

    public function register_hooks(): void
    {
        add_action('rest_api_init', array($this, 'routes'));
    }

    public function routes(): void
    {
        register_rest_route(
            'msp-nexus/v1',
            '/dynamic-fields',
            array(
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => static function (): bool {
                    return current_user_can('edit_posts');
                },
                'callback' => array($this, 'schema'),
                'args' => array('post_type' => array('sanitize_callback' => 'sanitize_key')),
            )
        );
        register_rest_route(
            'msp-nexus/v1',
            '/loop-layouts',
            array(
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => static function (): bool {
                    return current_user_can('edit_posts');
                },
                'callback' => array($this, 'loop_layouts'),
            )
        );
        register_rest_route(
            'msp-nexus/v1',
            '/query-options',
            array(
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => static function (): bool {
                    return current_user_can('edit_posts');
                },
                'callback' => array($this, 'query_options'),
            )
        );
    }

    public function schema(\WP_REST_Request $request): \WP_REST_Response
    {
        $post_type = sanitize_key((string) ($request->get_param('post_type') ?: 'post'));
        if (! post_type_exists($post_type)) {
            $post_type = 'post';
        }
        $sources = array(
            $this->source('post', __('Current post', 'msp-nexus-core'), array(
                'title' => __('Title', 'msp-nexus-core'), 'excerpt' => __('Excerpt', 'msp-nexus-core'), 'date' => __('Published date', 'msp-nexus-core'),
                'modified' => __('Modified date', 'msp-nexus-core'), 'author' => __('Author name', 'msp-nexus-core'), 'type' => __('Post type label', 'msp-nexus-core'),
                'permalink' => __('Permalink', 'msp-nexus-core'), 'featured_image' => __('Featured image', 'msp-nexus-core'),
                'featured_image_url' => __('Featured image URL', 'msp-nexus-core'), 'comment_count' => __('Comment count', 'msp-nexus-core'),
            )),
            $this->source('site', __('Organization settings', 'msp-nexus-core'), array_combine(self::SITE_FIELDS, array_map(array($this, 'label'), self::SITE_FIELDS))),
            $this->source('archive', __('Archive context', 'msp-nexus-core'), array('title' => __('Archive title', 'msp-nexus-core'), 'description' => __('Archive description', 'msp-nexus-core'))),
            $this->source('author', __('Post author', 'msp-nexus-core'), array('display_name' => __('Display name', 'msp-nexus-core'), 'first_name' => __('First name', 'msp-nexus-core'), 'last_name' => __('Last name', 'msp-nexus-core'), 'description' => __('Biography', 'msp-nexus-core'), 'url' => __('Website', 'msp-nexus-core'), 'avatar' => __('Avatar', 'msp-nexus-core'))),
            $this->source('user', __('Current visitor', 'msp-nexus-core'), array('display_name' => __('Display name', 'msp-nexus-core'), 'first_name' => __('First name', 'msp-nexus-core'), 'last_name' => __('Last name', 'msp-nexus-core'), 'avatar' => __('Avatar', 'msp-nexus-core'))),
            $this->source('term', __('Current term', 'msp-nexus-core'), array('name' => __('Name', 'msp-nexus-core'), 'description' => __('Description', 'msp-nexus-core'), 'count' => __('Post count', 'msp-nexus-core'), 'url' => __('Archive URL', 'msp-nexus-core'))),
            $this->source('date', __('Current date', 'msp-nexus-core'), array('full' => __('Site date format', 'msp-nexus-core'), 'year' => __('Year', 'msp-nexus-core'), 'month' => __('Month name', 'msp-nexus-core'))),
        );
        $meta = $this->allowed_meta($post_type);
        if ($meta) {
            $sources[] = $this->source('post_meta', __('Custom and ACF fields', 'msp-nexus-core'), $meta);
        }
        if (class_exists('WooCommerce')) {
            $sources[] = $this->source('woocommerce', __('WooCommerce product', 'msp-nexus-core'), array(
                'price' => __('Price', 'msp-nexus-core'), 'regular_price' => __('Regular price', 'msp-nexus-core'), 'sale_price' => __('Sale price', 'msp-nexus-core'),
                'sku' => __('SKU', 'msp-nexus-core'), 'stock' => __('Stock status', 'msp-nexus-core'), 'short_description' => __('Short description', 'msp-nexus-core'),
                'image' => __('Product image', 'msp-nexus-core'), 'gallery' => __('Gallery IDs', 'msp-nexus-core'), 'categories' => __('Categories', 'msp-nexus-core'),
            ));
        }
        return new \WP_REST_Response(array('postType' => $post_type, 'sources' => $sources));
    }

    public function loop_layouts(): \WP_REST_Response
    {
        $posts = get_posts(array('post_type' => 'msp_nexus_layout', 'post_status' => 'publish', 'posts_per_page' => 100, 'meta_query' => array(array('key' => '_msp_nexus_layout_area', 'value' => 'loop'))));
        $items = array();
        foreach ($posts as $post) {
            $items[] = array('value' => $post->post_name, 'label' => $post->post_title);
        }
        return new \WP_REST_Response($items);
    }

    public function query_options(): \WP_REST_Response
    {
        $types = array();
        foreach (get_post_types(array('public' => true), 'objects') as $object) {
            if ('attachment' !== $object->name) {
                $types[] = array('value' => $object->name, 'label' => $object->labels->singular_name);
            }
        }
        $taxonomies = array();
        foreach (get_taxonomies(array('public' => true), 'objects') as $taxonomy) {
            $taxonomies[] = array('value' => $taxonomy->name, 'label' => $taxonomy->labels->singular_name, 'types' => array_values((array) $taxonomy->object_type));
        }
        return new \WP_REST_Response(array('postTypes' => $types, 'taxonomies' => $taxonomies));
    }

    /** @return array<string, mixed> */
    public function resolve(string $source, string $field, int $post_id): array
    {
        $source = sanitize_key($source);
        $field = sanitize_key($field);
        $result = array('value' => '', 'url' => '', 'mediaId' => 0, 'kind' => 'text');
        if ('site' === $source && in_array($field, self::SITE_FIELDS, true)) {
            $settings = get_option('msp_nexus_settings', array());
            $result['value'] = is_array($settings) ? (string) ($settings[$field] ?? '') : '';
            if (in_array($field, array('support_url', 'cta_url', 'linkedin_url', 'facebook_url', 'youtube_url'), true)) {
                $result['url'] = $result['value'];
            } elseif ('sales_email' === $field) {
                $result['url'] = 'mailto:' . sanitize_email((string) $result['value']);
            } elseif (in_array($field, array('sales_phone', 'support_phone'), true)) {
                $result['url'] = 'tel:' . preg_replace('/[^0-9+]/', '', (string) $result['value']);
            }
        } elseif ('post' === $source && $post_id > 0) {
            $result = $this->post_value($field, $post_id, $result);
        } elseif ('post_meta' === $source && $post_id > 0 && array_key_exists($field, $this->allowed_meta((string) get_post_type($post_id)))) {
            $raw = function_exists('get_field') ? get_field($field, $post_id, false) : get_post_meta($post_id, $field, true);
            if (is_numeric($raw) && wp_attachment_is_image((int) $raw)) {
                $result['mediaId'] = (int) $raw;
                $result['kind'] = 'image';
            }
            $result['value'] = $this->scalar($raw);
        } elseif ('archive' === $source) {
            $result['value'] = 'description' === $field ? wp_strip_all_tags((string) get_the_archive_description()) : (string) get_the_archive_title();
        } elseif ('date' === $source) {
            $formats = array('year' => 'Y', 'month' => 'F', 'full' => (string) get_option('date_format'));
            $result['value'] = wp_date((string) ($formats[$field] ?? get_option('date_format')));
        } elseif ('author' === $source) {
            $author_id = $post_id > 0 ? (int) get_post_field('post_author', $post_id) : 0;
            $result = $this->user_value($field, $author_id, $result);
        } elseif ('user' === $source) {
            $result = $this->user_value($field, get_current_user_id(), $result);
        } elseif ('term' === $source) {
            $term = get_queried_object();
            if ($term instanceof \WP_Term) {
                if ('name' === $field || 'description' === $field || 'count' === $field) {
                    $result['value'] = (string) $term->{$field};
                } elseif ('url' === $field) {
                    $link = get_term_link($term);
                    $result['value'] = is_wp_error($link) ? '' : $link;
                    $result['url'] = $result['value'];
                }
            }
        } elseif ('woocommerce' === $source && function_exists('wc_get_product') && $post_id > 0) {
            $result = $this->product_value($field, $post_id, $result);
        }
        return $result;
    }

    /** @return array<string, string> */
    public function allowed_meta(string $post_type): array
    {
        $fields = array();
        if (function_exists('get_registered_meta_keys')) {
            foreach (get_registered_meta_keys('post', $post_type) as $key => $args) {
                if (! empty($args['show_in_rest'])) {
                    $fields[sanitize_key((string) $key)] = $this->label((string) $key);
                }
            }
        }
        if (function_exists('acf_get_field_groups') && function_exists('acf_get_fields')) {
            $groups = acf_get_field_groups(array('post_type' => $post_type));
            foreach ((array) $groups as $group) {
                foreach ((array) acf_get_fields($group) as $field) {
                    $name = sanitize_key((string) ($field['name'] ?? ''));
                    if ('' !== $name) {
                        $fields[$name] = sanitize_text_field((string) ($field['label'] ?? $this->label($name)));
                    }
                }
            }
        }
        ksort($fields);
        return $fields;
    }

    /** @param array<string, mixed> $result
     *  @return array<string, mixed>
     */
    private function post_value(string $field, int $post_id, array $result): array
    {
        if ('title' === $field) {
            $result['value'] = get_the_title($post_id);
        } elseif ('excerpt' === $field) {
            $result['value'] = get_the_excerpt($post_id);
        } elseif ('date' === $field) {
            $result['value'] = get_the_date('', $post_id);
        } elseif ('modified' === $field) {
            $result['value'] = get_the_modified_date('', $post_id);
        } elseif ('author' === $field) {
            $result['value'] = get_the_author_meta('display_name', (int) get_post_field('post_author', $post_id));
        } elseif ('type' === $field) {
            $object = get_post_type_object((string) get_post_type($post_id));
            $result['value'] = $object ? (string) $object->labels->singular_name : '';
        } elseif ('permalink' === $field) {
            $result['value'] = get_permalink($post_id) ?: '';
            $result['url'] = $result['value'];
        } elseif ('featured_image' === $field || 'featured_image_url' === $field) {
            $result['mediaId'] = (int) get_post_thumbnail_id($post_id);
            $result['value'] = $result['mediaId'] ? (string) wp_get_attachment_image_url($result['mediaId'], 'full') : '';
            $result['kind'] = 'featured_image' === $field ? 'image' : 'url';
        } elseif ('comment_count' === $field) {
            $result['value'] = (string) get_comments_number($post_id);
        }
        if ('' === (string) $result['url']) {
            $result['url'] = get_permalink($post_id) ?: '';
        }
        return $result;
    }

    /** @param array<string, mixed> $result
     *  @return array<string, mixed>
     */
    private function user_value(string $field, int $user_id, array $result): array
    {
        $user = $user_id > 0 ? get_userdata($user_id) : false;
        if (! $user) {
            return $result;
        }
        if (in_array($field, array('display_name', 'first_name', 'last_name', 'description', 'url'), true)) {
            $result['value'] = (string) $user->{$field};
            if ('url' === $field) {
                $result['url'] = $result['value'];
            }
        } elseif ('avatar' === $field) {
            $result['value'] = (string) get_avatar_url($user_id, array('size' => 512));
            $result['kind'] = 'image';
        }
        return $result;
    }

    /** @param array<string, mixed> $result
     *  @return array<string, mixed>
     */
    private function product_value(string $field, int $post_id, array $result): array
    {
        $product = wc_get_product($post_id);
        if (! $product) {
            return $result;
        }
        if ('price' === $field) {
            $result['value'] = wp_strip_all_tags((string) $product->get_price_html());
        } elseif ('regular_price' === $field) {
            $result['value'] = (string) $product->get_regular_price();
        } elseif ('sale_price' === $field) {
            $result['value'] = (string) $product->get_sale_price();
        } elseif ('sku' === $field) {
            $result['value'] = (string) $product->get_sku();
        } elseif ('stock' === $field) {
            $result['value'] = wp_strip_all_tags((string) wc_get_stock_html($product));
        } elseif ('short_description' === $field) {
            $result['value'] = wp_strip_all_tags((string) $product->get_short_description());
        } elseif ('image' === $field) {
            $result['mediaId'] = (int) $product->get_image_id();
            $result['value'] = $result['mediaId'] ? (string) wp_get_attachment_image_url($result['mediaId'], 'full') : '';
            $result['kind'] = 'image';
        } elseif ('gallery' === $field) {
            $result['value'] = implode(',', array_map('absint', $product->get_gallery_image_ids()));
        } elseif ('categories' === $field) {
            $result['value'] = wp_strip_all_tags((string) wc_get_product_category_list($post_id));
        }
        $result['url'] = get_permalink($post_id) ?: '';
        return $result;
    }

    /** @param mixed $value */
    private function scalar($value): string
    {
        if (is_scalar($value)) {
            return (string) $value;
        }
        if (is_array($value)) {
            return implode(', ', array_map(static function ($item): string { return is_scalar($item) ? (string) $item : ''; }, $value));
        }
        return '';
    }

    /** @param array<string, string> $fields
     *  @return array<string, mixed>
     */
    private function source(string $value, string $label, array $fields): array
    {
        $items = array();
        foreach ($fields as $field => $field_label) {
            $items[] = array('value' => $field, 'label' => $field_label);
        }
        return array('value' => $value, 'label' => $label, 'fields' => $items);
    }

    public function label(string $key): string
    {
        return ucwords(str_replace(array('_', '-'), ' ', ltrim($key, '_')));
    }
}
