import { mkdir, writeFile } from 'node:fs/promises';
import { createHash } from 'node:crypto';

const content = [];
const blocks = (paragraphs) => paragraphs.map((text) => `<!-- wp:paragraph --><p>${text}</p><!-- /wp:paragraph -->`).join('');
const add = (item) => content.push({ excerpt: '', content: '', status: 'publish', ...item });

const services = [
  ['managed-it','Managed IT Operations','An accountable operating model for support, monitoring, maintenance, vendors, lifecycle, and planning.'],
  ['co-managed-it','Co-Managed IT','Specialist capacity, coverage, and mature operations that extend an internal IT team.'],
  ['cybersecurity','Managed Cybersecurity','Layered prevention, detection, response, recovery, governance, and executive risk visibility.'],
  ['cloud','Cloud Services','Secure, observable, cost-aware Microsoft cloud and hybrid infrastructure operations.'],
  ['infrastructure','Network & Infrastructure','Reliable connectivity, wireless, servers, edge security, documentation, and lifecycle standards.'],
  ['help-desk','Employee Support','Responsive human support with context, ownership, communication, and measurable follow-through.'],
  ['continuity','Backup & Business Continuity','Recovery capabilities connected to business priorities, incident roles, communications, and testing.'],
  ['advisory','Technology Advisory','Budgeting, architecture, risk decisions, roadmaps, and business-aligned technology reviews.'],
  ['projects','Technology Projects','Disciplined discovery, design, change control, delivery, adoption, and operational handoff.'],
  ['data-ai','Data, Automation & Responsible AI','Practical data integration, reporting, automation, governance, and AI readiness.']
];
for (const [slug, title, excerpt] of services) add({ key:`service-${slug}`, post_type:'msp_service', slug, title, excerpt, content:blocks([excerpt, 'This capability works best as part of an integrated service model with documented ownership, recurring reviews, and a prioritized improvement roadmap.', 'Final scope, platforms, coverage, responsibilities, and commitments should be documented during discovery and contracting.']) });

const industries = [
  ['healthcare','Healthcare','Support care delivery, privacy, uptime, clinical workflows, and distributed teams.'],
  ['financial-services','Financial Services','Strengthen security, supervision, resilience, evidence, and client-service operations.'],
  ['legal','Legal Services','Protect confidential work while improving document, identity, collaboration, and support workflows.'],
  ['manufacturing','Manufacturing','Connect office and plant operations with resilient infrastructure, secure access, and vendor coordination.'],
  ['construction','Construction & Engineering','Support field teams, project collaboration, mobile access, job sites, and lifecycle planning.'],
  ['nonprofit','Nonprofit Organizations','Make limited resources go further with clear standards, secure collaboration, and predictable planning.'],
  ['professional-services','Professional Services','Deliver a responsive, secure technology experience for knowledge-intensive client teams.'],
  ['multi-location','Multi-Location Business','Establish one operating standard across locations without losing local context.']
];
for (const [slug,title,excerpt] of industries) add({ key:`industry-${slug}`, post_type:'msp_industry', slug, title, excerpt, content:blocks([excerpt, 'Technology decisions should reflect the workflows, constraints, dependencies, and risks that shape the organization.', 'Requirements vary by organization and jurisdiction; specific security, privacy, and compliance obligations must be validated during discovery.']) });

