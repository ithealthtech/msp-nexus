<?php
/** @var array<string, mixed> $attributes */
// The form carries a nonce that expires within a day, so a page showing it must never be served from a page cache.
if (! defined('DONOTCACHEPAGE')) {
    define('DONOTCACHEPAGE', true);
}
do_action('litespeed_control_set_nocache', 'msp-nexus consultation form nonce');
if (! headers_sent()) {
    nocache_headers();
}
$status = isset($_GET['msp_form']) ? sanitize_key(wp_unslash((string) $_GET['msp_form'])) : '';
$messages = array(
    'success' => array('success', __('Thank you. Your request was submitted.', 'msp-nexus-core')),
    'validation' => array('error', __('Complete every required field and provide a valid email address.', 'msp-nexus-core')),
    'rate' => array('error', __('Please wait a moment before submitting another request.', 'msp-nexus-core')),
    'delivery' => array('error', __('The request could not be delivered. Please use the published telephone or support channel.', 'msp-nexus-core')),
    'security' => array('error', __('The form session expired. Refresh the page and try again.', 'msp-nexus-core')),
);
?>
<section <?php echo get_block_wrapper_attributes(array('id' => 'msp-consultation-form', 'class' => 'msp-nexus-consultation')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <h2><?php echo esc_html((string) ($attributes['heading'] ?? __('Plan a consultation', 'msp-nexus-core'))); ?></h2>
    <?php if (isset($messages[$status])) : ?><div class="msp-nexus-consultation__notice is-<?php echo esc_attr($messages[$status][0]); ?>" role="<?php echo 'error' === $messages[$status][0] ? 'alert' : 'status'; ?>" tabindex="-1"><?php echo esc_html($messages[$status][1]); ?></div><?php endif; ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="<?php echo esc_attr(\MspNexusCore\Forms\ConsultationHandler::ACTION); ?>">
        <input type="hidden" name="return_url" value="<?php echo esc_url(remove_query_arg('msp_form')); ?>">
        <?php wp_nonce_field(\MspNexusCore\Forms\ConsultationHandler::ACTION, '_msp_nexus_nonce'); ?>
        <div class="msp-nexus-consultation__honeypot" aria-hidden="true"><label for="msp-company-website"><?php esc_html_e('Leave this field empty', 'msp-nexus-core'); ?></label><input id="msp-company-website" name="company_website" type="text" tabindex="-1" autocomplete="off"></div>
        <div class="msp-nexus-consultation__grid">
            <p><label for="msp-name"><?php esc_html_e('Name', 'msp-nexus-core'); ?> <span aria-hidden="true">*</span></label><input id="msp-name" name="name" type="text" autocomplete="name" required aria-required="true"></p>
            <p><label for="msp-email"><?php esc_html_e('Work email', 'msp-nexus-core'); ?> <span aria-hidden="true">*</span></label><input id="msp-email" name="email" type="email" autocomplete="email" required aria-required="true"></p>
            <p><label for="msp-company"><?php esc_html_e('Organization', 'msp-nexus-core'); ?></label><input id="msp-company" name="company" type="text" autocomplete="organization"></p>
            <p><label for="msp-phone"><?php esc_html_e('Telephone', 'msp-nexus-core'); ?></label><input id="msp-phone" name="phone" type="tel" autocomplete="tel"></p>
        </div>
        <p><label for="msp-message"><?php esc_html_e('What would you like to improve?', 'msp-nexus-core'); ?> <span aria-hidden="true">*</span></label><textarea id="msp-message" name="message" rows="6" required aria-required="true"></textarea></p>
        <p class="msp-nexus-consultation__consent"><label><input name="consent" type="checkbox" value="1" required> <?php echo esc_html((string) ($attributes['privacyText'] ?? '')); ?></label></p>
        <p><button class="wp-element-button" type="submit"><?php esc_html_e('Submit consultation request', 'msp-nexus-core'); ?></button></p>
    </form>
</section>
