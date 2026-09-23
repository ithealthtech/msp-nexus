import { readFile, readdir } from 'node:fs/promises';
import path from 'node:path';

// Contrast gate derived from the shipped theme.json and every style variation, so a
// palette edit cannot silently break legibility in one variation.
const themeDir = 'packages/msp-nexus';

function rgb(hex) {
  const value = hex.replace('#', '');
  return [0, 2, 4].map((offset) => Number.parseInt(value.slice(offset, offset + 2), 16) / 255);
}
function luminance(hex) {
  const values = rgb(hex).map((value) => value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4);
  return values[0] * 0.2126 + values[1] * 0.7152 + values[2] * 0.0722;
}
function contrast(a, b) {
  const [light, dark] = [luminance(a), luminance(b)].sort((x, y) => y - x);
  return (light + 0.05) / (dark + 0.05);
}

function palette(json) {
  return Object.fromEntries((json?.settings?.color?.palette ?? []).map((entry) => [entry.slug, entry.color]));
}
function pick(json) {
  const styles = json?.styles ?? {};
  return {
    background: styles.color?.background,
    text: styles.color?.text,
    link: styles.elements?.link?.color?.text,
    buttonBackground: styles.elements?.button?.color?.background,
    buttonText: styles.elements?.button?.color?.text
  };
}
function resolve(value, colors) {
  if (typeof value !== 'string') return undefined;
  const preset = value.match(/^var:preset\|color\|([a-z0-9-]+)$/);
  if (preset) return colors[preset[1]];
  return /^#[0-9a-f]{6}$/i.test(value) ? value : undefined;
}

const base = JSON.parse(await readFile(path.join(themeDir, 'theme.json'), 'utf8'));
const variations = [['Default', base]];
for (const file of (await readdir(path.join(themeDir, 'styles'))).filter((name) => name.endsWith('.json')).sort()) {
  const json = JSON.parse(await readFile(path.join(themeDir, 'styles', file), 'utf8'));
  variations.push([json.title ?? file, json]);
}

let failed = false;
for (const [title, json] of variations) {
  const colors = { ...palette(base), ...palette(json) };
  const merged = { ...pick(base) };
  for (const [key, value] of Object.entries(pick(json))) {
    if (value !== undefined) merged[key] = value;
  }
  const bg = resolve(merged.background, colors);
  const pairs = [
    ['body text', resolve(merged.text, colors), bg],
    ['links', resolve(merged.link, colors), bg],
    ['secondary text (slate)', colors.slate, bg],
    ['button label', resolve(merged.buttonText, colors), resolve(merged.buttonBackground, colors)],
    ['accent labels (cyan)', colors.cyan, bg]
  ];
  console.log(`${title}:`);
  for (const [label, foreground, background] of pairs) {
    if (!foreground || !background) {
      console.log(`  ${label}: unresolved (${foreground ?? '?'} on ${background ?? '?'})`);
      failed = true;
      continue;
    }
    const ratio = contrast(foreground, background);
    const ok = ratio >= 4.5;
    if (!ok) failed = true;
    console.log(`  ${ok ? 'ok  ' : 'FAIL'} ${label}: ${ratio.toFixed(2)}:1 (${foreground} on ${background})`);
  }
}
if (failed) {
  console.error('Design contrast check failed: every pair must reach WCAG AA 4.5:1.');
  process.exitCode = 1;
}