const outcomes = [
  ['productive-workforce','Build a More Productive Workforce','Reduce recurring friction and give employees a consistent route to effective support.'],
  ['cyber-resilience','Improve Cyber Resilience','Reduce likelihood, limit business impact, and recover through tested roles and controls.'],
  ['predictable-cost','Create More Predictable IT Cost','Connect service scope, lifecycle, consumption, projects, and risk decisions to a visible plan.'],
  ['executive-visibility','Increase Executive Visibility','Turn service, risk, investment, and roadmap data into decisions leaders can inspect.'],
  ['scale-confidently','Scale with Confidence','Standardize identity, devices, collaboration, infrastructure, onboarding, and governance as the organization grows.'],
  ['modernize-safely','Modernize Without Disruption','Sequence change around dependencies, adoption, continuity, and measurable operational handoff.']
];
for (const [slug,title,excerpt] of outcomes) add({ key:`outcome-${slug}`, post_type:'msp_outcome', slug, title, excerpt, content:blocks([excerpt, 'A useful outcome page connects the current condition to business impact, practical capabilities, adoption work, measures, and a clear next decision.']) + '<!-- wp:pattern {"slug":"msp-nexus/conversion-consultation"} /-->' });

const plans = [
  ['essential','Essential IT','A baseline for standardized support, monitoring, maintenance, and service reviews.'],
  ['resilient','Resilient IT','An expanded model with layered security operations, continuity, and governance.'],
  ['strategic','Strategic IT','A comprehensive model with deeper advisory, roadmap leadership, and complex multi-site coordination.']
];
for (const [slug,title,excerpt] of plans) add({ key:`plan-${slug}`, post_type:'msp_pricing_plan', slug, title, excerpt, content:blocks([excerpt, 'Every engagement is scoped around the organization, environment, coverage requirements, risk profile, and roadmap. Contact the team for a documented recommendation.']), meta:{ _msp_price_mode:'Contact for pricing', _msp_audience:'Organizations seeking a documented managed-services scope', _msp_disclaimer:'Availability, scope, terms, and pricing are confirmed in a written proposal.' } });

const cases = [
  ['harbor-health','Sample: Harbor Health Builds a Resilient Support Standard','Healthcare','Reduced fictional priority-one incident recurrence by 38% over twelve sample months.'],
  ['meridian-legal','Sample: Meridian Legal Modernizes Secure Collaboration','Legal services','Moved 96% of fictional active matters into a governed collaboration standard.'],
  ['forge-manufacturing','Sample: Forge Manufacturing Connects Five Facilities','Manufacturing','Improved fictional monitored network availability from 98.7% to 99.8%.'],
  ['civic-nonprofit','Sample: CivicWorks Creates a Predictable IT Roadmap','Nonprofit','Shifted fictional unplanned technology spend down by 24% in the sample model.']
];
for (const [slug,title,industry,metric] of cases) add({ key:`case-${slug}`, post_type:'msp_case_study', status:'draft', slug, title, excerpt:`A clearly fictional case-study example for ${industry}.`, content:blocks(['Sample situation: fragmented tools, unclear ownership, recurring disruption, and limited decision visibility.', 'Sample approach: discovery, stabilization, operating standards, adoption support, governance, and a sequenced roadmap.', `Sample result: ${metric} This metric is fictional and must never be published as a real claim.`]), meta:{ _msp_client_industry:industry, _msp_result_summary:'Fictional demonstration result', _msp_result_metric:metric } });

const testimonials = [
  ['alex-rivera','Alex Rivera','Chief Operating Officer','Harbor Health','The team gave us a clearer operating rhythm and made technology decisions easier to lead.'],
  ['jordan-lee','Jordan Lee','Managing Partner','Meridian Legal','Support became more consistent, and the roadmap helped us separate urgent work from important work.'],
  ['morgan-patel','Morgan Patel','VP of Operations','Forge Manufacturing','We finally had one view of risk, reliability, ownership, and the next set of investments.'],
  ['casey-brooks','Casey Brooks','Executive Director','CivicWorks','The team helped our fictional organization make disciplined choices without losing sight of the mission.'],
  ['taylor-kim','Taylor Kim','IT Director','Summit Engineering','The co-managed model added coverage and specialist depth while our internal team stayed in control.'],
  ['riley-johnson','Riley Johnson','Finance Director','Bluefield Services','The planning cadence made cost drivers and lifecycle decisions much easier to explain.']
];
for (const [slug,name,role,organization,quote] of testimonials) add({ key:`testimonial-${slug}`, post_type:'msp_testimonial', status:'draft', slug, title:`Sample testimonial — ${name}`, content:blocks([`“${quote}”`, 'This person and organization are fictional. Replace only with an approved customer quotation.']), meta:{ _msp_person_name:name, _msp_person_role:role, _msp_organization:organization } });

