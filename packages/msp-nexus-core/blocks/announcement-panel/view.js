(function () {
  'use strict';
  document.querySelectorAll('[data-msp-announcement]').forEach(function (root) {
    var config;
    try { config = JSON.parse(root.dataset.config || '{}'); } catch (error) { root.hidden = true; return; }
    function hasConsent() {
      if (!config.requireConsent || window.localStorage.getItem(config.consentKey) === 'granted') return true;
      try {
        var choice = JSON.parse(window.localStorage.getItem('msp_nexus_consent') || '{}');
        var category = config.consentKey.indexOf('analytics') !== -1 ? 'analytics' : (config.consentKey.indexOf('preferences') !== -1 ? 'preferences' : 'marketing');
        return choice[category] === true;
      } catch (error) { return false; }
    }
    var pathMatches = !config.path || window.location.pathname.indexOf(config.path) === 0;
    var referrerExcluded = config.excludedReferrer && document.referrer.indexOf(config.excludedReferrer) !== -1;
    var dismissedAt = Number(window.localStorage.getItem(config.storageKey) || 0);
    var frequencyMs = Number(config.frequency || 0) * 86400000;
    if (!pathMatches || referrerExcluded || (frequencyMs && Date.now() - dismissedAt < frequencyMs)) { root.hidden = true; return; }
    var dialog = root.querySelector('dialog');
    var close = root.querySelector('[data-msp-announcement-close]');
    var previousFocus = null;
    var scheduled = false;
    function dismiss() {
      window.localStorage.setItem(config.storageKey, String(Date.now()));
      if (dialog && dialog.open) dialog.close();
      root.hidden = true;
      if (previousFocus && typeof previousFocus.focus === 'function') previousFocus.focus();
    }
    function reveal() {
      if (scheduled || !hasConsent()) { root.hidden = true; return; }
      scheduled = true; root.hidden = false;
      if (dialog) window.setTimeout(function () {
        if (!document.body.contains(root)) return;
        previousFocus = document.activeElement; dialog.showModal(); if (close) close.focus();
      }, Math.min(60, Math.max(0, Number(config.delay || 0))) * 1000);
    }
    if (close) close.addEventListener('click', dismiss);
    if (dialog) dialog.addEventListener('cancel', function (event) { event.preventDefault(); dismiss(); });
    if (config.requireConsent) document.addEventListener('mspNexus:consent', reveal);
    reveal();
  });
})();
