<?php
/**
 * Email-gated download.
 *
 * @var array<string, mixed> $attributes
 */

use MspNexusCore\Forms\FormGuard;
use MspNexusCore\Forms\GatedDownloadHandler;

FormGuard::no_cache('msp-nexus gated download form nonce');
$attachment = absint($attributes['attachmentId'] ?? 0);
$file = GatedDownloadHandler::file_url($attachment);
$status = isset($_GET['msp_form']) ? sanitize_key(wp_unslash((string) $_GET['msp_form'])) : '';
$messages = FormGuard::messages(__('Thank you. Your download is ready below, and a copy of the link stays on this page for 24 hours.', 'msp-nexus-core'));
$unlocked = '' !== $file && GatedDownloadHandler::unlocked($attachment);
$req = '<span class="msp-nexus-form__required" aria-hidden="true">*</span>';
?>
<section <?php echo get_block_wrapper_attributes(array('id' => GatedDownloadHandler::ANCHOR, 'class' => 'msp-nexus-form msp-nexus-download')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <h2><?php echo esc_html((string) ($attributes['heading'] ?? '')); ?></h2>
    <?php if (isset($messages[$status]) && ('success' !== $status || $unlocked)) : ?><div class="msp-nexus-form__notice is-<?php echo esc_attr($messages[$status][0]); ?>" role="<?php echo 'error' === $messages[$status][0] ? 'alert' : 'status'; ?>" tabindex="-1"><?php echo esc_html($messages[$status][1]); ?></div><?php endif; ?>
    <?php if ('' === $file) : ?>
        <p class="msp-nexus-form__intro"><?php esc_html_e('This resource is being prepared. Please check back soon or contact us for a copy.', 'msp-nexus-core'); ?></p>
        <?php if (current_user_can('edit_posts')) : ?><p class="msp-nexus-form__hint"><?php esc_html_e('Editors: choose the PDF for this block (attachment ID) in the block settings.', 'msp-nexus-core'); ?></p><?php endif; ?>
    <?php elseif ($unlocked) : ?>
        <div class="msp-nexus-form__unlocked">
            <p class="msp-nexus-form__submit"><a class="wp-element-button" href="<?php echo esc_url($file); ?>" download><?php echo esc_html((string) ($attributes['downloadLabel'] ?? '')); ?></a></p>
            <p class="msp-nexus-form__hint"><?php esc_html_e('Questions about anything in it? Reply to our follow-up email or give us a call.', 'msp-nexus-core'); ?></p>
        </div>
    <?php else : ?>
        <p class="msp-nexus-form__intro"><?php echo esc_html((string) ($attributes['intro'] ?? '')); ?></p>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="action" value="<?php echo esc_attr(GatedDownloadHandler::ACTION); ?>">
            <input type="hidden" name="attachment" value="<?php echo esc_attr((string) $attachment); ?>">
            <input type="hidden" name="return_url" value="<?php echo esc_url(remove_query_arg(array('msp_form', 'msp_dl'))); ?>">
            <?php wp_nonce_field(GatedDownloadHandler::ACTION, '_msp_nexus_nonce'); ?>
            <?php echo FormGuard::honeypot(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in FormGuard. ?>
            <div class="msp-nexus-form__grid">
                <p><label for="msp-dl-name"><?php esc_html_e('Name', 'msp-nexus-core'); ?> <?php echo $req; // phpcs:ignore ?></label><input id="msp-dl-name" name="name" type="text" autocomplete="name" required aria-required="true"></p>
                <p><label for="msp-dl-email"><?php esc_html_e('Work email', 'msp-nexus-core'); ?> <?php echo $req; // phpcs:ignore ?></label><input id="msp-dl-email" name="email" type="email" autocomplete="email" required aria-required="true"></p>
                <p><label for="msp-dl-company"><?php esc_html_e('Company', 'msp-nexus-core'); ?></label><input id="msp-dl-company" name="company" type="text" autocomplete="organization"></p>
                <p><label for="msp-dl-title"><?php esc_html_e('Title', 'msp-nexus-core'); ?></label><input id="msp-dl-title" name="job_title" type="text" autocomplete="organization-title"></p>
            </div>
            <p class="msp-nexus-form__consent"><label><input name="follow_up" type="checkbox" value="1"> <?php esc_html_e('I would also like a free 15-minute call to talk through my results.', 'msp-nexus-core'); ?></label></p>
            <p class="msp-nexus-form__consent"><label><input name="consent" type="checkbox" value="1" required> <?php echo esc_html((string) ($attributes['privacyText'] ?? '')); ?></label></p>
            <p class="msp-nexus-form__submit"><button class="wp-element-button" type="submit"><?php echo esc_html((string) ($attributes['buttonLabel'] ?? '')); ?></button></p>
        </form>
    <?php endif; ?>
</section>
