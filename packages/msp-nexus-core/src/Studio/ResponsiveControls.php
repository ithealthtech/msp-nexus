<?php
/**
 * Portable per-device visibility, spacing, and motion controls.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Studio;

final class ResponsiveControls
{
    public function register_hooks(): void
    {
        add_filter('register_block_type_args', array($this, 'attributes'));
        add_filter('render_block', array($this, 'render'), 20, 2);
        add_action('enqueue_block_editor_assets', array($this, 'editor_assets'));
        add_action('wp_enqueue_scripts', array($this, 'front_assets'));
    }

    /** @param array<string, mixed> $args
     *  @return array<string, mixed>
     */
    public function attributes(array $args): array
    {
        $args['attributes'] = isset($args['attributes']) && is_array($args['attributes']) ? $args['attributes'] : array();
        foreach (array('mspHideDesktop', 'mspHideTablet', 'mspHideMobile') as $name) {
            $args['attributes'][$name] = array('type' => 'boolean', 'default' => false);
        }
        foreach (array('mspPaddingDesktop', 'mspPaddingTablet', 'mspPaddingMobile') as $name) {
            $args['attributes'][$name] = array('type' => 'string', 'default' => '');
        }
        $args['attributes']['mspEntranceAnimation'] = array('type' => 'string', 'default' => 'none');
        $args['attributes']['mspDeviceStyles'] = array('type' => 'object', 'default' => array());
        $args['attributes']['mspConditions'] = array('type' => 'object', 'default' => array());
        return $args;
    }

    /** @param array<string, mixed> $block */
    public function render(string $content, array $block): string
    {
        $attrs = is_array($block['attrs'] ?? null) ? $block['attrs'] : array();
        if (! is_admin() && ! empty(array_filter(is_array($attrs['mspConditions'] ?? null) ? $attrs['mspConditions'] : array()))) {
            if (! defined('DONOTCACHEPAGE')) {
                define('DONOTCACHEPAGE', true);
            }
        }
        if (! is_admin() && ! $this->conditions_match(is_array($attrs['mspConditions'] ?? null) ? $attrs['mspConditions'] : array())) {
            return '';
        }
        $classes = array();
        if (! empty($attrs['mspHideDesktop'])) {
            $classes[] = 'msp-hide-desktop';
        }
        if (! empty($attrs['mspHideTablet'])) {
            $classes[] = 'msp-hide-tablet';
        }
        if (! empty($attrs['mspHideMobile'])) {
            $classes[] = 'msp-hide-mobile';
        }
        $animation = sanitize_key((string) ($attrs['mspEntranceAnimation'] ?? 'none'));
        if (in_array($animation, array('fade', 'rise', 'slide'), true)) {
            $classes[] = 'msp-motion-' . $animation;
        }
        $styles = array();
        foreach (array('Desktop' => 'desktop', 'Tablet' => 'tablet', 'Mobile' => 'mobile') as $suffix => $device) {
            $value = $this->css_length((string) ($attrs['mspPadding' . $suffix] ?? ''));
            if ('' !== $value) {
                $styles[] = '--msp-padding-' . $device . ':' . $value;
                $classes[] = 'msp-responsive-padding';
            }
        }
        $device_styles = is_array($attrs['mspDeviceStyles'] ?? null) ? $attrs['mspDeviceStyles'] : array();
        foreach (array('desktop', 'tablet', 'mobile') as $device) {
            $values = is_array($device_styles[$device] ?? null) ? $device_styles[$device] : array();
            foreach ($this->style_properties() as $property => $type) {
                $value = $this->css_value((string) ($values[$property] ?? ''), $type);
                if ('' === $value) {
                    continue;
                }
                $styles[] = '--msp-' . $device . '-' . strtolower((string) preg_replace('/([A-Z])/', '-$1', $property)) . ':' . $value;
                $classes[] = 'msp-' . $device . '-' . strtolower((string) preg_replace('/([A-Z])/', '-$1', $property));
            }
            $rotate = $this->css_value((string) ($values['rotate'] ?? ''), 'angle');
            $scale = $this->css_value((string) ($values['scale'] ?? ''), 'scale');
            $translate_x = $this->css_value((string) ($values['translateX'] ?? ''), 'length');
            $translate_y = $this->css_value((string) ($values['translateY'] ?? ''), 'length');
            if ('' !== $rotate || '' !== $scale || '' !== $translate_x || '' !== $translate_y) {
                $styles[] = '--msp-' . $device . '-transform:translate(' . ($translate_x ?: '0') . ',' . ($translate_y ?: '0') . ') rotate(' . ($rotate ?: '0deg') . ') scale(' . ($scale ?: '1') . ')';
                $classes[] = 'msp-' . $device . '-transform';
            }
        }
        if (! $classes && ! $styles) {
            return $content;
        }
        if (! class_exists('WP_HTML_Tag_Processor')) {
            return $content;
        }
        $processor = new \WP_HTML_Tag_Processor($content);
        if (! $processor->next_tag()) {
            return $content;
        }
        foreach (array_unique($classes) as $class) {
            $processor->add_class($class);
        }
        if ($styles) {
            $existing = trim((string) $processor->get_attribute('style'));
            $processor->set_attribute('style', ($existing ? rtrim($existing, ';') . ';' : '') . implode(';', $styles));
        }
        return $processor->get_updated_html();
    }

    public function editor_assets(): void
    {
        wp_enqueue_script('msp-nexus-studio-editor');
        wp_enqueue_script('msp-nexus-studio-workspace');
        wp_enqueue_style('msp-nexus-studio');
        wp_enqueue_style('msp-nexus-studio-workspace');
        wp_enqueue_style('msp-nexus-media-effects');
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        wp_localize_script(
            'msp-nexus-studio-workspace',
            'mspNexusWorkspace',
            array(
                'studioMode' => isset($_GET['nexus-studio']) && '1' === sanitize_text_field(wp_unslash((string) $_GET['nexus-studio'])),
                'postType' => $screen && isset($screen->post_type) ? (string) $screen->post_type : '',
                'studioUrl' => admin_url('admin.php?page=msp-nexus-studio'),
                'layoutsUrl' => admin_url('edit.php?post_type=msp_nexus_layout'),
                'revisionUrl' => admin_url('revision.php?action=edit&revision='),
                'nonce' => wp_create_nonce('wp_rest'),
            )
        );
    }

    public function front_assets(): void
    {
        wp_enqueue_style('msp-nexus-studio');
        wp_enqueue_style('msp-nexus-media-effects');
    }

    private function css_length(string $value): string
    {
        $value = trim($value);
        if (preg_match('/^(?:0|[0-9]+(?:\.[0-9]+)?(?:px|rem|em|%|vw|vh))$/', $value)) {
            return $value;
        }
        return '';
    }

    /** @return array<string, string> */
    private function style_properties(): array
    {
        return array(
            'padding' => 'length-list', 'margin' => 'length-list', 'gap' => 'length', 'width' => 'size', 'maxWidth' => 'size',
            'minHeight' => 'size', 'fontSize' => 'length', 'lineHeight' => 'line-height', 'textAlign' => 'text-align', 'order' => 'integer',
            'position' => 'position', 'top' => 'offset', 'right' => 'offset', 'bottom' => 'offset', 'left' => 'offset', 'zIndex' => 'integer',
            'opacity' => 'opacity', 'display' => 'display', 'backgroundColor' => 'color', 'borderRadius' => 'length-list',
        );
    }

    private function css_value(string $value, string $type): string
    {
        $value = trim($value);
        if ('' === $value) {
            return '';
        }
        $length = '-?(?:0|[0-9]+(?:\.[0-9]+)?(?:px|rem|em|%|vw|vh|ch))';
        if ('length' === $type && preg_match('/^' . $length . '$/', $value)) {
            return $value;
        }
        if ('length-list' === $type && preg_match('/^' . $length . '(?:\s+' . $length . '){0,3}$/', $value)) {
            return $value;
        }
        if ('size' === $type && ('auto' === $value || preg_match('/^' . $length . '$/', $value))) {
            return $value;
        }
        if ('offset' === $type && ('auto' === $value || preg_match('/^' . $length . '$/', $value))) {
            return $value;
        }
        if ('integer' === $type && preg_match('/^-?[0-9]{1,5}$/', $value)) {
            return (string) (int) $value;
        }
        if ('line-height' === $type && preg_match('/^(?:[0-9]+(?:\.[0-9]+)?|' . $length . ')$/', $value)) {
            return $value;
        }
        if ('opacity' === $type && is_numeric($value) && (float) $value >= 0 && (float) $value <= 1) {
            return (string) (float) $value;
        }
        if ('angle' === $type && preg_match('/^-?[0-9]{1,3}(?:\.[0-9]+)?deg$/', $value)) {
            return $value;
        }
        if ('scale' === $type && is_numeric($value) && (float) $value >= 0.1 && (float) $value <= 5) {
            return (string) (float) $value;
        }
        if ('text-align' === $type && in_array($value, array('left', 'center', 'right', 'justify', 'start', 'end'), true)) {
            return $value;
        }
        if ('position' === $type && in_array($value, array('static', 'relative', 'absolute', 'sticky'), true)) {
            return $value;
        }
        if ('display' === $type && in_array($value, array('block', 'inline-block', 'flex', 'grid', 'none'), true)) {
            return $value;
        }
        if ('color' === $type) {
            return sanitize_hex_color($value) ?: '';
        }
        return '';
    }

    /** @param array<string, mixed> $conditions */
    private function conditions_match(array $conditions): bool
    {
        $user_state = sanitize_key((string) ($conditions['userState'] ?? 'any'));
        if ('logged_in' === $user_state && ! is_user_logged_in()) {
            return false;
        }
        if ('logged_out' === $user_state && is_user_logged_in()) {
            return false;
        }
        $roles = array_values(array_filter(array_map('sanitize_key', explode(',', (string) ($conditions['roles'] ?? '')))));
        if ($roles) {
            $user = wp_get_current_user();
            if (! array_intersect($roles, (array) $user->roles)) {
                return false;
            }
        }
        $now = current_time('timestamp');
        $start = sanitize_text_field((string) ($conditions['dateStart'] ?? ''));
        $end = sanitize_text_field((string) ($conditions['dateEnd'] ?? ''));
        if ('' !== $start) {
            $start_time = strtotime($start . ' 00:00:00');
            if (false !== $start_time && $now < $start_time) {
                return false;
            }
        }
        if ('' !== $end) {
            $end_time = strtotime($end . ' 23:59:59');
            if (false !== $end_time && $now > $end_time) {
                return false;
            }
        }
        $weekdays = array_values(array_filter(array_map('absint', explode(',', (string) ($conditions['weekdays'] ?? '')))));
        if ($weekdays && ! in_array((int) wp_date('N', $now), $weekdays, true)) {
            return false;
        }
        $post_types = array_values(array_filter(array_map('sanitize_key', explode(',', (string) ($conditions['postTypes'] ?? '')))));
        if ($post_types && ! in_array((string) get_post_type(), $post_types, true)) {
            return false;
        }
        $query_key = sanitize_key((string) ($conditions['queryKey'] ?? ''));
        if ('' !== $query_key) {
            $actual = sanitize_text_field(wp_unslash((string) ($_GET[$query_key] ?? '')));
            $expected = sanitize_text_field((string) ($conditions['queryValue'] ?? ''));
            if (('*' === $expected && '' === $actual) || ('*' !== $expected && $expected !== $actual)) {
                return false;
            }
        }
        $taxonomy = sanitize_key((string) ($conditions['taxonomy'] ?? ''));
        $terms = array_values(array_filter(array_map('sanitize_title', explode(',', (string) ($conditions['terms'] ?? '')))));
        if ($taxonomy && $terms) {
            if (is_singular() && ! has_term($terms, $taxonomy, get_queried_object_id())) {
                return false;
            }
            if (is_tax($taxonomy) && ! is_tax($taxonomy, $terms)) {
                return false;
            }
        }
        return true;
    }
}
