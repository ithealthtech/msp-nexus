(function (apiFetch, i18n) {
  'use strict';

  var config = window.mspNexusStarterLibrary;
  var state = { page: 1, facetsReady: false };
  var controls = {
    search: document.querySelector('[data-starter-search]'), vertical: document.querySelector('[data-starter-vertical]'),
    focus: document.querySelector('[data-starter-focus]'), style: document.querySelector('[data-starter-style]'),
    grid: document.querySelector('[data-starter-grid]'), count: document.querySelector('[data-starter-count]'), pages: document.querySelector('[data-starter-pages]')
  };
  if (!config || !controls.grid) return;

  function option(select, value, label) {
    var node = document.createElement('option'); node.value = value; node.textContent = label; select.appendChild(node);
  }
  function checkbox(form, name, label, checked) {
    var row = document.createElement('label'); var input = document.createElement('input'); input.type = 'checkbox'; input.name = name; input.value = '1'; input.checked = checked;
    row.append(input, document.createTextNode(' ' + label)); form.appendChild(row);
  }
  function hidden(form, name, value) { var input = document.createElement('input'); input.type = 'hidden'; input.name = name; input.value = value; form.appendChild(input); }
  function card(item) {
    var article = document.createElement('article'); article.className = 'msp-nexus-starter-card' + (item.id === config.selected ? ' is-selected' : '');
    var swatch = document.createElement('div'); swatch.className = 'msp-nexus-starter-card__swatch'; swatch.style.setProperty('--starter-accent', item.accent);
    var body = document.createElement('div'); body.className = 'msp-nexus-starter-card__body';
    var meta = document.createElement('p'); meta.className = 'msp-nexus-starter-card__meta'; meta.textContent = item.vertical_name + ' · ' + item.style_name;
    var title = document.createElement('h2'); title.textContent = item.name;
    var description = document.createElement('p'); description.textContent = item.description;
    var form = document.createElement('form'); form.method = 'post'; form.action = config.action;
    hidden(form, 'action', 'msp_nexus_apply_starter'); hidden(form, '_wpnonce', config.nonce); hidden(form, 'starter', item.id);
    checkbox(form, 'apply_settings', i18n.__('Apply design tokens', 'msp-nexus-core'), true);
    checkbox(form, 'create_pages', i18n.__('Create missing draft pages', 'msp-nexus-core'), false);
    checkbox(form, 'make_home', i18n.__('Publish new Home and set it as front page', 'msp-nexus-core'), false);
    checkbox(form, 'replace_home', i18n.__('Replace existing Nexus-owned Home composition', 'msp-nexus-core'), false);
    checkbox(form, 'create_navigation', i18n.__('Create a reusable navigation', 'msp-nexus-core'), false);
    var submit = document.createElement('button'); submit.className = 'button button-primary'; submit.type = 'submit'; submit.textContent = item.id === config.selected ? config.labels.selected : config.labels.apply; form.appendChild(submit);
    body.append(meta, title, description, form); article.append(swatch, body); return article;
  }
  function query() {
    var params = new window.URLSearchParams({ page: String(state.page), per_page: '24', search: controls.search.value, vertical: controls.vertical.value, focus: controls.focus.value, style: controls.style.value });
    controls.grid.setAttribute('aria-busy', 'true');
    apiFetch({ path: config.path + '?' + params.toString() }).then(function (data) {
      if (!state.facetsReady) {
        Object.keys(data.facets.verticals).forEach(function (key) { option(controls.vertical, key, data.facets.verticals[key]); });
        Object.keys(data.facets.focuses).forEach(function (key) { option(controls.focus, key, data.facets.focuses[key]); });
        Object.keys(data.facets.styles).forEach(function (key) { option(controls.style, key, data.facets.styles[key]); }); state.facetsReady = true;
      }
      controls.grid.replaceChildren(); data.items.forEach(function (item) { controls.grid.appendChild(card(item)); });
      controls.count.textContent = data.total ? i18n.sprintf(i18n._n('%d starter configuration', '%d starter configurations', data.total, 'msp-nexus-core'), data.total) : config.labels.empty;
      controls.pages.replaceChildren();
      for (var page = 1; page <= data.pages; page += 1) { var button = document.createElement('button'); button.type = 'button'; button.className = 'button'; button.textContent = String(page); button.dataset.page = String(page); if (page === data.page) button.setAttribute('aria-current', 'page'); controls.pages.appendChild(button); }
      controls.grid.removeAttribute('aria-busy');
    }).catch(function () { controls.grid.removeAttribute('aria-busy'); controls.count.textContent = i18n.__('The starter catalog could not be loaded.', 'msp-nexus-core'); });
  }
  var timer;
  controls.search.addEventListener('input', function () { window.clearTimeout(timer); state.page = 1; timer = window.setTimeout(query, 250); });
  [controls.vertical, controls.focus, controls.style].forEach(function (select) { select.addEventListener('change', function () { state.page = 1; query(); }); });
  controls.pages.addEventListener('click', function (event) { var button = event.target.closest('[data-page]'); if (button) { state.page = Number(button.dataset.page); query(); window.scrollTo({ top: controls.grid.offsetTop - 80, behavior: 'smooth' }); } });
  query();
})(window.wp.apiFetch, window.wp.i18n);
