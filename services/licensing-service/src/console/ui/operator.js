/* MSP Nexus licensing: operator console. */
(function () {
	'use strict';

	var N = window.NXC;
	var esc = N.esc;
	var ENTITLEMENTS = [
		['theme_updates', 'Theme updates'],
		['plugin_updates', 'Plugin updates'],
		['demo_library', 'Demo library'],
		['priority_support', 'Priority support']
	];
	var PLANS = ['starter', 'business', 'agency'];
	var cache = { licenses: null };

	function loadLicenses(force) {
		if (cache.licenses && !force) {
			return Promise.resolve(cache.licenses);
		}
		return N.api('GET', '/v1/operator/licenses').then(function (payload) {
			cache.licenses = payload.licenses;
			return cache.licenses;
		});
	}

	function customerLabel(license) {
		return license.customer_name || license.customer_email || 'Unnamed customer';
	}

	function sitesSummary(license) {
		var staging = license.active_staging_activations ? ' + ' + license.active_staging_activations + ' staging' : '';
		return license.active_production_activations + ' / ' + license.activation_limit + staging;
	}

	function expiryText(license) {
		var days = N.daysUntil(license.expires_at);
		var when = days >= 0 ? 'in ' + days + ' day' + (1 === days ? '' : 's') : Math.abs(days) + ' days ago';
		return N.fmtDate(license.expires_at) + '<span class="nxc-sub">' + esc(when) + '</span>';
	}

	function describe(event) {
		var labels = {
			'license.issued': 'License issued',
			'license.activated': 'Site activated',
			'license.deactivated': 'Site deactivated',
			'license.renewed': 'License renewed',
			'license.plan_changed': 'Plan changed',
			'license.details_changed': 'Customer details updated',
			'activation.transferred': 'Site moved',
			'release.upserted': 'Release saved',
			'release.status_changed': 'Release status changed',
			'privacy.requested': 'Privacy request received'
		};
		var label = 'license.status_changed' === event.action ? 'Status changed to ' + event.status : (labels[event.action] || event.action);
		var detail = [];
		if (event.site) {
			detail.push(event.site);
		}
		if (event.previousSite) {
			detail.push('from ' + event.previousSite);
		}
		if (event.plan) {
			detail.push((event.previousPlan ? event.previousPlan + ' → ' : '') + event.plan);
		}
		if (event.expiresAt) {
			detail.push('until ' + N.fmtDate(event.expiresAt));
		}
		if (event.product) {
			detail.push(event.product + ' ' + event.version + ' (' + event.channel + (event.status ? ', ' + event.status : '') + ')');
		}
		if (event.requestType) {
			detail.push(event.requestType);
		}
		return { label: label, detail: detail.join(' · ') };
	}

	function activityList(events, licenses) {
		if (!events.length) {
			return '<div class="nxc-empty">No activity yet.</div>';
		}
		var byId = {};
		(licenses || []).forEach(function (license) {
			byId[license.id] = license;
		});
		return '<ul class="nxc-list">' + events.map(function (event) {
			var text = describe(event);
			var license = event.licenseId && byId[event.licenseId];
			var who = license ? ' · <a href="#/licenses/' + esc(license.id) + '">' + esc(customerLabel(license)) + '</a>' : '';
			return '<li><span><strong>' + esc(text.label) + '</strong>' + who + (text.detail ? '<span class="nxc-sub nxc-muted">' + esc(text.detail) + '</span>' : '') + '</span><span class="nxc-muted" title="' + esc(N.fmtDateTime(event.at)) + '">' + esc(N.relative(event.at)) + '</span></li>';
		}).join('') + '</ul>';
	}

	/* ---------- Dashboard ---------- */

	function dashboard() {
		return Promise.all([loadLicenses(true), N.api('GET', '/v1/operator/audit?limit=8')]).then(function (results) {
			var licenses = results[0];
			var events = results[1].events;
			var count = function (test) {
				return licenses.filter(test).length;
			};
			var active = count(function (l) {
				return 'active' === l.status;
			});
			var expiring = licenses.filter(function (l) {
				var days = N.daysUntil(l.expires_at);
				// Active only: grace and expired licenses are counted once, under lapsed.
				return 'active' === l.status && days <= 30;
			}).sort(function (a, b) {
				return new Date(a.expires_at) - new Date(b.expires_at);
			});
			var lapsed = count(function (l) {
				return 'grace' === l.status || 'expired' === l.status;
			});
			var blocked = count(function (l) {
				return ['suspended', 'revoked', 'cancelled'].indexOf(l.status) >= 0;
			});
			var sites = licenses.reduce(function (sum, l) {
				return sum + l.active_production_activations;
			}, 0);
			var expiringList = expiring.length ? '<ul class="nxc-list">' + expiring.slice(0, 8).map(function (l) {
				return '<li><a href="#/licenses/' + esc(l.id) + '">' + esc(customerLabel(l)) + '</a><span>' + N.pill(l.status) + ' <span class="nxc-muted">' + esc(N.fmtDate(l.expires_at)) + '</span></span></li>';
			}).join('') + '</ul>' : '<div class="nxc-empty">Nothing expires in the next 30 days.</div>';
			return '<div class="nxc-page-head"><div><h1>Dashboard</h1><p>Licenses, sites, and recent changes at a glance.</p></div><a class="nxc-btn nxc-btn--primary" href="#/new">New license</a></div>' +
				'<div class="nxc-kpis">' +
				'<div class="nxc-kpi nxc-kpi--good"><span class="nxc-kpi__value">' + active + '</span><span class="nxc-kpi__label">Active licenses</span></div>' +
				'<div class="nxc-kpi"><span class="nxc-kpi__value">' + sites + '</span><span class="nxc-kpi__label">Production sites in use</span></div>' +
				'<div class="nxc-kpi nxc-kpi--warn"><span class="nxc-kpi__value">' + (expiring.length + lapsed) + '</span><span class="nxc-kpi__label">Expiring soon or lapsed</span></div>' +
				'<div class="nxc-kpi nxc-kpi--bad"><span class="nxc-kpi__value">' + blocked + '</span><span class="nxc-kpi__label">Suspended, revoked, or cancelled</span></div>' +
				'</div>' +
				'<div class="nxc-grid"><section class="nxc-card nxc-span-6"><h2>Expiring in the next 30 days</h2><p>Renew or follow up before updates stop.</p>' + expiringList + '</section>' +
				'<section class="nxc-card nxc-span-6"><h2>Recent activity</h2><p>The latest license and release changes.</p>' + activityList(events, licenses) + '<p><a href="#/activity">All activity →</a></p></section></div>';
		});
	}

	/* ---------- License list ---------- */

	function licenseRows(licenses) {
		if (!licenses.length) {
			return '<tr><td colspan="5"><div class="nxc-empty">No licenses match.</div></td></tr>';
		}
		return licenses.map(function (l) {
			return '<tr class="is-link" data-href="#/licenses/' + esc(l.id) + '"><td><a class="nxc-strong" href="#/licenses/' + esc(l.id) + '">' + esc(customerLabel(l)) + '</a><span class="nxc-sub">' + esc(l.customer_email || l.id) + '</span></td>' +
				'<td>' + esc(l.plan) + '</td><td>' + N.pill(l.status) + '</td><td class="nxc-hide-sm">' + esc(sitesSummary(l)) + '</td><td>' + expiryText(l) + '</td></tr>';
		}).join('');
	}

	function licenses() {
		return loadLicenses(true).then(function (list) {
			return '<div class="nxc-page-head"><div><h1>Licenses</h1><p>' + list.length + ' license' + (1 === list.length ? '' : 's') + '. Select one to manage it.</p></div><a class="nxc-btn nxc-btn--primary" href="#/new">New license</a></div>' +
				'<section class="nxc-card nxc-card--flush"><div class="nxc-toolbar"><label class="nxc-sr" for="nxc-filter">Search licenses</label><input class="nxc-input" id="nxc-filter" type="search" placeholder="Search by customer, email, plan, site, or ID">' +
				'<label class="nxc-sr" for="nxc-status">Status</label><select id="nxc-status" class="nxc-narrow"><option value="">All statuses</option><option>active</option><option>grace</option><option>expired</option><option>suspended</option><option>cancelled</option><option>revoked</option></select></div>' +
				'<div class="nxc-table-wrap"><table class="nxc-table"><thead><tr><th>Customer</th><th>Plan</th><th>Status</th><th class="nxc-hide-sm">Sites</th><th>Expires</th></tr></thead><tbody id="nxc-rows">' + licenseRows(list) + '</tbody></table></div></section>';
		});
	}

	function bindLicenseList(main) {
		var filter = main.querySelector('#nxc-filter');
		var status = main.querySelector('#nxc-status');
		var rows = main.querySelector('#nxc-rows');
		if (!filter || !rows) {
			return;
		}
		var apply = function () {
			var query = filter.value.trim().toLowerCase();
			var wanted = status.value;
			rows.innerHTML = licenseRows(cache.licenses.filter(function (l) {
				var haystack = [l.customer_name, l.customer_email, l.plan, l.id, l.notes].concat(l.activations.map(function (a) {
					return a.site;
				})).join(' ').toLowerCase();
				return (!query || haystack.indexOf(query) >= 0) && (!wanted || wanted === l.status);
			}));
		};
		filter.addEventListener('input', apply);
		status.addEventListener('change', apply);
	}

	/* ---------- License detail ---------- */

	function siteRows(license) {
		if (!license.activations.length) {
			return '<tr><td colspan="5"><div class="nxc-empty">No sites have activated this license yet.</div></td></tr>';
		}
		return license.activations.slice().sort(function (a, b) {
			return (a.deactivated_at ? 1 : 0) - (b.deactivated_at ? 1 : 0);
		}).map(function (a) {
			var state = a.deactivated_at ? '<span class="nxc-pill">deactivated</span>' : N.pill('active');
			var actions = a.deactivated_at ? '' : '<div class="nxc-actions"><button type="button" class="nxc-btn nxc-btn--small" data-action="transfer" data-activation="' + esc(a.id) + '" data-site="' + esc(a.site) + '">Move</button><button type="button" class="nxc-btn nxc-btn--small nxc-btn--danger" data-action="deactivate" data-activation="' + esc(a.id) + '" data-site="' + esc(a.site) + '">Deactivate</button></div>';
			return '<tr><td><span class="nxc-strong">' + esc(a.site) + '</span><span class="nxc-sub">' + (a.counts_toward_limit ? 'Production' : 'Staging (free)') + (a.is_multisite ? ' · Multisite' : '') + '</span></td>' +
				'<td>' + state + '</td><td class="nxc-hide-sm">' + esc(N.fmtDate(a.activated_at)) + '</td><td class="nxc-hide-sm" title="' + esc(N.fmtDateTime(a.last_seen_at)) + '">' + esc(N.relative(a.last_seen_at)) + '</td><td>' + actions + '</td></tr>';
		}).join('');
	}

	function statusButtons(license) {
		var admin = license.administrative_status;
		var buttons = [];
		if ('active' !== admin) {
			buttons.push('<button type="button" class="nxc-btn" data-action="status" data-status="active">Reinstate</button>');
		}
		if ('active' === admin) {
			buttons.push('<button type="button" class="nxc-btn" data-action="status" data-status="suspended">Suspend</button>');
		}
		if ('cancelled' !== admin && 'revoked' !== admin) {
			buttons.push('<button type="button" class="nxc-btn" data-action="status" data-status="cancelled">Cancel</button>');
		}
		if ('revoked' !== admin) {
			buttons.push('<button type="button" class="nxc-btn nxc-btn--danger" data-action="status" data-status="revoked">Revoke</button>');
		}
		return buttons.join('');
	}

	function licenseDetail(id) {
		return Promise.all([loadLicenses(true), N.api('GET', '/v1/operator/audit?limit=500')]).then(function (results) {
			var license = results[0].filter(function (l) {
				return l.id === id;
			})[0];
			if (!license) {
				throw new Error('That license was not found.');
			}
			var events = results[1].events.filter(function (event) {
				return event.licenseId === id;
			}).slice(0, 15);
			var entitlements = license.entitlements.length ? license.entitlements.map(function (e) {
				return '<span class="nxc-pill">' + esc(e.replace(/_/g, ' ')) + '</span>';
			}).join(' ') : '<span class="nxc-muted">None</span>';
			var notice = '';
			if ('grace' === license.status) {
				notice = '<div class="nxc-alert nxc-alert--warn">This license expired on ' + esc(N.fmtDate(license.expires_at)) + ' and is in its grace period until ' + esc(N.fmtDate(license.grace_ends_at)) + '. Renew it to keep updates flowing.</div>';
			} else if ('expired' === license.status) {
				notice = '<div class="nxc-alert nxc-alert--error">This license expired. Sites keep working but no longer receive updates.</div>';
			}
			return '<p class="nxc-crumbs"><a href="#/licenses">Licenses</a> / ' + esc(customerLabel(license)) + '</p>' +
				'<div class="nxc-page-head"><div><h1>' + esc(customerLabel(license)) + ' ' + N.pill(license.status) + '</h1><p>' + esc(license.plan) + ' plan · ' + esc(sitesSummary(license)) + ' sites · expires ' + esc(N.fmtDate(license.expires_at)) + '</p></div>' +
				'<div class="nxc-actions"><button type="button" class="nxc-btn nxc-btn--primary" data-action="renew">Renew</button><button type="button" class="nxc-btn" data-action="plan">Change plan</button><button type="button" class="nxc-btn" data-action="details">Edit customer</button></div></div>' +
				notice +
				'<div class="nxc-grid">' +
				'<section class="nxc-card nxc-span-8 nxc-card--flush"><h2>Sites</h2><p>Each production site uses one slot. Staging sites are free when the plan allows it.</p><div class="nxc-table-wrap"><table class="nxc-table"><thead><tr><th>Site</th><th>Status</th><th class="nxc-hide-sm">Activated</th><th class="nxc-hide-sm">Last check-in</th><th><span class="nxc-sr">Actions</span></th></tr></thead><tbody>' + siteRows(license) + '</tbody></table></div></section>' +
				'<section class="nxc-card nxc-span-4"><h2>Customer</h2><p>For your records only.</p><dl class="nxc-dl"><dt>Name</dt><dd>' + esc(license.customer_name || '—') + '</dd><dt>Email</dt><dd>' + (license.customer_email ? '<a href="mailto:' + esc(license.customer_email) + '">' + esc(license.customer_email) + '</a>' : '—') + '</dd><dt>Notes</dt><dd class="nxc-notes">' + esc(license.notes || '—') + '</dd></dl></section>' +
				'<section class="nxc-card nxc-span-6"><h2>License</h2><p>What this license allows.</p><dl class="nxc-dl">' +
				'<dt>License ID</dt><dd><code>' + esc(license.id) + '</code></dd>' +
				'<dt>Plan</dt><dd>' + esc(license.plan) + '</dd>' +
				'<dt>Production sites</dt><dd>' + esc(license.active_production_activations + ' of ' + license.activation_limit) + '</dd>' +
				'<dt>Free staging sites</dt><dd>' + (license.allow_free_staging ? 'Yes' : 'No') + '</dd>' +
				'<dt>Multisite</dt><dd>' + (license.allow_multisite ? 'Allowed' : 'Not allowed') + '</dd>' +
				'<dt>Includes</dt><dd>' + entitlements + '</dd>' +
				'<dt>Issued</dt><dd>' + esc(N.fmtDate(license.issued_at)) + '</dd>' +
				'<dt>Expires</dt><dd>' + esc(N.fmtDate(license.expires_at)) + '</dd>' +
				'<dt>Grace period ends</dt><dd>' + esc(N.fmtDate(license.grace_ends_at)) + '</dd>' +
				'<dt>Set status</dt><dd>' + esc(license.administrative_status) + '</dd></dl>' +
				'<h2 class="nxc-subhead">Change status</h2><p class="nxc-help nxc-help--spaced">Suspended and cancelled licenses stop receiving updates; revoked licenses are permanently blocked. Public websites keep working either way.</p><div class="nxc-actions">' + statusButtons(license) + '</div></section>' +
				'<section class="nxc-card nxc-span-6"><h2>Activity</h2><p>Changes to this license, newest first.</p>' + activityList(events) + '</section>' +
				'</div>';
		});
	}

	function bindLicenseDetail(main, id) {
		var license = (cache.licenses || []).filter(function (l) {
			return l.id === id;
		})[0];
		if (!license) {
			return;
		}
		var refresh = function (message) {
			cache.licenses = null;
			N.toast(message);
			window.dispatchEvent(new window.HashChangeEvent('hashchange'));
		};
		var fail = function (error) {
			N.toast(error.message, true);
		};
		main.addEventListener('click', function (event) {
			var button = event.target.closest('[data-action]');
			if (!button) {
				return;
			}
			var action = button.getAttribute('data-action');
			if ('renew' === action) {
				var base = new Date(Math.max(Date.now(), new Date(license.expires_at).getTime()));
				base.setFullYear(base.getFullYear() + 1);
				N.dialog({ title: 'Renew license', confirm: 'Renew', body: '<div class="nxc-field"><label for="nxc-renew">New expiry date</label><input id="nxc-renew" name="expires" type="date" required value="' + base.toISOString().slice(0, 10) + '"><p class="nxc-help">Renewing also sets the license back to active.</p></div>' }).then(function (data) {
					if (data) {
						N.api('POST', '/v1/operator/licenses/renew', { license_id: id, expires_at: new Date(data.get('expires') + 'T23:59:59Z').toISOString() }).then(function () {
							refresh('License renewed.');
						}, fail);
					}
				});
			} else if ('plan' === action) {
				var checks = ENTITLEMENTS.map(function (item) {
					return '<label class="nxc-check"><input type="checkbox" name="entitlements" value="' + item[0] + '"' + (license.entitlements.indexOf(item[0]) >= 0 ? ' checked' : '') + '> ' + esc(item[1]) + '</label>';
				}).join('');
				N.dialog({ title: 'Change plan', body: '<div class="nxc-field"><label for="nxc-plan">Plan</label><input id="nxc-plan" name="plan" type="text" required list="nxc-plans" value="' + esc(license.plan) + '"><datalist id="nxc-plans">' + PLANS.map(function (p) {
					return '<option value="' + p + '">';
				}).join('') + '</datalist></div><div class="nxc-field"><label for="nxc-limit">Production sites</label><input id="nxc-limit" name="limit" type="number" min="1" max="1000" required value="' + esc(license.activation_limit) + '"></div><div class="nxc-field"><span class="nxc-label">Includes</span><div class="nxc-checks">' + checks + '</div></div>' }).then(function (data) {
					if (data) {
						N.api('POST', '/v1/operator/licenses/plan', { license_id: id, plan: data.get('plan'), activation_limit: Number(data.get('limit')), entitlements: data.getAll('entitlements') }).then(function () {
							refresh('Plan updated.');
						}, fail);
					}
				});
			} else if ('details' === action) {
				N.dialog({ title: 'Edit customer', body: '<div class="nxc-field"><label for="nxc-name">Customer name</label><input id="nxc-name" name="name" type="text" maxlength="200" value="' + esc(license.customer_name) + '"></div><div class="nxc-field"><label for="nxc-email">Email</label><input id="nxc-email" name="email" type="email" maxlength="254" value="' + esc(license.customer_email) + '"></div><div class="nxc-field"><label for="nxc-notes">Notes</label><textarea id="nxc-notes" name="notes" maxlength="2000">' + esc(license.notes) + '</textarea></div>' }).then(function (data) {
					if (data) {
						N.api('POST', '/v1/operator/licenses/details', { license_id: id, customer_name: data.get('name'), customer_email: data.get('email'), notes: data.get('notes') }).then(function () {
							refresh('Customer details saved.');
						}, fail);
					}
				});
			} else if ('status' === action) {
				var status = button.getAttribute('data-status');
				var copy = {
					active: ['Reinstate this license?', 'Updates resume for its sites.', 'Reinstate', false],
					suspended: ['Suspend this license?', 'Its sites stop receiving updates until you reinstate it. Websites keep working.', 'Suspend', false],
					cancelled: ['Cancel this license?', 'Use this when the customer stops paying. Updates stop; you can reinstate later.', 'Cancel license', true],
					revoked: ['Revoke this license?', 'Revoking permanently blocks the key, for example after abuse or a chargeback. You can still reinstate it here if it was a mistake.', 'Revoke', true]
				}[status];
				N.dialog({ title: copy[0], confirm: copy[2], danger: copy[3], body: '<p class="nxc-muted nxc-m0">' + esc(copy[1]) + '</p>' }).then(function (data) {
					if (data) {
						N.api('POST', '/v1/operator/licenses/status', { license_id: id, status: status }).then(function () {
							refresh('Status set to ' + status + '.');
						}, fail);
					}
				});
			} else if ('transfer' === action) {
				N.dialog({ title: 'Move site', confirm: 'Move', body: '<p class="nxc-muted nxc-m0">Moves the activation from <strong>' + esc(button.getAttribute('data-site')) + '</strong> to a new address, for example after a domain change.</p><div class="nxc-field"><label for="nxc-site">New site address</label><input id="nxc-site" name="site" type="url" required placeholder="https://www.example.com"></div>' }).then(function (data) {
					if (data) {
						N.api('POST', '/v1/operator/activations/transfer', { license_id: id, activation_id: button.getAttribute('data-activation'), site_url: data.get('site') }).then(function () {
							refresh('Site moved.');
						}, fail);
					}
				});
			} else if ('deactivate' === action) {
				N.dialog({ title: 'Deactivate site?', confirm: 'Deactivate', danger: true, body: '<p class="nxc-muted nxc-m0"><strong>' + esc(button.getAttribute('data-site')) + '</strong> stops receiving updates and frees its slot. The site itself keeps working.</p>' }).then(function (data) {
					if (data) {
						N.api('POST', '/v1/operator/activations/deactivate', { license_id: id, activation_id: button.getAttribute('data-activation') }).then(function () {
							refresh('Site deactivated.');
						}, fail);
					}
				});
			}
		});
	}

	/* ---------- New license ---------- */

	function newLicense() {
		var nextYear = new Date();
		nextYear.setFullYear(nextYear.getFullYear() + 1);
		var checks = ENTITLEMENTS.map(function (item) {
			return '<label class="nxc-check"><input type="checkbox" name="entitlements" value="' + item[0] + '"' + ('priority_support' === item[0] ? '' : ' checked') + '> ' + esc(item[1]) + '</label>';
		}).join('');
		return '<div class="nxc-page-head"><div><h1>New license</h1><p>Creates a license key and a customer portal token. Both are shown once.</p></div></div>' +
			'<form class="nxc-card" id="nxc-new"><div class="nxc-form-grid">' +
			'<div class="nxc-field"><label for="f-name">Customer name</label><input id="f-name" name="customer_name" type="text" maxlength="200" placeholder="Acme IT Services"></div>' +
			'<div class="nxc-field"><label for="f-email">Customer email</label><input id="f-email" name="customer_email" type="email" maxlength="254" placeholder="owner@example.com"></div>' +
			'<div class="nxc-field"><label for="f-plan">Plan</label><input id="f-plan" name="plan" type="text" required list="f-plans" value="business"><datalist id="f-plans">' + PLANS.map(function (p) {
				return '<option value="' + p + '">';
			}).join('') + '</datalist></div>' +
			'<div class="nxc-field"><label for="f-limit">Production sites</label><input id="f-limit" name="activation_limit" type="number" min="1" max="1000" required value="1"></div>' +
			'<div class="nxc-field"><label for="f-expires">Expires</label><input id="f-expires" name="expires" type="date" required value="' + nextYear.toISOString().slice(0, 10) + '"></div>' +
			'<div class="nxc-field"><label for="f-grace">Grace period (days)</label><input id="f-grace" name="grace_days" type="number" min="0" max="90" value="14"><p class="nxc-help">Updates keep working this long after expiry.</p></div>' +
			'<div class="nxc-field nxc-field--wide"><span class="nxc-label">Includes</span><div class="nxc-checks">' + checks + '</div></div>' +
			'<div class="nxc-field nxc-field--wide"><span class="nxc-label">Site rules</span><div class="nxc-checks"><label class="nxc-check"><input type="checkbox" name="allow_free_staging" checked> Staging sites are free</label><label class="nxc-check"><input type="checkbox" name="allow_multisite"> Allow WordPress multisite</label></div></div>' +
			'<div class="nxc-field nxc-field--wide"><label for="f-notes">Notes</label><textarea id="f-notes" name="notes" maxlength="2000" placeholder="Order number, how they found you, anything useful later."></textarea></div>' +
			'</div><div class="nxc-actions nxc-mt"><button type="submit" class="nxc-btn nxc-btn--primary">Create license</button><a class="nxc-btn" href="#/licenses">Cancel</a></div></form>';
	}

	function bindNewLicense(main) {
		var form = main.querySelector('#nxc-new');
		if (!form) {
			return;
		}
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			var data = new window.FormData(form);
			var button = form.querySelector('[type="submit"]');
			button.disabled = true;
			N.api('POST', '/v1/operator/licenses', {
				customer_name: data.get('customer_name'),
				customer_email: data.get('customer_email'),
				notes: data.get('notes'),
				plan: data.get('plan'),
				activation_limit: Number(data.get('activation_limit')),
				expires_at: new Date(data.get('expires') + 'T23:59:59Z').toISOString(),
				grace_days: Number(data.get('grace_days')),
				allow_free_staging: !!data.get('allow_free_staging'),
				allow_multisite: !!data.get('allow_multisite'),
				entitlements: data.getAll('entitlements')
			}).then(function (result) {
				cache.licenses = null;
				main.innerHTML = '<div class="nxc-page-head"><div><h1>License created</h1><p>' + esc(customerLabel(result.license)) + ' · ' + esc(result.license.plan) + ' plan</p></div></div>' +
					'<section class="nxc-card nxc-max"><div class="nxc-alert nxc-alert--warn"><strong>Copy these now.</strong> They are shown only once and cannot be recovered; only their fingerprints are stored.</div>' +
					'<label>License key</label><p class="nxc-help">The customer enters this under MSP Nexus → Tools → License &amp; updates.</p><div class="nxc-secret"><code id="nxc-key">' + esc(result.license_key) + '</code><button type="button" class="nxc-btn nxc-btn--small" data-copy="nxc-key">Copy</button></div>' +
					'<label>Customer portal token</label><p class="nxc-help">Signs the customer in at <code>/portal</code> to see their sites and move them.</p><div class="nxc-secret"><code id="nxc-token">' + esc(result.portal_access_token) + '</code><button type="button" class="nxc-btn nxc-btn--small" data-copy="nxc-token">Copy</button></div>' +
					'<div class="nxc-actions"><a class="nxc-btn nxc-btn--primary" href="#/licenses/' + esc(result.license.id) + '">Open license</a><a class="nxc-btn" href="#/new">Create another</a></div></section>';
				main.addEventListener('click', function (clickEvent) {
					var copyButton = clickEvent.target.closest('[data-copy]');
					if (copyButton) {
						N.copy(document.getElementById(copyButton.getAttribute('data-copy')).textContent);
					}
				});
			}, function (error) {
				button.disabled = false;
				N.toast(error.message, true);
			});
		});
	}

	/* ---------- Releases ---------- */

	function releases() {
		return N.api('GET', '/v1/operator/releases').then(function (payload) {
			var list = payload.releases.slice().sort(function (a, b) {
				return a.product.localeCompare(b.product) || b.version.localeCompare(a.version, undefined, { numeric: true });
			});
			var rows = list.length ? list.map(function (r) {
				var next = [];
				if ('published' !== r.status) {
					next.push(['published', 'Publish']);
				}
				if ('published' === r.status) {
					next.push(['paused', 'Pause']);
				}
				if ('withdrawn' !== r.status) {
					next.push(['withdrawn', 'Withdraw']);
				}
				var buttons = next.map(function (pair) {
					return '<button type="button" class="nxc-btn nxc-btn--small' + ('withdrawn' === pair[0] ? ' nxc-btn--danger' : '') + '" data-release-status="' + pair[0] + '" data-product="' + esc(r.product) + '" data-version="' + esc(r.version) + '" data-channel="' + esc(r.channel) + '">' + pair[1] + '</button>';
				}).join('');
				var packaged = /^[a-f0-9]{64}$/.test(r.sha256) && r.size > 0;
				return '<tr><td><span class="nxc-strong">' + esc(r.product) + ' ' + esc(r.version) + '</span><span class="nxc-sub">' + esc(r.productType || '') + ' · ' + esc(r.file || '') + '</span></td><td>' + esc(r.channel) + '</td><td>' + N.pill(r.status) + '</td>' +
					'<td class="nxc-hide-sm">' + esc(r.rolloutPercent) + '%</td><td class="nxc-hide-sm">' + (packaged ? esc((r.size / 1048576).toFixed(2) + ' MB') : '<span class="nxc-pill nxc-pill--draft">no package</span>') + '</td><td><div class="nxc-actions">' + buttons + '</div></td></tr>';
			}).join('') : '<tr><td colspan="6"><div class="nxc-empty">No releases yet.</div></td></tr>';
			return '<div class="nxc-page-head"><div><h1>Releases</h1><p>What licensed sites are offered. Only published releases with a package are delivered.</p></div><button type="button" class="nxc-btn nxc-btn--primary" id="nxc-add-release">Add release</button></div>' +
				'<section class="nxc-card nxc-card--flush"><div class="nxc-table-wrap"><table class="nxc-table"><thead><tr><th>Release</th><th>Channel</th><th>Status</th><th class="nxc-hide-sm">Rollout</th><th class="nxc-hide-sm">Package</th><th><span class="nxc-sr">Actions</span></th></tr></thead><tbody>' + rows + '</tbody></table></div></section>';
		});
	}

	function bindReleases(main) {
		var reload = function (message) {
			N.toast(message);
			window.dispatchEvent(new window.HashChangeEvent('hashchange'));
		};
		main.addEventListener('click', function (event) {
			var statusButton = event.target.closest('[data-release-status]');
			if (statusButton) {
				var status = statusButton.getAttribute('data-release-status');
				N.dialog({ title: statusButton.textContent + ' ' + statusButton.getAttribute('data-product') + ' ' + statusButton.getAttribute('data-version') + '?', confirm: statusButton.textContent, danger: 'withdrawn' === status, body: '<p class="nxc-muted nxc-m0">' + ('published' === status ? 'Licensed sites on this channel will be offered this version.' : 'Sites stop being offered this version.') + '</p>' }).then(function (data) {
					if (data) {
						N.api('POST', '/v1/operator/releases/status', { product: statusButton.getAttribute('data-product'), version: statusButton.getAttribute('data-version'), channel: statusButton.getAttribute('data-channel'), status: status }).then(function () {
							reload('Release updated.');
						}, function (error) {
							N.toast(error.message, true);
						});
					}
				});
				return;
			}
			if (event.target.closest('#nxc-add-release')) {
				N.dialog({
					title: 'Add release',
					body: '<div class="nxc-form-grid">' +
						'<div class="nxc-field"><label for="r-product">Product</label><select id="r-product" name="product"><option value="msp-nexus">Theme (msp-nexus)</option><option value="msp-nexus-core">Plugin (msp-nexus-core)</option></select></div>' +
						'<div class="nxc-field"><label for="r-version">Version</label><input id="r-version" name="version" type="text" required pattern="\\d+\\.\\d+\\.\\d+.*" placeholder="1.0.0"></div>' +
						'<div class="nxc-field"><label for="r-channel">Channel</label><select id="r-channel" name="channel"><option>stable</option><option>beta</option><option>development</option></select></div>' +
						'<div class="nxc-field"><label for="r-status">Status</label><select id="r-status" name="status"><option value="draft">Draft</option><option value="published">Published</option></select></div>' +
						'<div class="nxc-field nxc-field--wide"><label for="r-sha">SHA-256</label><input id="r-sha" name="sha256" type="text" required pattern="[a-fA-F0-9]{64}" placeholder="64 hex characters from release-manifest.json"></div>' +
						'<div class="nxc-field"><label for="r-size">Size (bytes)</label><input id="r-size" name="size" type="number" min="1" required></div>' +
						'<div class="nxc-field"><label for="r-rollout">Rollout (%)</label><input id="r-rollout" name="rollout" type="number" min="0" max="100" value="100"></div>' +
						'<div class="nxc-field nxc-field--wide"><label for="r-notes">Changelog link</label><input id="r-notes" name="changelog" type="url" placeholder="https://"></div>' +
						'</div><p class="nxc-help">The package file must exist in the server package folder as <code>product-version.zip</code>.</p>'
				}).then(function (data) {
					if (!data) {
						return;
					}
					var product = data.get('product');
					var isTheme = 'msp-nexus' === product;
					N.api('POST', '/v1/operator/releases', {
						product: product,
						product_type: isTheme ? 'theme' : 'plugin',
						slug: isTheme ? 'msp-nexus' : 'msp-nexus-core/msp-nexus-core.php',
						version: data.get('version'),
						channel: data.get('channel'),
						status: data.get('status'),
						sha256: data.get('sha256'),
						size: Number(data.get('size')),
						file: product + '-' + data.get('version') + '.zip',
						rollout_percent: Number(data.get('rollout')),
						requires_wordpress: '6.7',
						tested_wordpress: '7.0',
						requires_php: '7.4.33',
						changelog_url: data.get('changelog') || ''
					}).then(function () {
						reload('Release saved.');
					}, function (error) {
						N.toast(error.message, true);
					});
				});
			}
		});
	}

	/* ---------- Activity ---------- */

	function activity() {
		return Promise.all([N.api('GET', '/v1/operator/audit?limit=200'), loadLicenses()]).then(function (results) {
			return '<div class="nxc-page-head"><div><h1>Activity</h1><p>The latest 200 license, site, and release changes.</p></div></div><section class="nxc-card">' + activityList(results[0].events, results[1]) + '</section>';
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		var current = { bind: null, arg: null };
		N.router([
			[/^\/$/, function () {
				current = { bind: null };
				return dashboard();
			}],
			[/^\/licenses$/, function () {
				current = { bind: bindLicenseList };
				return licenses();
			}],
			[/^\/licenses\/([0-9a-f-]{36})$/, function (id) {
				current = { bind: bindLicenseDetail, arg: id };
				return licenseDetail(id);
			}],
			[/^\/new$/, function () {
				current = { bind: bindNewLicense };
				return newLicense();
			}],
			[/^\/releases$/, function () {
				current = { bind: bindReleases };
				return releases();
			}],
			[/^\/activity$/, function () {
				current = { bind: null };
				return activity();
			}]
		], function (main) {
			// Each render replaces <main>'s content; re-create it so old listeners never stack.
			var fresh = main.cloneNode(true);
			main.parentNode.replaceChild(fresh, main);
			fresh.addEventListener('click', function (event) {
				var row = event.target.closest('tr[data-href]');
				if (row && !event.target.closest('a, button')) {
					window.location.hash = row.getAttribute('data-href');
				}
			});
			if (current.bind) {
				current.bind(fresh, current.arg);
			}
		});
	});
}());
