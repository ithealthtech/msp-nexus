# Design system

MSP Nexus uses a technical but human visual language: deep navy surfaces, high-legibility neutrals, electric cyan for actions, and restrained violet for data/AI accents. Rounded geometry is modest; borders and spacing do more work than decoration.

## Principles

- Communicate operational confidence, clarity, and measurable outcomes.
- Preserve a minimum 4.5:1 text contrast and visible keyboard focus.
- Keep body measure near 68 characters and use fluid type without extreme jumps.
- Treat motion as optional enhancement and honor `prefers-reduced-motion`.
- Use original abstract network and infrastructure imagery, never literal copies of reference sites.

## Breakpoints

Block layouts are intrinsically responsive. The few navigation-specific transitions use 48rem and 75rem. Content widths are `44rem` for reading, `76rem` for wide layouts, and edge gutters use `clamp(1rem, 3vw, 2.5rem)`.

## Component conventions

- Block CSS uses the `msp-nexus-` prefix.
- Plugin blocks use the `msp-nexus/` namespace.
- Every interactive control has an accessible name, keyboard behavior, and non-script fallback.
- Icons are inline, original SVG with `currentColor`; decorative icons are hidden from assistive technology.
- Patterns use real editable core blocks wherever dynamic data is not required.
