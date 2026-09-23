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
                'supports'        => $config['supports'],
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

    private function register_meta(): void
    {
        $fields = array(
            'msp_case_study' => array('_msp_client_industry', '_msp_result_summary', '_msp_result_metric'),
            'msp_location' => array('_msp_address', '_msp_phone', '_msp_service_area', '_msp_coordinates', '_msp_hours', '_msp_contact_email'),
            'msp_testimonial' => array('_msp_person_name', '_msp_person_role', '_msp_organization'),
            'msp_team' => array('_msp_person_role', '_msp_linkedin_url'),
            'msp_resource' => array('_msp_download_url', '_msp_reading_time'),
            'msp_partner' => array('_msp_partner_url', '_msp_partner_tier'),
            'msp_certification' => array('_msp_issuer', '_msp_valid_through', '_msp_verification_url'),
            'msp_pricing_plan' => array('_msp_audience', '_msp_price_mode', '_msp_price_display', '_msp_features', '_msp_highlighted', '_msp_disclaimer', '_msp_cta_url'),
            'msp_event' => array('_msp_event_start', '_msp_event_end', '_msp_event_timezone', '_msp_registration_url', '_msp_event_format'),
        );

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
