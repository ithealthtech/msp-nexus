import { access } from 'node:fs/promises';

const required = [
  'packages/msp-nexus/style.css',
  'packages/msp-nexus/theme.json',
  'packages/msp-nexus/templates/index.html',
  'packages/msp-nexus/screenshot.png',
  'packages/msp-nexus/readme.txt',
  'packages/msp-nexus-core/msp-nexus-core.php',
  'packages/msp-nexus-core/readme.txt',
  'packages/msp-nexus-child/style.css',
  'services/licensing-service/package.json',
  'docs/ARCHITECTURE.md',
  'docs/REQUIREMENTS_TRACEABILITY.md',
  'docs/INSTALLATION_AND_CONFIGURATION.md',
  'docs/CONTENT_MODEL.md',
  'docs/DEVELOPER_AND_HOOKS.md',
  'docs/UPGRADE_AND_MIGRATION.md',
  'docs/ACCESSIBILITY_STATEMENT.md',
  'docs/PRIVACY_AND_DATA_FLOW.md',
  'docs/COMMERCIAL_ENTITLEMENT_TERMS_TEMPLATE.md'
];

let failed = false;
for (const file of required) {
  try {
    await access(file);
  } catch {
    failed = true;
    console.error(`Missing required file: ${file}`);
  }
}

if (failed) process.exitCode = 1;
else console.log(`Structure check passed (${required.length} required files).`);
