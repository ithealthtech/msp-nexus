<?php
/**
 * Registers durable site-owned MSP content.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Content;

final class Registrar
{
    /** @var array<string, array{singular:string,plural:string,icon:string,supports:string[]}> */
    private const TYPES = array(
        'msp_service' => array('singular' => 'Service', 'plural' => 'Services', 'icon' => 'dashicons-admin-tools', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes')),
        'msp_industry' => array('singular' => 'Industry', 'plural' => 'Industries', 'icon' => 'dashicons-building', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions')),
        'msp_case_study' => array('singular' => 'Case Study', 'plural' => 'Case Studies', 'icon' => 'dashicons-chart-line', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions')),
        'msp_location' => array('singular' => 'Location', 'plural' => 'Locations', 'icon' => 'dashicons-location-alt', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes')),
        'msp_testimonial' => array('singular' => 'Testimonial', 'plural' => 'Testimonials', 'icon' => 'dashicons-format-quote', 'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'page-attributes')),
        'msp_team' => array('singular' => 'Team Member', 'plural' => 'Team', 'icon' => 'dashicons-groups', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes')),
        'msp_resource' => array('singular' => 'Resource', 'plural' => 'Resources', 'icon' => 'dashicons-media-document', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions')),
        'msp_faq' => array('singular' => 'FAQ', 'plural' => 'FAQs', 'icon' => 'dashicons-editor-help', 'supports' => array('title', 'editor', 'revisions', 'page-attributes')),
        'msp_outcome' => array('singular' => 'Business Outcome', 'plural' => 'Business Outcomes', 'icon' => 'dashicons-chart-area', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes')),
        'msp_partner' => array('singular' => 'Partner', 'plural' => 'Partners', 'icon' => 'dashicons-networking', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes')),
        'msp_certification' => array('singular' => 'Certification or Award', 'plural' => 'Certifications and Awards', 'icon' => 'dashicons-awards', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes')),
        'msp_pricing_plan' => array('singular' => 'Pricing Plan', 'plural' => 'Pricing Plans', 'icon' => 'dashicons-money-alt', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes')),
        'msp_event' => array('singular' => 'Webinar or Event', 'plural' => 'Webinars and Events', 'icon' => 'dashicons-calendar-alt', 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions')),
    );

    /**
     * Each type's own fields: meta key => [label, kind]. kind is text, textarea, url or toggle.
     * Types listed here get 'custom-fields' support, without which WordPress keeps registered
     * meta out of the REST API and the block editor, so the fields could never be saved.
     *
     * @var array<string, array<string, array{0:string,1:string}>>
     */
    public const FIELDS = array(
        'msp_case_study' => array('_msp_client_industry' => array('Client industry', 'text'), '_msp_result_summary' => array('Result summary', 'textarea'), '_msp_result_metric' => array('Headline metric', 'text')),
        'msp_location' => array('_msp_address' => array('Address', 'textarea'), '_msp_phone' => array('Phone', 'text'), '_msp_service_area' => array('Service area', 'text'), '_msp_coordinates' => array('Coordinates (lat,lng)', 'text'), '_msp_hours' => array('Hours', 'text'), '_msp_contact_email' => array('Contact email', 'text')),
        'msp_testimonial' => array('_msp_person_name' => array('Person', 'text'), '_msp_person_role' => array('Role', 'text'), '_msp_organization' => array('Organization', 'text')),
        'msp_team' => array('_msp_person_role' => array('Role', 'text'), '_msp_linkedin_url' => array('LinkedIn URL', 'url')),
        'msp_resource' => array('_msp_download_url' => array('Download URL', 'url'), '_msp_reading_time' => array('Reading time', 'text')),
        'msp_partner' => array('_msp_partner_url' => array('Partner URL', 'url'), '_msp_partner_tier' => array('Partner tier', 'text')),
        'msp_certification' => array('_msp_issuer' => array('Issuer', 'text'), '_msp_valid_through' => array('Valid through', 'text'), '_msp_verification_url' => array('Verification URL', 'url')),
        'msp_pricing_plan' => array('_msp_audience' => array('Audience', 'text'), '_msp_price_mode' => array('Price mode (for example Per month)', 'text'), '_msp_price_display' => array('Price shown', 'text'), '_msp_features' => array('Features (separate with ·)', 'textarea'), '_msp_highlighted' => array('Highlight this plan', 'toggle'), '_msp_disclaimer' => array('Disclaimer', 'textarea'), '_msp_cta_url' => array('Button URL', 'url')),
        'msp_event' => array('_msp_event_start' => array('Starts', 'text'), '_msp_event_end' => array('Ends', 'text'), '_msp_event_timezone' => array('Time zone', 'text'), '_msp_registration_url' => array('Registration URL', 'url'), '_msp_event_format' => array('Format', 'text')),
    );

    /** Admin menu that groups every content type instead of 13 top-level entries. */
    public const MENU_SLUG = 'msp-nexus-content';

    /** @return array<string, array{singular:string,plural:string,icon:string,supports:string[]}> */
    public static function types(): array
    {
        return self::TYPES;
    }

    private const FLUSH_FLAG = 'msp_nexus_flush_rewrites';

    /** @var array<int, string>|null */
    private $claimed_slugs = null;

    public function register(): void
    {
        foreach (self::TYPES as $slug => $config) {
            $this->register_type($slug, $config);
        }

        $this->register_taxonomies();
        $this->register_meta();

        add_action('transition_post_status', array($this, 'watch_page_slugs'), 10, 3);
        add_action('init', array($this, 'maybe_flush'), 99);
        add_action('enqueue_block_editor_assets', array($this, 'fields_panel'));
    }

    /**
     * Top-level published pages whose address matches an automatic listing (for example a
     * "Services" page at /services/). The page wins: that content type gets no listing, so
     * existing sites keep their URLs when the plugin is activated.
     *
     * @return array<int, string>
     */
    private function claimed_slugs(): array
    {
        if (null === $this->claimed_slugs) {
            global $wpdb;
            $slugs = array_map(array($this, 'archive_slug'), array_keys(self::TYPES));
            $placeholders = implode(',', array_fill(0, count($slugs), '%s'));
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- placeholders built above.
            $this->claimed_slugs = $wpdb->get_col($wpdb->prepare("SELECT post_name FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND post_parent = 0 AND post_name IN ($placeholders)", $slugs));
        }
        return $this->claimed_slugs;
    }

    /** @param \WP_Post $post */
    public function watch_page_slugs(string $new_status, string $old_status, $post): void
    {
        if (! $post instanceof \WP_Post || 'page' !== $post->post_type || ('publish' !== $new_status && 'publish' !== $old_status)) {
            return;
        }
        $slugs = array_map(array($this, 'archive_slug'), array_keys(self::TYPES));
        if (in_array($post->post_name, $slugs, true)) {
            // Post types are already registered for this request, so rebuild rules on the next one.
            update_option(self::FLUSH_FLAG, 1, false);
        }
    }

    public function maybe_flush(): void
    {
        if (get_option(self::FLUSH_FLAG)) {
            delete_option(self::FLUSH_FLAG);
            flush_rewrite_rules(false);
        }
    }

    /** @param array{singular:string,plural:string,icon:string,supports:string[]} $config */
    private function register_type(string $slug, array $config): void
    {
        $labels = array(
            'name'          => __($config['plural'], 'msp-nexus-core'),
            'singular_name' => __($config['singular'], 'msp-nexus-core'),
            'add_new_item'  => sprintf(__('Add New %s', 'msp-nexus-core'), $config['singular']),
            'edit_item'     => sprintf(__('Edit %s', 'msp-nexus-core'), $config['singular']),
            'view_item'     => sprintf(__('View %s', 'msp-nexus-core'), $config['singular']),
            'search_items'  => sprintf(__('Search %s', 'msp-nexus-core'), $config['plural']),
            'not_found'     => sprintf(__('No %s found.', 'msp-nexus-core'), strtolower($config['plural'])),
        );

        register_post_type(
            $slug,
            array(
                'labels'          => $labels,
                'public'          => true,
                'show_in_rest'    => true,
                'has_archive'     => ! in_array($slug, array('msp_testimonial', 'msp_faq'), true) && ! in_array($this->archive_slug($slug), $this->claimed_slugs(), true),
                'hierarchical'    => false,
                'show_in_menu'    => self::MENU_SLUG,
                'menu_icon'       => $config['icon'],
                'supports'        => isset(self::FIELDS[$slug]) ? array_values(array_unique(array_merge($config['supports'], array('custom-fields')))) : $config['supports'],
                'rewrite'         => array('slug' => $this->archive_slug($slug), 'with_front' => false),
                'template_lock'   => false,
                'map_meta_cap'    => true,
                'delete_with_user'=> false,
            )
        );
    }

    private function archive_slug(string $post_type): string
    {
        $slugs = array(
            'msp_service' => 'services', 'msp_industry' => 'industries', 'msp_case_study' => 'case-studies',
            'msp_location' => 'locations', 'msp_testimonial' => 'testimonials', 'msp_team' => 'team',
            'msp_resource' => 'resources', 'msp_faq' => 'faqs', 'msp_outcome' => 'outcomes',
            'msp_partner' => 'partners', 'msp_certification' => 'certifications',
            'msp_pricing_plan' => 'plans', 'msp_event' => 'events',
        );
        return $slugs[$post_type] ?? sanitize_title($post_type);
    }

    private function register_taxonomies(): void
    {
        register_taxonomy(
            'msp_service_category',
            array('msp_service', 'msp_case_study'),
            array(
                'labels' => array('name' => __('Service Categories', 'msp-nexus-core'), 'singular_name' => __('Service Category', 'msp-nexus-core')),
                'public' => true,
                'hierarchical' => true,
                'show_in_rest' => true,
                'rewrite' => array('slug' => 'service-category', 'with_front' => false),
            )
        );

        register_taxonomy(
            'msp_industry_category',
            array('msp_industry', 'msp_case_study'),
            array(
                'labels' => array('name' => __('Industry Categories', 'msp-nexus-core'), 'singular_name' => __('Industry Category', 'msp-nexus-core')),
                'public' => true,
                'hierarchical' => true,
                'show_in_rest' => true,
                'rewrite' => array('slug' => 'industry-category', 'with_front' => false),
            )
        );

        register_taxonomy(
            'msp_resource_type',
            array('msp_resource'),
            array(
                'labels' => array('name' => __('Resource Types', 'msp-nexus-core'), 'singular_name' => __('Resource Type', 'msp-nexus-core')),
                'public' => true,
                'hierarchical' => true,
                'show_in_rest' => true,
                'rewrite' => array('slug' => 'resource-type', 'with_front' => false),
            )
        );

        register_taxonomy(
            'msp_outcome_category',
            array('msp_outcome', 'msp_service', 'msp_case_study'),
            array('labels' => array('name' => __('Business Outcomes', 'msp-nexus-core'), 'singular_name' => __('Business Outcome', 'msp-nexus-core')), 'public' => true, 'hierarchical' => true, 'show_in_rest' => true, 'rewrite' => array('slug' => 'business-outcome', 'with_front' => false))
        );

        register_taxonomy(
            'msp_topic',
            array('msp_resource', 'msp_event', 'msp_case_study'),
            array('labels' => array('name' => __('Topics', 'msp-nexus-core'), 'singular_name' => __('Topic', 'msp-nexus-core')), 'public' => true, 'hierarchical' => true, 'show_in_rest' => true, 'rewrite' => array('slug' => 'topic', 'with_front' => false))
        );

        register_taxonomy(
            'msp_department',
            array('msp_team'),
            array('labels' => array('name' => __('Departments', 'msp-nexus-core'), 'singular_name' => __('Department', 'msp-nexus-core')), 'public' => true, 'hierarchical' => true, 'show_in_rest' => true, 'rewrite' => array('slug' => 'department', 'with_front' => false))
        );
    }

    /** The Details panel in the block editor, for the type being edited. */
    public function fields_panel(): void
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        $type = $screen ? (string) $screen->post_type : '';
        if (! isset(self::FIELDS[$type])) {
            return;
        }
        $fields = array();
        foreach (self::FIELDS[$type] as $key => $field) {
            $fields[] = array('key' => $key, 'label' => __($field[0], 'msp-nexus-core'), 'kind' => $field[1]);
        }
        wp_enqueue_script('msp-nexus-content-fields', plugins_url('assets/content-fields.js', MSP_NEXUS_CORE_FILE), array('wp-plugins', 'wp-edit-post', 'wp-editor', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n'), MSP_NEXUS_CORE_VERSION, true);
        wp_localize_script('msp-nexus-content-fields', 'mspNexusContentFields', array('title' => __('Details', 'msp-nexus-core'), 'fields' => $fields));
    }

    private function register_meta(): void
    {
        $fields = array_map(static fn (array $f): array => array_keys($f), self::FIELDS);

        foreach ($fields as $post_type => $keys) {
            foreach ($keys as $key) {
                register_post_meta(
                    $post_type,
                    $key,
                    array(
                        'type' => 'string',
                        'single' => true,
                        'show_in_rest' => true,
                        'sanitize_callback' => '_url' === substr($key, -4) ? 'esc_url_raw' : 'sanitize_text_field',
                        'auth_callback' => static fn (): bool => current_user_can('edit_posts'),
                    )
                );
            }
        }

        foreach (array_keys(self::TYPES) as $post_type) {
            register_post_meta(
                $post_type,
                '_msp_nexus_demo_key',
                array(
                    'type' => 'string',
                    'single' => true,
                    'show_in_rest' => false,
                    'sanitize_callback' => 'sanitize_key',
                    'auth_callback' => static fn (): bool => current_user_can('manage_options'),
                )
            );
        }
    }
}
