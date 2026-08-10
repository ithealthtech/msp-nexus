<?php
/**
 * Idempotent option-schema migrations.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Database;

final class MigrationRunner
{
    private const VERSION_OPTION = 'msp_nexus_core_schema_version';
    private const CURRENT_VERSION = 2;

    public function run(): void
    {
        $installed = (int) get_option(self::VERSION_OPTION, 0);
        if ($installed < 1) {
            $settings = get_option('msp_nexus_settings', array());
            if (! is_array($settings)) {
                $settings = array();
            }
            $settings += array('update_channel' => 'stable', 'automatic_updates' => false);
            update_option('msp_nexus_settings', $settings, false);
            update_option(self::VERSION_OPTION, 1, false);
        }
        if ($installed < 2) {
            $settings = get_option('msp_nexus_settings', array());
            if (! is_array($settings)) {
                $settings = array();
            }
            $settings += array('enable_schema' => true, 'enable_native_form' => true);
            update_option('msp_nexus_settings', $settings, false);
            update_option(self::VERSION_OPTION, self::CURRENT_VERSION, false);
        }
    }
}
