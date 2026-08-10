<?php
/**
 * Theme bootstrap.
 *
 * @package MspNexus
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('MSP_NEXUS_VERSION', '0.4.0');

require_once get_theme_file_path('inc/patterns.php');

/**
 * Register editor and front-end assets.
 */
function msp_nexus_setup(): void
{
    load_theme_textdomain('msp-nexus', get_theme_file_path('languages'));
    add_editor_style('assets/css/editor.css');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
}
add_action('after_setup_theme', 'msp_nexus_setup');

/**
 * Load the small global stylesheet. Component styles remain block-scoped.
 */
function msp_nexus_enqueue_assets(): void
{
    wp_enqueue_style(
        'msp-nexus-global',
        get_theme_file_uri('assets/css/global.css'),
        array(),
        MSP_NEXUS_VERSION
    );
}
add_action('wp_enqueue_scripts', 'msp_nexus_enqueue_assets');

/** Build a concise description for pages that do not have one yet. */
function msp_nexus_document_description(): string
{
    $description = '';
    if (is_singular()) {
        $post = get_queried_object();
        if ($post instanceof WP_Post) {
            $description = '' !== trim($post->post_excerpt) ? $post->post_excerpt : $post->post_content;
        }
    } elseif (is_category() || is_tag() || is_tax()) {
        $description = term_description();
    }

    $description = trim(wp_strip_all_tags(strip_shortcodes((string) $description)));
    if ('' === $description && (is_front_page() || is_home())) {
        $description = trim((string) get_bloginfo('description', 'display'));
    }
    if ('' === $description) {
        $description = sprintf(
            __('%s provides managed IT, cybersecurity, cloud, infrastructure, and technology advisory services.', 'msp-nexus'),
            get_bloginfo('name')
        );
    }
    return wp_html_excerpt($description, 160, '…');
}

/** Output one metadata description when a dedicated SEO plugin is not active. */
function msp_nexus_meta_description(): void
{
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION')) {
        return;
    }
    if (is_front_page() || is_home() || is_singular() || is_category() || is_tag() || is_tax() || is_post_type_archive()) {
        echo '<meta name="description" content="' . esc_attr(msp_nexus_document_description()) . '">' . "\n";
    }
}
add_action('wp_head', 'msp_nexus_meta_description', 1);

/** Supply a fallback only when Yoast or Rank Math has no description configured. */
function msp_nexus_seo_description_fallback($description): string
{
    $description = trim((string) $description);
    return '' !== $description ? $description : msp_nexus_document_description();
}
add_filter('wpseo_metadesc', 'msp_nexus_seo_description_fallback', 20);
add_filter('rank_math/frontend/description', 'msp_nexus_seo_description_fallback', 20);

/**
 * Add the theme's curated pattern categories.
 */
function msp_nexus_register_pattern_categories(): void
{
    $categories = array(
        'msp-nexus-heroes'     => __('MSP Nexus: Heroes', 'msp-nexus'),
        'msp-nexus-services'   => __('MSP Nexus: Services', 'msp-nexus'),
        'msp-nexus-trust'      => __('MSP Nexus: Trust & proof', 'msp-nexus'),
        'msp-nexus-content'    => __('MSP Nexus: Content', 'msp-nexus'),
        'msp-nexus-conversion' => __('MSP Nexus: Conversion', 'msp-nexus'),
        'msp-nexus-pages'      => __('MSP Nexus: Page starters', 'msp-nexus'),
    );

    foreach ($categories as $slug => $label) {
        register_block_pattern_category($slug, array('label' => $label));
    }
}
add_action('init', 'msp_nexus_register_pattern_categories');

/** Register lightweight visual variants for useful core blocks. */
function msp_nexus_register_block_styles(): void
{
    $styles = array(
        'core/button' => array('arrow' => __('Arrow action', 'msp-nexus'), 'soft' => __('Soft action', 'msp-nexus')),
        'core/group' => array('surface' => __('Elevated surface', 'msp-nexus'), 'signal' => __('Signal panel', 'msp-nexus')),
        'core/columns' => array('divided' => __('Divided columns', 'msp-nexus')),
        'core/image' => array('technology-frame' => __('Technology frame', 'msp-nexus')),
        'core/list' => array('checks' => __('Check list', 'msp-nexus')),
        'core/quote' => array('executive' => __('Executive quote', 'msp-nexus')),
        'core/navigation' => array('utility' => __('Utility navigation', 'msp-nexus')),
        'core/query' => array('card-grid' => __('Card grid', 'msp-nexus')),
        'core/table' => array('comparison' => __('Comparison table', 'msp-nexus')),
    );
    foreach ($styles as $block => $variants) {
        foreach ($variants as $name => $label) {
            register_block_style($block, array('name' => $name, 'label' => $label));
        }
    }
}
add_action('init', 'msp_nexus_register_block_styles');
