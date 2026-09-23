<?php
/**
 * Main plugin coordinator.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore;

use MspNexusCore\Admin\Settings;
use MspNexusCore\Admin\Diagnostics;
use MspNexusCore\Admin\Onboarding;
use MspNexusCore\Admin\LicenseScreen;
use MspNexusCore\Admin\Reset;
use MspNexusCore\Blocks\Registry as BlockRegistry;
use MspNexusCore\Content\Registrar as ContentRegistrar;
use MspNexusCore\Content\Bindings;
use MspNexusCore\Content\DynamicData;
use MspNexusCore\Licensing\Client as LicenseClient;
use MspNexusCore\Licensing\UpdateClient;
use MspNexusCore\Forms\ConsultationHandler;
use MspNexusCore\Seo\Schema;
use MspNexusCore\Cli\Commands;
use MspNexusCore\Database\MigrationRunner;
use MspNexusCore\Admin\Branding;
use MspNexusCore\Admin\ContentHub;
use MspNexusCore\Admin\Performance;
use MspNexusCore\Admin\Privacy;
use MspNexusCore\Admin\Portability;
use MspNexusCore\Admin\StarterSites;
use MspNexusCore\Admin\Studio;
use MspNexusCore\Admin\Integrations;
use MspNexusCore\Integrations\WooCommerce;
use MspNexusCore\Integrations\Elementor;
use MspNexusCore\Studio\DesignTokens;
use MspNexusCore\Studio\Layouts;
use MspNexusCore\Studio\ResponsiveControls;
use MspNexusCore\Studio\TemplateConditions;

final class Plugin
{
    private ContentRegistrar $content;

    public function __construct()
    {
        $this->content = new ContentRegistrar();
    }

    public function boot(): void
    {
        load_plugin_textdomain('msp-nexus-core', false, dirname(plugin_basename(MSP_NEXUS_CORE_FILE)) . '/languages');
        (new MigrationRunner())->run();
        add_action('init', array($this, 'register_content'));
        add_action('init', array(new Bindings(), 'register'));
        (new DynamicData())->register_hooks();
        $layouts = new Layouts();
        add_action('init', array($layouts, 'register'));
        add_action('init', array(new BlockRegistry(), 'register'));
        add_action('admin_init', array(new Settings(), 'register'));
        add_action('admin_menu', array(new Settings(), 'menu'));
        (new ContentHub())->register_hooks();
        (new Onboarding())->register_hooks();
        (new Diagnostics())->register_hooks();
        (new LicenseScreen())->register_hooks();
        (new Reset())->register_hooks();
        (new Studio())->register_hooks();
        (new StarterSites())->register_hooks();
        (new Portability())->register_hooks();
        (new Branding())->register_hooks();
        (new Performance())->register_hooks();
        (new Privacy())->register_hooks();
        (new Integrations())->register_hooks();
        (new TemplateConditions($layouts))->register_hooks();
        (new ResponsiveControls())->register_hooks();
        (new DesignTokens())->register_hooks();
        (new WooCommerce())->register_hooks();
        (new Elementor())->register_hooks();
        (new LicenseClient())->register_hooks();
        (new UpdateClient())->register_hooks();
        (new ConsultationHandler())->register_hooks();
        (new Schema())->register_hooks();
        if (defined('WP_CLI') && WP_CLI && class_exists('WP_CLI')) {
            \WP_CLI::add_command('msp-nexus', new Commands());
        }
    }

    public function register_content(): void
    {
        $this->content->register();
    }
}
