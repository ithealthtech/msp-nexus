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
		scheme.addEventListener('click', function () {
			var dark = !document.body.classList.contains('msp-nexus-ui--dark');
			document.body.classList.toggle('msp-nexus-ui--dark', dark);
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
}());
