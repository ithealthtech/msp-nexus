<?php
/**
 * Site-level business settings.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

final class Settings
{
    private const OPTION = 'msp_nexus_settings';

    public function register(): void
    {
        register_setting(
            'msp_nexus',
            self::OPTION,
            array(
                'type' => 'object',
                'default' => array(),
                'sanitize_callback' => array($this, 'sanitize'),
                'show_in_rest' => false,
            )
        );
    }

    public function menu(): void
    {
        add_menu_page(
            __('MSP Nexus', 'msp-nexus-core'),
            __('MSP Nexus', 'msp-nexus-core'),
            'manage_options',
            'msp-nexus',
            array($this, 'render'),
            'dashicons-shield-alt',
            58
        );
    }

    /** @param mixed $value
     *  @return array<string, mixed>
     */
    public function sanitize($value): array
    {
        if (! is_array($value)) {
            return array();
        }

        $channel = sanitize_key((string) ($value['update_channel'] ?? 'stable'));
        if (! in_array($channel, array('stable', 'beta', 'development'), true)) {
            $channel = 'stable';
        }

        $accent = sanitize_hex_color((string) ($value['accent_color'] ?? ''));

        return array(
            'organization_name' => sanitize_text_field((string) ($value['organization_name'] ?? '')),
            'sales_phone' => sanitize_text_field((string) ($value['sales_phone'] ?? '')),
            'support_phone' => sanitize_text_field((string) ($value['support_phone'] ?? '')),
            'support_url'   => esc_url_raw((string) ($value['support_url'] ?? '')),
            'sales_email'   => sanitize_email((string) ($value['sales_email'] ?? '')),
            'cta_url'       => esc_url_raw((string) ($value['cta_url'] ?? '')),
            'announcement'  => sanitize_text_field((string) ($value['announcement'] ?? '')),
            'linkedin_url'  => esc_url_raw((string) ($value['linkedin_url'] ?? '')),
            'facebook_url'  => esc_url_raw((string) ($value['facebook_url'] ?? '')),
            'youtube_url'   => esc_url_raw((string) ($value['youtube_url'] ?? '')),
            'company_address' => sanitize_textarea_field((string) ($value['company_address'] ?? '')),
            'business_hours' => sanitize_textarea_field((string) ($value['business_hours'] ?? '')),
            'emergency_message' => sanitize_textarea_field((string) ($value['emergency_message'] ?? '')),
            'enable_schema' => ! empty($value['enable_schema']),
            'enable_native_form' => ! empty($value['enable_native_form']),
            'update_channel' => $channel,
            'automatic_updates' => ! empty($value['automatic_updates']),
            'accent_color' => $accent ?: '#69bff5',
            'surface_color' => sanitize_hex_color((string) ($value['surface_color'] ?? '')) ?: '#121124',
            'text_color' => sanitize_hex_color((string) ($value['text_color'] ?? '')) ?: '#ffffff',
            'muted_color' => sanitize_hex_color((string) ($value['muted_color'] ?? '')) ?: '#d9d9e5',
            'focus_color' => sanitize_hex_color((string) ($value['focus_color'] ?? '')) ?: '#69bff5',
            'button_radius' => min(48, max(0, absint($value['button_radius'] ?? 3))),
            'button_padding_x' => min(64, max(8, absint($value['button_padding_x'] ?? 24))),
            'button_padding_y' => min(32, max(6, absint($value['button_padding_y'] ?? 14))),
            'card_radius' => min(64, max(0, absint($value['card_radius'] ?? 12))),
            'card_border' => min(8, max(0, absint($value['card_border'] ?? 1))),
            'card_shadow' => min(5, max(0, absint($value['card_shadow'] ?? 2))),
            'type_scale' => min(125, max(85, absint($value['type_scale'] ?? 100))),
            'heading_weight' => min(900, max(300, absint($value['heading_weight'] ?? 600))),
            'line_height' => min(200, max(120, absint($value['line_height'] ?? 165))),
            'content_width' => min(1200, max(480, absint($value['content_width'] ?? 704))),
            'wide_width' => min(1920, max(720, absint($value['wide_width'] ?? 1216))),
            'section_spacing' => min(160, max(24, absint($value['section_spacing'] ?? 80))),
            'link_underline' => ! empty($value['link_underline']),
        );
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $settings = get_option(self::OPTION, array());
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('MSP Nexus settings', 'msp-nexus-core'); ?></h1>
            <p><?php esc_html_e('Manage organization-wide contact and conversion details. Content remains available if the commercial license expires.', 'msp-nexus-core'); ?></p>
            <form action="options.php" method="post">
                <?php settings_fields('msp_nexus'); ?>
                <table class="form-table" role="presentation">
                    <?php $this->field('organization_name', __('Organization name', 'msp-nexus-core'), $settings); ?>
                    <?php $this->field('sales_phone', __('Primary sales phone', 'msp-nexus-core'), $settings, 'tel'); ?>
                    <?php $this->field('support_phone', __('Support phone', 'msp-nexus-core'), $settings); ?>
                    <?php $this->field('support_url', __('Support portal URL', 'msp-nexus-core'), $settings, 'url'); ?>
                    <?php $this->field('sales_email', __('Sales email', 'msp-nexus-core'), $settings, 'email'); ?>
                    <?php $this->field('cta_url', __('Primary consultation URL', 'msp-nexus-core'), $settings, 'url'); ?>
                    <?php $this->field('announcement', __('Header announcement', 'msp-nexus-core'), $settings); ?>
                    <?php $this->field('linkedin_url', __('LinkedIn URL', 'msp-nexus-core'), $settings, 'url'); ?>
                    <?php $this->field('facebook_url', __('Facebook URL', 'msp-nexus-core'), $settings, 'url'); ?>
                    <?php $this->field('youtube_url', __('YouTube URL', 'msp-nexus-core'), $settings, 'url'); ?>
                    <?php $this->textarea('company_address', __('Company address', 'msp-nexus-core'), $settings); ?>
                    <?php $this->textarea('business_hours', __('Default business hours', 'msp-nexus-core'), $settings); ?>
                    <?php $this->textarea('emergency_message', __('Emergency-support message', 'msp-nexus-core'), $settings); ?>
                    <tr><th colspan="2"><h2><?php esc_html_e('Global design controls', 'msp-nexus-core'); ?></h2><p class="description"><?php esc_html_e('These restrained tokens complement, and can be overridden by, native Global Styles.', 'msp-nexus-core'); ?></p></th></tr>
                    <?php $this->field('accent_color', __('Action accent color', 'msp-nexus-core'), $settings, 'color'); ?>
                    <?php $this->field('surface_color', __('Primary surface color', 'msp-nexus-core'), $settings, 'color'); ?>
                    <?php $this->field('text_color', __('Primary text color', 'msp-nexus-core'), $settings, 'color'); ?>
                    <?php $this->field('muted_color', __('Muted text color', 'msp-nexus-core'), $settings, 'color'); ?>
                    <?php $this->field('focus_color', __('Keyboard focus color', 'msp-nexus-core'), $settings, 'color'); ?>
                    <?php $this->number('button_radius', __('Button corner radius (px)', 'msp-nexus-core'), $settings, 0, 48, 3); ?>
                    <?php $this->number('button_padding_x', __('Button horizontal padding (px)', 'msp-nexus-core'), $settings, 8, 64, 24); ?>
                    <?php $this->number('button_padding_y', __('Button vertical padding (px)', 'msp-nexus-core'), $settings, 6, 32, 14); ?>
                    <?php $this->number('card_radius', __('Card corner radius (px)', 'msp-nexus-core'), $settings, 0, 64, 12); ?>
                    <?php $this->number('card_border', __('Card border width (px)', 'msp-nexus-core'), $settings, 0, 8, 1); ?>
                    <?php $this->number('card_shadow', __('Card shadow level (0–5)', 'msp-nexus-core'), $settings, 0, 5, 2); ?>
                    <?php $this->number('type_scale', __('Base type scale (%)', 'msp-nexus-core'), $settings, 85, 125, 100); ?>
                    <?php $this->number('heading_weight', __('Heading font weight', 'msp-nexus-core'), $settings, 300, 900, 600); ?>
                    <?php $this->number('line_height', __('Body line height (%)', 'msp-nexus-core'), $settings, 120, 200, 165); ?>
                    <?php $this->number('content_width', __('Content width (px)', 'msp-nexus-core'), $settings, 480, 1200, 704); ?>
                    <?php $this->number('wide_width', __('Wide content width (px)', 'msp-nexus-core'), $settings, 720, 1920, 1216); ?>
                    <?php $this->number('section_spacing', __('Default section spacing (px)', 'msp-nexus-core'), $settings, 24, 160, 80); ?>
                    <tr><th scope="row"><?php esc_html_e('Link presentation', 'msp-nexus-core'); ?></th><td><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION . '[link_underline]'); ?>" value="1" <?php checked(! array_key_exists('link_underline', $settings) || ! empty($settings['link_underline'])); ?>> <?php esc_html_e('Underline inline links by default', 'msp-nexus-core'); ?></label></td></tr>
                    <tr><th scope="row"><?php esc_html_e('Integrations', 'msp-nexus-core'); ?></th><td>
                        <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION . '[enable_schema]'); ?>" value="1" <?php checked(! array_key_exists('enable_schema', $settings) || ! empty($settings['enable_schema'])); ?>> <?php esc_html_e('Enable conservative built-in schema when no supported SEO plugin is active', 'msp-nexus-core'); ?></label><br>
                        <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION . '[enable_native_form]'); ?>" value="1" <?php checked(! array_key_exists('enable_native_form', $settings) || ! empty($settings['enable_native_form'])); ?>> <?php esc_html_e('Enable the native consultation form handler', 'msp-nexus-core'); ?></label>
                    </td></tr>
                    <tr>
                        <th scope="row"><label for="msp-nexus-update-channel"><?php esc_html_e('Update channel', 'msp-nexus-core'); ?></label></th>
                        <td><select id="msp-nexus-update-channel" name="<?php echo esc_attr(self::OPTION . '[update_channel]'); ?>">
                            <?php foreach (array('stable' => __('Stable', 'msp-nexus-core'), 'beta' => __('Beta', 'msp-nexus-core'), 'development' => __('Development', 'msp-nexus-core')) as $value => $label) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected((string) ($settings['update_channel'] ?? 'stable'), $value); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select><p class="description"><?php esc_html_e('Stable is recommended for production. Preview channels can contain incomplete changes.', 'msp-nexus-core'); ?></p></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Automatic updates', 'msp-nexus-core'); ?></th>
                        <td><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION . '[automatic_updates]'); ?>" value="1" <?php checked(! empty($settings['automatic_updates'])); ?>> <?php esc_html_e('Allow WordPress to install verified MSP Nexus theme and plugin updates automatically.', 'msp-nexus-core'); ?></label><p class="description"><?php esc_html_e('Disabled by default. Maintain a tested backup and staging workflow before enabling.', 'msp-nexus-core'); ?></p></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /** @param array<string, mixed> $settings */
    private function field(string $name, string $label, array $settings, string $type = 'text'): void
    {
        $id = 'msp-nexus-' . $name;
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td><input class="regular-text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr(self::OPTION . '[' . $name . ']'); ?>" type="<?php echo esc_attr($type); ?>" value="<?php echo esc_attr((string) ($settings[$name] ?? '')); ?>"></td>
        </tr>
        <?php
    }

    /** @param array<string, mixed> $settings */
    private function textarea(string $name, string $label, array $settings): void
    {
        $id = 'msp-nexus-' . $name;
        ?>
        <tr><th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th><td><textarea class="large-text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr(self::OPTION . '[' . $name . ']'); ?>" rows="3"><?php echo esc_textarea((string) ($settings[$name] ?? '')); ?></textarea></td></tr>
        <?php
    }

    /** @param array<string, mixed> $settings */
    private function number(string $name, string $label, array $settings, int $min, int $max, int $default): void
    {
        $id = 'msp-nexus-' . $name;
        ?>
        <tr><th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th><td><input class="small-text" id="<?php echo esc_attr($id); ?>" min="<?php echo esc_attr((string) $min); ?>" max="<?php echo esc_attr((string) $max); ?>" name="<?php echo esc_attr(self::OPTION . '[' . $name . ']'); ?>" type="number" value="<?php echo esc_attr((string) ($settings[$name] ?? $default)); ?>"></td></tr>
        <?php
    }
}
