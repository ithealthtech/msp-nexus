<?php
/**
 * Title: Industry: Legal
 * Slug: msp-nexus/industry-legal
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Industry page for law firms: confidentiality-first hero, practice areas, document-management uptime, a client-confidentiality program, engagement terms, FAQs, and a review offer.
 *
 * @package MspNexus
 */

echo msp_nexus_industry_page(array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the template.
    'modifier' => 'legal',
    'pill' => __('IT for law firms and legal departments', 'msp-nexus'),
    'title' => __('Protect privilege.', 'msp-nexus'),
    'title_accent' => __('Protect billable hours.', 'msp-nexus'),
    'lede' => __('IT support built around document management, deadlines, and the duty of confidentiality your clients trust you with.', 'msp-nexus'),
    'cta' => __('Book a confidentiality review', 'msp-nexus'),
    'cta_secondary' => __('How we protect client files', 'msp-nexus'),
    'status_title' => __('Firm systems check', 'msp-nexus'),
    'status' => array(
        __('Document management system', 'msp-nexus') => __('Monitored', 'msp-nexus'),
        __('Multi-factor sign-in', 'msp-nexus') => __('Enforced', 'msp-nexus'),
        __('Matter file backups', 'msp-nexus') => __('Verified', 'msp-nexus'),
        __('Wire-fraud email checks', 'msp-nexus') => __('Active', 'msp-nexus'),
        __('Confidentiality terms', 'msp-nexus') => __('Signed', 'msp-nexus'),
    ),
    'types_title' => __('Support that fits your practice', 'msp-nexus'),
    'types' => array(
        __('Litigation', 'msp-nexus') => __('E-discovery exports, large files, court deadlines', 'msp-nexus'),
        __('Corporate', 'msp-nexus') => __('Deal rooms, e-signature, and closing days', 'msp-nexus'),
        __('Estate & family', 'msp-nexus') => __('Sensitive client records and secure portals', 'msp-nexus'),
        __('Real estate', 'msp-nexus') => __('Closing funds and wire-fraud prevention', 'msp-nexus'),
    ),
    'flow_title' => __('A filing deadline does not wait for a frozen laptop.', 'msp-nexus'),
    'flow_body' => __('Deadline-driven requests go to the front of the queue. We know your document management, billing, and e-filing tools, and we coordinate with those vendors for you.', 'msp-nexus'),
    'flow_checks' => array(__('Court-deadline issues jump the queue', 'msp-nexus'), __('We work with your DMS and billing vendors', 'msp-nexus'), __('Updates scheduled around trial calendars', 'msp-nexus')),
    'image' => 'code-review',
    'image_alt' => __('A laptop screen in an office', 'msp-nexus'),
    'program_title' => __('A confidentiality program your clients would approve', 'msp-nexus'),
    'program' => array(
        __('Risk review', 'msp-nexus') => __('Where client data lives, who can reach it, and what would happen if it leaked.', 'msp-nexus'),
        __('Safeguards', 'msp-nexus') => __('Encryption, MFA, matter-level permissions, and secure client file sharing.', 'msp-nexus'),
        __('Training', 'msp-nexus') => __('Phishing and wire-fraud awareness for attorneys and staff.', 'msp-nexus'),
        __('Evidence', 'msp-nexus') => __('Written policies and reports for client security questionnaires and insurers.', 'msp-nexus'),
    ),
    'assurance_title' => __('Confidentiality is in our contract.', 'msp-nexus'),
    'assurance_body' => __('Our engagement terms include confidentiality obligations and limit our staff access to what the work requires.', 'msp-nexus'),
    'note' => __('Describe your real process here. This page is not legal advice; confirm professional-responsibility statements with counsel before launch.', 'msp-nexus'),
    'faq_title' => __('Common questions from firm administrators', 'msp-nexus'),
    'close_title' => __('Find the gaps before opposing counsel does.', 'msp-nexus'),
    'close_body' => __('A free review of access controls, backups, and email fraud defenses.', 'msp-nexus'),
    'close_cta' => __('Book the review', 'msp-nexus'),
));
