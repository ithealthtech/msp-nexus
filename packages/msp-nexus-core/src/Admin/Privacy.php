<?php
/**
 * Consent preferences and privacy-aware script activation.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

final class Privacy
{
    private const OPTION = 'msp_nexus_privacy';
    private const COOKIE = 'msp_nexus_consent';

    public function register_hooks(): void
    {
        add_action('admin_init', array($this, 'register'));
        add_action('admin_menu', array($this, 'menu'));
        add_action('wp_enqueue_scripts', array($this, 'assets'));
        add_action('wp_footer', array($this, 'banner'));
        add_shortcode('msp_nexus_consent_preferences', array($this, 'preferences_shortcode'));
    }

    public function register(): void
    {
        register_setting('msp_nexus_privacy', self::OPTION, array('type' => 'object', 'default' => array(), 'sanitize_callback' => array($this, 'sanitize')));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('Nexus privacy', 'msp-nexus-core'), __('Privacy & consent', 'msp-nexus-core'), 'manage_privacy_options', 'msp-nexus-privacy', array($this, 'render'));
    }

    /** @param mixed $value
     *  @return array<string, mixed>
     */
    public function sanitize($value): array
    {
        if (! is_array($value)) {
            return array();
        }
        $mode = sanitize_key((string) ($value['mode'] ?? 'opt-in'));
        return array(
            'enabled' => ! empty($value['enabled']),
            'mode' => in_array($mode, array('opt-in', 'notice'), true) ? $mode : 'opt-in',
            'message' => sanitize_textarea_field((string) ($value['message'] ?? '')),
            'privacy_url' => esc_url_raw((string) ($value['privacy_url'] ?? '')),
            'days' => min(365, max(1, absint($value['days'] ?? 180))),
            'show_reject' => ! empty($value['show_reject']),
            'analytics_label' => sanitize_text_field((string) ($value['analytics_label'] ?? '')),
            'marketing_label' => sanitize_text_field((string) ($value['marketing_label'] ?? '')),
            'preferences_label' => sanitize_text_field((string) ($value['preferences_label'] ?? '')),
        );
    }

    public function assets(): void
    {
        $settings = $this->settings();
        if (empty($settings['enabled'])) {
            return;
        }
        wp_enqueue_style('msp-nexus-consent', plugins_url('assets/consent.css', MSP_NEXUS_CORE_FILE), array(), MSP_NEXUS_CORE_VERSION);
        wp_enqueue_script('msp-nexus-consent', plugins_url('assets/consent.js', MSP_NEXUS_CORE_FILE), array(), MSP_NEXUS_CORE_VERSION, true);
        wp_localize_script('msp-nexus-consent', 'mspNexusConsent', array('cookie' => self::COOKIE, 'days' => (int) $settings['days'], 'mode' => $settings['mode']));
    }

    public function banner(): void
    {
        $settings = $this->settings();
        if (empty($settings['enabled'])) {
            return;
        }
        $message = $settings['message'] ?: __('We use essential storage for site operation. Optional analytics and marketing technologies run only according to your preferences.', 'msp-nexus-core');
        $privacy_url = $settings['privacy_url'] ?: get_privacy_policy_url();
        ?>
        <section class="msp-nexus-consent" data-msp-consent-banner hidden aria-label="<?php esc_attr_e('Privacy choices', 'msp-nexus-core'); ?>">
            <div class="msp-nexus-consent__summary"><p><?php echo esc_html($message); ?> <?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>"><?php esc_html_e('Privacy policy', 'msp-nexus-core'); ?></a><?php endif; ?></p>
            <div class="msp-nexus-consent__actions"><button type="button" data-consent-accept><?php esc_html_e('Accept optional technologies', 'msp-nexus-core'); ?></button><?php if (! empty($settings['show_reject'])) : ?><button type="button" data-consent-reject><?php esc_html_e('Use essentials only', 'msp-nexus-core'); ?></button><?php endif; ?><button type="button" data-consent-open aria-expanded="false"><?php esc_html_e('Choose preferences', 'msp-nexus-core'); ?></button></div></div>
            <form class="msp-nexus-consent__preferences" data-consent-preferences hidden><fieldset><legend><?php esc_html_e('Consent preferences', 'msp-nexus-core'); ?></legend><label><input type="checkbox" checked disabled> <strong><?php esc_html_e('Essential', 'msp-nexus-core'); ?></strong> <span><?php esc_html_e('Required for security, forms, navigation, and saved preferences.', 'msp-nexus-core'); ?></span></label><label><input type="checkbox" name="analytics"> <strong><?php echo esc_html((string) $settings['analytics_label']); ?></strong> <span><?php esc_html_e('Helps measure site use and content effectiveness.', 'msp-nexus-core'); ?></span></label><label><input type="checkbox" name="marketing"> <strong><?php echo esc_html((string) $settings['marketing_label']); ?></strong> <span><?php esc_html_e('Supports campaign attribution and personalized outreach.', 'msp-nexus-core'); ?></span></label><label><input type="checkbox" name="preferences"> <strong><?php echo esc_html((string) $settings['preferences_label']); ?></strong> <span><?php esc_html_e('Remembers optional experience and media choices.', 'msp-nexus-core'); ?></span></label></fieldset><button type="submit"><?php esc_html_e('Save choices', 'msp-nexus-core'); ?></button></form>
        </section>
        <?php
    }

    /** @param array<string, mixed> $attributes */
    public function preferences_shortcode(array $attributes = array()): string
    {
        unset($attributes);
        return '<button type="button" data-consent-manage>' . esc_html__('Manage privacy choices', 'msp-nexus-core') . '</button>';
    }

    public function render(): void
    {
        if (! current_user_can('manage_privacy_options')) {
            return;
        }
        $value = $this->settings();
        ?>
        <div class="wrap"><h1><?php esc_html_e('Nexus privacy and consent', 'msp-nexus-core'); ?></h1><p><?php esc_html_e('Provide accessible visitor choices and activate optional scripts only after the matching category is granted. This tool does not replace legal review or a complete data inventory.', 'msp-nexus-core'); ?></p><form action="options.php" method="post"><?php settings_fields('msp_nexus_privacy'); ?><table class="form-table" role="presentation">
        <tr><th scope="row"><?php esc_html_e('Consent interface', 'msp-nexus-core'); ?></th><td><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[enabled]" value="1" <?php checked(! empty($value['enabled'])); ?>> <?php esc_html_e('Enable visitor privacy choices', 'msp-nexus-core'); ?></label></td></tr>
        <tr><th scope="row"><label for="msp-privacy-mode"><?php esc_html_e('Mode', 'msp-nexus-core'); ?></label></th><td><select id="msp-privacy-mode" name="<?php echo esc_attr(self::OPTION); ?>[mode]"><option value="opt-in" <?php selected($value['mode'], 'opt-in'); ?>><?php esc_html_e('Prior opt-in for optional categories', 'msp-nexus-core'); ?></option><option value="notice" <?php selected($value['mode'], 'notice'); ?>><?php esc_html_e('Notice with category controls', 'msp-nexus-core'); ?></option></select></td></tr>
        <tr><th scope="row"><label for="msp-privacy-message"><?php esc_html_e('Banner message', 'msp-nexus-core'); ?></label></th><td><textarea class="large-text" rows="4" id="msp-privacy-message" name="<?php echo esc_attr(self::OPTION); ?>[message]"><?php echo esc_textarea((string) $value['message']); ?></textarea></td></tr>
        <?php $this->field('privacy_url', __('Privacy policy URL', 'msp-nexus-core'), $value, 'url'); $this->field('analytics_label', __('Analytics category label', 'msp-nexus-core'), $value); $this->field('marketing_label', __('Marketing category label', 'msp-nexus-core'), $value); $this->field('preferences_label', __('Preferences category label', 'msp-nexus-core'), $value); ?>
        <tr><th scope="row"><label for="msp-privacy-days"><?php esc_html_e('Choice lifetime (days)', 'msp-nexus-core'); ?></label></th><td><input id="msp-privacy-days" type="number" min="1" max="365" name="<?php echo esc_attr(self::OPTION); ?>[days]" value="<?php echo esc_attr((string) $value['days']); ?>"></td></tr>
        <tr><th scope="row"><?php esc_html_e('Essentials-only action', 'msp-nexus-core'); ?></th><td><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION); ?>[show_reject]" value="1" <?php checked(! empty($value['show_reject'])); ?>> <?php esc_html_e('Show an equally accessible essentials-only button', 'msp-nexus-core'); ?></label></td></tr>
        </table><?php submit_button(); ?></form><h2><?php esc_html_e('Script markup', 'msp-nexus-core'); ?></h2><p><?php esc_html_e('For reviewed custom integrations, use a non-executing script type and a category attribute. The browser activates it only after consent.', 'msp-nexus-core'); ?></p><pre>&lt;script type="text/plain" data-msp-consent="analytics" src="…"&gt;&lt;/script&gt;</pre><p><code>[msp_nexus_consent_preferences]</code></p></div>
        <?php
    }

    /** @param array<string, mixed> $value */
    private function field(string $key, string $label, array $value, string $type = 'text'): void
    {
        ?><tr><th scope="row"><label for="msp-privacy-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th><td><input class="regular-text" id="msp-privacy-<?php echo esc_attr($key); ?>" type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr(self::OPTION . '[' . $key . ']'); ?>" value="<?php echo esc_attr((string) $value[$key]); ?>"></td></tr><?php
    }

    /** @return array<string, mixed> */
    private function settings(): array
    {
        $value = get_option(self::OPTION, array());
        return wp_parse_args(is_array($value) ? $value : array(), array('enabled' => false, 'mode' => 'opt-in', 'message' => '', 'privacy_url' => '', 'days' => 180, 'show_reject' => true, 'analytics_label' => __('Analytics', 'msp-nexus-core'), 'marketing_label' => __('Marketing', 'msp-nexus-core'), 'preferences_label' => __('Experience preferences', 'msp-nexus-core')));
    }
}
