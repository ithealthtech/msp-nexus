<?php
/**
 * Title: Industry: Healthcare (Vitals)
 * Slug: msp-nexus/flagship-vitals-home
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Calm, clinical industry page for medical, dental, and behavioral health practices: HIPAA-first hero, practice types, EHR uptime, a four-step HIPAA program, BAA assurance, FAQs, and a book-a-call band.
 *
 * @package MspNexus
 */

echo msp_nexus_industry_page(array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the template.
    'modifier' => 'healthcare',
    'pill' => __('IT for medical, dental & behavioral health practices', 'msp-nexus'),
    'title' => __('Keep patients moving.', 'msp-nexus'),
    'title_accent' => __('Keep their data safe.', 'msp-nexus'),
    'lede' => __('Healthcare-focused IT support that understands EHR downtime costs appointments, and that HIPAA is a program, not a checkbox.', 'msp-nexus'),
    'cta' => __('Book a HIPAA IT review', 'msp-nexus'),
    'cta_secondary' => __('How our HIPAA program works', 'msp-nexus'),
    'status_title' => __('Practice systems check', 'msp-nexus'),
    'status' => array(
        __('EHR & practice management', 'msp-nexus') => __('Monitored', 'msp-nexus'),
        __('Encrypted workstations', 'msp-nexus') => __('Enforced', 'msp-nexus'),
        __('Nightly backups', 'msp-nexus') => __('Verified', 'msp-nexus'),
        __('Staff security training', 'msp-nexus') => __('Scheduled', 'msp-nexus'),
        __('Business associate agreement', 'msp-nexus') => __('Signed', 'msp-nexus'),
    ),
    'types_title' => __('Built around how your practice works', 'msp-nexus'),
    'types' => array(
        __('Medical', 'msp-nexus') => __('EHR, e-prescribing, and exam-room devices', 'msp-nexus'),
        __('Dental', 'msp-nexus') => __('Imaging, sensors, and chairside workstations', 'msp-nexus'),
        __('Behavioral health', 'msp-nexus') => __('Telehealth and extra-sensitive records', 'msp-nexus'),
        __('Therapy & rehab', 'msp-nexus') => __('Scheduling, tablets, and multi-location care', 'msp-nexus'),
    ),
    'flow_title' => __('When the EHR is slow, the waiting room fills up.', 'msp-nexus'),
    'flow_body' => __('We prioritize anything that touches patient flow: front-desk check-in, charting, imaging, and e-prescribing get answered first, and we work directly with your EHR vendor so you do not have to.', 'msp-nexus'),
    'flow_checks' => array(__('Patient-flow issues jump the queue', 'msp-nexus'), __('We call your EHR vendor for you', 'msp-nexus'), __('After-hours updates, never during clinic', 'msp-nexus')),
    'image' => 'laptop-support',
    'image_alt' => __('Hands typing on a laptop', 'msp-nexus'),
    'program_title' => __('A HIPAA program you can show an auditor', 'msp-nexus'),
    'program' => array(
        __('Risk analysis', 'msp-nexus') => __('A documented security risk analysis of every system that touches patient data.', 'msp-nexus'),
        __('Safeguards', 'msp-nexus') => __('Encryption, MFA, access controls, and backups put in place and checked.', 'msp-nexus'),
        __('Training', 'msp-nexus') => __('Short yearly training and phishing tests for every staff member.', 'msp-nexus'),
        __('Evidence', 'msp-nexus') => __('Policies, logs, and reports kept in one place for audits and insurers.', 'msp-nexus'),
    ),
    'assurance_title' => __('We sign a business associate agreement.', 'msp-nexus'),
    'assurance_body' => __('Any provider with access to your systems should. We sign ours before we touch a single device.', 'msp-nexus'),
    'note' => __('Describe your real process here. This page is not legal advice; have counsel review HIPAA claims before launch.', 'msp-nexus'),
    'faq_title' => __('Common questions from practice managers', 'msp-nexus'),
    'close_title' => __('See where your practice stands in 30 minutes.', 'msp-nexus'),
    'close_body' => __('A free review of your backups, access controls, and HIPAA documentation.', 'msp-nexus'),
    'close_cta' => __('Book the review', 'msp-nexus'),
));
