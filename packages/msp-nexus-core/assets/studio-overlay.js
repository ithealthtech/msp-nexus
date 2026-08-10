(function () {
  'use strict';
  if (!window.mspNexusStudio) return;
  var config = window.mspNexusStudio;
  document.documentElement.classList.add('msp-nexus-inspection-active');
  var toolbar = document.createElement('nav');
  toolbar.className = 'msp-nexus-inspection-toolbar';
  toolbar.setAttribute('aria-label', 'Nexus Studio inspection');
  var links = [{ label: config.labels.site, url: config.siteEditor }];
  if (config.contentEditor) links.push({ label: config.labels.content, url: config.contentEditor });
  links.push({ label: config.labels.exit, url: config.exitUrl });
  links.forEach(function (item) {
    var link = document.createElement('a');
    link.href = item.url;
    link.textContent = item.label;
    toolbar.appendChild(link);
  });
  document.body.appendChild(toolbar);
  document.querySelectorAll('header,main,main > *,footer').forEach(function (region) {
    region.classList.add('msp-nexus-inspection-region');
  });
})();
