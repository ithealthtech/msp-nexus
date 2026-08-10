(function () {
  'use strict';
  document.querySelectorAll('[data-nexus-tabs]').forEach(function (root) {
    var tabs = Array.from(root.querySelectorAll('[role="tab"]'));
    var panels = Array.from(root.querySelectorAll('[role="tabpanel"]'));
    function activate(index) {
      tabs.forEach(function (tab, itemIndex) { tab.setAttribute('aria-selected', itemIndex === index ? 'true' : 'false'); tab.tabIndex = itemIndex === index ? 0 : -1; });
      panels.forEach(function (panel, itemIndex) { panel.hidden = itemIndex !== index; });
      tabs[index].focus();
    }
    tabs.forEach(function (tab, index) {
      tab.addEventListener('click', function () { activate(index); });
      tab.addEventListener('keydown', function (event) {
        var vertical = root.dataset.orientation === 'vertical';
        var previous = vertical ? 'ArrowUp' : 'ArrowLeft';
        var next = vertical ? 'ArrowDown' : 'ArrowRight';
        if (event.key === previous || event.key === next || event.key === 'Home' || event.key === 'End') {
          event.preventDefault();
          if (event.key === 'Home') activate(0);
          else if (event.key === 'End') activate(tabs.length - 1);
          else activate((index + (event.key === next ? 1 : -1) + tabs.length) % tabs.length);
        }
      });
    });
  });
})();
