/* MSP Nexus licensing: customer portal. */
(function () {
	'use strict';

	var N = window.NXC;
	var esc = N.esc;

	function siteRows(license) {
		var sites = license.activations.filter(function (item) {
			return !item.deactivated_at;
		});
		if (!sites.length) {
			return '<tr><td colspan="4"><div class="nxc-empty">No sites yet. Enter your license key under MSP Nexus → Tools → License &amp; updates on your site.</div></td></tr>';
		}
		return sites.map(function (a) {
			return '<tr><td><span class="nxc-strong">' + esc(a.site) + '</span><span class="nxc-sub">' + (a.counts_toward_limit ? 'Production' : 'Staging (free)') + '</span></td>' +
				'<td class="nxc-hide-sm">' + esc(N.fmtDate(a.activated_at)) + '</td><td class="nxc-hide-sm">' + esc(N.relative(a.last_seen_at)) + '</td>' +
				'<td><button type="button" class="nxc-btn nxc-btn--small" data-action="transfer" data-license="' + esc(license.id) + '" data-activation="' + esc(a.id) + '" data-site="' + esc(a.site) + '">Move to new address</button></td></tr>';
		}).join('');
	}

	function licenses() {
		return N.api('GET', '/v1/customer/licenses').then(function (payload) {
			var cards = payload.licenses.map(function (license) {
				var notice = '';
				if ('grace' === license.status) {
					notice = '<div class="nxc-alert nxc-alert--warn">Your license expired on ' + esc(N.fmtDate(license.expires_at)) + '. Updates continue until ' + esc(N.fmtDate(license.grace_ends_at)) + '. Contact us to renew.</div>';
				} else if (['expired', 'suspended', 'cancelled', 'revoked'].indexOf(license.status) >= 0) {
					notice = '<div class="nxc-alert nxc-alert--error">This license is ' + esc(license.status) + ', so your sites are not receiving updates. Your websites keep working. Contact us to restore it.</div>';
				}
				return '<section class="nxc-card nxc-card--flush"><h2>' + esc(license.plan) + ' plan ' + N.pill(license.status) + '</h2><p>' + esc(license.active_production_activations + ' of ' + license.activation_limit) + ' production sites used · renews or expires ' + esc(N.fmtDate(license.expires_at)) + '</p>' +
					(notice ? '<div class="nxc-pad">' + notice + '</div>' : '') +
					'<div class="nxc-table-wrap"><table class="nxc-table"><thead><tr><th>Site</th><th class="nxc-hide-sm">Activated</th><th class="nxc-hide-sm">Last check-in</th><th><span class="nxc-sr">Actions</span></th></tr></thead><tbody>' + siteRows(license) + '</tbody></table></div></section>';
			}).join('');
			return '<div class="nxc-page-head"><div><h1>My licenses</h1><p>Your plan, its expiry date, and the sites using it.</p></div><button type="button" class="nxc-btn" id="nxc-export">Download my data</button></div><div class="nxc-stack">' + cards + '</div>';
		});
	}

	function bindLicenses(main) {
		main.addEventListener('click', function (event) {
			if (event.target.closest('#nxc-export')) {
				N.api('GET', '/v1/customer/export').then(function (payload) {
					var link = document.createElement('a');
					link.href = URL.createObjectURL(new window.Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' }));
					link.download = 'msp-nexus-license-data.json';
					document.body.appendChild(link);
					link.click();
					link.remove();
				}, function (error) {
					N.toast(error.message, true);
				});
				return;
			}
			var button = event.target.closest('[data-action="transfer"]');
			if (!button) {
				return;
			}
			N.dialog({ title: 'Move site', confirm: 'Move', body: '<p class="nxc-muted nxc-m0">Moves your license from <strong>' + esc(button.getAttribute('data-site')) + '</strong> to a new address, for example after a domain change.</p><div class="nxc-field"><label for="nxc-site">New site address</label><input id="nxc-site" name="site" type="url" required placeholder="https://www.example.com"></div>' }).then(function (data) {
				if (data) {
					N.api('POST', '/v1/customer/activations/transfer', { license_id: button.getAttribute('data-license'), activation_id: button.getAttribute('data-activation'), site_url: data.get('site') }).then(function () {
						N.toast('Site moved.');
						window.dispatchEvent(new window.HashChangeEvent('hashchange'));
					}, function (error) {
						N.toast(error.message, true);
					});
				}
			});
		});
	}

	function privacy() {
		return '<div class="nxc-page-head"><div><h1>Privacy requests</h1><p>Ask for a copy of your data, a correction, or deletion. We reply by email.</p></div></div>' +
			'<form class="nxc-card nxc-max" id="nxc-privacy"><div class="nxc-form">' +
			'<div class="nxc-field"><label for="p-action">Request</label><select id="p-action" name="action"><option value="export">A copy of my data</option><option value="correction">Correct my data</option><option value="deletion">Delete my data</option></select></div>' +
			'<div class="nxc-field"><label for="p-note">Details (optional)</label><textarea id="p-note" name="note" maxlength="1000"></textarea></div>' +
			'<div class="nxc-actions"><button type="submit" class="nxc-btn nxc-btn--primary">Send request</button></div></div></form>';
	}

	function bindPrivacy(main) {
		var form = main.querySelector('#nxc-privacy');
		if (!form) {
			return;
		}
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			var data = new window.FormData(form);
			N.api('POST', '/v1/customer/data-requests', { action: data.get('action'), note: data.get('note') }).then(function (result) {
				form.outerHTML = '<div class="nxc-card nxc-max"><div class="nxc-alert nxc-alert--good nxc-m0">Request received. Reference <code>' + esc(result.request_id) + '</code>.</div></div>';
			}, function (error) {
				N.toast(error.message, true);
			});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		var bind = null;
		N.router([
			[/^\/$/, function () {
				bind = bindLicenses;
				return licenses();
			}],
			[/^\/privacy$/, function () {
				bind = bindPrivacy;
				return privacy();
			}]
		], function (main) {
			var fresh = main.cloneNode(true);
			main.parentNode.replaceChild(fresh, main);
			if (bind) {
				bind(fresh);
			}
		});
	});
}());
