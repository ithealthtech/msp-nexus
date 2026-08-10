<?php
/**
 * Native display-condition engine for reusable Nexus layouts.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Studio;

final class TemplateConditions
{
    private Layouts $layouts;

    /** @var array<string, \WP_Post|null> */
    private array $resolved = array();

    private bool $rendering_region = false;

    public function __construct(Layouts $layouts)
    {
        $this->layouts = $layouts;
    }

    public function register_hooks(): void
    {
        add_filter('pre_get_block_template', array($this, 'template'), 10, 3);
        add_filter('render_block', array($this, 'template_part'), 9, 2);
        add_action('wp_footer', array($this, 'popups'), 30);
    }

    /**
     * @param mixed $template Existing short-circuit value.
     * @return mixed
     */
    public function template($template, string $id, string $template_type)
    {
        unset($id);
        if (null !== $template || 'wp_template' !== $template_type || is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return $template;
        }
        $layout = $this->resolve('template');
        if (! $layout instanceof \WP_Post || ! class_exists('WP_Block_Template')) {
            return $template;
        }
        $custom = new \WP_Block_Template();
        $custom->id = get_stylesheet() . '//nexus-' . $layout->post_name;
        $custom->theme = get_stylesheet();
        $custom->slug = 'nexus-' . $layout->post_name;
        $custom->content = $layout->post_content;
        $custom->source = 'custom';
        $custom->origin = 'plugin';
        $custom->type = 'wp_template';
        $custom->title = $layout->post_title;
        $custom->description = __('Conditional template supplied by Nexus Studio.', 'msp-nexus-core');
        $custom->status = 'publish';
        $custom->has_theme_file = false;
        $custom->is_custom = true;
        $custom->wp_id = $layout->ID;
        return $custom;
    }

    /**
     * @param array<string, mixed> $block Parsed block.
     */
    public function template_part(string $content, array $block): string
    {
        if ($this->rendering_region || is_admin() || 'core/template-part' !== ($block['blockName'] ?? '')) {
            return $content;
        }
        $area = sanitize_key((string) ($block['attrs']['area'] ?? ''));
        if (! in_array($area, array('header', 'footer'), true)) {
            return $content;
        }
        $layout = $this->resolve($area);
        if (! $layout instanceof \WP_Post) {
            return $content;
        }
        $this->rendering_region = true;
        $replacement = do_blocks($layout->post_content);
        $this->rendering_region = false;
        $classes = 'msp-nexus-layout-region msp-nexus-layout-region--' . $area;
        $data = '';
        if ('header' === $area) {
            $config = array(
                'sticky' => (bool) get_post_meta($layout->ID, '_msp_nexus_header_sticky', true),
                'overlay' => (bool) get_post_meta($layout->ID, '_msp_nexus_header_overlay', true),
                'shrink' => (bool) get_post_meta($layout->ID, '_msp_nexus_header_shrink', true),
                'hideScroll' => (bool) get_post_meta($layout->ID, '_msp_nexus_header_hide_scroll', true),
                'offset' => min(1000, absint(get_post_meta($layout->ID, '_msp_nexus_header_offset', true) ?: 40)),
            );
            $classes .= ! empty($config['sticky']) ? ' is-sticky' : '';
            $classes .= ! empty($config['overlay']) ? ' is-overlay' : '';
            wp_enqueue_style('msp-nexus-header-runtime');
            wp_enqueue_script('msp-nexus-header-runtime');
            $data = ' data-header-config="' . esc_attr((string) wp_json_encode($config)) . '"';
        }
        return '<div class="' . esc_attr($classes) . '" data-layout="' . esc_attr($layout->post_name) . '"' . $data . '>' . $replacement . '</div>';
    }

    public function popups(): void
    {
        if (is_admin()) {
            return;
        }
        $matches = array_values(array_filter($this->layouts->published('popup'), array($this, 'matches')));
        if (! $matches) {
            return;
        }
        wp_enqueue_style('msp-nexus-studio');
        wp_enqueue_script('msp-nexus-popup-runtime', plugins_url('assets/popup-runtime.js', MSP_NEXUS_CORE_FILE), array(), MSP_NEXUS_CORE_VERSION, true);
        foreach ($matches as $layout) {
            $config = array(
                'trigger' => (string) (get_post_meta($layout->ID, '_msp_nexus_popup_trigger', true) ?: 'delay'),
                'delay' => min(300, (int) (get_post_meta($layout->ID, '_msp_nexus_popup_delay', true) ?: 4)),
                'scroll' => min(100, max(1, (int) (get_post_meta($layout->ID, '_msp_nexus_popup_scroll', true) ?: 50))),
                'days' => min(365, (int) (get_post_meta($layout->ID, '_msp_nexus_popup_cookie_days', true) ?: 14)),
                'selector' => (string) get_post_meta($layout->ID, '_msp_nexus_popup_selector', true),
                'storageKey' => 'msp_nexus_popup_' . $layout->ID,
                'animation' => (string) (get_post_meta($layout->ID, '_msp_nexus_popup_animation', true) ?: 'fade'),
                'position' => (string) (get_post_meta($layout->ID, '_msp_nexus_popup_position', true) ?: 'center'),
                'closeOverlay' => '0' !== (string) get_post_meta($layout->ID, '_msp_nexus_popup_close_overlay', true),
                'requireConsent' => (bool) get_post_meta($layout->ID, '_msp_nexus_popup_require_consent', true),
                'consentKey' => (string) (get_post_meta($layout->ID, '_msp_nexus_popup_consent_key', true) ?: 'msp_consent_marketing'),
                'group' => (string) get_post_meta($layout->ID, '_msp_nexus_popup_group', true),
                'weight' => min(100, max(1, (int) (get_post_meta($layout->ID, '_msp_nexus_popup_weight', true) ?: 50))),
            );
            ?>
            <div class="msp-nexus-studio-popup is-<?php echo esc_attr($config['position']); ?> is-animation-<?php echo esc_attr($config['animation']); ?>" data-nexus-popup="<?php echo esc_attr((string) $layout->ID); ?>" data-config="<?php echo esc_attr((string) wp_json_encode($config)); ?>" hidden>
                <dialog aria-labelledby="msp-nexus-popup-title-<?php echo esc_attr((string) $layout->ID); ?>" data-popup-name="<?php echo esc_attr($layout->post_name); ?>">
                    <button type="button" class="msp-nexus-studio-popup__close" data-nexus-popup-close aria-label="<?php esc_attr_e('Close dialog', 'msp-nexus-core'); ?>">&times;</button>
                    <div id="msp-nexus-popup-title-<?php echo esc_attr((string) $layout->ID); ?>"><?php echo do_blocks($layout->post_content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                </dialog>
            </div>
            <?php
        }
    }

    public function render_region(string $slug, string $area): string
    {
        if ($this->rendering_region) {
            return '';
        }
        $layout = $this->layouts->by_slug($slug, $area);
        if (! $layout instanceof \WP_Post) {
            return '';
        }
        $this->rendering_region = true;
        $content = do_blocks($layout->post_content);
        $this->rendering_region = false;
        return $content;
    }

    private function resolve(string $area): ?\WP_Post
    {
        if (array_key_exists($area, $this->resolved)) {
            return $this->resolved[$area];
        }
        foreach ($this->layouts->published($area) as $layout) {
            if ($this->matches($layout)) {
                $this->resolved[$area] = $layout;
                return $layout;
            }
        }
        $this->resolved[$area] = null;
        return null;
    }

    public function matches(\WP_Post $layout): bool
    {
        $context = (string) (get_post_meta($layout->ID, '_msp_nexus_condition_context', true) ?: 'all');
        $post_type = sanitize_key((string) get_post_meta($layout->ID, '_msp_nexus_condition_post_type', true));
        if ('' !== $post_type) {
            $current_type = is_singular() ? (string) get_post_type() : (string) get_query_var('post_type');
            if (is_array(get_query_var('post_type'))) {
                $current_type = '';
            }
            if ($post_type !== $current_type) {
                return false;
            }
        }
        $context_matches = 'all' === $context
            || ('front_page' === $context && is_front_page())
            || ('singular' === $context && is_singular())
            || ('archive' === $context && (is_archive() || is_home()))
            || ('search' === $context && is_search())
            || ('404' === $context && is_404());
        if (! $context_matches) {
            return false;
        }
        $include_ids = $this->ids((string) get_post_meta($layout->ID, '_msp_nexus_condition_include_ids', true));
        $exclude_ids = $this->ids((string) get_post_meta($layout->ID, '_msp_nexus_condition_exclude_ids', true));
        $object_id = is_singular() || is_front_page() ? get_queried_object_id() : 0;
        if ($include_ids && (0 === $object_id || ! in_array($object_id, $include_ids, true))) {
            return false;
        }
        if ($exclude_ids && in_array($object_id, $exclude_ids, true)) {
            return false;
        }
        $taxonomy = sanitize_key((string) get_post_meta($layout->ID, '_msp_nexus_condition_taxonomy', true));
        $include_terms = $this->slugs((string) get_post_meta($layout->ID, '_msp_nexus_condition_include_terms', true));
        $exclude_terms = $this->slugs((string) get_post_meta($layout->ID, '_msp_nexus_condition_exclude_terms', true));
        if ($taxonomy && taxonomy_exists($taxonomy)) {
            if ($include_terms && ! $this->term_matches($taxonomy, $include_terms, $object_id)) {
                return false;
            }
            if ($exclude_terms && $this->term_matches($taxonomy, $exclude_terms, $object_id)) {
                return false;
            }
        }
        $user_state = sanitize_key((string) (get_post_meta($layout->ID, '_msp_nexus_condition_user_state', true) ?: 'any'));
        $roles = $this->slugs((string) get_post_meta($layout->ID, '_msp_nexus_condition_roles', true));
        if ('any' !== $user_state || $roles) {
            $this->disable_page_cache();
        }
        if ('logged_in' === $user_state && ! is_user_logged_in()) {
            return false;
        }
        if ('logged_out' === $user_state && is_user_logged_in()) {
            return false;
        }
        if ($roles && ! array_intersect($roles, (array) wp_get_current_user()->roles)) {
            return false;
        }
        $languages = array_values(array_filter(array_map('sanitize_text_field', explode(',', (string) get_post_meta($layout->ID, '_msp_nexus_condition_languages', true)))));
        if ($languages && ! in_array(determine_locale(), $languages, true)) {
            return false;
        }
        $now = current_time('timestamp');
        $start = sanitize_text_field((string) get_post_meta($layout->ID, '_msp_nexus_condition_date_start', true));
        $end = sanitize_text_field((string) get_post_meta($layout->ID, '_msp_nexus_condition_date_end', true));
        $weekdays = $this->ids((string) get_post_meta($layout->ID, '_msp_nexus_condition_weekdays', true));
        if ($start || $end || $weekdays) {
            $this->disable_page_cache();
        }
        if ($start && false !== strtotime($start . ' 00:00:00') && $now < (int) strtotime($start . ' 00:00:00')) {
            return false;
        }
        if ($end && false !== strtotime($end . ' 23:59:59') && $now > (int) strtotime($end . ' 23:59:59')) {
            return false;
        }
        if ($weekdays && ! in_array((int) wp_date('N', $now), $weekdays, true)) {
            return false;
        }
        $query_key = sanitize_key((string) get_post_meta($layout->ID, '_msp_nexus_condition_query_key', true));
        if ($query_key) {
            $this->disable_page_cache();
            $actual = sanitize_text_field(wp_unslash((string) ($_GET[$query_key] ?? '')));
            $expected = sanitize_text_field((string) get_post_meta($layout->ID, '_msp_nexus_condition_query_value', true));
            if (('*' === $expected && '' === $actual) || ('*' !== $expected && $actual !== $expected)) {
                return false;
            }
        }
        return true;
    }

    /** @return array<int, int> */
    private function ids(string $value): array
    {
        return array_values(array_filter(array_map('absint', explode(',', $value))));
    }

    /** @return array<int, string> */
    private function slugs(string $value): array
    {
        return array_values(array_filter(array_map('sanitize_key', explode(',', $value))));
    }

    /** @param array<int, string> $terms */
    private function term_matches(string $taxonomy, array $terms, int $object_id): bool
    {
        if ($object_id > 0 && is_singular()) {
            return has_term($terms, $taxonomy, $object_id);
        }
        if (is_tax($taxonomy)) {
            return is_tax($taxonomy, $terms);
        }
        return false;
    }

    private function disable_page_cache(): void
    {
        if (! defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
    }
}
