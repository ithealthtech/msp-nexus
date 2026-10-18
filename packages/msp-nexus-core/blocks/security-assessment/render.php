<?php
/**
 * Security self-assessment.
 *
 * @var array<string, mixed> $attributes
 */

use MspNexusCore\Forms\AssessmentHandler;
use MspNexusCore\Forms\FormGuard;

FormGuard::no_cache('msp-nexus security assessment nonce');
$questions = AssessmentHandler::questions();
$labels = AssessmentHandler::answers();
$answers = AssessmentHandler::from_token();
$status = isset($_GET['msp_form']) ? sanitize_key(wp_unslash((string) $_GET['msp_form'])) : '';
$errors = array(
    'incomplete' => __('Answer all ten questions to see your score.', 'msp-nexus-core'),
    'validation' => __('Enter a valid email address and tick the consent box to receive your results.', 'msp-nexus-core'),
    'rate' => __('Please wait a moment before submitting again.', 'msp-nexus-core'),
    'security' => __('The page expired. Refresh and try again.', 'msp-nexus-core'),
    'delivery' => __('We could not email your results. Your score is below; call us for a copy.', 'msp-nexus-core'),
);
$audit = esc_url((string) ($attributes['auditUrl'] ?? '/request-an-audit/'));
$resource = esc_url((string) ($attributes['resourceUrl'] ?? ''));
?>
<section <?php echo get_block_wrapper_attributes(array('id' => AssessmentHandler::ANCHOR, 'class' => 'msp-nexus-form msp-nexus-assessment')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<?php if ('' !== $answers) :
    $score = AssessmentHandler::score($answers);
    $tier = AssessmentHandler::tier($score);
    ?>
    <div class="msp-nexus-assessment__result is-<?php echo esc_attr($tier['key']); ?>">
        <?php if ('sent' === $status) : ?><div class="msp-nexus-form__notice is-success" role="status"><?php esc_html_e('Thank you. Your results are on their way to our team, and we will follow up within one business day.', 'msp-nexus-core'); ?></div><?php endif; ?>
        <p class="msp-nexus-assessment__eyebrow"><?php esc_html_e('Your security score', 'msp-nexus-core'); ?></p>
        <p class="msp-nexus-assessment__score" role="status"><span><?php echo esc_html((string) $score); ?></span>/100</p>
        <div class="msp-nexus-assessment__meter" aria-hidden="true"><span class="is-<?php echo esc_attr((string) (10 * (int) round($score / 10))); ?>"></span></div>
        <h2><?php echo esc_html($tier['label']); ?></h2>
        <p><?php echo esc_html($tier['text']); ?></p>
    </div>
    <?php $gaps = array_filter($questions, static function ($i) use ($answers) { return '2' !== $answers[$i]; }, ARRAY_FILTER_USE_KEY); ?>
    <?php if ($gaps) : ?>
        <h3><?php esc_html_e('Where to start', 'msp-nexus-core'); ?></h3>
        <ol class="msp-nexus-assessment__gaps">
            <?php foreach ($gaps as $i => $q) : ?>
                <li class="is-<?php echo '0' === $answers[$i] ? 'missing' : 'partial'; ?>">
                    <strong><?php echo esc_html($q['fix']); ?></strong>
                    <span><?php echo esc_html($q['why']); ?> <em><?php echo esc_html($labels[$answers[$i]] . ' · ' . $q['control']); ?></em></span>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
    <div class="msp-nexus-assessment__next">
        <p class="msp-nexus-form__submit"><a class="wp-element-button" href="<?php echo $audit; // phpcs:ignore ?>"><?php esc_html_e('Get an expert audit', 'msp-nexus-core'); ?></a></p>
        <?php if ('' !== $resource) : ?><p><a href="<?php echo $resource; // phpcs:ignore ?>"><?php esc_html_e('Read the free Cybersecurity Essentials booklet', 'msp-nexus-core'); ?></a></p><?php endif; ?>
        <p><a href="<?php echo esc_url(remove_query_arg(array('msp_score', 'msp_form'))); ?>#<?php echo esc_attr(AssessmentHandler::ANCHOR); ?>"><?php esc_html_e('Retake the assessment', 'msp-nexus-core'); ?></a></p>
    </div>
    <?php if ('sent' !== $status) : ?>
        <form class="msp-nexus-assessment__email" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <h3><?php esc_html_e('Want a specialist to review your answers?', 'msp-nexus-core'); ?></h3>
            <p class="msp-nexus-form__intro"><?php esc_html_e('Send your results to our team and we will reply with practical next steps. No obligation.', 'msp-nexus-core'); ?></p>
            <?php if (isset($errors[$status])) : ?><div class="msp-nexus-form__notice is-error" role="alert"><?php echo esc_html($errors[$status]); ?></div><?php endif; ?>
            <input type="hidden" name="action" value="<?php echo esc_attr(AssessmentHandler::ACTION); ?>">
            <input type="hidden" name="return_url" value="<?php echo esc_url(remove_query_arg(array('msp_score', 'msp_form'))); ?>">
            <?php foreach (str_split($answers) as $i => $value) : ?><input type="hidden" name="answer[<?php echo esc_attr((string) $i); ?>]" value="<?php echo esc_attr($value); ?>"><?php endforeach; ?>
            <?php wp_nonce_field(AssessmentHandler::ACTION, '_msp_nexus_nonce'); ?>
            <?php echo FormGuard::honeypot(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in FormGuard. ?>
            <div class="msp-nexus-form__grid">
                <p><label for="msp-as-name"><?php esc_html_e('Name', 'msp-nexus-core'); ?></label><input id="msp-as-name" name="name" type="text" autocomplete="name"></p>
                <p><label for="msp-as-email"><?php esc_html_e('Work email', 'msp-nexus-core'); ?> <span class="msp-nexus-form__required" aria-hidden="true">*</span></label><input id="msp-as-email" name="email" type="email" autocomplete="email" required aria-required="true"></p>
                <p><label for="msp-as-company"><?php esc_html_e('Company', 'msp-nexus-core'); ?></label><input id="msp-as-company" name="company" type="text" autocomplete="organization"></p>
                <p><label for="msp-as-phone"><?php esc_html_e('Phone', 'msp-nexus-core'); ?></label><input id="msp-as-phone" name="phone" type="tel" autocomplete="tel"></p>
            </div>
            <p class="msp-nexus-form__consent"><label><input name="consent" type="checkbox" value="1" required> <?php esc_html_e('I agree that IT Done Right may use these answers to contact me about my results.', 'msp-nexus-core'); ?></label></p>
            <p class="msp-nexus-form__submit"><button class="wp-element-button" type="submit"><?php esc_html_e('Send my results', 'msp-nexus-core'); ?></button></p>
        </form>
    <?php endif; ?>
<?php else : ?>
    <h2><?php echo esc_html((string) ($attributes['heading'] ?? '')); ?></h2>
    <p class="msp-nexus-form__intro"><?php echo esc_html((string) ($attributes['intro'] ?? '')); ?></p>
    <?php if (isset($errors[$status])) : ?><div class="msp-nexus-form__notice is-error" role="alert" tabindex="-1"><?php echo esc_html($errors[$status]); ?></div><?php endif; ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="<?php echo esc_attr(AssessmentHandler::ACTION); ?>">
        <input type="hidden" name="return_url" value="<?php echo esc_url(remove_query_arg(array('msp_score', 'msp_form'))); ?>">
        <?php wp_nonce_field(AssessmentHandler::ACTION, '_msp_nexus_nonce'); ?>
        <ol class="msp-nexus-assessment__questions">
            <?php foreach ($questions as $i => $q) : ?>
                <li>
                    <fieldset>
                        <legend><?php echo esc_html($q['q']); ?></legend>
                        <div class="msp-nexus-assessment__choices">
                            <?php foreach ($labels as $value => $label) : ?>
                                <label><input type="radio" name="answer[<?php echo esc_attr((string) $i); ?>]" value="<?php echo esc_attr((string) $value); ?>" required> <span><?php echo esc_html($label); ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                </li>
            <?php endforeach; ?>
        </ol>
        <p class="msp-nexus-form__submit"><button class="wp-element-button" type="submit"><?php esc_html_e('See my score', 'msp-nexus-core'); ?></button></p>
    </form>
<?php endif; ?>
</section>
