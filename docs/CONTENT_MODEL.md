# Content model

MSP Nexus Core keeps business content portable in normal WordPress posts, terms, metadata, revisions, and REST responses.

| Type | Purpose | Important metadata |
|---|---|---|
| Service | Managed and professional capabilities | Core title, content, excerpt, image, order |
| Industry | Market-specific context | Core fields |
| Business Outcome | Buyer goals and measurable change | Core fields |
| Case Study | Situation, approach, and verified results | Industry, result summary, result metric |
| Testimonial | Approved quotations | Person, role, organization |
| Team Member | Leadership and specialist profiles | Role, LinkedIn URL |
| Location | Real offices and service areas | Address, coordinates, area, hours, phone, email |
| Resource | Guides, briefs, videos, and tools | Download URL, reading time |
| Webinar or Event | Scheduled educational content | Start/end, timezone, format, registration URL |
| Partner | Authorized relationships | URL and tier |
| Certification or Award | Verifiable proof | Issuer, validity date, verification URL |
| Pricing Plan | Editable commercial presentation | Audience, display mode, features, highlight, disclaimer, CTA |
| FAQ | Reusable question and answer | Core title/content/order |

Relationship taxonomies cover service categories, industries, business outcomes, resource types, topics, and departments. These are public, hierarchical where useful, REST-visible, and retain content if the theme changes. Normal WordPress posts remain the editorial blog.

All `_msp_` metadata is registered with explicit sanitization, REST visibility where intended, and edit capability checks. `_msp_nexus_demo_key` is private import provenance and requires `manage_options`.