const team = [
  ['maya-chen','Maya Chen','Chief Executive Officer'],['daniel-okafor','Daniel Okafor','Chief Technology Officer'],['sofia-martinez','Sofia Martinez','VP, Client Operations'],
  ['noah-williams','Noah Williams','Director of Cybersecurity'],['priya-shah','Priya Shah','Director of Cloud & Data'],['ethan-brooks','Ethan Brooks','Director of Strategic Advisory']
];
for (const [slug,title,role] of team) add({ key:`team-${slug}`, post_type:'msp_team', status:'draft', slug, title, excerpt:`Fictional ${role} profile for the starter library.`, content:blocks([`${title} is a fictional team member created solely to demonstrate an editable leadership profile.`, 'Replace the biography, credentials, portrait, links, and role with reviewed information before launch.']), meta:{ _msp_person_role:role } });

const locations = [
  ['north-harbor','North Harbor Office','100 Example Avenue, North Harbor, NY 10000','Northeast'],
  ['lakeview','Lakeview Office','200 Sample Street, Lakeview, IL 60000','Midwest'],
  ['redwood','Redwood Office','300 Demonstration Way, Redwood, CA 90000','West']
];
for (const [slug,title,address,area] of locations) add({ key:`location-${slug}`, post_type:'msp_location', status:'draft', slug, title:`Sample: ${title}`, excerpt:`Fictional ${area} service location.`, content:blocks(['This address, office, and service area are fictional demo content. Replace them before launch.']), meta:{ _msp_address:address, _msp_phone:'(800) 555-0199', _msp_service_area:area } });

const resources = [
  ['it-roadmap','Build an Executive IT Roadmap','Guide','publish',['Start with business priorities, operating friction, material risk, lifecycle obligations, and known growth plans. Group the findings into outcomes leaders can understand rather than a list of tools.', 'Sequence initiatives by dependency, risk, effort, and business timing. Assign an accountable owner, an expected decision date, and a practical measure of progress to every roadmap item.', 'Review the roadmap on a recurring cadence. Update assumptions, completed work, new risks, cost forecasts, and the next decisions so the plan remains useful instead of becoming a static document.']],
  ['security-questions','Questions for a Practical Security Review','Checklist','publish',['Begin with identity: who has access, how privileged accounts are controlled, how multifactor authentication is enforced, and how access is removed when roles change.', 'Review devices, cloud services, backups, monitoring, incident responsibilities, third-party access, user education, and recovery testing. Ask for evidence that controls operate as described.', 'Finish with a prioritized action list that names owners, dependencies, expected risk reduction, target dates, and the way each improvement will be verified.']],
  ['service-metrics','Service Metrics Leaders Can Use','Article','publish',['Useful service reporting connects technical work to employee experience, operational risk, and planned improvement. Ticket volume alone rarely explains whether technology is helping the business.', 'Review response and resolution in context, recurring issue patterns, satisfaction, aging work, security exposure, lifecycle risk, project progress, and roadmap decisions. Include trends and exceptions instead of presenting percentages without context.', 'Every review should end with named actions, owners, decisions, and follow-up dates. The goal is a shared operating rhythm that makes progress visible and problems harder to ignore.']],
  ['co-managed-model','Brief: Designing a Co-Managed IT Model','Brief'],
  ['cloud-cost','Article: Four Cloud Cost Signals to Watch','Article'],['incident-tabletop','Toolkit: Incident Tabletop Starter','Toolkit'],['continuity-test','Checklist: Business Continuity Test','Checklist'],
  ['ai-readiness','Guide: Responsible AI Readiness','Gated guide'],['identity-baseline','Article: Identity Is the New Operating Perimeter','Article'],['lifecycle-budget','Template: Technology Lifecycle Budget','Template'],
  ['vendor-risk','Brief: Practical Vendor Risk Review','Brief'],['annual-planning','Webinar: Technology Planning Without the Fire Drill','Webinar']
];
for (const [slug,title,type,status = 'draft',article] of resources) add({ key:`resource-${slug}`, post_type:'msp_resource', status, slug, title:status === 'publish' ? title : `Sample ${title}`, excerpt:status === 'publish' ? `${type} for turning technology priorities into visible, accountable action.` : `Original fictional ${type.toLowerCase()} entry for the starter resource library.`, content:blocks(article ?? [`This is a sample ${type.toLowerCase()} landing page. Replace it with a complete, reviewed, original resource before enabling public promotion or collection of contact details.`, 'Explain the intended audience, what the visitor will learn, format, access conditions, privacy use, and an accessible alternative.']), meta:{ _msp_reading_time:type === 'Webinar' ? '45 minutes' : '7 minutes' } });

