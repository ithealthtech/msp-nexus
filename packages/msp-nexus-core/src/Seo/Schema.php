<?php
/**
 * Conservative structured data for MSP-owned content types.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Seo;

final class Schema
{
    public function register_hooks(): void
    {
        add_action('wp_head', array($this, 'render'), 30);
    }

    public function render(): void
    {
        $settings = get_option('msp_nexus_settings', array());
        if (is_array($settings) && array_key_exists('enable_schema', $settings) && empty($settings['enable_schema'])) {
            return;
        }
        if (! is_singular() || $this->seo_plugin_controls_schema() || ! apply_filters('msp_nexus_schema_enabled', true)) {
            return;
        }
        $post = get_queried_object();
        if (! $post instanceof \WP_Post || '' === trim(wp_strip_all_tags($post->post_content))) {
            return;
        }
        $schema = $this->schema_for($post);
        if (null === $schema) {
            return;
        }
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /** @return array<string, mixed>|null */
    private function schema_for(\WP_Post $post): ?array
    {
        $base = array('@context' => 'https://schema.org', 'name' => get_the_title($post), 'url' => get_permalink($post));
        if ('msp_service' === $post->post_type) {
            return array_merge($base, array('@type' => 'Service', 'description' => $this->description($post), 'provider' => array('@type' => 'Organization', 'name' => get_bloginfo('name'), 'url' => home_url('/'))));
        }
        if ('msp_resource' === $post->post_type) {
            $schema = array_merge($base, array('@type' => 'Article', 'description' => $this->description($post), 'datePublished' => get_the_date(DATE_W3C, $post), 'dateModified' => get_the_modified_date(DATE_W3C, $post), 'author' => array('@type' => 'Organization', 'name' => get_bloginfo('name'))));
            $image = get_the_post_thumbnail_url($post, 'full');
            if ($image) {
                $schema['image'] = esc_url_raw($image);
            }
            return $schema;
        }
        if ('msp_event' === $post->post_type) {
            $schema = array_merge($base, array(
                '@type' => 'Event',
                'description' => $this->description($post),
                'startDate' => (string) get_post_meta($post->ID, '_msp_event_start', true),
                'endDate' => (string) get_post_meta($post->ID, '_msp_event_end', true),
                'eventAttendanceMode' => 'https://schema.org/OnlineEventAttendanceMode',
                'eventStatus' => 'https://schema.org/EventScheduled',
                'organizer' => array('@type' => 'Organization', 'name' => get_bloginfo('name'), 'url' => home_url('/')),
            ));
            $registration = esc_url_raw((string) get_post_meta($post->ID, '_msp_registration_url', true));
            if ('' !== $registration) {
                $schema['offers'] = array('@type' => 'Offer', 'url' => $registration, 'availability' => 'https://schema.org/InStock', 'price' => '0', 'priceCurrency' => 'USD');
            }
            return $schema;
        }
        if ('msp_team' === $post->post_type) {
            return array_merge($base, array('@type' => 'Person', 'description' => $this->description($post), 'jobTitle' => (string) get_post_meta($post->ID, '_msp_person_role', true), 'worksFor' => array('@type' => 'Organization', 'name' => get_bloginfo('name'), 'url' => home_url('/'))));
        }
        if ('msp_location' === $post->post_type) {
            $settings = get_option('msp_nexus_settings', array());
            $organization = is_array($settings) ? (string) ($settings['organization_name'] ?? get_bloginfo('name')) : get_bloginfo('name');
            return array_merge($base, array('@type' => 'ProfessionalService', 'description' => $this->description($post), 'parentOrganization' => array('@type' => 'Organization', 'name' => $organization, 'url' => home_url('/')), 'address' => array('@type' => 'PostalAddress', 'streetAddress' => (string) get_post_meta($post->ID, '_msp_address', true)), 'telephone' => (string) get_post_meta($post->ID, '_msp_phone', true), 'areaServed' => (string) get_post_meta($post->ID, '_msp_service_area', true)));
        }
        if ('msp_faq' === $post->post_type) {
            return array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array(array('@type' => 'Question', 'name' => get_the_title($post), 'acceptedAnswer' => array('@type' => 'Answer', 'text' => trim(wp_strip_all_tags($post->post_content))))));
        }
        return null;
    }

    private function description(\WP_Post $post): string
    {
        $source = '' !== trim($post->post_excerpt) ? $post->post_excerpt : $post->post_content;
        return wp_trim_words(wp_strip_all_tags($source), 35, '…');
    }

    private function seo_plugin_controls_schema(): bool
    {
        return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION');
    }
}
