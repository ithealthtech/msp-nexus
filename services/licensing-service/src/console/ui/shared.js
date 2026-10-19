/* Shared helpers for the operator console and the customer portal. No dependencies. */
(function () {
	'use strict';

	var MESSAGES = {
		activation_limit_reached: 'That license has no free production site slots.',
		activation_not_found: 'That site activation no longer exists.',
		csrf_failed: 'Your session expired or the page is out of date. Reload and try again.',
		invalid_json: 'The request could not be read.',
		invalid_license_status: 'That status is not allowed.',
		invalid_release: 'Check the release fields: product, version, SHA-256 (64 hex characters), and size are required.',
		invalid_release_status: 'That release status is not allowed.',
		invalid_request: 'Some values are missing or invalid.',
		license_not_found: 'That license was not found.',
		operator_access_denied: 'You are signed out.',
		portal_access_denied: 'You are signed out.',
		rate_limited: 'Too many requests. Wait a minute and try again.',
		release_not_found: 'That release was not found.',
		webhook_not_configured: 'Commerce webhooks are not configured on this server.'
	};

	var schemeKey = 'nxc-scheme';
	function applyScheme(value) {
		if ('dark' === value || 'light' === value) {
			document.documentElement.setAttribute('data-scheme', value);
		} else {
			document.documentElement.removeAttribute('data-scheme');
		}
	}
	try {
		applyScheme(window.localStorage.getItem(schemeKey));
	} catch (error) {
		// Storage can be blocked; the system scheme applies.
	}

	function isDark() {
		var forced = document.documentElement.getAttribute('data-scheme');
		if (forced) {
			return 'dark' === forced;
		}
		return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
	}

	function esc(value) {
		return String(null === value || undefined === value ? '' : value)
			.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
	}

	function fmtDate(iso) {
		if (!iso) {
			return '—';
		}
		var date = new Date(iso);
		return isNaN(date.getTime()) ? '—' : date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
	}

	function fmtDateTime(iso) {
		if (!iso) {
			return '—';
		}
		var date = new Date(iso);
		return isNaN(date.getTime()) ? '—' : date.toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
	}

	function daysUntil(iso) {
		return Math.ceil((new Date(iso).getTime() - Date.now()) / 86400000);
	}

	function relative(iso) {
		if (!iso) {
			return 'never';
		}
		var minutes = Math.round((Date.now() - new Date(iso).getTime()) / 60000);
		if (minutes < 1) {
			return 'just now';
		}
		if (minutes < 60) {
			return minutes + ' min ago';
		}
		if (minutes < 1440) {
			return Math.round(minutes / 60) + ' h ago';
		}
		return Math.round(minutes / 1440) + ' days ago';
	}

	function pill(status) {
		return '<span class="nxc-pill nxc-pill--' + esc(status) + '">' + esc(status) + '</span>';
	}

	function csrf() {
		var meta = document.querySelector('meta[name="csrf-token"]');
		return meta ? meta.getAttribute('content') : '';
	}

	function api(method, path, body) {
		var options = { method: method, credentials: 'same-origin', headers: { accept: 'application/json' } };
		if ('GET' !== method) {
			options.headers['content-type'] = 'application/json';
			options.headers['x-csrf-token'] = csrf();
			options.body = JSON.stringify(body || {});
		}
		return window.fetch(path, options).then(function (response) {
			return response.json().catch(function () {
				return {};
			}).then(function (payload) {
				if (401 === response.status) {
					window.location.reload();
				}
				if (!response.ok) {
					var error = new Error(MESSAGES[payload.error] || 'Something went wrong (' + (payload.error || response.status) + ').');
					error.code = payload.error;
					throw error;
				}
				return payload;
			});
		});
	}

	var toastTimer = null;
	function toast(message, isError) {
		var node = document.getElementById('nxc-toast');
		if (!node) {
			return;
		}
		node.textContent = message;
		node.classList.toggle('is-error', !!isError);
		node.hidden = false;
		window.clearTimeout(toastTimer);
		toastTimer = window.setTimeout(function () {
			node.hidden = true;
		}, isError ? 7000 : 3500);
	}

	/**
	 * Opens a modal form. Resolves with the submitted FormData, or null when cancelled.
	 * `body` is trusted markup built by the caller with esc() for any data.
	 */
	function dialog(options) {
		return new Promise(function (resolve) {
			var node = document.createElement('dialog');
			node.className = 'nxc-dialog';
			node.setAttribute('aria-labelledby', 'nxc-dialog-title');
			node.innerHTML = '<form method="dialog"><h2 id="nxc-dialog-title">' + esc(options.title) + '</h2>' +
				(options.body || '') +
				'<div class="nxc-actions"><button type="button" class="nxc-btn" data-cancel>Cancel</button>' +
				'<button type="submit" class="nxc-btn ' + (options.danger ? 'nxc-btn--danger' : 'nxc-btn--primary') + '">' + esc(options.confirm || 'Save') + '</button></div></form>';
			document.body.appendChild(node);
			var form = node.querySelector('form');
			var done = function (value) {
				node.close();
				node.remove();
				resolve(value);
			};
			node.querySelector('[data-cancel]').addEventListener('click', function () {
				done(null);
			});
			node.addEventListener('cancel', function (event) {
				event.preventDefault();
				done(null);
			});
			form.addEventListener('submit', function (event) {
				event.preventDefault();
				done(new window.FormData(form));
			});
			node.showModal();
			var first = form.querySelector('input, select, textarea');
			if (first) {
				first.focus();
			}
		});
	}

	function copy(text) {
		if (window.navigator.clipboard && window.navigator.clipboard.writeText) {
			return window.navigator.clipboard.writeText(text).then(function () {
				toast('Copied to the clipboard.');
			}, function () {
				toast('Copy failed. Select the text and copy it manually.', true);
			});
		}
		toast('Copy is not available here. Select the text and copy it manually.', true);
		return Promise.resolve();
	}

	function router(routes, render) {
		var go = function () {
			// Look <main> up each time: renders may replace the element.
			var main = document.getElementById('app');
			var hash = window.location.hash || '#/';
			var path = hash.slice(1) || '/';
			var tabs = document.querySelectorAll('.nxc-tab');
			Array.prototype.forEach.call(tabs, function (tab) {
				var route = tab.getAttribute('data-route').slice(1);
				var current = '/' === route ? '/' === path : 0 === path.indexOf(route);
				if (current) {
					tab.setAttribute('aria-current', 'page');
				} else {
					tab.removeAttribute('aria-current');
				}
			});
			for (var index = 0; index < routes.length; index++) {
				var match = path.match(routes[index][0]);
				if (match) {
					main.innerHTML = '<p class="nxc-muted">Loading…</p>';
					Promise.resolve(routes[index][1].apply(null, match.slice(1))).then(function (html) {
						if ('string' === typeof html) {
							main.innerHTML = html;
						}
						if (render) {
							render(main);
						}
					}).catch(function (error) {
						main.innerHTML = '<div class="nxc-alert nxc-alert--error" role="alert">' + esc(error.message) + '</div>';
					});
					return;
				}
			}
			main.innerHTML = '<div class="nxc-empty">Page not found. <a href="#/">Go to the start page</a>.</div>';
		};
		window.addEventListener('hashchange', function () {
			go();
			document.getElementById('app').focus({ preventScroll: true });
			window.scrollTo(0, 0);
		});
		go();
	}

	document.addEventListener('DOMContentLoaded', function () {
		var toggle = document.getElementById('nxc-scheme');
		if (toggle) {
			toggle.setAttribute('aria-pressed', isDark() ? 'true' : 'false');
			toggle.addEventListener('click', function () {
				var next = isDark() ? 'light' : 'dark';
				applyScheme(next);
				toggle.setAttribute('aria-pressed', 'dark' === next ? 'true' : 'false');
				try {
					window.localStorage.setItem(schemeKey, next);
				} catch (error) {
					// Not remembered when storage is blocked.
				}
			});
		}
	});

	window.NXC = { api: api, copy: copy, daysUntil: daysUntil, dialog: dialog, esc: esc, fmtDate: fmtDate, fmtDateTime: fmtDateTime, pill: pill, relative: relative, router: router, toast: toast };
}());
