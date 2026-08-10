(function () {
  'use strict';
  if (window.matchMedia('(prefers-reduced-motion:reduce)').matches) return;
  document.querySelectorAll('[data-nexus-counter]').forEach(function (root) {
    var config;
    var output = root.querySelector('[data-counter-value]');
    try { config = JSON.parse(root.dataset.nexusCounter || '{}'); } catch (error) { return; }
    if (!output || !window.IntersectionObserver) return;
    var observer = new window.IntersectionObserver(function (entries) {
      if (!entries[0].isIntersecting) return;
      observer.disconnect();
      var start = window.performance.now();
      function frame(now) {
        var progress = Math.min(1, (now - start) / Math.max(1, Number(config.duration || 1200)));
        var eased = 1 - Math.pow(1 - progress, 3);
        output.textContent = (Number(config.value || 0) * eased).toLocaleString(undefined, { minimumFractionDigits: Number(config.decimals || 0), maximumFractionDigits: Number(config.decimals || 0) });
        if (progress < 1) window.requestAnimationFrame(frame);
      }
      window.requestAnimationFrame(frame);
    });
    observer.observe(root);
  });
})();
