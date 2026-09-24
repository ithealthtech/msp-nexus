<?php
/**
 * Curated pattern library.
 *
 * Patterns are registered from compact original definitions to keep the theme
 * maintainable while exposing sixty distinct editor entries.
 *
 * @package MspNexus
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/** @return array<int, array<string, string>> */
function msp_nexus_section_pattern_definitions(): array
{
    return array(
        array('slug'=>'hero-operations','title'=>'Hero: Operational confidence','category'=>'msp-nexus-heroes','kicker'=>'Managed technology operations','heading'=>'Your technology should accelerate the business—not interrupt it.','body'=>'A security-first team keeps people productive, systems resilient, and leadership informed.','cta'=>'Plan your IT roadmap'),
        array('slug'=>'hero-security','title'=>'Hero: Security command center','category'=>'msp-nexus-heroes','kicker'=>'Cybersecurity operations','heading'=>'Turn cyber risk into a managed business discipline.','body'=>'Unify prevention, detection, response, recovery, and executive reporting under one accountable program.','cta'=>'Assess your exposure'),
        array('slug'=>'hero-cloud','title'=>'Hero: Cloud modernization','category'=>'msp-nexus-heroes','kicker'=>'Cloud & infrastructure','heading'=>'Modernize infrastructure without losing control.','body'=>'Build a practical cloud operating model for performance, cost visibility, governance, and resilience.','cta'=>'Build a cloud plan'),
        array('slug'=>'hero-copilot','title'=>'Hero: Responsible AI','category'=>'msp-nexus-heroes','kicker'=>'AI enablement','heading'=>'Put AI to work with guardrails your business can trust.','body'=>'Prepare identities, information, policies, and teams for responsible adoption that produces measurable value.','cta'=>'Start AI readiness'),
        array('slug'=>'hero-co-managed','title'=>'Hero: Co-managed IT','category'=>'msp-nexus-heroes','kicker'=>'Extend your internal team','heading'=>'Give your IT team more capacity, coverage, and specialist depth.','body'=>'Add 24/7 operations and project expertise while your internal leaders stay in control.','cta'=>'Explore co-managed IT'),
        array('slug'=>'hero-compliance','title'=>'Hero: Compliance readiness','category'=>'msp-nexus-heroes','kicker'=>'Governance, risk & compliance','heading'=>'Make audit readiness part of daily operations.','body'=>'Translate requirements into durable controls, evidence, ownership, and a prioritized improvement roadmap.','cta'=>'Map your requirements'),
        array('slug'=>'hero-continuity','title'=>'Hero: Business continuity','category'=>'msp-nexus-heroes','kicker'=>'Resilience by design','heading'=>'Keep the business moving when systems do not.','body'=>'Connect backup, recovery, communications, security response, and testing in one continuity program.','cta'=>'Review resilience'),
        array('slug'=>'hero-local','title'=>'Hero: Local expertise','category'=>'msp-nexus-heroes','kicker'=>'Responsive capability. Local accountability.','heading'=>'Get responsive IT leadership close to your business.','body'=>'Close working relationships supported by disciplined service operations and specialist expertise.','cta'=>'Start a conversation'),
        array('slug'=>'services-grid','title'=>'Services: Dynamic service grid','category'=>'msp-nexus-services','kicker'=>'Capabilities','heading'=>'One accountable partner across the technology lifecycle.','body'=>'Connect day-to-day support with cybersecurity, cloud, communications, data, and strategic planning.','cta'=>'Explore all services'),
        array('slug'=>'services-managed','title'=>'Services: Managed IT overview','category'=>'msp-nexus-services','kicker'=>'Managed IT','heading'=>'A proactive operating model for everyday technology.','body'=>'Standardize support, monitoring, maintenance, vendors, lifecycle planning, and executive reporting.','cta'=>'See managed IT'),
        array('slug'=>'services-security','title'=>'Services: Security pillars','category'=>'msp-nexus-services','kicker'=>'Layered defense','heading'=>'Reduce likelihood, limit impact, and recover with confidence.','body'=>'Align identity, endpoint, network, cloud, people, monitoring, incident response, and governance.','cta'=>'Strengthen security'),
        array('slug'=>'services-cloud','title'=>'Services: Cloud platform','category'=>'msp-nexus-services','kicker'=>'Cloud operations','heading'=>'Operate cloud services as a governed platform.','body'=>'Improve reliability, identity, cost, backup, observability, and end-user experience across hybrid environments.','cta'=>'Optimize the cloud'),
        array('slug'=>'services-advisory','title'=>'Services: Strategic advisory','category'=>'msp-nexus-services','kicker'=>'Technology leadership','heading'=>'Turn priorities into an executable technology roadmap.','body'=>'Use business reviews, budgeting, risk decisions, architecture, and lifecycle plans to make progress visible.','cta'=>'Meet an advisor'),
        array('slug'=>'services-helpdesk','title'=>'Services: Support experience','category'=>'msp-nexus-services','kicker'=>'Human support','heading'=>'Fast answers with context, ownership, and follow-through.','body'=>'Give every employee a clear path to help and leadership a transparent view of service performance.','cta'=>'Improve support'),
        array('slug'=>'services-network','title'=>'Services: Network & infrastructure','category'=>'msp-nexus-services','kicker'=>'Reliable foundations','heading'=>'Design infrastructure for secure, observable performance.','body'=>'Standardize connectivity, wireless, servers, edge security, lifecycle management, and vendor accountability.','cta'=>'Modernize infrastructure'),
        array('slug'=>'services-projects','title'=>'Services: Projects delivery','category'=>'msp-nexus-services','kicker'=>'Project execution','heading'=>'Deliver change without destabilizing operations.','body'=>'Combine discovery, architecture, change control, communication, documentation, and accountable handoff.','cta'=>'Discuss a project'),
        array('slug'=>'services-communications','title'=>'Services: Modern communications','category'=>'msp-nexus-services','kicker'=>'Connect the workforce','heading'=>'Bring calling, meetings, messaging, and contact workflows together.','body'=>'Create a secure communications experience that works across offices, homes, and the field.','cta'=>'Unify communications'),
        array('slug'=>'services-data','title'=>'Services: Data & automation','category'=>'msp-nexus-services','kicker'=>'Operational intelligence','heading'=>'Make trustworthy data easier to use.','body'=>'Improve integration, reporting, automation, governance, and responsible AI readiness.','cta'=>'Activate your data'),
        array('slug'=>'trust-logos','title'=>'Trust: Operating principles','category'=>'msp-nexus-trust','kicker'=>'How service is delivered','heading'=>'Clear ownership from first response through long-term planning.','body'=>'Bring support, security, cloud, vendors, lifecycle, and strategy into one visible operating rhythm.','cta'=>'See how service works'),
        array('slug'=>'trust-metrics','title'=>'Trust: Service metrics','category'=>'msp-nexus-trust','kicker'=>'Performance you can inspect','heading'=>'Measure the experience—not just ticket volume.','body'=>'Show response, resolution, satisfaction, risk reduction, coverage, and roadmap progress with honest context.','cta'=>'See how service is measured'),
        array('slug'=>'trust-testimonial','title'=>'Trust: Service promise','category'=>'msp-nexus-trust','kicker'=>'The experience clients deserve','heading'=>'Technology should feel calm, visible, and ready for what comes next.','body'=>'Set expectations around communication, ownership, documentation, measurable reviews, and continuous improvement.','cta'=>'See the service approach'),
        array('slug'=>'trust-case-study','title'=>'Trust: Featured case study','category'=>'msp-nexus-trust','kicker'=>'Measured outcome','heading'=>'From fragmented support to a resilient operating standard.','body'=>'Present the starting condition, constraints, solution, adoption work, and measurable business results.','cta'=>'Read the case study'),
        array('slug'=>'trust-compliance','title'=>'Trust: Compliance framework','category'=>'msp-nexus-trust','kicker'=>'Evidence over claims','heading'=>'Connect controls to evidence, ownership, and review.','body'=>'Describe supported frameworks accurately and avoid implying certification where none exists.','cta'=>'Discuss compliance'),
        array('slug'=>'trust-process','title'=>'Trust: Delivery process','category'=>'msp-nexus-trust','kicker'=>'A visible operating rhythm','heading'=>'Discover. Stabilize. Standardize. Improve.','body'=>'Set clear expectations from transition through recurring service reviews and roadmap execution.','cta'=>'See the process'),
        array('slug'=>'trust-team','title'=>'Trust: Specialist team','category'=>'msp-nexus-trust','kicker'=>'Depth when it matters','heading'=>'Your service team backed by specialists across the stack.','body'=>'Highlight service leadership, security, cloud, infrastructure, compliance, and project delivery roles.','cta'=>'Meet the team'),
        array('slug'=>'trust-guarantee','title'=>'Trust: Service commitment','category'=>'msp-nexus-trust','kicker'=>'Clear accountability','heading'=>'Know what is covered, how it is measured, and who owns the outcome.','body'=>'Explain service commitments in precise language tied to the actual agreement.','cta'=>'Review service options'),
        array('slug'=>'trust-locations','title'=>'Trust: Multi-location coverage','category'=>'msp-nexus-trust','kicker'=>'Coverage where work happens','heading'=>'A consistent operating standard across every location.','body'=>'Pair centralized operations with local context, onsite coordination, and documented site standards.','cta'=>'Find a location'),
        array('slug'=>'trust-awards','title'=>'Trust: Awards & recognition','category'=>'msp-nexus-trust','kicker'=>'Evidence over decoration','heading'=>'Credentials should include dates, scope, and verifiable sources.','body'=>'Publish only current awards and certifications the organization is authorized to display.','cta'=>'Review the service approach'),
        array('slug'=>'content-audience','title'=>'Content: Two audience paths','category'=>'msp-nexus-content','kicker'=>'Built around your operating model','heading'=>'Full IT ownership or an extension of your internal team.','body'=>'Help buyers choose between fully managed and co-managed engagement paths without hiding the tradeoffs.','cta'=>'Compare approaches'),
        array('slug'=>'content-industries','title'=>'Content: Industry expertise grid','category'=>'msp-nexus-content','kicker'=>'Industry context','heading'=>'Technology operations shaped by how your organization works.','body'=>'Show relevant workflows, constraints, risks, regulations, and outcomes for the markets you actually serve.','cta'=>'Explore industries'),
        array('slug'=>'content-outcomes','title'=>'Content: Business outcomes','category'=>'msp-nexus-content','kicker'=>'Start with the outcome','heading'=>'Productivity, resilience, security, visibility, and controlled cost.','body'=>'Translate technical capabilities into decisions and results executives and employees can recognize.','cta'=>'Define success'),
        array('slug'=>'content-plan','title'=>'Content: Managed plan teaser','category'=>'msp-nexus-content','kicker'=>'Predictable managed plan','heading'=>'A clear baseline with options that match complexity.','body'=>'Explain what is included, what changes price, and where optional projects or consumption apply.','cta'=>'See plan structure'),
        array('slug'=>'content-resources','title'=>'Content: Featured resources','category'=>'msp-nexus-content','kicker'=>'Practical guidance','heading'=>'Use these resources to make the next decision clearer.','body'=>'Feature original guides, checklists, webinars, and analysis maintained by your subject-matter experts.','cta'=>'Browse resources'),
        array('slug'=>'content-team','title'=>'Content: Leadership profiles','category'=>'msp-nexus-content','kicker'=>'Accountable leadership','heading'=>'People responsible for service quality and long-term outcomes.','body'=>'Introduce leaders with useful context about their roles, experience, and customer responsibilities.','cta'=>'Meet leadership'),
        array('slug'=>'content-timeline','title'=>'Content: Transformation timeline','category'=>'msp-nexus-content','kicker'=>'A practical sequence','heading'=>'Stabilize today while building toward tomorrow.','body'=>'Show a phased transition from discovery and risk containment through standards, roadmap, and optimization.','cta'=>'Build your sequence'),
        array('slug'=>'content-responsible-ai','title'=>'Content: Responsible AI framework','category'=>'msp-nexus-content','kicker'=>'Responsible adoption','heading'=>'Prepare people, data, access, policy, and measurement together.','body'=>'Frame AI adoption as organizational change supported by security, governance, training, and feedback.','cta'=>'Assess AI readiness'),
        array('slug'=>'content-faq','title'=>'Content: Dynamic FAQ list','category'=>'msp-nexus-content','kicker'=>'Common questions','heading'=>'Straight answers before the first conversation.','body'=>'Use the dynamic FAQ block to maintain accurate answers once and reuse them across the site.','cta'=>'Ask another question'),
        array('slug'=>'content-locations','title'=>'Content: Service area directory','category'=>'msp-nexus-content','kicker'=>'Where we work','heading'=>'Local teams connected to one service platform.','body'=>'Present locations, service areas, contact details, hours, and onsite capabilities without doorway pages.','cta'=>'Find local support'),
        array('slug'=>'pricing-three-plans','title'=>'Pricing: Three managed plans','category'=>'msp-nexus-content','kicker'=>'Plan architecture','heading'=>'Compare three clearly scoped operating models.','body'=>'Present verified inclusions, assumptions, optional work, price drivers, and contract disclaimers without artificial urgency.','cta'=>'Compare plans'),
        array('slug'=>'pricing-feature-comparison','title'=>'Pricing: Feature comparison','category'=>'msp-nexus-content','kicker'=>'Understand the differences','heading'=>'Make scope differences easy to inspect.','body'=>'Use the comparison table style with descriptive row headings, text indicators, mobile reflow, and a plain-language alternative.','cta'=>'Review scope'),
        array('slug'=>'pricing-custom-scope','title'=>'Pricing: Custom scope explainer','category'=>'msp-nexus-content','kicker'=>'Complex environments','heading'=>'Price the real operating model—not a misleading per-seat number.','body'=>'Explain the users, locations, devices, coverage, risk, platforms, projects, and consumption that shape a responsible proposal.','cta'=>'Scope your environment'),
        array('slug'=>'conversion-consultation','title'=>'Conversion: Consultation CTA','category'=>'msp-nexus-conversion','kicker'=>'Make the next move','heading'=>'Build a clearer technology operating plan.','body'=>'Tell us what is changing, what is getting in the way, and what a better outcome looks like.','cta'=>'Plan a consultation'),
        array('slug'=>'conversion-discovery-call','title'=>'Conversion: Discovery call','category'=>'msp-nexus-conversion','kicker'=>'A focused first conversation','heading'=>'Clarify the current condition and the decision ahead.','body'=>'Set a useful agenda: business context, recurring friction, risk, priorities, timing, stakeholders, and a candid next step.','cta'=>'Schedule discovery'),
        array('slug'=>'conversion-contact-options','title'=>'Conversion: Contact choices','category'=>'msp-nexus-conversion','kicker'=>'Choose the right route','heading'=>'Sales, support, partnerships, and careers should reach different teams.','body'=>'Give visitors direct, accessible choices and reserve urgent support channels for existing clients.','cta'=>'Choose a contact route'),
        array('slug'=>'conversion-assessment','title'=>'Conversion: Security assessment CTA','category'=>'msp-nexus-conversion','kicker'=>'Start with evidence','heading'=>'Identify the exposures that deserve action first.','body'=>'Review controls, dependencies, business impact, and practical priorities with a security advisor.','cta'=>'Request an assessment'),
        array('slug'=>'conversion-support','title'=>'Conversion: Support CTA','category'=>'msp-nexus-conversion','kicker'=>'Existing client?','heading'=>'Reach the support team through your dedicated service channels.','body'=>'Keep urgent support separate from sales forms so requests reach the right workflow quickly.','cta'=>'Open the support portal'),
        array('slug'=>'conversion-newsletter','title'=>'Conversion: Newsletter signup','category'=>'msp-nexus-conversion','kicker'=>'Useful, not noisy','heading'=>'Get practical technology guidance for business leaders.','body'=>'Send a concise monthly briefing about risk, operations, adoption, and important platform changes.','cta'=>'Subscribe to insights'),
        array('slug'=>'conversion-resource','title'=>'Conversion: Guide download','category'=>'msp-nexus-conversion','kicker'=>'Decision guide','heading'=>'Create a practical brief for your next IT planning session.','body'=>'Offer a genuinely useful original resource and explain how contact information will be used.','cta'=>'Get the guide'),
        array('slug'=>'conversion-event','title'=>'Conversion: Event registration','category'=>'msp-nexus-conversion','kicker'=>'Live briefing','heading'=>'Join a focused conversation with working practitioners.','body'=>'Set expectations for the topic, audience, agenda, speakers, duration, and follow-up.','cta'=>'Reserve a seat'),
        array('slug'=>'conversion-careers','title'=>'Conversion: Careers CTA','category'=>'msp-nexus-conversion','kicker'=>'Do work that matters','heading'=>'Help organizations use technology with more confidence.','body'=>'Describe the culture honestly and connect candidates to current roles and the hiring process.','cta'=>'Explore careers'),
        array('slug'=>'conversion-referral','title'=>'Conversion: Partner referral','category'=>'msp-nexus-conversion','kicker'=>'Work together','heading'=>'Bring complementary expertise to the same client outcome.','body'=>'Give advisors, vendors, and community partners a clear route to begin a responsible referral.','cta'=>'Start a partner conversation'),
        array('slug'=>'conversion-emergency','title'=>'Conversion: Incident response CTA','category'=>'msp-nexus-conversion','kicker'=>'Active incident','heading'=>'Move quickly without losing evidence or coordination.','body'=>'Direct existing clients to the emergency process and new organizations to a clearly scoped response intake.','cta'=>'Get incident help'),
        array('slug'=>'conversion-final','title'=>'Conversion: Final split CTA','category'=>'msp-nexus-conversion','kicker'=>'Get in touch','heading'=>'Ready for technology that moves with the business?','body'=>'Start with a focused conversation about recurring friction, material risk, and the operating model you need next.','cta'=>'Get started')
    );
}