add({ key:'event-annual-planning', post_type:'msp_event', status:'draft', slug:'technology-planning-without-the-fire-drill', title:'Sample webinar: Technology Planning Without the Fire Drill', excerpt:'A clearly fictional webinar listing.', content:blocks(['This webinar and its speakers are fictional demo content. Replace dates, presenters, registration terms, accessibility accommodations, recording availability, and privacy details before launch.']), meta:{ _msp_event_start:'2030-01-15T14:00:00-05:00', _msp_event_end:'2030-01-15T15:00:00-05:00', _msp_event_timezone:'America/New_York', _msp_event_format:'Online sample event' } });

for (const [slug,title,tier] of [['microsoft','Sample technology partner: Microsoft','Sample cloud platform'],['cisco','Sample technology partner: Cisco','Sample infrastructure platform'],['veeam','Sample technology partner: Veeam','Sample continuity platform']]) add({ key:`partner-${slug}`, post_type:'msp_partner', status:'draft', slug, title, excerpt:'Fictional partner placeholder; do not imply an actual relationship.', content:blocks(['This relationship entry is a fictional editable sample. Verify authorization, program status, trademark rules, and current tier before publication.']), meta:{ _msp_partner_tier:tier } });

for (const [slug,title,issuer] of [['security-program','Sample Security Program Recognition','Example issuer'],['service-quality','Sample Service Quality Award','Example publisher']]) add({ key:`certification-${slug}`, post_type:'msp_certification', status:'draft', slug, title, excerpt:'Fictional proof placeholder requiring verification.', content:blocks(['This certification or award is fictional demo content. Replace only with a current, verifiable credential and approved usage language.']), meta:{ _msp_issuer:issuer } });

const faqs = [
  ['onboarding','What happens during onboarding?','A strong transition inventories dependencies, documents standards, addresses urgent risk, communicates with users, and establishes an improvement roadmap.'],
  ['co-managed','Can you work with an internal IT team?','Yes. A co-managed model should define responsibilities, tools, escalation, coverage, documentation, and shared measures before service begins.'],
  ['pricing','What affects managed service pricing?','Common factors include users, sites, devices, hours, scope, security requirements, platform complexity, projects, consumption, and current-state remediation.'],
  ['security','Is cybersecurity included?','The exact security scope belongs in a reviewed service agreement. Avoid broad promises; identify controls, responsibilities, exclusions, reporting, and response paths.'],
  ['onsite','Is onsite support available?','Onsite coverage depends on geography, plan, urgency, scheduling, and the agreement. Confirm the available coverage during discovery.'],
  ['tools','Will existing tools need to change?','Discovery should identify where existing tools remain appropriate and where standardization materially improves security, supportability, cost, or visibility.'],
  ['projects','Are projects included?','Some plans include defined project capacity while others price projects separately. State the actual commercial model clearly.'],
  ['switching','Can we switch from another provider?','Yes. A responsible transition coordinates access, documentation, vendors, backups, security, user communication, and continuity without disparaging the outgoing provider.']
];
for (const [slug,title,answer] of faqs) add({ key:`faq-${slug}`, post_type:'msp_faq', slug, title, content:blocks([answer]) });

