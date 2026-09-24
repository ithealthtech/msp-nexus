<?php
/**
 * Plugin Name: MSP Nexus Core
 * Plugin URI: https://itdonerightnc.com/
 * Description: Site-owned MSP content, dynamic blocks, onboarding, diagnostics, and commercial entitlement services for MSP Nexus.
 * Version: 0.5.1
 * Requires at least: 6.7
 * Requires PHP: 7.4.33
 * Author: IT Done Right LLC
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: msp-nexus-core
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('MSP_NEXUS_CORE_VERSION', '0.5.1');
define('MSP_NEXUS_CORE_FILE', __FILE__);
define('MSP_NEXUS_CORE_PATH', plugin_dir_path(__FILE__));

spl_autoload_register(
    static function (string $class): void {
        $prefix = 'MspNexusCore\\';
        if (0 !== strpos($class, $prefix)) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $file     = MSP_NEXUS_CORE_PATH . 'src/' . str_replace('\\', '/', $relative) . '.php';
        if (is_readable($file)) {
            require_once $file;
        }
    }
);

register_activation_hook(
    __FILE__,
    static function (): void {
        $plugin = new MspNexusCore\Plugin();
        $plugin->register_content();
        (new MspNexusCore\Database\MigrationRunner())->run();
        flush_rewrite_rules();
        update_option('msp_nexus_core_version', MSP_NEXUS_CORE_VERSION, false);
    }
);

register_deactivation_hook(
    __FILE__,
    static function (): void {
        wp_clear_scheduled_hook('msp_nexus_validate_license');
        flush_rewrite_rules();
    }
);

add_action(
    'plugins_loaded',
    static function (): void {
        (new MspNexusCore\Plugin())->boot();
    }
);
