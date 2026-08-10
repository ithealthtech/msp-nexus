(function () {
  'use strict';
  var roots = Array.from(document.querySelectorAll('[data-nexus-popup]'));
  var groups = {};
  roots.forEach(function (root) {
    try { root.nexusConfig = JSON.parse(root.dataset.config || '{}'); } catch (error) { root.nexusConfig = null; }
    if (root.nexusConfig && root.nexusConfig.group) {
      if (!groups[root.nexusConfig.group]) groups[root.nexusConfig.group] = [];
      groups[root.nexusConfig.group].push(root);
    }
  });
  Object.keys(groups).forEach(function (group) {
    var variants = groups[group];
    var key = 'msp_nexus_popup_variant_' + group;
    var saved = window.localStorage.getItem(key);
    var chosen = variants.filter(function (root) { return root.dataset.nexusPopup === saved; })[0];
    if (!chosen) {
      var total = variants.reduce(function (sum, root) { return sum + Number(root.nexusConfig.weight || 1); }, 0);
      var target = Math.random() * total;
      chosen = variants[0];
      variants.some(function (root) {
        target -= Number(root.nexusConfig.weight || 1);
        if (target <= 0) { chosen = root; return true; }
        return false;
      });
      window.localStorage.setItem(key, chosen.dataset.nexusPopup);
    }
    variants.forEach(function (root) { root.nexusVariantActive = root === chosen; });
  });

  roots.forEach(function (root) {
    var dialog = root.querySelector('dialog');
    var close = root.querySelector('[data-nexus-popup-close]');
    var config = root.nexusConfig;
    var shown = false;
    if (!config || (config.group && !root.nexusVariantActive)) return;
    function consentGranted() {
      if (!config.requireConsent || window.localStorage.getItem(config.consentKey) === 'granted' || document.cookie.indexOf(encodeURIComponent(config.consentKey) + '=granted') !== -1) return true;
      try {
        var choice = JSON.parse(window.localStorage.getItem('msp_nexus_consent') || '{}');
        var category = config.consentKey.indexOf('analytics') !== -1 ? 'analytics' : (config.consentKey.indexOf('preferences') !== -1 ? 'preferences' : 'marketing');
        return choice[category] === true;
      } catch (error) { return false; }
    }
    var dismissedAt = Number(window.localStorage.getItem(config.storageKey) || 0);
    if (config.days && Date.now() - dismissedAt < Number(config.days) * 86400000) return;
    function event(name, extra) {
      var detail = Object.assign({ id: root.dataset.nexusPopup, group: config.group || '', trigger: config.trigger || '' }, extra || {});
      document.dispatchEvent(new window.CustomEvent('msp-nexus:popup-' + name, { detail: detail }));
      if (Array.isArray(window.dataLayer)) window.dataLayer.push(Object.assign({ event: 'msp_nexus_popup_' + name }, detail));
    }
    function show() {
      if (shown || !dialog || !consentGranted()) return;
      shown = true;
      root.hidden = false;
      dialog.showModal();
      event('open');
      if (close) close.focus();
    }
    if (config.requireConsent) document.addEventListener('mspNexus:consent', function () { if (consentGranted()) show(); });
    function dismiss(reason) {
      window.localStorage.setItem(config.storageKey, String(Date.now()));
      if (dialog && dialog.open) dialog.close();
      root.hidden = true;
      event('close', { reason: reason || 'dismiss' });
    }
    if (close) close.addEventListener('click', function () { dismiss('button'); });
    if (dialog) {
      dialog.addEventListener('cancel', function (nativeEvent) { nativeEvent.preventDefault(); dismiss('escape'); });
      dialog.addEventListener('click', function (nativeEvent) { if (config.closeOverlay && nativeEvent.target === dialog) dismiss('overlay'); });
    }
    root.addEventListener('click', function (nativeEvent) {
      var link = nativeEvent.target.closest ? nativeEvent.target.closest('a') : null;
      if (link) event('conversion', { url: link.href });
    });
    if (config.trigger === 'click' && config.selector) {
      try {
        document.querySelectorAll(config.selector).forEach(function (trigger) { trigger.addEventListener('click', function (nativeEvent) { nativeEvent.preventDefault(); show(); }); });
      } catch (error) { return; }
    } else if (config.trigger === 'scroll') {
      window.addEventListener('scroll', function onScroll() {
        var available = document.documentElement.scrollHeight - window.innerHeight;
        if (available > 0 && (window.scrollY / available) * 100 >= Number(config.scroll || 50)) { window.removeEventListener('scroll', onScroll); show(); }
      }, { passive: true });
    } else if (config.trigger === 'exit') {
      document.addEventListener('mouseout', function onExit(nativeEvent) { if (nativeEvent.clientY <= 0) { document.removeEventListener('mouseout', onExit); show(); } });
    } else {
      window.setTimeout(show, Math.max(0, Number(config.delay || 0)) * 1000);
    }
  });
})();
