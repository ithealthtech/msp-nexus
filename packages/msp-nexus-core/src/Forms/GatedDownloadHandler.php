<?php
/**
 * Email-gated downloads (ebooks, guides, checklists).
 *
 * The visitor's details go to the sales inbox, then the page shows the download link through a signed
 * token that expires after a day. The file URL itself is a normal Media Library URL and is not secret;
 * the gate exists to collect the lead, not to protect the file.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Forms;

final class GatedDownloadHandler
{
    public const ACTION = 'msp_nexus_gated_download';
    public const ANCHOR = 'msp-download-form';
    private const TTL = DAY_IN_SECONDS;

    public function register_hooks(): void
    {
        add_action('admin_post_' . self::ACTION, array($this, 'submit'));
        add_action('admin_post_nopriv_' . self::ACTION, array($this, 'submit'));
    }

    public function submit(): void
    {
        $return = FormGuard::check(self::ACTION, self::ANCHOR);
        $attachment = absint($_POST['attachment'] ?? 0);
        $data = array(
            'name' => FormGuard::text('name'),
            'email' => sanitize_email(wp_unslash((string) ($_POST['email'] ?? ''))),
            'company' => FormGuard::text('company'),
            'role' => FormGuard::text('job_title'),
            'phone' => FormGuard::text('phone'),
            'consent' => ! empty($_POST['consent']),
            'follow_up' => ! empty($_POST['follow_up']),
        );
        if ('' === $data['name'] || ! is_email($data['email']) || ! $data['consent'] || ! self::file_url($attachment)) {
            FormGuard::redirect($return, 'validation', self::ANCHOR);
        }
        if (true === apply_filters('msp_nexus_form_is_spam', false, $data)) {
            FormGuard::redirect($return, 'success', self::ANCHOR, self::token_args($attachment));
        }
        $title = get_the_title($attachment);
        $body = sprintf(
            "Download: %s\n\nName: %s\nEmail: %s\nCompany: %s\nTitle: %s\nPhone: %s\nWants a follow-up conversation: %s\n",
            $title,
            $data['name'],
            $data['email'],
            '' === $data['company'] ? '—' : $data['company'],
            '' === $data['role'] ? '—' : $data['role'],
            '' === $data['phone'] ? '—' : $data['phone'],
            $data['follow_up'] ? 'Yes' : 'No'
        );
        /* translators: 1: download title, 2: visitor name */
        $subject = sprintf(__('Download: %1$s — %2$s', 'msp-nexus-core'), $title, $data['name']);
        $sent = wp_mail(FormGuard::recipient(), $subject, $body, array('Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>'));
        do_action('msp_nexus_gated_download_processed', $data, $attachment, $sent);
        // The visitor gets the file even if the lead email fails; the failure is reported through the action above.
        FormGuard::redirect($return, 'success', self::ANCHOR, self::token_args($attachment));
    }

    public static function file_url(int $attachment): string
    {
        if ($attachment < 1 || 'attachment' !== get_post_type($attachment)) {
            return '';
        }
        return (string) wp_get_attachment_url($attachment);
    }

    /** @return array<string, string> */
    private static function token_args(int $attachment): array
    {
        $expires = time() + self::TTL;
        return array('msp_dl' => $attachment . '.' . $expires . '.' . self::sign($attachment, $expires));
    }

    /** Whether the current URL carries a valid, unexpired token for this attachment. */
    public static function unlocked(int $attachment): bool
    {
        $token = sanitize_text_field(wp_unslash((string) ($_GET['msp_dl'] ?? '')));
        $parts = explode('.', $token);
        if (3 !== count($parts) || (int) $parts[0] !== $attachment || (int) $parts[1] < time()) {
            return false;
        }
        return hash_equals(self::sign($attachment, (int) $parts[1]), $parts[2]);
    }

    private static function sign(int $attachment, int $expires): string
    {
        return substr(hash_hmac('sha256', $attachment . '|' . $expires, wp_salt('auth')), 0, 32);
    }
}
