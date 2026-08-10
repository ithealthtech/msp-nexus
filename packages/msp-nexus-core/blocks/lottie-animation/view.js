(function () {
  'use strict';
  document.querySelectorAll('[data-lottie-config]').forEach(function (root) {
    if (!window.lottie) return;
    var config; try { config = JSON.parse(root.dataset.lottieConfig || '{}'); } catch (error) { return; }
    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var animation = window.lottie.loadAnimation({ container: root, renderer: 'svg', loop: !reduced && !!config.loop, autoplay: false, path: config.url, rendererSettings: { progressiveLoad: true, preserveAspectRatio: 'xMidYMid meet' } });
    animation.setSpeed(Number(config.speed || 1));
    function play() { if (!reduced) animation.play(); }
    if (config.playOnHover) { root.addEventListener('mouseenter', play); root.addEventListener('mouseleave', function () { animation.pause(); }); }
    if (config.autoPlay && config.startWhenVisible && 'IntersectionObserver' in window) {
      var observer = new window.IntersectionObserver(function (entries) { entries.forEach(function (entry) { if (entry.isIntersecting) { play(); observer.disconnect(); } }); }, { rootMargin: '100px' }); observer.observe(root);
    } else if (config.autoPlay) play();
    if (reduced) animation.goToAndStop(0, true);
  });
})();