/**
 * Route each pattern action to the most useful next step.
 *
 * @param array<string, string> $item Pattern definition.
 */
function msp_nexus_pattern_cta_url(array $item): string
{
    $routes = array(
        'hero-security' => '/services/cybersecurity/',
        'hero-cloud' => '/services/cloud/',
        'hero-copilot' => '/services/data-ai/',
        'hero-co-managed' => '/services/co-managed-it/',
        'hero-compliance' => '/services/cybersecurity/',
        'hero-continuity' => '/services/continuity/',
        'hero-local' => '/contact/',
        'services-grid' => '/services/',
        'services-managed' => '/services/managed-it/',
        'services-security' => '/services/cybersecurity/',
        'services-cloud' => '/services/cloud/',
        'services-advisory' => '/services/advisory/',
        'services-helpdesk' => '/services/help-desk/',
        'services-network' => '/services/infrastructure/',
        'services-projects' => '/services/projects/',
        'services-communications' => '/services/',
        'services-data' => '/services/data-ai/',
        'trust-logos' => '/about-us/',
        'trust-metrics' => '/about-us/',
        'trust-testimonial' => '/about-us/',
        'trust-case-study' => '/contact/',
        'trust-compliance' => '/services/cybersecurity/',
        'trust-process' => '/about-us/',
        'trust-team' => '/about-us/',
        'trust-guarantee' => '/plans/',
        'trust-locations' => '/contact/',
        'trust-awards' => '/about-us/',
        'content-audience' => '/services/',
        'content-industries' => '/industries/',
        'content-outcomes' => '/services/',
        'content-plan' => '/plans/',
        'content-resources' => '/resources/',
        'content-team' => '/about-us/',
        'content-timeline' => '/services/',
        'content-responsible-ai' => '/services/data-ai/',
        'content-faq' => '/contact/',
        'content-locations' => '/contact/',
        'pricing-three-plans' => '/plans/',
        'pricing-feature-comparison' => '/plans/',
        'pricing-custom-scope' => '/plans/',
        'conversion-support' => '/support/',
        'conversion-newsletter' => '/resources/',
        'conversion-resource' => '/resources/',
        'conversion-event' => '/events/',
        'conversion-careers' => '/careers/',
    );
    $slug = sanitize_key((string) ($item['slug'] ?? ''));
    return $routes[$slug] ?? '/contact/';
}

