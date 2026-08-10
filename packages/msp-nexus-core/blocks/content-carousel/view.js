(function () {
  'use strict';
  document.querySelectorAll('[data-nexus-carousel]').forEach(function (root) {
    var config = {};
    var slides = Array.from(root.querySelectorAll('[aria-roledescription="slide"]'));
    var dots = Array.from(root.querySelectorAll('[data-carousel-index]'));
    var previous = root.querySelector('[data-carousel-previous]');
    var next = root.querySelector('[data-carousel-next]');
    var pause = root.querySelector('[data-carousel-pause]');
    var index = 0;
    var timer;
    var paused = false;
    try { config = JSON.parse(root.dataset.nexusCarousel || '{}'); } catch (error) { config = {}; }
    function show(nextIndex) {
      index = (nextIndex + slides.length) % slides.length;
      slides.forEach(function (slide, slideIndex) { slide.hidden = slideIndex !== index; });
      dots.forEach(function (dot, dotIndex) { dot.setAttribute('aria-current', dotIndex === index ? 'true' : 'false'); });
    }
    function stop() { if (timer) window.clearInterval(timer); timer = null; }
    function start() { if (!config.autoPlay || paused || window.matchMedia('(prefers-reduced-motion:reduce)').matches) return; stop(); timer = window.setInterval(function () { show(index + 1); }, Number(config.interval || 7000)); }
    if (previous) previous.addEventListener('click', function () { show(index - 1); start(); });
    if (next) next.addEventListener('click', function () { show(index + 1); start(); });
    dots.forEach(function (dot) { dot.addEventListener('click', function () { show(Number(dot.dataset.carouselIndex)); start(); }); });
    if (pause) pause.addEventListener('click', function () { paused = !paused; pause.setAttribute('aria-pressed', paused ? 'true' : 'false'); pause.textContent = paused ? 'Play' : 'Pause'; if (paused) stop(); else start(); });
    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', start);
    start();
  });
})();
