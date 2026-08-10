(function () {
  'use strict';
  document.querySelectorAll('[data-nexus-menu-panel]').forEach(function (panel) {
    var config = {};
    var timer;
    try { config = JSON.parse(panel.dataset.config || '{}'); } catch (error) { config = {}; }
    function close() { panel.open = false; }
    if (config.hoverOpen && window.matchMedia('(hover:hover)').matches) {
      panel.addEventListener('mouseenter', function () { window.clearTimeout(timer); panel.open = true; });
      panel.addEventListener('mouseleave', function () { timer = window.setTimeout(close, 180); });
    }
    panel.addEventListener('keydown', function (event) { if (event.key === 'Escape') { close(); panel.querySelector('summary').focus(); } });
    if (config.closeOutside) document.addEventListener('click', function (event) { if (panel.open && !panel.contains(event.target)) close(); });
  });
})();
