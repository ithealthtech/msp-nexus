<?php
/**
 * Native block-binding sources for organization settings.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Content;

final class Bindings
{
    public function register(): void
    {
        if (! function_exists('register_block_bindings_source')) {
            return;
        }
        register_block_bindings_source(
            'msp-nexus/settings',
            array(
                'label' => __('MSP Nexus organization setting', 'msp-nexus-core'),
                'get_value_callback' => array($this, 'value'),
                'uses_context' => array(),
            )
        );
    }

    /**
     * @param array<string, mixed> $source_args Binding arguments.
     * @param mixed $block_instance Block instance.
     * @param string $attribute_name Bound attribute.
     * @return string|null
     */
    public function value(array $source_args, $block_instance, string $attribute_name): ?string
    {
        unset($block_instance);
        $field = sanitize_key((string) ($source_args['field'] ?? ''));
        $allowed = array('organization_name', 'sales_phone', 'support_phone', 'support_url', 'sales_email', 'cta_url', 'announcement', 'linkedin_url', 'facebook_url', 'youtube_url', 'company_address', 'business_hours', 'emergency_message');
        if (! in_array($field, $allowed, true)) {
            return null;
        }
        $settings = get_option('msp_nexus_settings', array());
        if (! is_array($settings)) {
            return null;
        }
        $value = (string) ($settings[$field] ?? '');
        if ('' === trim($value)) {
            return null;
        }
        if ('url' === $attribute_name && in_array($field, array('sales_phone', 'support_phone'), true)) {
            return 'tel:' . preg_replace('/[^0-9+]/', '', $value);
        }
        if ('url' === $attribute_name && 'sales_email' === $field) {
            return 'mailto:' . sanitize_email($value);
        }
        return $value;
    }
}
