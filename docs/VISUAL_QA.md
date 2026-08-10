# Visual and responsive QA

## Tested artifact

The review used the packaged `msp-nexus` theme and `msp-nexus-core` plugin on a clean WordPress 7.0.3 installation provided by the official WordPress Playground CLI. The production-safe starter was imported twice to exercise idempotency before the homepage was inspected.

## Browser inspection — 2026-08-08

| Width | Viewport | Horizontal overflow | Navigation | Image load | Browser warnings |
|---|---:|---:|---|---|---:|
| Desktop | 1440 × 1000 | 0 px | Full navigation | Complete | 0 |
| Tablet | 768 × 1024 | 0 px | Overlay menu | Complete | 0 |
| Mobile | 390 × 844 | 0 px | Overlay menu; compact header | Complete | 0 |

The rendered page contains exactly one H1 and one each of the header, main, and footer landmarks. The hero has meaningful alternative text. The labeled core Navigation controls were used to open and dismiss the mobile menu. The page also includes a visible skip link, focus styles, reduced-motion handling, responsive reflow, and no horizontal overflow.

## Lighthouse

The final audit is stored in `artifacts/lighthouse-home.json`:

- Performance: 95
- Accessibility: 100
- Best Practices: 100
- SEO: 100
- Largest Contentful Paint: 2.7 seconds
- Cumulative Layout Shift: 0

The measurement includes the temporary local WebAssembly WordPress runtime. Lighthouse wrote a valid report and then emitted a Windows temporary-profile cleanup warning; this did not affect the report.

## Captures

- `artifacts/screenshots/wordpress-home-desktop-1440.png`
- `artifacts/screenshots/wordpress-home-tablet-768.png`
- `artifacts/screenshots/wordpress-home-mobile-390.png`

## Dataprise visual reaffirmation

The live Dataprise homepage was reviewed at desktop and mobile sizes from the initial hero through its footer. The redesigned MSP experience now follows the reference's material visual characteristics:

- an uninterrupted near-black campaign canvas and compact sticky navigation;
- a white and blue-violet display headline paired with a right-weighted human technology image;
- large, deliberate negative-space transitions;
- audience forks, responsible-AI value points, and a long two-column service matrix;
- restrained glowing geometry, thin blue borders, and clipped-corner panels;
- prominent recognition, testimonial, managed-plan, and resource sections;
- a large blue-gradient final conversion panel and framed multi-column footer; and
- a mobile flow that keeps the copy first, hero image second, and uses the native accessible Navigation overlay.

The implementation remains a clean-room design: it uses original copy, an independently generated fictional person, original CSS geometry, distinct branding, and no Dataprise logos, source code, photography, icons, or proprietary assets.

## Remaining environment-specific checks

Before production launch, repeat browser QA against the customer's hosting stack, real content, licensed assets, consent platform, selected SEO/forms plugins, mail delivery, CDN/cache, and analytics configuration. Also verify RTL and long translations with the actual translation catalog and run WooCommerce checkout tests if WooCommerce is enabled.