function msp_nexus_section_pattern_content(array $item): string
{
    if ('msp-nexus-heroes' === $item['category']) {
        $image = esc_url(get_theme_file_uri('assets/images/hero-team-growth-v2.jpg'));
        $heading = esc_html($item['heading']);
        $body = esc_html($item['body']);
        if ('hero-operations' === $item['slug']) {
            $heading = 'Stronger IT teams.<br>Smoother operations.<br><span>Technology built for momentum.</span>';
            $body = 'Bring support, security, cloud, and strategic guidance into one accountable operating model. Reduce disruption, strengthen resilience, and give your people room to do their best work.';
        }
        $cta_url = msp_nexus_pattern_cta_url($item);
        $content = sprintf(
            '<!-- wp:group {"align":"full","className":"msp-nexus-hero","textColor":"white","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull msp-nexus-hero has-white-color has-text-color"><!-- wp:columns {"align":"wide","verticalAlignment":"center","className":"msp-nexus-hero__columns"} --><div class="wp-block-columns alignwide are-vertically-aligned-center msp-nexus-hero__columns"><!-- wp:column {"verticalAlignment":"center","width":"49%%","className":"msp-nexus-hero__copy"} --><div class="wp-block-column is-vertically-aligned-center msp-nexus-hero__copy" style="flex-basis:49%%"><!-- wp:paragraph {"className":"msp-nexus-eyebrow"} --><p class="msp-nexus-eyebrow">%1$s</p><!-- /wp:paragraph --><!-- wp:heading {"level":1,"fontSize":"display","className":"msp-nexus-hero__title"} --><h1 class="wp-block-heading msp-nexus-hero__title has-display-font-size">%2$s</h1><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"large","className":"msp-nexus-hero__body"} --><p class="msp-nexus-hero__body has-large-font-size">%3$s</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="%5$s">%4$s</a></div><!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/services/">Explore services</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"51%%","className":"msp-nexus-hero__visual"} --><div class="wp-block-column is-vertically-aligned-center msp-nexus-hero__visual" style="flex-basis:51%%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="%6$s" alt="Technology operations leader using a tablet within an abstract secure digital network"/></figure><!-- /wp:image --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->',
            esc_html($item['kicker']), $heading, $body, esc_html($item['cta']), esc_url($cta_url), $image
        );
        return str_replace('<img src=', '<img width="1536" height="1024" fetchpriority="high" decoding="async" src=', $content);
    }

    $dynamic = 'msp-nexus-services' === $item['category'] ? '<!-- wp:msp-nexus/service-grid {"align":"wide"} /-->' : '';
    if ('msp-nexus-content' === $item['category'] && 'content-faq' === $item['slug']) {
        $dynamic = '<!-- wp:msp-nexus/faq-list {"align":"wide"} /-->';
    }
    $visuals = array(
        'content-audience' => '<!-- wp:html --><div class="msp-nexus-signal-links"><a href="/services/managed-it/"><span>01</span>Explore fully managed IT <b>↗</b></a><a href="/services/co-managed-it/"><span>02</span>Extend an internal IT team <b>↗</b></a></div><!-- /wp:html -->',
        'content-responsible-ai' => '<!-- wp:html --><ul class="msp-nexus-value-list"><li><b>Smarter operations</b><span>Automate repeatable work while people stay accountable.</span></li><li><b>Proactive support</b><span>Use signals and context to prevent disruption.</span></li><li><b>Personalized service</b><span>Apply the right standards to each environment.</span></li><li><b>Responsible by design</b><span>Protect data, privacy, access, and human oversight.</span></li></ul><!-- /wp:html -->',
        'content-outcomes' => '<!-- wp:html --><div class="msp-nexus-outcome-grid"><article><strong>01</strong><span>Responsive support that keeps work moving</span></article><article><strong>02</strong><span>Resilient systems designed for disruption</span></article><article><strong>03</strong><span>Security decisions connected to business risk</span></article><article><strong>04</strong><span>Clear priorities, ownership, and investment visibility</span></article></div><!-- /wp:html -->',
        'trust-logos' => '<!-- wp:html --><div class="msp-nexus-recognition"><span>One<br>Team</span><span>Proactive<br>Operations</span><span>Visible<br>Ownership</span><span>Continuous<br>Improvement</span></div><!-- /wp:html -->',
        'trust-testimonial' => '<!-- wp:html --><div class="msp-nexus-feature-quote"><p>Good IT operations should feel calm, visible, and ready for what comes next.</p><cite>The service standard we work toward</cite></div><!-- /wp:html -->',
        'content-plan' => '<!-- wp:html --><div class="msp-nexus-plan-card"><span>MANAGED OPERATIONS</span><strong>One accountable plan.</strong><p>People, process, security, cloud, lifecycle, and leadership—structured around the complexity of your environment.</p><a href="/plans/">View the plan architecture ↗</a></div><!-- /wp:html -->',
        'content-resources' => '<!-- wp:html --><div class="msp-nexus-resource-grid"><article><span>IT LEADERSHIP</span><h3>Build an executive IT roadmap</h3><a href="/resources/it-roadmap/">Read the guide ↗</a></article><article><span>CYBERSECURITY</span><h3>Questions for a practical security review</h3><a href="/resources/security-questions/">Use the checklist ↗</a></article><article><span>SERVICE OPERATIONS</span><h3>Service metrics leaders can use</h3><a href="/resources/service-metrics/">Read the article ↗</a></article></div><!-- /wp:html -->',
        'conversion-final' => '<!-- wp:html --><div class="msp-nexus-final-message"><span>WHAT TO EXPECT</span><p>We will clarify the current condition, the decision ahead, and the most responsible next step for your organization.</p></div><!-- /wp:html -->'
    );
    if (isset($visuals[$item['slug']])) {
        $dynamic = $visuals[$item['slug']];
    }
    if ('' === $dynamic) {
        $dynamic = '<!-- wp:html --><div class="msp-nexus-orbit-card"><span aria-hidden="true"></span><p>Strategy connected to day-to-day execution, visible ownership, and measurable progress.</p></div><!-- /wp:html -->';
    }
    $class = 'msp-nexus-section msp-nexus-section--' . sanitize_html_class($item['slug']);
    $cta_url = msp_nexus_pattern_cta_url($item);
    return sprintf(
        '<!-- wp:group {"align":"full","className":"%7$s","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull %7$s"><!-- wp:columns {"align":"wide","verticalAlignment":"top","className":"msp-nexus-section__columns"} --><div class="wp-block-columns alignwide are-vertically-aligned-top msp-nexus-section__columns"><!-- wp:column {"verticalAlignment":"top","width":"42%%","className":"msp-nexus-section__intro"} --><div class="wp-block-column is-vertically-aligned-top msp-nexus-section__intro" style="flex-basis:42%%"><!-- wp:paragraph {"className":"msp-nexus-eyebrow"} --><p class="msp-nexus-eyebrow">%1$s</p><!-- /wp:paragraph --><!-- wp:heading {"level":2,"fontSize":"x-large"} --><h2 class="wp-block-heading has-x-large-font-size">%2$s</h2><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">%3$s</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="%5$s">%4$s</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"top","width":"58%%","className":"msp-nexus-section__visual"} --><div class="wp-block-column is-vertically-aligned-top msp-nexus-section__visual" style="flex-basis:58%%">%6$s</div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->',
        esc_html($item['kicker']), esc_html($item['heading']), esc_html($item['body']), esc_html($item['cta']), esc_url($cta_url), $dynamic, esc_attr($class)
    );
}

