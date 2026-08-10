(function () {
  'use strict';
  document.querySelectorAll('[data-nexus-offcanvas]').forEach(function (root) {
    var trigger = root.querySelector('.msp-nexus-offcanvas__trigger');
    var dialog = root.querySelector('dialog');
    var close = root.querySelector('[data-offcanvas-close]');
    var config = {};
    try { config = JSON.parse(root.dataset.config || '{}'); } catch (error) { config = {}; }
    if (!trigger || !dialog) return;
    function open() { dialog.showModal(); trigger.setAttribute('aria-expanded', 'true'); if (close) close.focus(); }
    function dismiss() { if (dialog.open) dialog.close(); trigger.setAttribute('aria-expanded', 'false'); trigger.focus(); }
    trigger.addEventListener('click', open);
    if (close) close.addEventListener('click', dismiss);
    dialog.addEventListener('cancel', function (event) { event.preventDefault(); dismiss(); });
    dialog.addEventListener('click', function (event) { if (event.target === dialog) dismiss(); });
    if (config.closeOnNavigate) dialog.querySelectorAll('a').forEach(function (link) { link.addEventListener('click', function () { if (dialog.open) dialog.close(); }); });
  });
})();
