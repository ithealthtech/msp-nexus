# Upgrade and migration guide

Theme, plugin, service, schema, demo manifest, and release metadata use independent semantic versions. Back up files and the database before every production update.

MSP Nexus Core runs forward-only, idempotent option migrations during plugin boot. The current schema version is recorded in `msp_nexus_schema_version`; defaults are added without overwriting administrator values. Plugin deactivation preserves content and settings. Uninstall preserves data unless `MSP_NEXUS_REMOVE_SETTINGS_ON_UNINSTALL` is explicitly defined and true.

The update client verifies the signed canonical manifest, product identity, compatibility requirements, authorized package URL, and SHA-256 package digest. It records preflight, installation, failure, and rollback history. Automatic updates are off until an administrator opts in and selects a channel. If entitlement or the service is unavailable, the installed site continues to render.

For recovery, restore the prior signed package or hosting backup, verify the recorded migration version, clear relevant WordPress caches, and run MSP Nexus > Diagnostics. Never downgrade the database blindly.