/** @return array<int, array<string, mixed>> */
function msp_nexus_page_pattern_definitions(): array
{
    return array(
        array('slug'=>'page-home','title'=>'Page: MSP homepage','patterns'=>array('hero-operations','content-audience','content-responsible-ai','services-grid','content-outcomes','conversion-consultation','trust-logos','trust-testimonial','content-plan','content-resources','conversion-final')),
        array('slug'=>'page-home-security','title'=>'Page: Security-first homepage','patterns'=>array('hero-security','trust-compliance','services-security','content-outcomes','content-timeline','trust-process','content-resources','content-faq','conversion-assessment','conversion-final')),
        array('slug'=>'page-home-co-managed','title'=>'Page: Co-managed IT homepage','patterns'=>array('hero-co-managed','content-audience','services-advisory','services-helpdesk','services-projects','trust-team','trust-process','content-plan','content-faq','conversion-consultation')),
        array('slug'=>'page-home-cloud','title'=>'Page: Cloud and innovation homepage','patterns'=>array('hero-cloud','content-responsible-ai','services-cloud','services-data','content-outcomes','content-resources','trust-process','content-faq','conversion-consultation','conversion-final')),
        array('slug'=>'page-managed-it','title'=>'Page: Managed IT services','patterns'=>array('hero-operations','services-managed','services-helpdesk','trust-process','content-plan','trust-testimonial','content-faq','conversion-consultation')),
        array('slug'=>'page-cybersecurity','title'=>'Page: Cybersecurity','patterns'=>array('hero-security','services-security','trust-compliance','content-timeline','trust-case-study','content-faq','conversion-assessment')),
        array('slug'=>'page-cloud','title'=>'Page: Cloud services','patterns'=>array('hero-cloud','services-cloud','services-network','content-outcomes','trust-case-study','content-faq','conversion-consultation')),
        array('slug'=>'page-co-managed','title'=>'Page: Co-managed IT','patterns'=>array('hero-co-managed','content-audience','services-advisory','services-projects','trust-team','trust-metrics','conversion-final')),
        array('slug'=>'page-compliance','title'=>'Page: Compliance readiness','patterns'=>array('hero-compliance','trust-compliance','services-security','content-timeline','trust-process','content-faq','conversion-assessment')),
        array('slug'=>'page-ai','title'=>'Page: AI readiness','patterns'=>array('hero-copilot','content-responsible-ai','services-data','trust-process','content-resources','content-faq','conversion-consultation')),
        array('slug'=>'page-industries','title'=>'Page: Industries overview','patterns'=>array('hero-local','content-industries','content-outcomes','trust-case-study','trust-locations','conversion-consultation')),
        array('slug'=>'page-industry-detail','title'=>'Page: Industry detail','patterns'=>array('hero-compliance','content-outcomes','services-grid','trust-compliance','trust-case-study','content-faq','conversion-assessment')),
        array('slug'=>'page-about','title'=>'Page: About the MSP','patterns'=>array('hero-local','trust-process','content-team','trust-metrics','trust-logos','content-audience','conversion-final')),
        array('slug'=>'page-resources','title'=>'Page: Resource center','patterns'=>array('hero-cloud','content-resources','conversion-newsletter','conversion-event','conversion-resource')),
        array('slug'=>'page-contact','title'=>'Page: Contact and consultation','patterns'=>array('conversion-consultation','trust-locations','conversion-support','content-faq'))
    );
}

