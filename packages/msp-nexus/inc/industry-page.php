<?php
/**
 * Shared industry page template. Healthcare, legal, and manufacturing pages render the same
 * structure with their own copy and accent, so every industry page in a site stays consistent.
 *
 * @package MspNexus
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @param array<string, mixed> $c Page copy: modifier, pill, title, title_accent, lede, cta, cta_secondary,
 *                                status_title, status (label => state), types_title, types (name => detail),
 *                                flow_title, flow_body, flow_checks, image, image_alt, program_title,
 *                                program (name => detail), assurance_title, assurance_body, note, faq_title,
 *                                close_title, close_body, close_cta.
 */
function msp_nexus_industry_page(array $c): string
{
    $e = static function ($value): string {
        return esc_html((string) $value);
    };
    $modifier = '' !== (string) ($c['modifier'] ?? '') ? ' nx-vt--' . sanitize_html_class((string) $c['modifier']) : '';
    $section = static function (string $class, string $inner, string $layout = '{"type":"constrained"}', string $anchor = '') use ($modifier): string {
        $classes = 'nx-vt ' . $class . $modifier;
        $anchor_attr = '' !== $anchor ? ',"anchor":"' . esc_attr($anchor) . '"' : '';
        $id_attr = '' !== $anchor ? ' id="' . esc_attr($anchor) . '"' : '';
        return '<!-- wp:group {"tagName":"section","align":"full"' . $anchor_attr . ',"className":"' . esc_attr($classes) . '","layout":' . $layout . '} --><section' . $id_attr . ' class="wp-block-group alignfull ' . esc_attr($classes) . '">' . $inner . '</section><!-- /wp:group -->';
    };
    $para = static function (string $text, string $class = ''): string {
        $attrs = '' !== $class ? ' {"className":"' . esc_attr($class) . '"}' : '';
        $cls = '' !== $class ? ' class="' . esc_attr($class) . '"' : '';
        return '<!-- wp:paragraph' . $attrs . ' --><p' . $cls . '>' . $text . '</p><!-- /wp:paragraph -->';
    };
    $heading = static function (string $text, string $class, int $level = 2, bool $center = false): string {
        $attrs = array('className' => $class);
        if (2 !== $level) {
            $attrs['level'] = $level;
        }
        if ($center) {
            $attrs['textAlign'] = 'center';
        }
        $align = $center ? ' has-text-align-center' : '';
        return '<!-- wp:heading ' . wp_json_encode($attrs) . ' --><h' . $level . ' class="wp-block-heading' . $align . ' ' . esc_attr($class) . '">' . $text . '</h' . $level . '><!-- /wp:heading -->';
    };
    $list = static function (array $items, string $class, bool $ordered = false, string $align = ''): string {
        $attrs = array('className' => $class);
        if ($ordered) {
            $attrs['ordered'] = true;
        }
        if ('' !== $align) {
            $attrs['align'] = $align;
        }
        $tag = $ordered ? 'ol' : 'ul';
        $out = '<!-- wp:list ' . wp_json_encode($attrs) . ' --><' . $tag . ' class="wp-block-list' . ('' !== $align ? ' align' . $align : '') . ' ' . esc_attr($class) . '">';
        foreach ($items as $item) {
            $out .= '<!-- wp:list-item --><li>' . $item . '</li><!-- /wp:list-item -->';
        }
        return $out . '</' . $tag . '><!-- /wp:list -->';
    };
    $button = static function (string $label, string $href, string $class): string {
        return '<!-- wp:button {"className":"' . esc_attr($class) . '"} --><div class="wp-block-button ' . esc_attr($class) . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url($href) . '">' . $label . '</a></div><!-- /wp:button -->';
    };

    $status = array();
    foreach ((array) $c['status'] as $label => $state) {
        $status[] = $e($label) . '<b>' . $e($state) . '</b>';
    }
    $types = array();
    foreach ((array) $c['types'] as $name => $detail) {
        $types[] = '<strong>' . $e($name) . '</strong>' . $e($detail);
    }
    $program = array();
    foreach ((array) $c['program'] as $name => $detail) {
        $program[] = '<strong>' . $e($name) . '</strong>' . $e($detail);
    }

    $hero = '<!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-vt-hero__grid"} --><div class="wp-block-columns alignwide are-vertically-aligned-center nx-vt-hero__grid"><!-- wp:column {"verticalAlignment":"center","width":"55%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">'
        . $para($e($c['pill']), 'nx-vt-pill')
        . $heading($e($c['title']) . ' <span>' . $e($c['title_accent']) . '</span>', 'nx-vt-hero__title', 1)
        . $para($e($c['lede']), 'nx-vt-hero__lede')
        . '<!-- wp:buttons {"className":"nx-vt-actions"} --><div class="wp-block-buttons nx-vt-actions">' . $button($e($c['cta']), '/contact/', 'nx-vt-btn') . $button($e($c['cta_secondary']), '#program', 'nx-vt-btn-soft') . '</div><!-- /wp:buttons -->'
        . '</div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"45%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%"><!-- wp:group {"className":"nx-vt-status","layout":{"type":"default"}} --><div class="wp-block-group nx-vt-status">'
        . $para($e($c['status_title']), 'nx-vt-status__head') . $list($status, 'nx-vt-status__list')
        . '</div><!-- /wp:group --></div><!-- /wp:column --></div><!-- /wp:columns -->';

    $flow = '<!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"nx-vt-uptime__grid"} --><div class="wp-block-columns alignwide are-vertically-aligned-center nx-vt-uptime__grid"><!-- wp:column {"verticalAlignment":"center","width":"50%","className":"nx-vt-uptime__media"} --><div class="wp-block-column is-vertically-aligned-center nx-vt-uptime__media" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="' . esc_url(get_theme_file_uri('assets/images/stock/' . $c['image'] . '.webp')) . '" alt="' . esc_attr((string) $c['image_alt']) . '" loading="lazy" decoding="async"/></figure><!-- /wp:image --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"50%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">'
        . $heading($e($c['flow_title']), 'nx-vt-title') . $para($e($c['flow_body'])) . $list(array_map($e, (array) $c['flow_checks']), 'nx-vt-checks')
        . '</div><!-- /wp:column --></div><!-- /wp:columns -->';

    $assurance = '<!-- wp:group {"align":"wide","className":"nx-vt-baa","layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} --><div class="wp-block-group alignwide nx-vt-baa">' . $para('', 'nx-vt-baa__icon nx-icon-shield') . $para('<strong>' . $e($c['assurance_title']) . '</strong> ' . $e($c['assurance_body'])) . '</div><!-- /wp:group -->';

    $close = '<!-- wp:group {"align":"wide","className":"nx-vt-close__panel","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} --><div class="wp-block-group alignwide nx-vt-close__panel"><!-- wp:group {"layout":{"type":"default"}} --><div class="wp-block-group">'
        . $heading($e($c['close_title']), 'nx-vt-close__title') . $para($e($c['close_body']))
        . '</div><!-- /wp:group --><!-- wp:buttons --><div class="wp-block-buttons">' . $button($e($c['close_cta']), '/contact/', 'nx-vt-btn nx-vt-btn--white') . '</div><!-- /wp:buttons --></div><!-- /wp:group -->';

    return $section('nx-vt-hero', $hero)
        . $section('nx-vt-practices', $heading($e($c['types_title']), 'nx-vt-title', 2, true) . $list($types, 'nx-vt-types', false, 'wide'))
        . $section('nx-vt-uptime', $flow)
        . $section('nx-vt-program', $heading($e($c['program_title']), 'nx-vt-title', 2, true) . $list($program, 'nx-vt-steps', true, 'wide') . $assurance . $para($e($c['note']), 'nx-vt-note alignwide'), '{"type":"constrained"}', 'program')
        . $section('nx-vt-faq', $heading($e($c['faq_title']), 'nx-vt-title', 2, true) . '<!-- wp:msp-nexus/faq-list {"count":6} /-->', '{"type":"constrained","contentSize":"48rem"}')
        . $section('nx-vt-close', $close);
}
