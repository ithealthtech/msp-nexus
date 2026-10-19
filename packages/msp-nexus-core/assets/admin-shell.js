/* MSP Nexus admin shell: Tools menu and the per-user light or dark scheme. */
(function () {
	'use strict';

	var shell = document.querySelector('.msp-nexus-shell');
	if (!shell) {
		return;
	}

	var toggle = shell.querySelector('.msp-nexus-shell__more > button');
	var menu = toggle ? document.getElementById(toggle.getAttribute('aria-controls')) : null;

	function setOpen(open) {
		if (!toggle || !menu) {
			return;
		}
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		menu.hidden = !open;
	}

	if (toggle && menu) {
		toggle.addEventListener('click', function () {
			setOpen(menu.hidden);
			if (!menu.hidden) {
				var first = menu.querySelector('a');
				if (first) {
					first.focus();
				}
			}
		});
		document.addEventListener('click', function (event) {
			if (!menu.hidden && !toggle.parentNode.contains(event.target)) {
				setOpen(false);
			}
		});
		document.addEventListener('keydown', function (event) {
			if ('Escape' === event.key && !menu.hidden) {
				setOpen(false);
				toggle.focus();
			}
		});
		menu.addEventListener('keydown', function (event) {
			if ('ArrowDown' !== event.key && 'ArrowUp' !== event.key) {
				return;
			}
			var links = Array.prototype.slice.call(menu.querySelectorAll('a'));
			var index = links.indexOf(document.activeElement);
			var next = 'ArrowDown' === event.key ? index + 1 : index - 1;
			if (links.length) {
				event.preventDefault();
				links[(next + links.length) % links.length].focus();
			}
		});
	}

	var scheme = shell.querySelector('.msp-nexus-shell__scheme');
	var config = window.mspNexusShell || {};
	if (scheme) {
		var prefersDark = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
		var isDark = function () {
			var classes = document.body.classList;
			return classes.contains('msp-nexus-ui--dark') || (classes.contains('msp-nexus-ui--auto') && !!prefersDark && prefersDark.matches);
		};
		scheme.setAttribute('aria-pressed', isDark() ? 'true' : 'false');
		scheme.addEventListener('click', function () {
			var dark = !isDark();
			document.body.classList.remove('msp-nexus-ui--auto', 'msp-nexus-ui--light', 'msp-nexus-ui--dark');
			document.body.classList.add(dark ? 'msp-nexus-ui--dark' : 'msp-nexus-ui--light');
			scheme.setAttribute('aria-pressed', dark ? 'true' : 'false');
			if (!config.ajaxUrl) {
				return;
			}
			var body = new window.FormData();
			body.append('action', config.action);
			body.append('nonce', config.nonce);
			body.append('scheme', dark ? 'dark' : 'light');
			window.fetch(config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body });
		});
	}

	/* Settings screen: one section at a time, keyboard tabs, and search across every field. */
	var settings = document.querySelector('.msp-nexus-settings');
	if (settings) {
		var tabs = Array.prototype.slice.call(settings.querySelectorAll('.msp-nexus-settings__tab'));
		var panels = Array.prototype.slice.call(settings.querySelectorAll('.msp-nexus-settings__panel'));
		var search = settings.querySelector('#msp-nexus-settings-search');
		var empty = settings.querySelector('.msp-nexus-settings__empty');
		var storageKey = 'mspNexusSettingsTab';

		var activate = function (id, moveFocus) {
			tabs.forEach(function (tab) {
				var selected = tab.getAttribute('aria-controls') === id;
				tab.setAttribute('aria-selected', selected ? 'true' : 'false');
				tab.setAttribute('tabindex', selected ? '0' : '-1');
				if (selected && moveFocus) {
					tab.focus();
				}
			});
			panels.forEach(function (panel) {
				panel.classList.toggle('is-active', panel.id === id);
			});
			try {
				window.sessionStorage.setItem(storageKey, id);
			} catch (error) {
				// Storage can be unavailable; the tab simply is not remembered.
			}
			if (window.history && window.history.replaceState) {
				window.history.replaceState(null, '', '#' + id.replace('msp-nexus-section-', ''));
			}
		};

		var initial = 'msp-nexus-section-' + window.location.hash.replace('#', '');
		if (!document.getElementById(initial)) {
			try {
				initial = window.sessionStorage.getItem(storageKey) || '';
			} catch (error) {
				initial = '';
			}
		}
		if (!panels.some(function (panel) { return panel.id === initial; })) {
			initial = panels.length ? panels[0].id : '';
		}
		settings.classList.add('is-tabbed');
		activate(initial, false);

		tabs.forEach(function (tab, index) {
			tab.addEventListener('click', function (event) {
				event.preventDefault();
				if (search && search.value) {
					search.value = '';
					filter('');
				}
				activate(tab.getAttribute('aria-controls'), false);
			});
			tab.addEventListener('keydown', function (event) {
				var next = null;
				if ('ArrowDown' === event.key || 'ArrowRight' === event.key) {
					next = (index + 1) % tabs.length;
				} else if ('ArrowUp' === event.key || 'ArrowLeft' === event.key) {
					next = (index - 1 + tabs.length) % tabs.length;
				} else if ('Home' === event.key) {
					next = 0;
				} else if ('End' === event.key) {
					next = tabs.length - 1;
				}
				if (null !== next) {
					event.preventDefault();
					activate(tabs[next].getAttribute('aria-controls'), true);
				}
			});
		});

		var filter = function (raw) {
			var query = raw.trim().toLowerCase();
			var total = 0;
			settings.classList.toggle('is-searching', '' !== query);
			panels.forEach(function (panel) {
				var matches = 0;
				Array.prototype.forEach.call(panel.querySelectorAll('.form-table'), function (table) {
					var visible = 0;
					Array.prototype.forEach.call(table.querySelectorAll('tr'), function (row) {
						var hit = '' === query || -1 !== row.textContent.toLowerCase().indexOf(query);
						row.classList.toggle('is-hidden', !hit);
						visible += hit ? 1 : 0;
					});
					table.classList.toggle('is-empty', 0 === visible);
					var heading = table.previousElementSibling;
					if (heading && heading.classList.contains('msp-nexus-settings__group')) {
						heading.classList.toggle('is-empty', 0 === visible);
					}
					matches += visible;
				});
				panel.classList.toggle('is-empty', 0 === matches);
				total += matches;
				var tab = settings.querySelector('[aria-controls="' + panel.id + '"]');
				var count = tab ? tab.querySelector('.msp-nexus-settings__count') : null;
				if (count) {
					count.textContent = String(matches);
					count.hidden = '' === query || 0 === matches;
				}
			});
			if (empty) {
				empty.hidden = '' === query || total > 0;
			}
		};

		// A field with an invalid value may sit in a hidden section; show it so the browser can point at it.
		var form = settings.querySelector('form');
		if (form) {
			form.addEventListener('submit', function (event) {
				if (form.checkValidity()) {
					return;
				}
				event.preventDefault();
				var invalid = form.querySelector(':invalid');
				var panel = invalid ? invalid.closest('.msp-nexus-settings__panel') : null;
				if (search && search.value) {
					search.value = '';
					filter('');
				}
				if (panel) {
					activate(panel.id, false);
				}
				if (invalid) {
					invalid.reportValidity();
				}
			});
			Array.prototype.forEach.call(form.querySelectorAll('[type="submit"]'), function (button) {
				button.addEventListener('click', function (event) {
					if (!form.checkValidity()) {
						event.preventDefault();
						form.dispatchEvent(new window.Event('submit', { cancelable: true }));
					}
				});
			});
		}

		if (search) {
			search.addEventListener('input', function () {
				filter(search.value);
			});
			search.addEventListener('keydown', function (event) {
				// Enter would submit the settings form; searching should never save.
				if ('Enter' === event.key) {
					event.preventDefault();
				}
				if ('Escape' === event.key) {
					search.value = '';
					filter('');
				}
			});
		}
	}
}());
