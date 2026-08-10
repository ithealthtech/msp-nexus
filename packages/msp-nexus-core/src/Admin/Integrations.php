<?php
/**
 * Purpose-built integration status and configuration guidance.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

final class Integrations
{
    public function register_hooks(): void
    {
        add_action('admin_menu', array($this, 'menu'));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('Nexus integrations', 'msp-nexus-core'), __('Integrations', 'msp-nexus-core'), 'manage_options', 'msp-nexus-integrations', array($this, 'render'));
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        $integrations = array(
            array(__('Commerce', 'msp-nexus-core'), class_exists('WooCommerce'), __('WooCommerce', 'msp-nexus-core'), __('More than 30 Studio template elements, product dynamic values, conditional layouts, wishlist, quick view, side cart, catalog mode, galleries, and checkout presentation become available.', 'msp-nexus-core')),
            array(__('Alternate editor', 'msp-nexus-core'), did_action('elementor/loaded') || defined('ELEMENTOR_VERSION'), __('Elementor', 'msp-nexus-core'), __('Six Nexus widgets bridge services, FAQs, forms, dynamic values, loops, and WooCommerce templates into Elementor while retaining site-owned content.', 'msp-nexus-core')),
            array(__('Custom fields', 'msp-nexus-core'), function_exists('acf_get_field_groups'), __('Advanced Custom Fields', 'msp-nexus-core'), __('Registered ACF fields appear in the visual Dynamic Value picker and loop-query controls.', 'msp-nexus-core')),
            array(__('Forms', 'msp-nexus-core'), defined('WPCF7_VERSION') || class_exists('WPForms') || class_exists('GFForms'), __('Contact Form 7, WPForms, or Gravity Forms', 'msp-nexus-core'), __('Use your selected form plugin or the built-in consultation form. Avoid running duplicate public forms.', 'msp-nexus-core')),
            array(__('SEO', 'msp-nexus-core'), defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('SEOPRESS_VERSION'), __('Yoast SEO, Rank Math, or SEOPress', 'msp-nexus-core'), __('Nexus yields schema ownership to supported SEO plugins to reduce duplicate structured data.', 'msp-nexus-core')),
            array(__('Multilingual', 'msp-nexus-core'), defined('ICL_SITEPRESS_VERSION') || function_exists('pll_languages_list'), __('WPML or Polylang', 'msp-nexus-core'), __('Translate Nexus content, templates, strings, and navigation with the plugin that owns language routing.', 'msp-nexus-core')),
            array(__('Caching', 'msp-nexus-core'), defined('WP_CACHE') && WP_CACHE, __('Host cache or performance plugin', 'msp-nexus-core'), __('Purge caches after applying starter profiles, changing templates, or importing a bundle.', 'msp-nexus-core')),
            array(__('Email delivery', 'msp-nexus-core'), defined('WP_MAIL_SMTP_VERSION') || defined('POST_SMTP_VER') || class_exists('FluentMail\App\App'), __('Reviewed SMTP provider', 'msp-nexus-core'), __('Use authenticated transactional email and complete DNS authentication; Nexus does not store SMTP secrets.', 'msp-nexus-core')),
            array(__('Analytics and tags', 'msp-nexus-core'), defined('GOOGLESITEKIT_VERSION') || defined('GTM4WP_VERSION') || class_exists('MonsterInsights_Lite'), __('Site Kit, GTM4WP, or MonsterInsights', 'msp-nexus-core'), __('Connect optional scripts to the Nexus consent categories and verify regional requirements before launch.', 'msp-nexus-core')),
            array(__('Security', 'msp-nexus-core'), defined('WORDFENCE_VERSION') || defined('ITSEC_VERSION') || class_exists('SG_Security'), __('Wordfence, Solid Security, or host security', 'msp-nexus-core'), __('Nexus keeps security notices visible and avoids replacing a dedicated firewall, malware scanner, or login-protection service.', 'msp-nexus-core')),
            array(__('Optimization', 'msp-nexus-core'), defined('WP_ROCKET_VERSION') || defined('LSCWP_V') || defined('AUTOPTIMIZE_PLUGIN_VERSION'), __('WP Rocket, LiteSpeed Cache, or Autoptimize', 'msp-nexus-core'), __('Use one page-cache owner and test conditional layouts, carts, consent choices, and authenticated routes before enabling optimization.', 'msp-nexus-core')),
            array(__('Marketing and CRM', 'msp-nexus-core'), defined('LEADIN_PLUGIN_VERSION') || class_exists('MC4WP_Plugin') || defined('FLUENTCRM'), __('HubSpot, Mailchimp, or FluentCRM', 'msp-nexus-core'), __('Connect forms through reviewed hooks, document data flows, and obtain the required consent before optional tracking.', 'msp-nexus-core')),
            array(__('CRM and automation', 'msp-nexus-core'), has_action('msp_nexus_consultation_submitted'), __('Hook-based connector', 'msp-nexus-core'), __('Developers can attach a reviewed CRM connector to msp_nexus_consultation_submitted without changing form markup.', 'msp-nexus-core')),
        );
        ?>
        <div class="wrap"><h1><?php esc_html_e('Nexus integrations', 'msp-nexus-core'); ?></h1><p><?php esc_html_e('Nexus does not bundle paid plugins or silently transmit visitor data. Select only the tools your operating and privacy model requires.', 'msp-nexus-core'); ?></p><div class="msp-nexus-integration-grid">
        <?php foreach ($integrations as $item) : ?><section class="card"><p><span class="msp-nexus-integration-status <?php echo $item[1] ? 'is-active' : ''; ?>"><?php echo $item[1] ? esc_html__('Detected', 'msp-nexus-core') : esc_html__('Available', 'msp-nexus-core'); ?></span></p><h2><?php echo esc_html($item[0]); ?></h2><p><strong><?php echo esc_html($item[2]); ?></strong></p><p><?php echo esc_html($item[3]); ?></p></section><?php endforeach; ?>
        </div><p><a class="button" href="<?php echo esc_url(admin_url('plugins.php')); ?>"><?php esc_html_e('Manage installed plugins', 'msp-nexus-core'); ?></a> <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=msp-nexus-diagnostics')); ?>"><?php esc_html_e('Open diagnostics', 'msp-nexus-core'); ?></a></p></div><style>.msp-nexus-integration-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;max-width:1100px}.msp-nexus-integration-grid .card{margin:0;max-width:none;padding:18px}.msp-nexus-integration-status{background:#f0f0f1;border-radius:999px;display:inline-block;font-size:12px;padding:3px 9px}.msp-nexus-integration-status.is-active{background:#d7f2df;color:#0a5b25}</style>
        <?php
    }
}