function msp_nexus_register_patterns(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    foreach (msp_nexus_section_pattern_definitions() as $item) {
        register_block_pattern('msp-nexus/' . $item['slug'], array('title' => __($item['title'], 'msp-nexus'), 'categories' => array($item['category']), 'content' => msp_nexus_section_pattern_content($item)));
    }
    foreach (msp_nexus_page_pattern_definitions() as $page) {
        $content = '';
        foreach ($page['patterns'] as $slug) {
            $content .= '<!-- wp:pattern {"slug":"msp-nexus/' . esc_attr($slug) . '"} /-->';
        }
        register_block_pattern('msp-nexus/' . $page['slug'], array('title' => __($page['title'], 'msp-nexus'), 'categories' => array('msp-nexus-pages'), 'blockTypes' => array('core/post-content'), 'content' => $content));
    }
}
/*
 * The generated library is only needed where patterns are browsed or inserted: admin screens, the
 * editor's REST requests, and WP-CLI. Public page views skip building it unless a template or post
 * embeds one of these patterns, in which case it is registered just before that pattern block renders.
 */
add_action('init', static function (): void {
    if (is_admin() || (defined('WP_CLI') && WP_CLI)) {
        msp_nexus_register_patterns();
    }
}, 20);
add_action('rest_api_init', 'msp_nexus_register_patterns');
add_filter('pre_render_block', static function ($pre_render, array $block) {
    if ('core/pattern' === ($block['blockName'] ?? '') && 0 === strpos((string) ($block['attrs']['slug'] ?? ''), 'msp-nexus/')) {
        msp_nexus_register_patterns();
    }
    return $pre_render;
}, 10, 2);
