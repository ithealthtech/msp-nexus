<?php
/**
 * Dynamic block registration.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Blocks;

final class Registry
{
    public function register(): void
    {
        wp_register_script(
            'msp-nexus-lottie',
            plugins_url('assets/vendor/lottie-light.min.js', MSP_NEXUS_CORE_FILE),
            array(),
            '5.13.0',
            true
        );
        wp_register_script(
            'msp-nexus-core-blocks-editor',
            plugins_url('assets/blocks-editor.js', MSP_NEXUS_CORE_FILE),
            array('wp-api-fetch', 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-element', 'wp-i18n', 'wp-server-side-render'),
            MSP_NEXUS_CORE_VERSION,
            true
        );
        wp_register_script(
            'msp-nexus-studio-editor',
            plugins_url('assets/studio-editor.js', MSP_NEXUS_CORE_FILE),
            array('wp-blocks', 'wp-block-editor', 'wp-components', 'wp-compose', 'wp-element', 'wp-hooks', 'wp-i18n'),
            MSP_NEXUS_CORE_VERSION,
            true
        );
        wp_register_script(
            'msp-nexus-studio-workspace',
            plugins_url('assets/studio-workspace.js', MSP_NEXUS_CORE_FILE),
            array('wp-api-fetch', 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-i18n', 'wp-plugins'),
            MSP_NEXUS_CORE_VERSION,
            true
        );
        wp_register_style(
            'msp-nexus-studio',
            plugins_url('assets/studio.css', MSP_NEXUS_CORE_FILE),
            array(),
            MSP_NEXUS_CORE_VERSION
        );
        wp_register_style(
            'msp-nexus-woocommerce',
            plugins_url('assets/woocommerce.css', MSP_NEXUS_CORE_FILE),
            array('msp-nexus-studio'),
            MSP_NEXUS_CORE_VERSION
        );
        wp_register_style(
            'msp-nexus-studio-workspace',
            plugins_url('assets/studio-workspace.css', MSP_NEXUS_CORE_FILE),
            array('msp-nexus-studio'),
            MSP_NEXUS_CORE_VERSION
        );
        wp_register_script(
            'msp-nexus-header-runtime',
            plugins_url('assets/header-runtime.js', MSP_NEXUS_CORE_FILE),
            array(),
            MSP_NEXUS_CORE_VERSION,
            true
        );
        wp_register_style(
            'msp-nexus-header-runtime',
            plugins_url('assets/header-runtime.css', MSP_NEXUS_CORE_FILE),
            array('msp-nexus-studio'),
            MSP_NEXUS_CORE_VERSION
        );
        wp_register_style(
            'msp-nexus-media-effects',
            plugins_url('assets/media-effects.css', MSP_NEXUS_CORE_FILE),
            array('msp-nexus-studio'),
            MSP_NEXUS_CORE_VERSION
        );
        $blocks = glob(MSP_NEXUS_CORE_PATH . 'blocks/*/block.json');
        if (! is_array($blocks)) {
            return;
        }

        foreach ($blocks as $metadata) {
            register_block_type(dirname($metadata));
        }

        add_filter(
            'block_categories_all',
            static function (array $categories): array {
                array_unshift($categories, array('slug' => 'msp-nexus', 'title' => __('MSP Nexus Studio', 'msp-nexus-core')));
                return $categories;
            }
        );
    }
}
