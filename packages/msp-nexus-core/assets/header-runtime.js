(function () {
  'use strict';
  document.querySelectorAll('[data-header-config]').forEach(function (header) {
    var config = {}; var previous = window.scrollY;
    try { config = JSON.parse(header.dataset.headerConfig || '{}'); } catch (error) { config = {}; }
    function update() {
      var current = window.scrollY; var active = current > Number(config.offset || 40);
      header.classList.toggle('is-scrolled', active && !!config.shrink);
      header.classList.toggle('is-hidden', active && !!config.hideScroll && current > previous && current - previous > 2);
      previous = current;
    }
    update(); window.addEventListener('scroll', update, { passive: true });
  });
})();
