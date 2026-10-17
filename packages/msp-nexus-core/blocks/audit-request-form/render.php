<?php
/**
 * Third-party MSP audit request form.
 *
 * @var array<string, mixed> $attributes
 */

use MspNexusCore\Forms\AuditRequestHandler;
use MspNexusCore\Forms\FormGuard;

FormGuard::no_cache('msp-nexus audit request form nonce');
$status = isset($_GET['msp_form']) ? sanitize_key(wp_unslash((string) $_GET['msp_form'])) : '';
$messages = FormGuard::messages(__('Thank you. Your audit request is in. We will reply within one business day to schedule a short discovery call.', 'msp-nexus-core'));
$options = AuditRequestHandler::options();
$req = '<span class="msp-nexus-form__required" aria-hidden="true">*</span>';
$select = static function (string $name, string $id, array $choices, bool $required = false): string {
    $html = '<select id="' . esc_attr($id) . '" name="' . esc_attr($name) . '"' . ($required ? ' required aria-required="true"' : '') . '><option value="">' . esc_html__('Select one', 'msp-nexus-core') . '</option>';
    foreach ($choices as $choice) {
        $html .= '<option>' . esc_html($choice) . '</option>';
    }
    return $html . '</select>';
};
?>
<section <?php echo get_block_wrapper_attributes(array('id' => AuditRequestHandler::ANCHOR, 'class' => 'msp-nexus-form msp-nexus-audit-form')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <h2><?php echo esc_html((string) ($attributes['heading'] ?? '')); ?></h2>
    <p class="msp-nexus-form__intro"><?php echo esc_html((string) ($attributes['intro'] ?? '')); ?></p>
    <?php if (isset($messages[$status])) : ?><div class="msp-nexus-form__notice is-<?php echo esc_attr($messages[$status][0]); ?>" role="<?php echo 'error' === $messages[$status][0] ? 'alert' : 'status'; ?>" tabindex="-1"><?php echo esc_html($messages[$status][1]); ?></div><?php endif; ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="<?php echo esc_attr(AuditRequestHandler::ACTION); ?>">
        <input type="hidden" name="return_url" value="<?php echo esc_url(remove_query_arg('msp_form')); ?>">
        <?php wp_nonce_field(AuditRequestHandler::ACTION, '_msp_nexus_nonce'); ?>
        <?php echo FormGuard::honeypot(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in FormGuard. ?>

        <fieldset>
            <legend><?php esc_html_e('About you', 'msp-nexus-core'); ?></legend>
            <div class="msp-nexus-form__grid">
                <p><label for="msp-audit-first"><?php esc_html_e('First name', 'msp-nexus-core'); ?> <?php echo $req; // phpcs:ignore ?></label><input id="msp-audit-first" name="first_name" type="text" autocomplete="given-name" required aria-required="true"></p>
                <p><label for="msp-audit-last"><?php esc_html_e('Last name', 'msp-nexus-core'); ?> <?php echo $req; // phpcs:ignore ?></label><input id="msp-audit-last" name="last_name" type="text" autocomplete="family-name" required aria-required="true"></p>
                <p><label for="msp-audit-email"><?php esc_html_e('Work email', 'msp-nexus-core'); ?> <?php echo $req; // phpcs:ignore ?></label><input id="msp-audit-email" name="email" type="email" autocomplete="email" required aria-required="true"></p>
                <p><label for="msp-audit-phone"><?php esc_html_e('Phone', 'msp-nexus-core'); ?></label><input id="msp-audit-phone" name="phone" type="tel" autocomplete="tel"></p>
                <p><label for="msp-audit-title"><?php esc_html_e('Title or position', 'msp-nexus-core'); ?></label><input id="msp-audit-title" name="job_title" type="text" autocomplete="organization-title"></p>
                <p><label for="msp-audit-contact"><?php esc_html_e('Best way to reach you', 'msp-nexus-core'); ?></label><?php echo $select('contact_preference', 'msp-audit-contact', $options['contact']); // phpcs:ignore ?></p>
            </div>
        </fieldset>

        <fieldset>
            <legend><?php esc_html_e('About your business', 'msp-nexus-core'); ?></legend>
            <div class="msp-nexus-form__grid msp-nexus-form__grid--3">
                <p><label for="msp-audit-company"><?php esc_html_e('Company name', 'msp-nexus-core'); ?> <?php echo $req; // phpcs:ignore ?></label><input id="msp-audit-company" name="company" type="text" autocomplete="organization" required aria-required="true"></p>
                <p><label for="msp-audit-city"><?php esc_html_e('City', 'msp-nexus-core'); ?></label><input id="msp-audit-city" name="city" type="text" autocomplete="address-level2"></p>
                <p><label for="msp-audit-state"><?php esc_html_e('State', 'msp-nexus-core'); ?></label><input id="msp-audit-state" name="state" type="text" autocomplete="address-level1"></p>
            </div>
            <div class="msp-nexus-form__grid">
                <p><label for="msp-audit-employees"><?php esc_html_e('Number of employees', 'msp-nexus-core'); ?></label><?php echo $select('employees', 'msp-audit-employees', $options['employees']); // phpcs:ignore ?></p>
            </div>
            <p><label for="msp-audit-about"><?php esc_html_e('Tell us about your company', 'msp-nexus-core'); ?> <?php echo $req; // phpcs:ignore ?></label><textarea id="msp-audit-about" name="about" rows="4" required aria-required="true" aria-describedby="msp-audit-about-hint"></textarea><span id="msp-audit-about-hint" class="msp-nexus-form__hint"><?php esc_html_e('What you do, how many locations you have, and roughly how many computers and servers.', 'msp-nexus-core'); ?></span></p>
            <p><label for="msp-audit-industry"><?php esc_html_e('Industry-specific software or IT concerns', 'msp-nexus-core'); ?></label><textarea id="msp-audit-industry" name="industry_concerns" rows="3" aria-describedby="msp-audit-industry-hint"></textarea><span id="msp-audit-industry-hint" class="msp-nexus-form__hint"><?php esc_html_e('For example Dentrix, Dolphin, QuickBooks, an EHR or practice-management system, or a compliance framework such as HIPAA or CMMC.', 'msp-nexus-core'); ?></span></p>
        </fieldset>

        <fieldset>
            <legend><?php esc_html_e('Your current IT support', 'msp-nexus-core'); ?></legend>
            <div class="msp-nexus-form__grid">
                <p><label for="msp-audit-provider"><?php esc_html_e('Current IT provider', 'msp-nexus-core'); ?></label><input id="msp-audit-provider" name="current_provider" type="text" aria-describedby="msp-audit-provider-hint"><span id="msp-audit-provider-hint" class="msp-nexus-form__hint"><?php esc_html_e('Optional. Kept confidential; we never contact your provider without your permission.', 'msp-nexus-core'); ?></span></p>
                <p><label for="msp-audit-provider-type"><?php esc_html_e('Who handles your IT today?', 'msp-nexus-core'); ?></label><?php echo $select('provider_type', 'msp-audit-provider-type', $options['provider_type']); // phpcs:ignore ?></p>
                <p><label for="msp-audit-satisfaction"><?php esc_html_e('Are you satisfied with your current IT provider?', 'msp-nexus-core'); ?></label><?php echo $select('satisfaction', 'msp-audit-satisfaction', $options['satisfaction']); // phpcs:ignore ?></p>
                <p><label for="msp-audit-reason"><?php esc_html_e('Main reason for the audit', 'msp-nexus-core'); ?> <?php echo $req; // phpcs:ignore ?></label><?php echo $select('reason', 'msp-audit-reason', $options['reason'], true); // phpcs:ignore ?></p>
            </div>
            <fieldset>
                <legend><?php esc_html_e('Are you experiencing any of these?', 'msp-nexus-core'); ?></legend>
                <ul class="msp-nexus-form__checks">
                    <?php foreach ($options['issues'] as $issue) : ?>
                        <li><label><input type="checkbox" name="issues[]" value="<?php echo esc_attr($issue); ?>"> <?php echo esc_html($issue); ?></label></li>
                    <?php endforeach; ?>
                </ul>
            </fieldset>
        </fieldset>

        <p class="msp-nexus-form__consent"><label><input name="consent" type="checkbox" value="1" required> <?php echo esc_html((string) ($attributes['privacyText'] ?? '')); ?></label></p>
        <p class="msp-nexus-form__submit"><button class="wp-element-button" type="submit"><?php esc_html_e('Request my audit', 'msp-nexus-core'); ?></button></p>
    </form>
</section>
