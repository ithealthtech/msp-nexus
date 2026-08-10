<?php
/**
 * Conservative uninstall policy: site content and settings are preserved unless
 * the administrator explicitly opts in through wp-config.php.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (! defined('MSP_NEXUS_REMOVE_SETTINGS_ON_UNINSTALL') || true !== MSP_NEXUS_REMOVE_SETTINGS_ON_UNINSTALL) {
    return;
}

$options = array(
    'msp_nexus_settings',
    'msp_nexus_core_version',
    'msp_nexus_core_schema_version',
    'msp_nexus_license_state',
    'msp_nexus_import_job',
    'msp_nexus_import_ledger',
    'msp_nexus_update_history',
);

foreach ($options as $option) {
    delete_option($option);
    delete_site_option($option);
}
