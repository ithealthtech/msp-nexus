<?php
/**
 * Composable starter-site catalog.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Starter;

final class Catalog
{
    /** @return array<string, string> */
    public static function verticals(): array
    {
        return array(
            'professional-services' => __('Professional Services', 'msp-nexus-core'),
            'healthcare' => __('Healthcare', 'msp-nexus-core'),
            'legal' => __('Legal', 'msp-nexus-core'),
            'financial-services' => __('Financial Services', 'msp-nexus-core'),
            'manufacturing' => __('Manufacturing', 'msp-nexus-core'),
            'construction' => __('Construction', 'msp-nexus-core'),
            'nonprofit' => __('Nonprofit', 'msp-nexus-core'),
            'education' => __('Education', 'msp-nexus-core'),
            'government' => __('Government', 'msp-nexus-core'),
            'retail' => __('Retail', 'msp-nexus-core'),
            'hospitality' => __('Hospitality', 'msp-nexus-core'),
            'real-estate' => __('Real Estate', 'msp-nexus-core'),
            'life-sciences' => __('Life Sciences', 'msp-nexus-core'),
            'logistics' => __('Logistics', 'msp-nexus-core'),
            'saas' => __('Software & SaaS', 'msp-nexus-core'),
            'insurance' => __('Insurance', 'msp-nexus-core'),
            'accounting' => __('Accounting', 'msp-nexus-core'),
            'automotive' => __('Automotive', 'msp-nexus-core'),
            'multi-location' => __('Multi-location Business', 'msp-nexus-core'),
            'general-msp' => __('Full-service MSP', 'msp-nexus-core'),
        );
    }

    /** @return array<string, array<string, string>> */
    public static function focuses(): array
    {
        return array(
            'managed-operations' => array('name' => __('Managed Operations', 'msp-nexus-core'), 'pattern' => 'page-home', 'summary' => __('Proactive support, infrastructure, security, lifecycle, and leadership.', 'msp-nexus-core')),
            'security' => array('name' => __('Cybersecurity', 'msp-nexus-core'), 'pattern' => 'page-home-security', 'summary' => __('Risk reduction, detection, response, recovery, and governance.', 'msp-nexus-core')),
            'co-managed' => array('name' => __('Co-managed IT', 'msp-nexus-core'), 'pattern' => 'page-home-co-managed', 'summary' => __('Capacity, coverage, tooling, and specialists for internal IT teams.', 'msp-nexus-core')),
            'cloud' => array('name' => __('Cloud Modernization', 'msp-nexus-core'), 'pattern' => 'page-home-cloud', 'summary' => __('Cloud platforms, identity, cost control, resilience, and adoption.', 'msp-nexus-core')),
            'compliance' => array('name' => __('Compliance Readiness', 'msp-nexus-core'), 'pattern' => 'page-home-security', 'summary' => __('Controls, evidence, ownership, and continuous audit readiness.', 'msp-nexus-core')),
            'continuity' => array('name' => __('Business Continuity', 'msp-nexus-core'), 'pattern' => 'page-home', 'summary' => __('Backup, recovery, communications, testing, and incident coordination.', 'msp-nexus-core')),
            'ai-readiness' => array('name' => __('AI Readiness', 'msp-nexus-core'), 'pattern' => 'page-home-cloud', 'summary' => __('Responsible AI adoption across identity, information, policy, and people.', 'msp-nexus-core')),
            'support-experience' => array('name' => __('Support Experience', 'msp-nexus-core'), 'pattern' => 'page-home-co-managed', 'summary' => __('Responsive help, clear ownership, employee experience, and reporting.', 'msp-nexus-core')),
            'technology-strategy' => array('name' => __('Technology Strategy', 'msp-nexus-core'), 'pattern' => 'page-home', 'summary' => __('Roadmaps, budgets, architecture, lifecycle, and executive decisions.', 'msp-nexus-core')),
            'digital-workplace' => array('name' => __('Digital Workplace', 'msp-nexus-core'), 'pattern' => 'page-home-cloud', 'summary' => __('Secure collaboration, communications, devices, adoption, and employee experience.', 'msp-nexus-core')),
        );
    }

    /** @return array<string, array<string, string>> */
    public static function styles(): array
    {
        return array(
            'enterprise-dark' => array('name' => __('Enterprise Dark', 'msp-nexus-core'), 'accent' => '#36e0ee'),
            'cybersecurity' => array('name' => __('Security Signal', 'msp-nexus-core'), 'accent' => '#20d5e8'),
            'clean-corporate-light' => array('name' => __('Corporate Light', 'msp-nexus-core'), 'accent' => '#075ea8'),
            'cloud-innovation' => array('name' => __('Cloud Innovation', 'msp-nexus-core'), 'accent' => '#7888ff'),
        );
    }

    /** @return array<int, array<string, string>> */
    public static function all(): array
    {
        $items = array();
        foreach (self::verticals() as $vertical_key => $vertical_name) {
            foreach (self::focuses() as $focus_key => $focus) {
                foreach (self::styles() as $style_key => $style) {
                    $id = $vertical_key . '--' . $focus_key . '--' . $style_key;
                    $items[] = array(
                        'id' => $id,
                        'name' => sprintf(__('%1$s: %2$s', 'msp-nexus-core'), $vertical_name, $focus['name']),
                        'description' => sprintf(__('%1$s positioning for %2$s organizations.', 'msp-nexus-core'), $focus['summary'], $vertical_name),
                        'vertical' => $vertical_key,
                        'vertical_name' => $vertical_name,
                        'focus' => $focus_key,
                        'focus_name' => $focus['name'],
                        'style' => $style_key,
                        'style_name' => $style['name'],
                        'accent' => $style['accent'],
                        'pattern' => $focus['pattern'],
                    );
                }
            }
        }
        return $items;
    }

    /** @return array<string, string>|null */
    public static function get(string $id): ?array
    {
        foreach (self::all() as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }
}
