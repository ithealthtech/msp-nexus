<?php
/**
 * Title: Industry: Manufacturing
 * Slug: msp-nexus/industry-manufacturing
 * Categories: msp-nexus-pages
 * Block Types: core/post-content
 * Description: Industry page for manufacturers: uptime-first hero, operation types, ERP and shop-floor support, a CMMC and NIST readiness program, OT network separation, FAQs, and a plant review offer.
 *
 * @package MspNexus
 */

echo msp_nexus_industry_page(array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the template.
    'modifier' => 'manufacturing',
    'pill' => __('IT for manufacturers and distributors', 'msp-nexus'),
    'title' => __('Keep the line running.', 'msp-nexus'),
    'title_accent' => __('Keep attackers off the floor.', 'msp-nexus'),
    'lede' => __('IT and security support that knows the difference between the office network and the plant floor, and treats downtime like the lost revenue it is.', 'msp-nexus'),
    'cta' => __('Book a plant IT review', 'msp-nexus'),
    'cta_secondary' => __('Our CMMC readiness program', 'msp-nexus'),
    'status_title' => __('Plant systems check', 'msp-nexus'),
    'status' => array(
        __('ERP & MES servers', 'msp-nexus') => __('Monitored', 'msp-nexus'),
        __('Office / OT network split', 'msp-nexus') => __('Segmented', 'msp-nexus'),
        __('Production data backups', 'msp-nexus') => __('Verified', 'msp-nexus'),
        __('Remote vendor access', 'msp-nexus') => __('Controlled', 'msp-nexus'),
        __('CUI handling policy', 'msp-nexus') => __('Documented', 'msp-nexus'),
    ),
    'types_title' => __('From the front office to the shop floor', 'msp-nexus'),
    'types' => array(
        __('Discrete', 'msp-nexus') => __('CNC, CAD/CAM, and machine-connected PCs', 'msp-nexus'),
        __('Process', 'msp-nexus') => __('Batch records, historians, and control rooms', 'msp-nexus'),
        __('Job shops', 'msp-nexus') => __('Quoting, scheduling, and rugged devices', 'msp-nexus'),
        __('Distribution', 'msp-nexus') => __('Warehouse Wi-Fi, scanners, and shipping', 'msp-nexus'),
    ),
    'flow_title' => __('When the ERP stops, so does shipping.', 'msp-nexus'),
    'flow_body' => __('Production-impacting issues get answered first, around your shifts. We coordinate with your ERP and equipment vendors, and schedule changes for planned downtime.', 'msp-nexus'),
    'flow_checks' => array(__('Production-stopping issues jump the queue', 'msp-nexus'), __('Support that follows your shift schedule', 'msp-nexus'), __('Changes timed for planned downtime', 'msp-nexus')),
    'image' => 'server-racks',
    'image_alt' => __('Network racks with patch cabling', 'msp-nexus'),
    'program_title' => __('A CMMC and NIST 800-171 readiness program', 'msp-nexus'),
    'program' => array(
        __('Gap assessment', 'msp-nexus') => __('Where controlled information lives and how today’s controls compare to the requirements.', 'msp-nexus'),
        __('Remediation', 'msp-nexus') => __('MFA, logging, encryption, and access controls put in place in priority order.', 'msp-nexus'),
        __('Documentation', 'msp-nexus') => __('A system security plan and plan of action that reflect how you actually work.', 'msp-nexus'),
        __('Ongoing evidence', 'msp-nexus') => __('Monitoring and reports that keep you ready between assessments.', 'msp-nexus'),
    ),
    'assurance_title' => __('We keep the plant floor separate.', 'msp-nexus'),
    'assurance_body' => __('Office and machine networks are segmented, and vendor remote access is logged and time-limited.', 'msp-nexus'),
    'note' => __('Describe your real process here. Confirm CMMC and NIST statements against current requirements before launch.', 'msp-nexus'),
    'faq_title' => __('Common questions from plant and operations managers', 'msp-nexus'),
    'close_title' => __('Find the single points of failure on your floor.', 'msp-nexus'),
    'close_body' => __('A free review of backups, network segmentation, and vendor access.', 'msp-nexus'),
    'close_cta' => __('Book the review', 'msp-nexus'),
));
