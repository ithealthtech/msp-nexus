(function (wp) {
  'use strict';

  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var useState = wp.element.useState;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var ToggleControl = wp.components.ToggleControl;
  var TextControl = wp.components.TextControl;
  var SelectControl = wp.components.SelectControl;
  var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
  var __ = wp.i18n.__;

  var elements = [
    ['msp-hero', 'MSP Hero', 'A conversion-focused split hero for a primary service promise.', 'cover'],
    ['service-cards', 'Service Cards', 'A scannable managed-service capability grid.', 'cards'],
    ['service-comparison', 'Service Comparison', 'Compare managed, co-managed, and project delivery.', 'columns'],
    ['security-maturity', 'Security Maturity', 'Present a staged security improvement model.', 'steps'],
    ['compliance-readiness', 'Compliance Readiness', 'Connect controls, evidence, and executive ownership.', 'cards'],
    ['business-continuity', 'Business Continuity', 'Explain resilient operations and recovery planning.', 'split'],
    ['cloud-roadmap', 'Cloud Roadmap', 'Frame migration, governance, optimization, and adoption.', 'steps'],
    ['ai-readiness', 'AI Readiness', 'Position responsible AI discovery and enablement.', 'split'],
    ['co-managed-team', 'Co-managed Team', 'Show how internal IT and the MSP work together.', 'columns'],
    ['support-experience', 'Support Experience', 'Explain intake, triage, communication, and resolution.', 'steps'],
    ['process-timeline', 'Process Timeline', 'A reusable assess, plan, improve, and measure sequence.', 'steps'],
    ['outcome-metrics', 'Outcome Metrics', 'A responsible metrics band without unverifiable claims.', 'metrics'],
    ['client-quote', 'Client Quote', 'An accessible testimonial or service-standard statement.', 'quote'],
    ['case-study-spotlight', 'Case Study Spotlight', 'Pair a customer challenge with response and outcomes.', 'split'],
    ['industry-grid', 'Industry Grid', 'Guide visitors to tailored industry pages.', 'cards'],
    ['leadership-team', 'Leadership Team', 'Introduce accountable technology leaders.', 'cards'],
    ['location-network', 'Location Network', 'Present service areas and support coverage.', 'cards'],
    ['resource-library', 'Resource Library', 'Feature useful guides, briefings, and events.', 'cards'],
    ['pricing-paths', 'Pricing Paths', 'Explain plan scope and pricing drivers responsibly.', 'columns'],
    ['technology-partners', 'Technology Partners', 'Present verified partner relationships.', 'logos'],
    ['certification-wall', 'Certification Wall', 'Present current certifications with context.', 'logos'],
    ['risk-assessment', 'Risk Assessment CTA', 'Invite an evidence-led security or resilience review.', 'cta'],
    ['consultation-cta', 'Consultation CTA', 'A clear next-step section for qualified conversations.', 'cta'],
    ['support-panel', 'Client Support Panel', 'Separate urgent client support from sales inquiries.', 'split'],
    ['executive-faq', 'Executive FAQ Intro', 'Introduce decision-focused frequently asked questions.', 'split'],
    ['newsletter-briefing', 'Executive Briefing Signup', 'Offer a useful, consent-aware recurring briefing.', 'cta'],
    ['event-briefing', 'Event Briefing', 'Set topic, audience, speakers, timing, and registration.', 'split'],
    ['career-culture', 'Careers and Culture', 'Describe values, work, and the hiring process honestly.', 'split'],
    ['partner-referral', 'Partner Referral', 'Invite responsible referrals and complementary partnerships.', 'cta'],
    ['final-decision', 'Final Decision CTA', 'Close a page with expectations and a direct next action.', 'cta'],
    ['incident-response-hero', 'Incident Response Hero', 'Direct active incidents into a calm, evidence-preserving response path.', 'cover'],
    ['vcio-hero', 'Virtual CIO Hero', 'Position executive technology planning and accountable roadmaps.', 'cover'],
    ['microsoft-cloud-hero', 'Microsoft Cloud Hero', 'Introduce Microsoft 365, Azure, adoption, and governance services.', 'cover'],
    ['backup-recovery-hero', 'Backup and Recovery Hero', 'Frame continuity around business impact and verified recovery.', 'cover'],
    ['network-modernization-hero', 'Network Modernization Hero', 'Introduce resilient connectivity, Wi-Fi, SD-WAN, and lifecycle planning.', 'cover'],
    ['zero-trust-hero', 'Zero Trust Hero', 'Explain identity-led security without relying on fear-based claims.', 'cover'],
    ['identity-access', 'Identity and Access', 'Present identity, MFA, conditional access, and lifecycle controls.', 'cards'],
    ['endpoint-management', 'Endpoint Management', 'Explain device standards, patching, configuration, and visibility.', 'cards'],
    ['email-security', 'Email Security', 'Present layered email protection, awareness, and response workflows.', 'cards'],
    ['backup-services', 'Backup Services', 'Compare backup coverage, retention, testing, and recovery ownership.', 'columns'],
    ['network-operations', 'Network Operations', 'Show monitoring, configuration, carrier, and escalation responsibilities.', 'cards'],
    ['technology-procurement', 'Technology Procurement', 'Explain standards, quoting, lifecycle, logistics, and warranty handling.', 'steps'],
    ['project-delivery', 'Project Delivery', 'Structure discovery, design, execution, handoff, and adoption.', 'steps'],
    ['helpdesk-workflow', 'Help Desk Workflow', 'Describe accessible support intake, prioritization, and communication.', 'steps'],
    ['noc-operations', 'NOC Operations', 'Present monitoring, event handling, escalation, and continuous improvement.', 'steps'],
    ['soc-operations', 'SOC Operations', 'Present detection, investigation, containment, and reporting workflows.', 'steps'],
    ['executive-roadmap', 'Executive Roadmap', 'Connect current condition, priorities, sequencing, investment, and ownership.', 'steps'],
    ['responsibility-matrix', 'Responsibility Matrix', 'Clarify client, MSP, vendor, and shared responsibilities.', 'columns'],
    ['service-catalog', 'Service Catalog', 'Organize included, optional, project, and third-party services.', 'cards'],
    ['onboarding-plan', 'Client Onboarding Plan', 'Set expectations for discovery, transition, stabilization, and review.', 'steps'],
    ['escalation-path', 'Escalation Path', 'Show technical, service, security, and executive escalation routes.', 'steps'],
    ['sla-explainer', 'SLA Explainer', 'Explain response targets, priorities, coverage, and exclusions accurately.', 'columns'],
    ['service-review', 'Service Review Agenda', 'Outline a useful recurring service and strategy review.', 'steps'],
    ['client-portal', 'Client Portal Introduction', 'Guide clients to tickets, requests, assets, reports, and knowledge.', 'split'],
    ['maintenance-notice', 'Maintenance Notice', 'Communicate planned work, scope, timing, impact, and contact routes.', 'alert'],
    ['service-status', 'Service Status Notice', 'Communicate current condition, affected services, updates, and ownership.', 'alert'],
    ['assessment-checklist', 'Assessment Checklist', 'Present evidence-led discovery questions and expected inputs.', 'cards'],
    ['roi-framework', 'Technology Value Framework', 'Discuss cost, risk, capacity, resilience, and opportunity responsibly.', 'metrics'],
    ['plan-comparison', 'Managed Plan Comparison', 'Compare inclusions and assumptions without artificial urgency.', 'columns'],
    ['technology-stack', 'Technology Stack', 'Organize verified platforms by identity, endpoint, network, cloud, and security.', 'logos'],
    ['solution-architecture', 'Solution Architecture', 'Explain how users, identity, devices, applications, data, and controls connect.', 'split'],
    ['maturity-roadmap', 'Maturity Roadmap', 'Show practical progression from stabilization to optimization.', 'steps'],
    ['healthcare-industry', 'Healthcare Industry', 'Frame availability, privacy, clinical workflows, and vendor dependencies.', 'split'],
    ['financial-industry', 'Financial Services Industry', 'Frame governance, resilience, privacy, audit, and third-party risk.', 'split'],
    ['legal-industry', 'Legal Industry', 'Frame confidentiality, document workflows, mobility, and continuity.', 'split'],
    ['manufacturing-industry', 'Manufacturing Industry', 'Frame plant connectivity, operational dependencies, security, and recovery.', 'split'],
    ['construction-industry', 'Construction Industry', 'Frame field collaboration, connectivity, devices, and project lifecycle.', 'split'],
    ['nonprofit-industry', 'Nonprofit Industry', 'Frame mission delivery, budget stewardship, privacy, and distributed teams.', 'split'],
    ['education-industry', 'Education Industry', 'Frame identity, learning systems, privacy, devices, and seasonal demand.', 'split'],
    ['webinar-promotion', 'Webinar Promotion', 'Present a focused topic, audience, presenters, schedule, and registration.', 'cta'],
    ['resource-lead-magnet', 'Resource Lead Magnet', 'Offer an original guide with transparent data-use expectations.', 'cta'],
    ['security-briefing', 'Executive Security Briefing', 'Summarize material risks, decisions, owners, and next review points.', 'split'],
    ['quarterly-review', 'Quarterly Business Review', 'Structure outcomes, service health, risk, roadmap, and decisions.', 'steps'],
    ['verified-recognition', 'Verified Recognition', 'Present current awards, memberships, or designations with sources.', 'logos'],
    ['community-impact', 'Community Impact', 'Describe verified community commitments and measurable activities.', 'split']
  ];

  function textTemplate(title, description) {
    return [
      ['core/paragraph', { className: 'msp-nexus-eyebrow', content: __('MSP Nexus element', 'msp-nexus-core') }],
      ['core/heading', { level: 2, content: title }],
      ['core/paragraph', { content: description, fontSize: 'large' }]
    ];
  }

  function templateFor(title, description, kind) {
    var intro = textTemplate(title, description);
    if (kind === 'cards' || kind === 'columns' || kind === 'metrics' || kind === 'logos' || kind === 'steps') {
      return intro.concat([['core/columns', {}, [
        ['core/column', {}, [['core/heading', { level: 3, content: __('Clear capability', 'msp-nexus-core') }], ['core/paragraph', { content: __('Replace this guidance with accurate, specific content.', 'msp-nexus-core') }]]],
        ['core/column', {}, [['core/heading', { level: 3, content: __('Visible ownership', 'msp-nexus-core') }], ['core/paragraph', { content: __('Explain who is accountable and how progress is measured.', 'msp-nexus-core') }]]],
        ['core/column', {}, [['core/heading', { level: 3, content: __('Responsible next step', 'msp-nexus-core') }], ['core/paragraph', { content: __('Give visitors a practical route to continue.', 'msp-nexus-core') }]]]
      ]]]);
    }
    if (kind === 'quote') {
      return intro.concat([['core/quote', { value: '<p>' + __('Add a verified customer quotation or clearly label this as your service standard.', 'msp-nexus-core') + '</p>', citation: __('Verified source', 'msp-nexus-core') }]]);
    }
    if (kind === 'cta') {
      return intro.concat([['core/buttons', {}, [['core/button', { text: __('Plan a consultation', 'msp-nexus-core'), url: '/contact/' }]]]]);
    }
    return [['core/columns', { verticalAlignment: 'center' }, [
      ['core/column', {}, intro],
      ['core/column', {}, [['core/group', { className: 'msp-nexus-card', layout: { type: 'constrained' } }, [['core/heading', { level: 3, content: __('Supporting detail', 'msp-nexus-core') }], ['core/paragraph', { content: __('Use this space for evidence, an image, process details, or a specific business outcome.', 'msp-nexus-core') }]]]]]
    ]]];
  }

  elements.forEach(function (item) {
    wp.blocks.registerBlockVariation('core/group', {
      name: 'msp-nexus-' + item[0],
      title: item[1],
      description: item[2],
      category: 'msp-nexus',
      icon: 'shield-alt',
      scope: ['inserter'],
      attributes: { align: 'full', className: 'msp-nexus-element msp-nexus-element--' + item[0], layout: { type: 'constrained' } },
      innerBlocks: templateFor(item[1], item[2], item[3])
    });
  });

  var serviceElementFamilies = [
    ['managed-it', 'Managed IT', 'proactive technology operations'], ['cybersecurity', 'Cybersecurity', 'risk reduction and response'],
    ['cloud', 'Cloud Services', 'governed cloud operations'], ['microsoft-365', 'Microsoft 365', 'secure productivity and adoption'],
    ['azure', 'Microsoft Azure', 'cloud infrastructure and modernization'], ['help-desk', 'Help Desk', 'responsive employee support'],
    ['co-managed-it', 'Co-managed IT', 'capacity for internal IT teams'], ['networking', 'Network Services', 'resilient connectivity and visibility'],
    ['backup', 'Backup', 'protected data and tested recovery'], ['continuity', 'Business Continuity', 'coordinated operational resilience'],
    ['compliance', 'Compliance', 'controls, evidence, and ownership'], ['vcio', 'Virtual CIO', 'technology roadmaps and decisions'],
    ['procurement', 'IT Procurement', 'standards, lifecycle, and logistics'], ['projects', 'IT Projects', 'controlled technology change'],
    ['communications', 'Unified Communications', 'calling, meetings, and collaboration'], ['data-ai', 'Data and AI', 'responsible information activation'],
    ['identity', 'Identity Security', 'access governance and lifecycle'], ['endpoint', 'Endpoint Management', 'secure device operations'],
    ['soc', 'Security Operations Center', 'detection, investigation, and response'], ['noc', 'Network Operations Center', 'monitoring, escalation, and improvement']
  ];
  var serviceElementFormats = [
    ['overview', 'Overview', 'cards', __('Summarize the capability, ownership, evidence, and next step.', 'msp-nexus-core')],
    ['benefits', 'Business Benefits', 'columns', __('Connect the service to productivity, risk, resilience, and visibility.', 'msp-nexus-core')],
    ['process', 'Delivery Process', 'steps', __('Explain how discovery, transition, operations, review, and improvement work.', 'msp-nexus-core')],
    ['challenge-solution', 'Challenge and Solution', 'split', __('Pair common operating friction with a responsible service response.', 'msp-nexus-core')],
    ['outcomes', 'Outcome Metrics', 'metrics', __('Present measurable outcomes with scope, time period, and honest context.', 'msp-nexus-core')],
    ['questions', 'Buyer Questions', 'cards', __('Answer the practical questions buyers should resolve before selecting this service.', 'msp-nexus-core')],
    ['cta', 'Consultation CTA', 'cta', __('Offer a focused conversation about the current condition and next decision.', 'msp-nexus-core')]
  ];
  serviceElementFamilies.forEach(function (family) {
    serviceElementFormats.forEach(function (format) {
      var title = family[1] + ': ' + format[1];
      var description = format[3] + ' ' + __('Designed for', 'msp-nexus-core') + ' ' + family[2] + '.';
      wp.blocks.registerBlockVariation('core/group', {
        name: 'msp-nexus-solution-' + family[0] + '-' + format[0], title: title, description: description,
        category: 'msp-nexus', icon: 'shield-alt', scope: ['inserter'],
        attributes: { align: 'full', className: 'msp-nexus-element msp-nexus-element--solution-' + family[0] + '-' + format[0], layout: { type: 'constrained' } },
        innerBlocks: templateFor(title, description, format[2])
      });
    });
  });

  [
    ['signal-card', 'Signal card'], ['glass-panel', 'Glass panel'], ['accent-edge', 'Accent edge'], ['soft-surface', 'Soft surface'],
    ['compact-stack', 'Compact stack'], ['metric-card', 'Metric card'], ['sticky-panel', 'Sticky panel'], ['dark-panel', 'Dark panel']
  ].forEach(function (style) {
    wp.blocks.registerBlockStyle('core/group', { name: style[0], label: style[1] });
  });
  wp.blocks.registerBlockStyle('core/button', { name: 'arrow-link', label: __('Arrow link', 'msp-nexus-core') });
  wp.blocks.registerBlockStyle('core/image', { name: 'signal-frame', label: __('Signal frame', 'msp-nexus-core') });
  wp.blocks.registerBlockStyle('core/image', { name: 'mask-blob', label: __('Organic mask', 'msp-nexus-core') });
  wp.blocks.registerBlockStyle('core/image', { name: 'mask-hexagon', label: __('Hexagon mask', 'msp-nexus-core') });
  wp.blocks.registerBlockStyle('core/image', { name: 'mask-arch', label: __('Arch mask', 'msp-nexus-core') });
  wp.blocks.registerBlockVariation('core/cover', {
    name: 'msp-nexus-video-hero', title: __('Video Background Hero', 'msp-nexus-core'), description: __('A native Cover video background with accessible text and reduced-motion fallback guidance.', 'msp-nexus-core'), category: 'msp-nexus', icon: 'video-alt3', scope: ['inserter'],
    attributes: { align: 'full', backgroundType: 'video', dimRatio: 65, minHeight: 620, className: 'msp-nexus-video-background' },
    innerBlocks: [['core/group', { layout: { type: 'constrained' } }, [['core/heading', { level: 1, content: __('Technology operations built for momentum.', 'msp-nexus-core') }], ['core/paragraph', { content: __('Choose a concise, muted background video and provide an equivalent poster image and meaningful page content.', 'msp-nexus-core'), fontSize: 'large' }]]]]
  });
  wp.blocks.registerBlockVariation('core/cover', {
    name: 'msp-nexus-video-band', title: __('Video Background Band', 'msp-nexus-core'), description: __('A compact native Cover video section for a process, culture, or capability moment.', 'msp-nexus-core'), category: 'msp-nexus', icon: 'format-video', scope: ['inserter'],
    attributes: { align: 'full', backgroundType: 'video', dimRatio: 70, minHeight: 420, className: 'msp-nexus-video-background' }, innerBlocks: textTemplate(__('Video story', 'msp-nexus-core'), __('Keep motion supportive, compressed, muted, and optional for visitors who prefer reduced motion.', 'msp-nexus-core'))
  });

  var deviceFields = [
    ['padding', 'Padding', 'Example: 32px or 2rem 3rem'], ['margin', 'Margin', 'Negative values are supported'], ['gap', 'Gap', 'Example: 24px'],
    ['width', 'Width', 'Example: 100% or 32rem'], ['maxWidth', 'Maximum width', 'Example: 72rem'], ['minHeight', 'Minimum height', 'Example: 70vh'],
    ['fontSize', 'Font size', 'Example: 1.25rem'], ['lineHeight', 'Line height', 'Example: 1.4'], ['borderRadius', 'Border radius', 'Example: 12px'],
    ['order', 'Flex/grid order', 'Whole number'], ['top', 'Top offset', 'Requires positioned content'], ['right', 'Right offset', 'Requires positioned content'],
    ['bottom', 'Bottom offset', 'Requires positioned content'], ['left', 'Left offset', 'Requires positioned content'], ['zIndex', 'Layer (z-index)', 'Whole number'],
    ['translateX', 'Move horizontally', 'Example: -20px'], ['translateY', 'Move vertically', 'Example: 2rem'], ['rotate', 'Rotate', 'Example: -3deg'],
    ['scale', 'Scale', 'Between 0.1 and 5'], ['opacity', 'Opacity', 'Between 0 and 1'], ['backgroundColor', 'Background color', 'Hex color']
  ];

  function setAttribute(props, name, value) {
    var update = {};
    update[name] = value;
    props.setAttributes(update);
  }

  var withResponsiveControls = createHigherOrderComponent(function (BlockEdit) {
    return function (props) {
      if (!props.name || props.name === 'core/html' || props.name === 'core/shortcode') return el(BlockEdit, props);
      var attrs = props.attributes;
      var _device = useState('desktop');
      var device = _device[0];
      var setDevice = _device[1];
      var deviceStyles = attrs.mspDeviceStyles || {};
      var currentStyles = deviceStyles[device] || {};
      var conditions = attrs.mspConditions || {};

      function setDeviceStyle(name, value) {
        var all = Object.assign({}, deviceStyles);
        all[device] = Object.assign({}, currentStyles);
        all[device][name] = value;
        setAttribute(props, 'mspDeviceStyles', all);
      }
      function setCondition(name, value) {
        var next = Object.assign({}, conditions);
        next[name] = value;
        setAttribute(props, 'mspConditions', next);
      }

      return el(Fragment, {},
        el(BlockEdit, props),
        el(InspectorControls, {},
          el(PanelBody, { title: __('Nexus device design', 'msp-nexus-core'), initialOpen: false },
            el(SelectControl, { label: __('Editing device', 'msp-nexus-core'), value: device, options: [
              { label: __('Desktop', 'msp-nexus-core'), value: 'desktop' }, { label: __('Tablet', 'msp-nexus-core'), value: 'tablet' }, { label: __('Mobile', 'msp-nexus-core'), value: 'mobile' }
            ], onChange: setDevice }),
            el(ToggleControl, { label: __('Hide on desktop', 'msp-nexus-core'), checked: !!attrs.mspHideDesktop, onChange: function (value) { setAttribute(props, 'mspHideDesktop', value); } }),
            el(ToggleControl, { label: __('Hide on tablet', 'msp-nexus-core'), checked: !!attrs.mspHideTablet, onChange: function (value) { setAttribute(props, 'mspHideTablet', value); } }),
            el(ToggleControl, { label: __('Hide on mobile', 'msp-nexus-core'), checked: !!attrs.mspHideMobile, onChange: function (value) { setAttribute(props, 'mspHideMobile', value); } }),
            el(SelectControl, { label: __('Display', 'msp-nexus-core'), value: currentStyles.display || '', options: [
              { label: __('Inherit', 'msp-nexus-core'), value: '' }, { label: __('Block', 'msp-nexus-core'), value: 'block' }, { label: __('Inline block', 'msp-nexus-core'), value: 'inline-block' },
              { label: __('Flex', 'msp-nexus-core'), value: 'flex' }, { label: __('Grid', 'msp-nexus-core'), value: 'grid' }, { label: __('None', 'msp-nexus-core'), value: 'none' }
            ], onChange: function (value) { setDeviceStyle('display', value); } }),
            el(SelectControl, { label: __('Position', 'msp-nexus-core'), value: currentStyles.position || '', options: [
              { label: __('Inherit', 'msp-nexus-core'), value: '' }, { label: __('Static', 'msp-nexus-core'), value: 'static' }, { label: __('Relative', 'msp-nexus-core'), value: 'relative' },
              { label: __('Absolute', 'msp-nexus-core'), value: 'absolute' }, { label: __('Sticky', 'msp-nexus-core'), value: 'sticky' }
            ], onChange: function (value) { setDeviceStyle('position', value); } }),
            el(SelectControl, { label: __('Text alignment', 'msp-nexus-core'), value: currentStyles.textAlign || '', options: [
              { label: __('Inherit', 'msp-nexus-core'), value: '' }, { label: __('Start', 'msp-nexus-core'), value: 'start' }, { label: __('Center', 'msp-nexus-core'), value: 'center' },
              { label: __('End', 'msp-nexus-core'), value: 'end' }, { label: __('Justify', 'msp-nexus-core'), value: 'justify' }
            ], onChange: function (value) { setDeviceStyle('textAlign', value); } }),
            deviceFields.map(function (field) {
              return el(TextControl, { key: field[0], label: __(field[1], 'msp-nexus-core'), help: __(field[2], 'msp-nexus-core'), value: currentStyles[field[0]] || '', onChange: function (value) { setDeviceStyle(field[0], value); } });
            }),
            el(SelectControl, { label: __('Entrance motion', 'msp-nexus-core'), value: attrs.mspEntranceAnimation || 'none', options: [
              { label: __('None', 'msp-nexus-core'), value: 'none' }, { label: __('Fade', 'msp-nexus-core'), value: 'fade' },
              { label: __('Rise', 'msp-nexus-core'), value: 'rise' }, { label: __('Slide', 'msp-nexus-core'), value: 'slide' }
            ], onChange: function (value) { setAttribute(props, 'mspEntranceAnimation', value); } })
          ),
          el(PanelBody, { title: __('Nexus display conditions', 'msp-nexus-core'), initialOpen: false },
            el(SelectControl, { label: __('Visitor state', 'msp-nexus-core'), value: conditions.userState || 'any', options: [
              { label: __('Everyone', 'msp-nexus-core'), value: 'any' }, { label: __('Logged-in visitors', 'msp-nexus-core'), value: 'logged_in' }, { label: __('Logged-out visitors', 'msp-nexus-core'), value: 'logged_out' }
            ], onChange: function (value) { setCondition('userState', value); } }),
            el(TextControl, { label: __('Allowed user roles', 'msp-nexus-core'), help: __('Comma-separated role slugs; leave empty for any role.', 'msp-nexus-core'), value: conditions.roles || '', onChange: function (value) { setCondition('roles', value); } }),
            el(TextControl, { label: __('Start date', 'msp-nexus-core'), help: __('YYYY-MM-DD in the site timezone.', 'msp-nexus-core'), value: conditions.dateStart || '', onChange: function (value) { setCondition('dateStart', value); } }),
            el(TextControl, { label: __('End date', 'msp-nexus-core'), help: __('YYYY-MM-DD in the site timezone.', 'msp-nexus-core'), value: conditions.dateEnd || '', onChange: function (value) { setCondition('dateEnd', value); } }),
            el(TextControl, { label: __('Weekdays', 'msp-nexus-core'), help: __('Comma-separated ISO days: 1 is Monday and 7 is Sunday.', 'msp-nexus-core'), value: conditions.weekdays || '', onChange: function (value) { setCondition('weekdays', value); } }),
            el(TextControl, { label: __('Post types', 'msp-nexus-core'), help: __('Comma-separated post type slugs.', 'msp-nexus-core'), value: conditions.postTypes || '', onChange: function (value) { setCondition('postTypes', value); } }),
            el(TextControl, { label: __('Taxonomy', 'msp-nexus-core'), value: conditions.taxonomy || '', onChange: function (value) { setCondition('taxonomy', value); } }),
            el(TextControl, { label: __('Term slugs', 'msp-nexus-core'), value: conditions.terms || '', onChange: function (value) { setCondition('terms', value); } }),
            el(TextControl, { label: __('Query parameter', 'msp-nexus-core'), value: conditions.queryKey || '', onChange: function (value) { setCondition('queryKey', value); } }),
            el(TextControl, { label: __('Required query value', 'msp-nexus-core'), help: __('Use * to require any non-empty value.', 'msp-nexus-core'), value: conditions.queryValue || '', onChange: function (value) { setCondition('queryValue', value); } })
          )
        )
      );
    };
  }, 'withNexusResponsiveControls');

  function wrapperState(attrs) {
    var classes = [];
    var style = {};
    if (attrs.mspHideDesktop) classes.push('msp-hide-desktop');
    if (attrs.mspHideTablet) classes.push('msp-hide-tablet');
    if (attrs.mspHideMobile) classes.push('msp-hide-mobile');
    ['desktop', 'tablet', 'mobile'].forEach(function (device) {
      var values = (attrs.mspDeviceStyles || {})[device] || {};
      Object.keys(values).forEach(function (property) {
        if (!values[property] || ['rotate', 'scale', 'translateX', 'translateY'].indexOf(property) !== -1) return;
        var slug = property.replace(/[A-Z]/g, function (letter) { return '-' + letter.toLowerCase(); });
        classes.push('msp-' + device + '-' + slug);
        style['--msp-' + device + '-' + slug] = values[property];
      });
      if (values.rotate || values.scale || values.translateX || values.translateY) {
        classes.push('msp-' + device + '-transform');
        style['--msp-' + device + '-transform'] = 'translate(' + (values.translateX || '0') + ',' + (values.translateY || '0') + ') rotate(' + (values.rotate || '0deg') + ') scale(' + (values.scale || '1') + ')';
      }
    });
    return { classes: classes, style: style };
  }

  var withResponsivePreview = createHigherOrderComponent(function (BlockListBlock) {
    return function (props) {
      var state = wrapperState(props.attributes || {});
      var wrapperProps = Object.assign({}, props.wrapperProps || {});
      wrapperProps.style = Object.assign({}, wrapperProps.style || {}, state.style);
      return el(BlockListBlock, Object.assign({}, props, { className: ((props.className || '') + ' ' + state.classes.join(' ')).trim(), wrapperProps: wrapperProps }));
    };
  }, 'withNexusResponsivePreview');

  wp.hooks.addFilter('editor.BlockEdit', 'msp-nexus/responsive-controls', withResponsiveControls);
  wp.hooks.addFilter('editor.BlockListBlock', 'msp-nexus/responsive-preview', withResponsivePreview);
})(window.wp);
