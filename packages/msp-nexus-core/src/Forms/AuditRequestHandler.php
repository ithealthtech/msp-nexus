<?php
/**
 * Third-party MSP audit requests.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Forms;

final class AuditRequestHandler
{
    public const ACTION = 'msp_nexus_audit_request';
    public const ANCHOR = 'msp-audit-form';

    public function register_hooks(): void
    {
        add_action('admin_post_' . self::ACTION, array($this, 'submit'));
        add_action('admin_post_nopriv_' . self::ACTION, array($this, 'submit'));
    }

    /** @return array<string, string[]> */
    public static function options(): array
    {
        return array(
            'employees' => array('1–10', '11–25', '26–50', '51–100', '101–250', '250+'),
            'satisfaction' => array(__('Yes', 'msp-nexus-core'), __('No', 'msp-nexus-core'), __('Not sure', 'msp-nexus-core'), __('Recently switched providers', 'msp-nexus-core')),
            'provider_type' => array(__('Outsourced IT company (MSP)', 'msp-nexus-core'), __('Internal IT staff', 'msp-nexus-core'), __('Both (co-managed)', 'msp-nexus-core'), __('A friend or part-time contractor', 'msp-nexus-core'), __('No one right now', 'msp-nexus-core')),
            'issues' => array(__('Slow performance', 'msp-nexus-core'), __('Slow or unreachable phone support', 'msp-nexus-core'), __('Network outages', 'msp-nexus-core'), __('Email problems', 'msp-nexus-core'), __('Security concerns', 'msp-nexus-core'), __('Weak Wi-Fi signal', 'msp-nexus-core'), __('Application crashes', 'msp-nexus-core'), __('Phone system problems', 'msp-nexus-core'), __('Unclear or rising IT bills', 'msp-nexus-core'), __('Backups I have never seen tested', 'msp-nexus-core'), __('None of these', 'msp-nexus-core')),
            'reason' => array(__('Verify my IT provider is doing what they claim', 'msp-nexus-core'), __('Security or compliance concern', 'msp-nexus-core'), __('Cyber-insurance questionnaire or renewal', 'msp-nexus-core'), __('Considering a new IT provider', 'msp-nexus-core'), __('After an outage, breach, or data loss', 'msp-nexus-core'), __('Merger, acquisition, or leadership change', 'msp-nexus-core'), __('Other', 'msp-nexus-core')),
            'contact' => array(__('Email', 'msp-nexus-core'), __('Phone', 'msp-nexus-core')),
        );
    }

    public function submit(): void
    {
        $return = FormGuard::check(self::ACTION, self::ANCHOR);
        $o = self::options();
        $data = array(
            'first' => FormGuard::text('first_name'),
            'last' => FormGuard::text('last_name'),
            'company' => FormGuard::text('company'),
            'title' => FormGuard::text('job_title'),
            'email' => sanitize_email(wp_unslash((string) ($_POST['email'] ?? ''))),
            'phone' => FormGuard::text('phone'),
            'city' => FormGuard::text('city'),
            'state' => FormGuard::text('state'),
            'employees' => FormGuard::choice('employees', $o['employees']),
            'provider' => FormGuard::text('current_provider'),
            'provider_type' => FormGuard::choice('provider_type', $o['provider_type']),
            'satisfaction' => FormGuard::choice('satisfaction', $o['satisfaction']),
            'issues' => FormGuard::choices('issues', $o['issues']),
            'reason' => FormGuard::choice('reason', $o['reason']),
            'about' => FormGuard::textarea('about'),
            'industry' => FormGuard::textarea('industry_concerns'),
            'contact' => FormGuard::choice('contact_preference', $o['contact']),
            'consent' => ! empty($_POST['consent']),
        );
        if ('' === $data['first'] || '' === $data['last'] || '' === $data['company'] || ! is_email($data['email']) || '' === $data['reason'] || '' === $data['about'] || ! $data['consent']) {
            FormGuard::redirect($return, 'validation', self::ANCHOR);
        }
        if (true === apply_filters('msp_nexus_form_is_spam', false, $data)) {
            FormGuard::redirect($return, 'success', self::ANCHOR);
        }
        $name = trim($data['first'] . ' ' . $data['last']);
        $lines = array(
            'Name' => $name,
            'Title' => $data['title'],
            'Company' => $data['company'],
            'Email' => $data['email'],
            'Phone' => $data['phone'],
            'Location' => trim($data['city'] . ', ' . $data['state'], ', '),
            'Employees' => $data['employees'],
            'Current IT provider' => $data['provider'],
            'Provider type' => $data['provider_type'],
            'Satisfied with current provider' => $data['satisfaction'],
            'Problems they are seeing' => implode(', ', $data['issues']),
            'Reason for the audit' => $data['reason'],
            'Preferred contact' => $data['contact'],
        );
        $body = '';
        foreach ($lines as $label => $value) {
            $body .= $label . ': ' . ('' === $value ? '—' : $value) . "\n";
        }
        $body .= "\nAbout the company:\n" . $data['about'] . "\n\nIndustry-specific systems or concerns:\n" . ('' === $data['industry'] ? '—' : $data['industry']) . "\n";
        /* translators: 1: company, 2: contact name */
        $subject = sprintf(__('3rd-party MSP audit request: %1$s (%2$s)', 'msp-nexus-core'), $data['company'], $name);
        $sent = wp_mail(FormGuard::recipient(), $subject, $body, array('Reply-To: ' . $name . ' <' . $data['email'] . '>'));
        do_action('msp_nexus_audit_request_processed', $data, $sent);
        FormGuard::redirect($return, $sent ? 'success' : 'delivery', self::ANCHOR);
    }
}
