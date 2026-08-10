(function () {
  'use strict';
  document.querySelectorAll('[data-nexus-video]').forEach(function (root) {
    var trigger = root.querySelector('.msp-nexus-video-dialog__trigger');
    var dialog = root.querySelector('dialog');
    var close = root.querySelector('[data-video-close]');
    var frame = root.querySelector('[data-video-url]');
    if (!trigger || !dialog || !frame) return;
    function load() {
      if (frame.children.length) return;
      var media;
      if (frame.dataset.videoFile === '1') { media = document.createElement('video'); media.controls = true; media.autoplay = true; }
      else { media = document.createElement('iframe'); media.allow = 'autoplay; fullscreen; picture-in-picture'; media.allowFullscreen = true; media.title = root.querySelector('h2').textContent; }
      media.src = frame.dataset.videoUrl;
      frame.appendChild(media);
    }
    function dismiss() { if (dialog.open) dialog.close(); frame.replaceChildren(); trigger.focus(); }
    trigger.addEventListener('click', function () { dialog.showModal(); load(); if (close) close.focus(); });
    if (close) close.addEventListener('click', dismiss);
    dialog.addEventListener('cancel', function (event) { event.preventDefault(); dismiss(); });
    dialog.addEventListener('click', function (event) { if (event.target === dialog) dismiss(); });
  });
})();
