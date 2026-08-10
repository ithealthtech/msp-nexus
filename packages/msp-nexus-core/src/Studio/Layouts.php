<?php
/**
 * Reusable block layouts for templates, regions, menus, loops, and popups.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Studio;

final class Layouts
{
    public const POST_TYPE = 'msp_nexus_layout';

    /** @var array<int, string> */
    private const AREAS = array('template', 'header', 'footer', 'mega_menu', 'popup', 'loop');

    /** @var array<int, string> */
    private const CONTEXTS = array('all', 'front_page', 'singular', 'archive', 'search', '404');

    public function register(): void
    {
        register_post_type(
            self::POST_TYPE,
            array(
                'labels' => array(
                    'name' => __('Nexus layouts', 'msp-nexus-core'),
                    'singular_name' => __('Nexus layout', 'msp-nexus-core'),
                    'add_new_item' => __('Add Nexus layout', 'msp-nexus-core'),
                    'edit_item' => __('Edit Nexus layout', 'msp-nexus-core'),
                ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => 'msp-nexus',
                'show_in_rest' => true,
                'supports' => array('title', 'editor', 'revisions', 'custom-fields'),
                'capability_type' => 'page',
                'map_meta_cap' => true,
                'menu_icon' => 'dashicons-layout',
            )
        );

        $this->register_meta();
        add_action('add_meta_boxes_' . self::POST_TYPE, array($this, 'meta_box'));
        add_action('save_post_' . self::POST_TYPE, array($this, 'save_meta'), 10, 2);
    }

    private function register_meta(): void
    {
        $keys = array(
            '_msp_nexus_layout_area' => 'sanitize_key',
            '_msp_nexus_condition_context' => 'sanitize_key',
            '_msp_nexus_condition_post_type' => 'sanitize_key',
            '_msp_nexus_condition_priority' => 'absint',
            '_msp_nexus_condition_include_ids' => 'sanitize_text_field',
            '_msp_nexus_condition_exclude_ids' => 'sanitize_text_field',
            '_msp_nexus_condition_taxonomy' => 'sanitize_key',
            '_msp_nexus_condition_include_terms' => 'sanitize_text_field',
            '_msp_nexus_condition_exclude_terms' => 'sanitize_text_field',
            '_msp_nexus_condition_user_state' => 'sanitize_key',
            '_msp_nexus_condition_roles' => 'sanitize_text_field',
            '_msp_nexus_condition_languages' => 'sanitize_text_field',
            '_msp_nexus_condition_date_start' => 'sanitize_text_field',
            '_msp_nexus_condition_date_end' => 'sanitize_text_field',
            '_msp_nexus_condition_weekdays' => 'sanitize_text_field',
            '_msp_nexus_condition_query_key' => 'sanitize_key',
            '_msp_nexus_condition_query_value' => 'sanitize_text_field',
            '_msp_nexus_popup_trigger' => 'sanitize_key',
            '_msp_nexus_popup_delay' => 'absint',
            '_msp_nexus_popup_scroll' => 'absint',
            '_msp_nexus_popup_cookie_days' => 'absint',
            '_msp_nexus_popup_selector' => 'sanitize_text_field',
            '_msp_nexus_popup_animation' => 'sanitize_key',
            '_msp_nexus_popup_position' => 'sanitize_key',
            '_msp_nexus_popup_close_overlay' => 'rest_sanitize_boolean',
            '_msp_nexus_popup_require_consent' => 'rest_sanitize_boolean',
            '_msp_nexus_popup_consent_key' => 'sanitize_key',
            '_msp_nexus_popup_group' => 'sanitize_key',
            '_msp_nexus_popup_weight' => 'absint',
            '_msp_nexus_header_sticky' => 'rest_sanitize_boolean',
            '_msp_nexus_header_overlay' => 'rest_sanitize_boolean',
            '_msp_nexus_header_shrink' => 'rest_sanitize_boolean',
            '_msp_nexus_header_hide_scroll' => 'rest_sanitize_boolean',
            '_msp_nexus_header_offset' => 'absint',
        );
        foreach ($keys as $key => $sanitize) {
            register_post_meta(
                self::POST_TYPE,
                $key,
                array(
                    'type' => in_array($key, array('_msp_nexus_popup_close_overlay', '_msp_nexus_popup_require_consent', '_msp_nexus_header_sticky', '_msp_nexus_header_overlay', '_msp_nexus_header_shrink', '_msp_nexus_header_hide_scroll'), true) ? 'boolean' : (in_array($key, array('_msp_nexus_condition_priority', '_msp_nexus_popup_delay', '_msp_nexus_popup_scroll', '_msp_nexus_popup_cookie_days', '_msp_nexus_popup_weight', '_msp_nexus_header_offset'), true) ? 'integer' : 'string'),
                    'single' => true,
                    'show_in_rest' => true,
                    'sanitize_callback' => $sanitize,
                    'auth_callback' => static function (): bool {
                        return current_user_can('edit_pages');
                    },
                )
            );
        }
    }

    public function meta_box(): void
    {
        add_meta_box('msp-nexus-layout-rules', __('Nexus display rules', 'msp-nexus-core'), array($this, 'render_meta_box'), self::POST_TYPE, 'side', 'high');
    }

    public function render_meta_box(\WP_Post $post): void
    {
        wp_nonce_field('msp_nexus_layout_rules', 'msp_nexus_layout_nonce');
        $area = (string) get_post_meta($post->ID, '_msp_nexus_layout_area', true);
        $context = (string) get_post_meta($post->ID, '_msp_nexus_condition_context', true);
        $trigger = (string) get_post_meta($post->ID, '_msp_nexus_popup_trigger', true);
        ?>
        <p><label for="msp-layout-area"><strong><?php esc_html_e('Layout area', 'msp-nexus-core'); ?></strong></label><br>
        <select class="widefat" id="msp-layout-area" name="msp_nexus_layout_area">
            <?php foreach (self::AREAS as $value) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($area ?: 'template', $value); ?>><?php echo esc_html(ucwords(str_replace('_', ' ', $value))); ?></option><?php endforeach; ?>
        </select></p>
        <p><label for="msp-layout-context"><strong><?php esc_html_e('Display context', 'msp-nexus-core'); ?></strong></label><br>
        <select class="widefat" id="msp-layout-context" name="msp_nexus_condition_context">
            <?php foreach (self::CONTEXTS as $value) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($context ?: 'all', $value); ?>><?php echo esc_html(ucwords(str_replace('_', ' ', $value))); ?></option><?php endforeach; ?>
        </select></p>
        <p><label for="msp-layout-post-type"><strong><?php esc_html_e('Post type (optional)', 'msp-nexus-core'); ?></strong></label><br><input class="widefat" id="msp-layout-post-type" name="msp_nexus_condition_post_type" value="<?php echo esc_attr((string) get_post_meta($post->ID, '_msp_nexus_condition_post_type', true)); ?>" placeholder="page"></p>
        <p><label for="msp-layout-priority"><strong><?php esc_html_e('Priority', 'msp-nexus-core'); ?></strong></label><br><input class="small-text" id="msp-layout-priority" min="0" max="999" name="msp_nexus_condition_priority" type="number" value="<?php echo esc_attr((string) (get_post_meta($post->ID, '_msp_nexus_condition_priority', true) ?: 10)); ?>"></p>
        <details><summary><strong><?php esc_html_e('Advanced include/exclude rules', 'msp-nexus-core'); ?></strong></summary>
        <?php $this->text_meta($post, 'include_ids', __('Include object IDs', 'msp-nexus-core'), __('Comma-separated page, post, product, or content IDs.', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'exclude_ids', __('Exclude object IDs', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'taxonomy', __('Taxonomy slug', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'include_terms', __('Include term slugs', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'exclude_terms', __('Exclude term slugs', 'msp-nexus-core')); ?>
        <p><label><?php esc_html_e('Visitor state', 'msp-nexus-core'); ?><br><select class="widefat" name="msp_nexus_condition_user_state"><?php $user_state = (string) get_post_meta($post->ID, '_msp_nexus_condition_user_state', true); foreach (array('any' => __('Everyone', 'msp-nexus-core'), 'logged_in' => __('Logged in', 'msp-nexus-core'), 'logged_out' => __('Logged out', 'msp-nexus-core')) as $value => $label) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($user_state ?: 'any', $value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label></p>
        <?php $this->text_meta($post, 'roles', __('User role slugs', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'languages', __('Locale codes', 'msp-nexus-core'), __('Example: en_US, es_ES', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'date_start', __('Start date', 'msp-nexus-core'), __('YYYY-MM-DD in the site timezone.', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'date_end', __('End date', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'weekdays', __('Weekdays', 'msp-nexus-core'), __('ISO day numbers 1–7, separated by commas.', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'query_key', __('Query parameter', 'msp-nexus-core')); ?>
        <?php $this->text_meta($post, 'query_value', __('Required query value', 'msp-nexus-core'), __('Use * for any non-empty value.', 'msp-nexus-core')); ?>
        </details>
        <hr>
        <p><strong><?php esc_html_e('Header behavior', 'msp-nexus-core'); ?></strong></p>
        <p><label><input type="checkbox" name="msp_nexus_header_sticky" value="1" <?php checked((bool) get_post_meta($post->ID, '_msp_nexus_header_sticky', true)); ?>> <?php esc_html_e('Keep this header visible while scrolling', 'msp-nexus-core'); ?></label></p>
        <p><label><input type="checkbox" name="msp_nexus_header_overlay" value="1" <?php checked((bool) get_post_meta($post->ID, '_msp_nexus_header_overlay', true)); ?>> <?php esc_html_e('Overlay the header above the first content section', 'msp-nexus-core'); ?></label></p>
        <p><label><input type="checkbox" name="msp_nexus_header_shrink" value="1" <?php checked((bool) get_post_meta($post->ID, '_msp_nexus_header_shrink', true)); ?>> <?php esc_html_e('Use compact spacing after scrolling', 'msp-nexus-core'); ?></label></p>
        <p><label><input type="checkbox" name="msp_nexus_header_hide_scroll" value="1" <?php checked((bool) get_post_meta($post->ID, '_msp_nexus_header_hide_scroll', true)); ?>> <?php esc_html_e('Hide while scrolling down and reveal while scrolling up', 'msp-nexus-core'); ?></label></p>
        <p><label><?php esc_html_e('Scroll activation offset (px)', 'msp-nexus-core'); ?><br><input class="small-text" min="0" max="1000" name="msp_nexus_header_offset" type="number" value="<?php echo esc_attr((string) (get_post_meta($post->ID, '_msp_nexus_header_offset', true) ?: 40)); ?>"></label></p>
        <p class="description"><?php esc_html_e('Header fields are used only when the layout area is Header.', 'msp-nexus-core'); ?></p><hr>
        <p><strong><?php esc_html_e('Popup behavior', 'msp-nexus-core'); ?></strong></p>
        <p><label for="msp-popup-trigger"><?php esc_html_e('Trigger', 'msp-nexus-core'); ?></label><br><select class="widefat" id="msp-popup-trigger" name="msp_nexus_popup_trigger">
            <?php foreach (array('delay', 'scroll', 'exit', 'click') as $value) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($trigger ?: 'delay', $value); ?>><?php echo esc_html(ucfirst($value)); ?></option><?php endforeach; ?>
        </select></p>
        <p><label><?php esc_html_e('Delay (seconds)', 'msp-nexus-core'); ?><br><input class="small-text" min="0" max="300" name="msp_nexus_popup_delay" type="number" value="<?php echo esc_attr((string) (get_post_meta($post->ID, '_msp_nexus_popup_delay', true) ?: 4)); ?>"></label></p>
        <p><label><?php esc_html_e('Scroll depth (%)', 'msp-nexus-core'); ?><br><input class="small-text" min="1" max="100" name="msp_nexus_popup_scroll" type="number" value="<?php echo esc_attr((string) (get_post_meta($post->ID, '_msp_nexus_popup_scroll', true) ?: 50)); ?>"></label></p>
        <p><label><?php esc_html_e('Dismissal period (days)', 'msp-nexus-core'); ?><br><input class="small-text" min="0" max="365" name="msp_nexus_popup_cookie_days" type="number" value="<?php echo esc_attr((string) (get_post_meta($post->ID, '_msp_nexus_popup_cookie_days', true) ?: 14)); ?>"></label></p>
        <p><label><?php esc_html_e('Click selector', 'msp-nexus-core'); ?><br><input class="widefat" name="msp_nexus_popup_selector" value="<?php echo esc_attr((string) get_post_meta($post->ID, '_msp_nexus_popup_selector', true)); ?>" placeholder=".open-consultation"></label></p>
        <p><label><?php esc_html_e('Animation', 'msp-nexus-core'); ?><br><select class="widefat" name="msp_nexus_popup_animation"><?php $animation = (string) get_post_meta($post->ID, '_msp_nexus_popup_animation', true); foreach (array('fade', 'rise', 'slide', 'zoom') as $value) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($animation ?: 'fade', $value); ?>><?php echo esc_html(ucfirst($value)); ?></option><?php endforeach; ?></select></label></p>
        <p><label><?php esc_html_e('Position', 'msp-nexus-core'); ?><br><select class="widefat" name="msp_nexus_popup_position"><?php $position = (string) get_post_meta($post->ID, '_msp_nexus_popup_position', true); foreach (array('center', 'top', 'bottom', 'left', 'right') as $value) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($position ?: 'center', $value); ?>><?php echo esc_html(ucfirst($value)); ?></option><?php endforeach; ?></select></label></p>
        <p><label><input type="checkbox" name="msp_nexus_popup_close_overlay" value="1" <?php checked('0' !== (string) get_post_meta($post->ID, '_msp_nexus_popup_close_overlay', true)); ?>> <?php esc_html_e('Close when the backdrop is clicked', 'msp-nexus-core'); ?></label></p>
        <p><label><input type="checkbox" name="msp_nexus_popup_require_consent" value="1" <?php checked((bool) get_post_meta($post->ID, '_msp_nexus_popup_require_consent', true)); ?>> <?php esc_html_e('Require stored consent before showing', 'msp-nexus-core'); ?></label></p>
        <p><label><?php esc_html_e('Consent storage key', 'msp-nexus-core'); ?><br><input class="widefat" name="msp_nexus_popup_consent_key" value="<?php echo esc_attr((string) (get_post_meta($post->ID, '_msp_nexus_popup_consent_key', true) ?: 'msp_consent_marketing')); ?>"></label></p>
        <p><label><?php esc_html_e('Variant group', 'msp-nexus-core'); ?><br><input class="widefat" name="msp_nexus_popup_group" value="<?php echo esc_attr((string) get_post_meta($post->ID, '_msp_nexus_popup_group', true)); ?>" placeholder="consultation-offer"></label></p>
        <p><label><?php esc_html_e('Variant weight', 'msp-nexus-core'); ?><br><input class="small-text" min="1" max="100" name="msp_nexus_popup_weight" type="number" value="<?php echo esc_attr((string) (get_post_meta($post->ID, '_msp_nexus_popup_weight', true) ?: 50)); ?>"></label></p>
        <p class="description"><?php esc_html_e('Popup fields are used only when the layout area is Popup. Publish a rule only after previewing it.', 'msp-nexus-core'); ?></p>
        <?php
    }

    public function save_meta(int $post_id, \WP_Post $post): void
    {
        if ('publish' !== $post->post_status && 'draft' !== $post->post_status && 'pending' !== $post->post_status) {
            return;
        }
        if (! isset($_POST['msp_nexus_layout_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['msp_nexus_layout_nonce'])), 'msp_nexus_layout_rules') || ! current_user_can('edit_post', $post_id)) {
            return;
        }
        $area = sanitize_key((string) ($_POST['msp_nexus_layout_area'] ?? 'template'));
        $context = sanitize_key((string) ($_POST['msp_nexus_condition_context'] ?? 'all'));
        $trigger = sanitize_key((string) ($_POST['msp_nexus_popup_trigger'] ?? 'delay'));
        update_post_meta($post_id, '_msp_nexus_layout_area', in_array($area, self::AREAS, true) ? $area : 'template');
        update_post_meta($post_id, '_msp_nexus_condition_context', in_array($context, self::CONTEXTS, true) ? $context : 'all');
        update_post_meta($post_id, '_msp_nexus_condition_post_type', sanitize_key((string) ($_POST['msp_nexus_condition_post_type'] ?? '')));
        update_post_meta($post_id, '_msp_nexus_condition_priority', min(999, absint($_POST['msp_nexus_condition_priority'] ?? 10)));
        foreach (array('include_ids', 'exclude_ids', 'include_terms', 'exclude_terms', 'roles', 'languages', 'date_start', 'date_end', 'weekdays', 'query_value') as $name) {
            update_post_meta($post_id, '_msp_nexus_condition_' . $name, sanitize_text_field((string) ($_POST['msp_nexus_condition_' . $name] ?? '')));
        }
        update_post_meta($post_id, '_msp_nexus_condition_taxonomy', sanitize_key((string) ($_POST['msp_nexus_condition_taxonomy'] ?? '')));
        update_post_meta($post_id, '_msp_nexus_condition_query_key', sanitize_key((string) ($_POST['msp_nexus_condition_query_key'] ?? '')));
        $user_state = sanitize_key((string) ($_POST['msp_nexus_condition_user_state'] ?? 'any'));
        update_post_meta($post_id, '_msp_nexus_condition_user_state', in_array($user_state, array('any', 'logged_in', 'logged_out'), true) ? $user_state : 'any');
        update_post_meta($post_id, '_msp_nexus_popup_trigger', in_array($trigger, array('delay', 'scroll', 'exit', 'click'), true) ? $trigger : 'delay');
        update_post_meta($post_id, '_msp_nexus_popup_delay', min(300, absint($_POST['msp_nexus_popup_delay'] ?? 4)));
        update_post_meta($post_id, '_msp_nexus_popup_scroll', min(100, max(1, absint($_POST['msp_nexus_popup_scroll'] ?? 50))));
        update_post_meta($post_id, '_msp_nexus_popup_cookie_days', min(365, absint($_POST['msp_nexus_popup_cookie_days'] ?? 14)));
        update_post_meta($post_id, '_msp_nexus_popup_selector', sanitize_text_field((string) ($_POST['msp_nexus_popup_selector'] ?? '')));
        $animation = sanitize_key((string) ($_POST['msp_nexus_popup_animation'] ?? 'fade'));
        $position = sanitize_key((string) ($_POST['msp_nexus_popup_position'] ?? 'center'));
        update_post_meta($post_id, '_msp_nexus_popup_animation', in_array($animation, array('fade', 'rise', 'slide', 'zoom'), true) ? $animation : 'fade');
        update_post_meta($post_id, '_msp_nexus_popup_position', in_array($position, array('center', 'top', 'bottom', 'left', 'right'), true) ? $position : 'center');
        update_post_meta($post_id, '_msp_nexus_popup_close_overlay', ! empty($_POST['msp_nexus_popup_close_overlay']));
        update_post_meta($post_id, '_msp_nexus_popup_require_consent', ! empty($_POST['msp_nexus_popup_require_consent']));
        update_post_meta($post_id, '_msp_nexus_popup_consent_key', sanitize_key((string) ($_POST['msp_nexus_popup_consent_key'] ?? 'msp_consent_marketing')));
        update_post_meta($post_id, '_msp_nexus_popup_group', sanitize_key((string) ($_POST['msp_nexus_popup_group'] ?? '')));
        update_post_meta($post_id, '_msp_nexus_popup_weight', min(100, max(1, absint($_POST['msp_nexus_popup_weight'] ?? 50))));
        update_post_meta($post_id, '_msp_nexus_header_sticky', ! empty($_POST['msp_nexus_header_sticky']));
        update_post_meta($post_id, '_msp_nexus_header_overlay', ! empty($_POST['msp_nexus_header_overlay']));
        update_post_meta($post_id, '_msp_nexus_header_shrink', ! empty($_POST['msp_nexus_header_shrink']));
        update_post_meta($post_id, '_msp_nexus_header_hide_scroll', ! empty($_POST['msp_nexus_header_hide_scroll']));
        update_post_meta($post_id, '_msp_nexus_header_offset', min(1000, absint($_POST['msp_nexus_header_offset'] ?? 40)));
    }

    /** @return array<int, \WP_Post> */
    public function published(string $area): array
    {
        if (! in_array($area, self::AREAS, true)) {
            return array();
        }
        $posts = get_posts(
            array(
                'post_type' => self::POST_TYPE,
                'post_status' => 'publish',
                'posts_per_page' => 100,
                'orderby' => 'date',
                'order' => 'DESC',
                'meta_query' => array(array('key' => '_msp_nexus_layout_area', 'value' => $area)),
            )
        );
        if (! is_array($posts)) {
            return array();
        }
        usort(
            $posts,
            static function (\WP_Post $first, \WP_Post $second): int {
                $first_priority = (int) (get_post_meta($first->ID, '_msp_nexus_condition_priority', true) ?: 10);
                $second_priority = (int) (get_post_meta($second->ID, '_msp_nexus_condition_priority', true) ?: 10);
                if ($first_priority === $second_priority) {
                    return $second->ID <=> $first->ID;
                }
                return $second_priority <=> $first_priority;
            }
        );
        return $posts;
    }

    public function by_slug(string $slug, string $area = ''): ?\WP_Post
    {
        $posts = get_posts(array('post_type' => self::POST_TYPE, 'post_status' => 'publish', 'name' => sanitize_title($slug), 'posts_per_page' => 1));
        $post = $posts[0] ?? null;
        if (! $post instanceof \WP_Post || ('' !== $area && $area !== get_post_meta($post->ID, '_msp_nexus_layout_area', true))) {
            return null;
        }
        return $post;
    }

    private function text_meta(\WP_Post $post, string $name, string $label, string $description = ''): void
    {
        $key = '_msp_nexus_condition_' . $name;
        ?>
        <p><label><?php echo esc_html($label); ?><br><input class="widefat" name="msp_nexus_condition_<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) get_post_meta($post->ID, $key, true)); ?>"></label><?php if ('' !== $description) : ?><br><span class="description"><?php echo esc_html($description); ?></span><?php endif; ?></p>
        <?php
    }
}
