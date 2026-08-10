(function () {
  'use strict';
  var config = window.mspNexusConsent;
  var banner = document.querySelector('[data-msp-consent-banner]');
  if (!config || !banner) return;

  function read() {
    try { return JSON.parse(window.localStorage.getItem(config.cookie) || 'null'); } catch (error) { return null; }
  }
  function activate(choice) {
    document.querySelectorAll('script[type="text/plain"][data-msp-consent]').forEach(function (blocked) {
      var category = blocked.dataset.mspConsent;
      if (!choice[category] || blocked.dataset.mspActivated) return;
      var script = document.createElement('script');
      Array.from(blocked.attributes).forEach(function (attribute) { if (attribute.name !== 'type' && attribute.name !== 'data-msp-consent') script.setAttribute(attribute.name, attribute.value); });
      script.text = blocked.text; script.dataset.mspActivated = 'true'; blocked.replaceWith(script);
    });
    document.dispatchEvent(new window.CustomEvent('mspNexus:consent', { detail: choice }));
  }
  function save(choice) {
    choice.essential = true; choice.updated = new Date().toISOString();
    window.localStorage.setItem(config.cookie, JSON.stringify(choice));
    var expiry = new Date(Date.now() + Number(config.days) * 86400000).toUTCString();
    document.cookie = encodeURIComponent(config.cookie) + '=' + encodeURIComponent(JSON.stringify(choice)) + '; expires=' + expiry + '; path=/; SameSite=Lax' + (window.location.protocol === 'https:' ? '; Secure' : '');
    banner.hidden = true; activate(choice);
  }
  function open() { banner.hidden = false; banner.querySelector('[data-consent-open]').focus(); }
  var choice = read();
  if (choice) activate(choice); else banner.hidden = false;
  banner.addEventListener('click', function (event) {
    if (event.target.closest('[data-consent-accept]')) save({ analytics: true, marketing: true, preferences: true });
    if (event.target.closest('[data-consent-reject]')) save({ analytics: false, marketing: false, preferences: false });
    var toggle = event.target.closest('[data-consent-open]');
    if (toggle) { var preferences = banner.querySelector('[data-consent-preferences]'); preferences.hidden = !preferences.hidden; toggle.setAttribute('aria-expanded', preferences.hidden ? 'false' : 'true'); }
  });
  banner.querySelector('[data-consent-preferences]').addEventListener('submit', function (event) { event.preventDefault(); var data = new window.FormData(event.target); save({ analytics: data.has('analytics'), marketing: data.has('marketing'), preferences: data.has('preferences') }); });
  document.addEventListener('click', function (event) { if (event.target.closest('[data-consent-manage]')) open(); });
})();
