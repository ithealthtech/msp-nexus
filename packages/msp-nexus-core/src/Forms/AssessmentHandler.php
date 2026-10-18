<?php
/**
 * "How secure is your business?" self-assessment.
 *
 * Ten yes / partly / no questions, each mapped to a CIS Control. The score is computed on the server,
 * so the assessment works without JavaScript. Results come back to the page through a signed token
 * (answers + signature); an email address is optional and, when given, sends the results to sales.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Forms;

final class AssessmentHandler
{
    public const ACTION = 'msp_nexus_assessment';
    public const ANCHOR = 'msp-assessment';

    public function register_hooks(): void
    {
        add_action('admin_post_' . self::ACTION, array($this, 'submit'));
        add_action('admin_post_nopriv_' . self::ACTION, array($this, 'submit'));
    }

    /** @return array<int, array{q: string, why: string, fix: string, control: string}> */
    public static function questions(): array
    {
        return array(
            array('q' => __('Do all staff sign in to email and remote access with multi-factor authentication (a code or app prompt, not just a password)?', 'msp-nexus-core'), 'why' => __('Stolen passwords are the most common way attackers get in.', 'msp-nexus-core'), 'fix' => __('Turn on multi-factor sign-in for email, VPN, and admin accounts first, then every cloud app.', 'msp-nexus-core'), 'control' => 'CIS 6'),
            array('q' => __('Are Windows, macOS, and business applications updated automatically within a few weeks of each release?', 'msp-nexus-core'), 'why' => __('Attackers exploit known holes within days of a patch being published.', 'msp-nexus-core'), 'fix' => __('Automate operating system and third-party patching, and report monthly on anything missed.', 'msp-nexus-core'), 'control' => 'CIS 7'),
            array('q' => __('Are your files and email backed up off-site, and has someone actually tested a restore in the last six months?', 'msp-nexus-core'), 'why' => __('Ransomware now targets backups, and untested backups often fail when needed.', 'msp-nexus-core'), 'fix' => __('Keep an off-site or immutable copy, back up Microsoft 365 or Google too, and test a restore every quarter.', 'msp-nexus-core'), 'control' => 'CIS 11'),
            array('q' => __('Does every computer and server run endpoint protection that someone monitors for alerts?', 'msp-nexus-core'), 'why' => __('Antivirus that nobody watches lets an attack run for days unnoticed.', 'msp-nexus-core'), 'fix' => __('Deploy managed endpoint detection and response on every device, with alerts reviewed around the clock.', 'msp-nexus-core'), 'control' => 'CIS 10'),
            array('q' => __('Do employees get security awareness or phishing training at least once a year?', 'msp-nexus-core'), 'why' => __('Most breaches start with a person clicking a convincing email.', 'msp-nexus-core'), 'fix' => __('Run short annual training plus regular simulated phishing emails.', 'msp-nexus-core'), 'control' => 'CIS 14'),
            array('q' => __('Do you have an up-to-date list of every computer, device, and piece of software your business uses?', 'msp-nexus-core'), 'why' => __('You cannot protect what you do not know you have.', 'msp-nexus-core'), 'fix' => __('Keep an automatically updated inventory of devices and software, and remove anything unauthorized.', 'msp-nexus-core'), 'control' => 'CIS 1–2'),
            array('q' => __('When someone leaves, is their access to email, files, and apps removed the same day?', 'msp-nexus-core'), 'why' => __('Former employees with live accounts are a quiet, common risk.', 'msp-nexus-core'), 'fix' => __('Use an offboarding checklist that disables every account and resets shared passwords the same day.', 'msp-nexus-core'), 'control' => 'CIS 5'),
            array('q' => __('Is your firewall up to date and managed, with guest Wi-Fi kept separate from business systems?', 'msp-nexus-core'), 'why' => __('An outdated firewall or flat network lets one infected device reach everything.', 'msp-nexus-core'), 'fix' => __('Keep firewall firmware current, review its rules, and separate guest and business networks.', 'msp-nexus-core'), 'control' => 'CIS 12'),
            array('q' => __('Is incoming email filtered for phishing, malicious links, and spoofed senders?', 'msp-nexus-core'), 'why' => __('Email is how most attacks are delivered.', 'msp-nexus-core'), 'fix' => __('Add advanced email filtering and set up SPF, DKIM, and DMARC for your domain.', 'msp-nexus-core'), 'control' => 'CIS 9'),
            array('q' => __('Do you have a written plan for what to do, and who to call, if you are hacked or hit by ransomware?', 'msp-nexus-core'), 'why' => __('Without a plan, the first hours of an incident are lost to confusion.', 'msp-nexus-core'), 'fix' => __('Write a one-page incident plan with contacts, first steps, and your insurer’s requirements.', 'msp-nexus-core'), 'control' => 'CIS 17'),
        );
    }

    /** @return array<string, string> answer value => label */
    public static function answers(): array
    {
        return array('2' => __('Yes', 'msp-nexus-core'), '1' => __('Partly', 'msp-nexus-core'), '0' => __('No or not sure', 'msp-nexus-core'));
    }

    public function submit(): void
    {
        $email = sanitize_email(wp_unslash((string) ($_POST['email'] ?? '')));
        $return = '' !== $email ? FormGuard::check(self::ACTION, self::ANCHOR) : $this->verify_only();
        $count = count(self::questions());
        $raw = isset($_POST['answer']) ? (array) wp_unslash($_POST['answer']) : array();
        $answers = '';
        for ($i = 0; $i < $count; $i++) {
            $value = (string) ($raw[$i] ?? '');
            if (! in_array($value, array('0', '1', '2'), true)) {
                FormGuard::redirect($return, 'incomplete', self::ANCHOR);
            }
            $answers .= $value;
        }
        if ('' !== $email) {
            $name = FormGuard::text('name');
            if (! is_email($email) || empty($_POST['consent'])) {
                FormGuard::redirect($return, 'validation', self::ANCHOR);
            }
            $labels = self::answers();
            $body = sprintf("Security self-assessment: %d / 100 (%s)\n\nName: %s\nEmail: %s\nCompany: %s\nPhone: %s\n\n", self::score($answers), self::tier(self::score($answers))['label'], '' === $name ? '—' : $name, $email, FormGuard::text('company') ?: '—', FormGuard::text('phone') ?: '—');
            foreach (self::questions() as $i => $q) {
                $body .= sprintf("%s — %s\n  %s\n", $q['control'], $labels[$answers[$i]], $q['q']);
            }
            /* translators: 1: score, 2: email */
            $subject = sprintf(__('Security self-assessment: %1$d/100 from %2$s', 'msp-nexus-core'), self::score($answers), $email);
            $sent = wp_mail(FormGuard::recipient(), $subject, $body, array('Reply-To: ' . ('' === $name ? $email : $name . ' <' . $email . '>')));
            do_action('msp_nexus_assessment_processed', $answers, $email, $sent);
        }
        $status = '' === $email ? 'scored' : (! empty($sent) ? 'sent' : 'delivery');
        FormGuard::redirect($return, $status, self::ANCHOR, array('msp_score' => $answers . '.' . self::sign($answers)));
    }

    /** Anonymous scoring still needs a valid nonce; no rate limit, nothing is sent. */
    private function verify_only(): string
    {
        $return = FormGuard::return_url();
        if (! isset($_POST['_msp_nexus_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['_msp_nexus_nonce'])), self::ACTION)) {
            FormGuard::redirect($return, 'security', self::ANCHOR);
        }
        return $return;
    }

    public static function score(string $answers): int
    {
        $max = 2 * strlen($answers);
        return $max > 0 ? (int) round(100 * array_sum(array_map('intval', str_split($answers))) / $max) : 0;
    }

    /** @return array{key: string, label: string, text: string} */
    public static function tier(int $score): array
    {
        if ($score >= 80) {
            return array('key' => 'strong', 'label' => __('Strong foundation', 'msp-nexus-core'), 'text' => __('You have most of the essentials in place. An independent audit can confirm they are configured and working the way you think.', 'msp-nexus-core'));
        }
        if ($score >= 50) {
            return array('key' => 'gaps', 'label' => __('Some important gaps', 'msp-nexus-core'), 'text' => __('You have a start, but the gaps below are the kind attackers look for. Most can be closed in weeks, not months.', 'msp-nexus-core'));
        }
        return array('key' => 'risk', 'label' => __('At risk', 'msp-nexus-core'), 'text' => __('Several basic protections are missing. The good news: the fixes below are well understood and affordable for a business your size.', 'msp-nexus-core'));
    }

    /** Answers string from a valid token in the current URL, or ''. */
    public static function from_token(): string
    {
        $token = sanitize_text_field(wp_unslash((string) ($_GET['msp_score'] ?? '')));
        $parts = explode('.', $token);
        if (2 !== count($parts) || ! preg_match('/^[012]{' . count(self::questions()) . '}$/', $parts[0])) {
            return '';
        }
        return hash_equals(self::sign($parts[0]), $parts[1]) ? $parts[0] : '';
    }

    private static function sign(string $answers): string
    {
        return substr(hash_hmac('sha256', 'assessment|' . $answers, wp_salt('auth')), 0, 20);
    }
}
