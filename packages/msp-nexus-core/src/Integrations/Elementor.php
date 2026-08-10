<?php
/**
 * Optional Elementor bridge for site-owned Nexus content.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Integrations;

final class Elementor
{
    public function register_hooks(): void
    {
        add_action('elementor/elements/categories_registered', array($this, 'category'));
        add_action('elementor/widgets/register', array($this, 'widgets'));
    }

    /** @param object $manager */
    public function category($manager): void
    {
        if (method_exists($manager, 'add_category')) {
            $manager->add_category('msp-nexus', array('title' => __('MSP Nexus', 'msp-nexus-core'), 'icon' => 'fa fa-shield-alt'));
        }
    }

    /** @param object $manager */
    public function widgets($manager): void
    {
        if (! class_exists('Elementor\Widget_Base') || ! method_exists($manager, 'register')) {
            return;
        }
        require_once MSP_NEXUS_CORE_PATH . 'src/Integrations/Elementor/Widgets.php';
        foreach (array(
            'MspNexusCore\Integrations\Elementor\ServiceGridWidget',
            'MspNexusCore\Integrations\Elementor\FaqWidget',
            'MspNexusCore\Integrations\Elementor\ConsultationWidget',
            'MspNexusCore\Integrations\Elementor\DynamicValueWidget',
            'MspNexusCore\Integrations\Elementor\ContentLoopWidget',
            'MspNexusCore\Integrations\Elementor\WooTemplateWidget',
        ) as $class) {
            if (class_exists($class)) {
                $manager->register(new $class());
            }
        }
    }
}
