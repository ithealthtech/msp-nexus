import { readFileSync } from 'node:fs';

const escapeHtml = (value) => String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');

const ASSET_FILES = {
  'console.css': 'text/css; charset=utf-8',
  'operator.js': 'text/javascript; charset=utf-8',
  'portal.js': 'text/javascript; charset=utf-8',
  'shared.js': 'text/javascript; charset=utf-8'
};

/** Console assets are read once at startup from an allowlist; nothing else under /console/assets is served. */
export function loadAssets() {
  const assets = new Map();
  for (const [name, type] of Object.entries(ASSET_FILES)) {
    assets.set(name, { body: readFileSync(new URL(`./ui/${name}`, import.meta.url)), type });
  }
  return assets;
}

const APPS = {
  operator: {
    title: 'MSP Nexus Licensing',
    base: '/operator',
    script: 'operator.js',
    nav: [['#/', 'Dashboard'], ['#/licenses', 'Licenses'], ['#/new', 'New license'], ['#/releases', 'Releases'], ['#/activity', 'Activity']]
  },
  customer: {
    title: 'MSP Nexus Customer Portal',
    base: '/portal',
    script: 'portal.js',
    nav: [['#/', 'My licenses'], ['#/privacy', 'Privacy requests']]
  }
};

function head(title, csrf = '') {
  return `<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow"><title>${escapeHtml(title)}</title>${csrf ? `<meta name="csrf-token" content="${escapeHtml(csrf)}">` : ''}<link rel="stylesheet" href="/console/assets/console.css"><script src="/console/assets/shared.js" defer></script>`;
}

const brand = (title) => `<a class="nxc-brand" href="#/"><span class="nxc-mark" aria-hidden="true"></span><span>${escapeHtml(title)}</span></a>`;

export function signInPage(kind, { error = '' } = {}) {
  const app = APPS[kind];
  const isOperator = 'operator' === kind;
  const label = isOperator ? 'Operator access key' : 'Portal access token';
  const help = isOperator
    ? 'The OPERATOR_TOKEN configured on this server. Keep it in a password manager.'
    : 'The portal access token you received with your license. Contact support if you lost it.';
  return `${head(`Sign in · ${app.title}`)}</head><body class="nxc nxc-auth"><main class="nxc-auth__card">
<div class="nxc-auth__brand"><span class="nxc-mark" aria-hidden="true"></span><span>${escapeHtml(app.title)}</span></div>
<h1>Sign in</h1>
<p class="nxc-muted">${isOperator ? 'Manage licenses, sites, and releases.' : 'See your licenses and the sites they are active on.'}</p>
${error ? `<p class="nxc-alert nxc-alert--error" role="alert">${escapeHtml(error)}</p>` : ''}
<form method="post" action="${app.base}/session" class="nxc-form">
<label for="nxc-secret">${label}</label>
<input id="nxc-secret" name="secret" type="password" autocomplete="current-password" required autofocus aria-describedby="nxc-secret-help">
<p id="nxc-secret-help" class="nxc-help">${escapeHtml(help)}</p>
<button type="submit" class="nxc-btn nxc-btn--primary nxc-btn--block">Sign in</button>
</form></main></body></html>`;
}

export function appPage(kind, csrf) {
  const app = APPS[kind];
  const nav = app.nav.map(([href, label]) => `<a class="nxc-tab" href="${href}" data-route="${href}">${escapeHtml(label)}</a>`).join('');
  return `${head(app.title, csrf)}<script src="/console/assets/${app.script}" defer></script></head><body class="nxc" data-app="${kind}">
<a class="nxc-skip" href="#app">Skip to content</a>
<header class="nxc-bar">${brand(app.title)}<nav class="nxc-nav" aria-label="Main">${nav}</nav>
<div class="nxc-bar__actions"><button type="button" class="nxc-icon-btn" id="nxc-scheme" aria-pressed="false" title="Toggle dark mode"><span aria-hidden="true">◐</span><span class="nxc-sr">Dark mode</span></button>
<form method="post" action="${app.base}/session/end"><input type="hidden" name="csrf" value="${escapeHtml(csrf)}"><button type="submit" class="nxc-btn nxc-btn--ghost nxc-btn--small">Sign out</button></form></div></header>
<main id="app" class="nxc-main" tabindex="-1"><p class="nxc-muted">Loading…</p></main>
<div class="nxc-toast" id="nxc-toast" role="status" aria-live="polite" hidden></div>
<noscript><p class="nxc-main">This console needs JavaScript.</p></noscript>
</body></html>`;
}
