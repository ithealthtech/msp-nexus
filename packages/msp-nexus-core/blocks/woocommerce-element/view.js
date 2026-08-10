(function () {
  'use strict';

  function wishlist() {
    try { return JSON.parse(window.localStorage.getItem('mspNexusWishlist') || '[]').map(String); } catch (error) { return []; }
  }
  function saveWishlist(ids) {
    window.localStorage.setItem('mspNexusWishlist', JSON.stringify(ids));
    document.dispatchEvent(new window.CustomEvent('mspNexus:wishlist', { detail: { ids: ids } }));
  }
  function refreshWishlist() {
    var ids = wishlist();
    document.querySelectorAll('[data-wishlist-product]').forEach(function (item) { item.hidden = ids.indexOf(item.dataset.wishlistProduct) === -1; });
    document.querySelectorAll('.msp-nexus-wishlist').forEach(function (list) {
      var empty = list.querySelector('.msp-nexus-wishlist__empty');
      if (empty) empty.hidden = list.querySelectorAll('[data-wishlist-product]:not([hidden])').length > 0;
    });
    document.querySelectorAll('[data-nexus-wishlist]').forEach(function (button) {
      var selected = ids.indexOf(button.dataset.nexusWishlist) !== -1;
      button.setAttribute('aria-pressed', selected ? 'true' : 'false'); button.classList.toggle('is-selected', selected);
    });
  }
  function shopView(mode, columns) {
    mode = mode || window.localStorage.getItem('mspNexusShopView') || 'grid';
    columns = columns || window.localStorage.getItem('mspNexusShopColumns') || '';
    document.body.classList.toggle('msp-nexus-shop-list', mode === 'list');
    document.querySelectorAll('.msp-nexus-shop-view [data-shop-view]').forEach(function (button) { button.setAttribute('aria-pressed', button.dataset.shopView === mode ? 'true' : 'false'); });
    document.querySelectorAll('.woocommerce ul.products').forEach(function (list) { if (columns) list.style.setProperty('--msp-shop-columns', columns); });
  }
  function ajaxFilter(form) {
    var url = new window.URL(form.action || window.location.href, window.location.href); var data = new window.FormData(form);
    data.forEach(function (value, key) { if (value) url.searchParams.set(key, String(value)); else url.searchParams.delete(key); });
    form.setAttribute('aria-busy', 'true');
    window.fetch(url.toString(), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (response) { if (!response.ok) throw new Error('request_failed'); return response.text(); }).then(function (html) {
      var page = new window.DOMParser().parseFromString(html, 'text/html');
      var currentProducts = document.querySelector('.woocommerce ul.products'); var nextProducts = page.querySelector('.woocommerce ul.products');
      var currentPages = document.querySelector('.woocommerce nav.woocommerce-pagination'); var nextPages = page.querySelector('.woocommerce nav.woocommerce-pagination');
      if (!currentProducts || !nextProducts) { window.location.assign(url.toString()); return; }
      currentProducts.replaceWith(nextProducts); if (currentPages && nextPages) currentPages.replaceWith(nextPages); else if (currentPages) currentPages.remove();
      window.history.pushState({}, '', url.toString()); form.removeAttribute('aria-busy'); refreshWishlist(); shopView();
      document.dispatchEvent(new window.CustomEvent('mspNexus:productsFiltered', { detail: { url: url.toString() } }));
    }).catch(function () { window.location.assign(url.toString()); });
  }

  document.addEventListener('click', function (event) {
    var wishlistButton = event.target.closest('[data-nexus-wishlist], [data-wishlist-remove]');
    if (wishlistButton) {
      var id = String(wishlistButton.dataset.nexusWishlist || wishlistButton.dataset.wishlistRemove); var ids = wishlist();
      ids = ids.indexOf(id) === -1 && wishlistButton.hasAttribute('data-nexus-wishlist') ? ids.concat(id) : ids.filter(function (value) { return value !== id; });
      saveWishlist(ids); refreshWishlist();
    }
    var quick = event.target.closest('[data-nexus-quick-view]');
    if (quick) {
      var template = document.getElementById(quick.getAttribute('aria-controls')); var quickDialog = document.querySelector('.msp-nexus-quick-view-dialog');
      if (template && quickDialog) { quickDialog.querySelector('[data-quick-view-content]').replaceChildren(template.content.cloneNode(true)); quickDialog.showModal(); }
    }
    [['[data-side-cart-open]', '.msp-nexus-side-cart__dialog'], ['[data-account-open]', '.msp-nexus-account-dialog'], ['[data-shop-sidebar-open]', '.msp-nexus-shop-sidebar']].forEach(function (pair) {
      var trigger = event.target.closest(pair[0]); if (trigger) { var dialog = trigger.parentElement.querySelector(pair[1]); if (dialog) dialog.showModal(); }
    });
    var close = event.target.closest('[data-side-cart-close], [data-quick-view-close], [data-account-close], [data-shop-sidebar-close]'); if (close) close.closest('dialog').close();
    var view = event.target.closest('[data-shop-view]'); if (view) { window.localStorage.setItem('mspNexusShopView', view.dataset.shopView); shopView(view.dataset.shopView); }
    var swatch = event.target.closest('[data-swatch-value]');
    if (swatch) {
      var group = swatch.closest('[data-attribute]'); var select = group ? group.parentElement.querySelector('select') : null;
      if (select) { select.value = swatch.dataset.swatchValue; select.dispatchEvent(new window.Event('change', { bubbles: true })); group.querySelectorAll('button').forEach(function (button) { button.setAttribute('aria-pressed', button === swatch ? 'true' : 'false'); }); }
    }
  });
  document.addEventListener('change', function (event) {
    var columns = event.target.closest('[data-shop-columns]'); if (columns) { window.localStorage.setItem('mspNexusShopColumns', columns.value); shopView(null, columns.value); }
    var select = event.target.closest('.variations select'); if (select) { var group = select.parentElement.querySelector('.msp-nexus-swatches'); if (group) group.querySelectorAll('button').forEach(function (button) { button.setAttribute('aria-pressed', button.dataset.swatchValue === select.value ? 'true' : 'false'); }); }
    var filter = event.target.closest('.msp-nexus-shop-filters[data-instant="true"] select'); if (filter) filter.form.requestSubmit();
  });
  document.addEventListener('submit', function (event) { var form = event.target.closest('.msp-nexus-shop-filters[data-ajax="true"]'); if (form) { event.preventDefault(); ajaxFilter(form); } });

  function initialize() {
    if (!document.querySelector('.msp-nexus-quick-view-dialog')) {
      var dialog = document.createElement('dialog'); dialog.className = 'msp-nexus-quick-view-dialog'; dialog.innerHTML = '<button type="button" data-quick-view-close aria-label="Close">&times;</button><div data-quick-view-content></div>'; document.body.appendChild(dialog);
    }
    refreshWishlist(); shopView();
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize); else initialize();
})();
