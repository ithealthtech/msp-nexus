import { createHash } from 'node:crypto';
import { createReadStream, createWriteStream } from 'node:fs';
import { copyFile, mkdir, readdir, readFile, stat, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { TarArchive, ZipArchive } from 'archiver';

const epoch = new Date('2020-01-01T00:00:00.000Z');
const artifacts = path.resolve('artifacts');
const releaseVersion = JSON.parse(await readFile('package.json', 'utf8')).version;
await mkdir(artifacts, { recursive: true });

async function filesUnder(root, relative = '') {
  const directory = path.join(root, relative);
  const entries = await readdir(directory, { withFileTypes: true });
  const result = [];
  for (const entry of entries.sort((a, b) => a.name.localeCompare(b.name))) {
    if (['node_modules', 'vendor', 'test', 'tests', 'data', 'coverage', 'dist', 'build'].includes(entry.name)) continue;
    const child = path.join(relative, entry.name);
    if (entry.isDirectory()) result.push(...await filesUnder(root, child));
    else result.push(child);
  }
  return result;
}

async function archivePackage({ source, output, rootName, format = 'zip', extras = [] }) {
  const outputPath = path.join(artifacts, output);
  const sink = createWriteStream(outputPath);
  const archive = format === 'zip'
    ? new ZipArchive({ zlib: { level: 9 } })
    : new TarArchive({ gzip: true, gzipOptions: { level: 9 } });
  const done = new Promise((resolve, reject) => {
    sink.on('close', resolve);
    sink.on('error', reject);
    archive.on('error', reject);
  });
  archive.pipe(sink);
  for (const relative of await filesUnder(source)) {
    archive.append(createReadStream(path.join(source, relative)), {
      name: path.posix.join(rootName, relative.split(path.sep).join('/')),
      date: epoch,
      mode: 0o644
    });
  }
  for (const extra of extras) {
    archive.append(createReadStream(extra.source), { name: path.posix.join(rootName, extra.name), date: epoch, mode: 0o644 });
  }
  await archive.finalize();
  await done;
  const bytes = await readFile(outputPath);
  return { file: output, bytes: (await stat(outputPath)).size, sha256: createHash('sha256').update(bytes).digest('hex') };
}

const packages = [];
packages.push(await archivePackage({ source: 'packages/msp-nexus', output: `msp-nexus-${releaseVersion}.zip`, rootName: 'msp-nexus' }));
packages.push(await archivePackage({ source: 'packages/msp-nexus-core', output: `msp-nexus-core-${releaseVersion}.zip`, rootName: 'msp-nexus-core', extras: [
  { source: 'node_modules/lottie-web/build/player/lottie_light.min.js', name: 'assets/vendor/lottie-light.min.js' },
  { source: 'node_modules/lottie-web/LICENSE.md', name: 'licenses/lottie-web-MIT.txt' }
] }));
packages.push(await archivePackage({ source: 'packages/msp-nexus-child', output: `msp-nexus-child-${releaseVersion}.zip`, rootName: 'msp-nexus-child' }));
packages.push(await archivePackage({ source: 'services/licensing-service', output: `msp-nexus-licensing-service-${releaseVersion}.tar.gz`, rootName: 'msp-nexus-licensing-service', format: 'tar' }));
packages.push(await archivePackage({ source: 'demo', output: `msp-nexus-demo-${releaseVersion}.zip`, rootName: 'msp-nexus-demo' }));

await copyFile(path.join(artifacts, `msp-nexus-${releaseVersion}.zip`), path.join(artifacts, 'msp-nexus.zip'));
await copyFile(path.join(artifacts, `msp-nexus-core-${releaseVersion}.zip`), path.join(artifacts, 'msp-nexus-core.zip'));
await copyFile(path.join(artifacts, `msp-nexus-child-${releaseVersion}.zip`), path.join(artifacts, 'msp-nexus-child.zip'));

const manifest = { schema: 1, version: releaseVersion, source_date_epoch: epoch.toISOString(), packages };
await writeFile(path.join(artifacts, 'release-manifest.json'), `${JSON.stringify(manifest, null, 2)}\n`);

const assertions = `<?php
require '/wordpress/wp-load.php';
$failures = array();
if ('msp-nexus' !== get_template()) $failures[] = 'theme_not_active';
if (!is_plugin_active('msp-nexus-core/msp-nexus-core.php')) $failures[] = 'plugin_not_active';
if (!wp_is_block_theme()) $failures[] = 'not_a_block_theme';
if (version_compare(PHP_VERSION, '7.4.33', '<')) $failures[] = 'php_below_7.4.33:' . PHP_VERSION;
foreach (array('msp_service','msp_industry','msp_case_study','msp_location','msp_testimonial','msp_team','msp_resource','msp_faq','msp_outcome','msp_partner','msp_certification','msp_pricing_plan','msp_event','msp_nexus_layout') as $type) if (!post_type_exists($type)) $failures[] = 'missing_post_type:' . $type;
foreach (array('msp-nexus/service-grid','msp-nexus/faq-list','msp-nexus/consultation-form','msp-nexus/breadcrumbs','msp-nexus/content-directory','msp-nexus/announcement-panel','msp-nexus/dynamic-value','msp-nexus/content-loop','msp-nexus/layout-region','msp-nexus/menu-panel','msp-nexus/offcanvas-menu','msp-nexus/metric-counter','msp-nexus/tabs','msp-nexus/comparison-table','msp-nexus/progress-meter','msp-nexus/icon-card','msp-nexus/video-dialog','msp-nexus/content-carousel','msp-nexus/lottie-animation','msp-nexus/product-showcase','msp-nexus/woocommerce-element','msp-nexus/purchase-gate') as $block) if (!WP_Block_Type_Registry::get_instance()->is_registered($block)) $failures[] = 'missing_block:' . $block;
if (800 !== count(MspNexusCore\\Admin\\StarterSites::definitions())) $failures[] = 'starter_site_count';
if (2 !== (int) get_option('msp_nexus_core_schema_version')) $failures[] = 'migration_not_applied';
$patterns = array_filter(WP_Block_Patterns_Registry::get_instance()->get_all_registered(), static fn($pattern) => 0 === strpos((string)($pattern['name'] ?? ''), 'msp-nexus/'));
if (count($patterns) < 60) $failures[] = 'pattern_count:' . count($patterns);
update_option('blogname', 'IT Done Right');
update_option('blogdescription', 'Managed technology operations for resilient, secure, and productive organizations.');
$onboarding = new MspNexusCore\\Admin\\Onboarding();
$manifest_method = new ReflectionMethod($onboarding, 'manifest');
$manifest_method->setAccessible(true);
$item_method = new ReflectionMethod($onboarding, 'import_item');
$item_method->setAccessible(true);
$manifest = $manifest_method->invoke($onboarding);
if (!is_array($manifest) || 81 !== count($manifest['content'] ?? array())) $failures[] = 'demo_manifest_invalid';
if (is_array($manifest)) {
  foreach ($manifest['content'] as $item) { $imported = $item_method->invoke($onboarding, $item); if ('failed' === ($imported['result'] ?? 'failed')) $failures[] = 'demo_import:' . ($item['key'] ?? 'unknown'); }
  foreach ($manifest['content'] as $item) { $imported = $item_method->invoke($onboarding, $item); if ('updated' !== ($imported['result'] ?? '')) $failures[] = 'demo_idempotency:' . ($item['key'] ?? 'unknown'); }
}
$original_settings = get_option('msp_nexus_settings', array());
update_option('msp_nexus_settings', array_merge(is_array($original_settings) ? $original_settings : array(), array('organization_name' => 'IT Done Right')));
$dynamic_html = do_blocks('<!-- wp:msp-nexus/dynamic-value {"source":"site","field":"organization_name"} /-->');
if (false === strpos($dynamic_html, 'IT Done Right')) $failures[] = 'dynamic_value_render';
$loop_html = do_blocks('<!-- wp:msp-nexus/content-loop {"postType":"msp_service","count":2,"columns":2} /-->');
if (false === strpos($loop_html, 'msp-nexus-content-loop__card')) $failures[] = 'content_loop_render';
$layout_id = wp_insert_post(array('post_type' => 'msp_nexus_layout', 'post_status' => 'publish', 'post_name' => 'qa-layout-region', 'post_title' => 'QA layout region', 'post_content' => '<!-- wp:paragraph --><p>Reusable region proof</p><!-- /wp:paragraph -->'));
update_post_meta($layout_id, '_msp_nexus_layout_area', 'loop');
update_post_meta($layout_id, '_msp_nexus_condition_context', 'all');
$region_html = do_blocks('<!-- wp:msp-nexus/layout-region {"layoutSlug":"qa-layout-region","area":"loop"} /-->');
if (false === strpos($region_html, 'Reusable region proof')) $failures[] = 'layout_region_render';
$responsive_html = (new MspNexusCore\\Studio\\ResponsiveControls())->render('<div>Responsive</div>', array('attrs' => array('mspHideMobile' => true, 'mspPaddingDesktop' => '2rem')));
if (false === strpos($responsive_html, 'msp-hide-mobile') || false === strpos($responsive_html, '--msp-padding-desktop:2rem')) $failures[] = 'responsive_controls_render';
$tabs_html = do_blocks('<!-- wp:msp-nexus/tabs {"items":"Operations|Visible ownership\\nSecurity|Layered safeguards"} /-->');
if (false === strpos($tabs_html, 'role="tablist"') || false === strpos($tabs_html, 'Operations')) $failures[] = 'tabs_render';
$icon_html = do_blocks('<!-- wp:msp-nexus/icon-card {"icon":"shield","heading":"Secure operations"} /-->');
if (false === strpos($icon_html, '<svg') || false === strpos($icon_html, 'Secure operations')) $failures[] = 'icon_card_render';
$privacy_button = do_shortcode('[msp_nexus_consent_preferences]');
if (false === strpos($privacy_button, 'data-consent-manage')) $failures[] = 'privacy_preferences_shortcode';
$purchase_gate = do_blocks('<!-- wp:msp-nexus/purchase-gate {"productIds":"1"} --><!-- wp:paragraph --><p>Protected proof</p><!-- /wp:paragraph --><!-- /wp:msp-nexus/purchase-gate -->');
if (false === strpos($purchase_gate, 'is-locked') || false !== strpos($purchase_gate, 'Protected proof')) $failures[] = 'purchase_gate_locked';
$template_id = wp_insert_post(array('post_type' => 'msp_nexus_layout', 'post_status' => 'publish', 'post_name' => 'qa-template', 'post_title' => 'QA template', 'post_content' => '<!-- wp:post-content /-->'));
update_post_meta($template_id, '_msp_nexus_layout_area', 'template');
update_post_meta($template_id, '_msp_nexus_condition_context', 'all');
$conditions = new MspNexusCore\\Studio\\TemplateConditions(new MspNexusCore\\Studio\\Layouts());
$conditional_template = $conditions->template(null, 'msp-nexus//index', 'wp_template');
if (!($conditional_template instanceof WP_Block_Template) || false === strpos($conditional_template->content, 'wp:post-content')) $failures[] = 'conditional_template_resolution';
$demo_count = count(get_posts(array('post_type' => 'any', 'post_status' => 'any', 'meta_key' => '_msp_nexus_demo_key', 'fields' => 'ids', 'posts_per_page' => 100)));
if (81 !== $demo_count) $failures[] = 'demo_count:' . $demo_count;
$contact = get_page_by_path('contact');
$about = get_page_by_path('about-us');
if (!($contact instanceof WP_Post) || !has_block('msp-nexus/consultation-form', $contact->post_content)) $failures[] = 'contact_form_missing';
if (!($about instanceof WP_Post) || !has_block('core/pattern', $about->post_content) || 'page-no-title' !== get_page_template_slug($about->ID)) $failures[] = 'about_page_missing';
$home = get_page_by_path('home');
if (!($home instanceof WP_Post) || 'IT Done Right' !== $home->post_title) $failures[] = 'homepage_title_not_site_name';
if ($home instanceof WP_Post) {
  update_option('show_on_front', 'page');
  update_option('page_on_front', $home->ID);
}
$diagnostics = new MspNexusCore\\Admin\\Diagnostics();
$launch_check = array_values(array_filter($diagnostics->checks(), static fn($check) => 'Launch safety' === ($check['label'] ?? '')));
if (empty($launch_check) || 'pass' !== ($launch_check[0]['status'] ?? '')) $failures[] = 'unverified_starter_content_published';
$result = array('wordpress' => get_bloginfo('version'), 'php' => PHP_VERSION, 'multisite' => is_multisite(), 'theme' => get_template(), 'patterns' => count($patterns), 'demo_records' => $demo_count, 'failures' => $failures);
echo wp_json_encode($result);
if ($failures) throw new Exception('qa_assertions_failed:' . implode(',', $failures));`;

function qaBlueprint(multisite) {
  const steps = [
    { step: 'defineWpConfigConsts', consts: { WP_DEBUG: true, WP_DEBUG_LOG: true, WP_DEBUG_DISPLAY: true, WP_DISABLE_FATAL_ERROR_HANDLER: true } }
  ];
  if (multisite) steps.push({ step: 'enableMultisite' });
  steps.push(
    { step: 'installPlugin', pluginData: { resource: 'bundled', path: `/msp-nexus-core-${releaseVersion}.zip` }, options: { activate: true } },
    { step: 'installTheme', themeData: { resource: 'bundled', path: `/msp-nexus-${releaseVersion}.zip` }, options: { activate: true } },
    { step: 'runPHP', code: assertions }
  );
  return { $schema: 'https://playground.wordpress.net/blueprint-schema.json', preferredVersions: { php: '7.4', wp: 'latest' }, steps };
}

await writeFile(path.join(artifacts, 'qa-single-site.blueprint.json'), `${JSON.stringify(qaBlueprint(false), null, 2)}\n`);
await writeFile(path.join(artifacts, 'qa-multisite.blueprint.json'), `${JSON.stringify(qaBlueprint(true), null, 2)}\n`);
console.log(JSON.stringify(manifest, null, 2));