const pages = [
  ['home','Homepage','Managed technology services homepage.','<!-- wp:pattern {"slug":"msp-nexus/page-home"} /-->'],
  ['about-us','About us','A practical operating partner for support, security, cloud, and technology planning.','<!-- wp:pattern {"slug":"msp-nexus/page-about"} /-->','publish','page-no-title'],
  ['careers','Careers — Content Review Required','Starter careers page.',blocks(['This draft is a starting point for the careers page.', 'Add accurate culture, equal-opportunity, accessibility, location, compensation, hiring-process, privacy, and open-role information reviewed for applicable law before publication.']),'draft'],
  ['support','Client Support','A clear route for existing clients who need technical help.',blocks(['Existing clients should use the verified support portal for service requests. For urgent or high-impact issues, follow the escalation instructions in your service agreement.']) + '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"metadata":{"bindings":{"url":{"source":"msp-nexus/settings","args":{"field":"support_url"}}}}} --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Open the support portal</a></div><!-- /wp:button --></div><!-- /wp:buttons -->'],
  ['privacy','Privacy Policy — Professional Review Required','Placeholder only.',blocks(['This placeholder is not legal advice and is not a privacy policy. A qualified professional must document actual controllers, data categories, purposes, legal bases, sharing, transfers, retention, security, rights, contact routes, and jurisdiction-specific obligations.']),'draft'],
  ['accessibility','Accessibility Statement — Professional Review Required','Placeholder only.',blocks(['This placeholder is not a conformance claim. Document the actual standard, testing scope, known limitations, remediation process, accessible alternatives, feedback route, and review date.']),'draft'],
  ['cookies','Cookie Notice — Professional Review Required','Placeholder only.',blocks(['Do not publish this placeholder as a cookie notice. Inventory actual storage and tracking technologies, classify purposes, document duration and providers, and integrate an appropriate consent platform where required.']),'draft'],
  ['legal','Terms & Legal Notices — Professional Review Required','Placeholder only.',blocks(['This placeholder is not legal advice or a contract. Qualified counsel must prepare terms that match the organization, services, jurisdictions, intellectual property, acceptable use, disclaimers, and dispute process.']),'draft'],
  ['contact','Contact us','Start a conversation about the technology outcomes your organization needs.',blocks(['Tell us what is changing, what is getting in the way, and what a better technology outcome looks like.']) + '<!-- wp:msp-nexus/consultation-form {"heading":"Plan a consultation"} /-->']
];
for (const [slug,title,excerpt,body,status = 'publish',template = ''] of pages) add({ key:`page-${slug}`, post_type:'page', status, template, slug, title, excerpt, content:body });

const manifest = {
  schema: 1,
  version: '2.1.0',
  brand: 'MSP Nexus production-safe starter',
  notice: 'Generic services, industries, outcomes, plans, FAQs, and core pages publish by default. Unverified proof, people, locations, resources, events, relationships, and legal placeholders remain drafts until reviewed.',
  counts: content.reduce((acc, item) => ({ ...acc, [item.post_type]: (acc[item.post_type] ?? 0) + 1 }), {}),
  content
};

await mkdir('demo', { recursive: true });
const encoded = `${JSON.stringify(manifest, null, 2)}\n`;
const digest = createHash('sha256').update(encoded).digest('hex');
await writeFile('demo/msp-nexus-content.json', encoded);
await writeFile('packages/msp-nexus-core/demo/content.json', encoded);
await writeFile('packages/msp-nexus-core/demo/content.sha256', `${digest}  content.json\n`);
console.log(`Generated ${content.length} production-safe starter records.`);
