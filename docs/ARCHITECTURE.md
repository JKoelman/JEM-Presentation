# Architecture

## Goal

JEM Presentation is an independent presentation layer above JEM. It does not replace JEM and should not require changes to JEM core for the current integration.

## Ownership boundary

JEM remains responsible for:

- event data;
- ACL;
- registration;
- links and actions;
- normal event HTML rendering.

JEM Presentation stores only its own assignment data. An assignment conceptually maps a JEM event ID to a presentation profile and layout.

```text
context_type = event
context_id   = <JEM event ID>
profile      = modern
layout       = standard | hero | two-column
params       = ...
```

JEM tables are not modified for presentation assignments.

## Components

```text
JEM Presentation
|
+-- com_jempresentation
|     management, assignments, registry, integration status
|
+-- plg_system_jempresentationruntime
|     route/event resolver + conditional assets
|
+-- presentation profiles/layouts
|     modern / standard / hero / two-column / ...
|
+-- optional Template Bridge
      minimal compatibility bridge where JEM render state
      currently needs to be influenced
```

## Runtime contract

A public JEM event route is resolved to its JEM event ID. If an assignment exists, the runtime resolves the canonical profile/layout IDs and conditionally loads only the required presentation assets.

An event without an assignment must remain normal JEM output.

## Integration levels

### Level 1 — Theme

Examples: Modern, Festival, Sports, Outdoor.

Implementation: conditional CSS/assets.

### Level 2 — Layout within existing JEM markup

Examples: Standard, Two Column.

Implementation: CSS Grid/responsive presentation while JEM keeps rendering its own HTML.

### Level 3 — Influence JEM render position

Example: Hero.

Current implementation: optional Cassiopeia Thin Override Bridge.

Preferred future implementation: a native JEM presentation/render hook.

### Level 4 — True content composition

Examples include profile-specific labels, composite hero content with date/location/CTA, fundamental block reordering, and presentation-specific content blocks.

This level should preferably be supported by a future native JEM extension point rather than a large template override or a replacement renderer.

## Thin Override Bridge

The current bridge is intentionally small. For the active request only, it can influence the JEM image render state required by the Hero presentation, then delegate to the installed original JEM event template.

It must not:

- change JEM database values;
- copy or replace the complete JEM event renderer;
- manipulate final HTML in `onAfterRender`;
- reorganize presentation-critical DOM structure with JavaScript;
- overwrite an existing user/template override.

Resolver preference over time:

```text
native JEM presentation API available?
    YES -> use native API
    NO  -> optional compatibility bridge
```

## Central registry

Profile/layout IDs and their metadata belong in one canonical registry. Dropdowns, management metadata, integration labels, and preview information must consume that registry instead of maintaining separate hardcoded value lists.

Canonical IDs must remain stable and language-independent.

## Current confirmed layouts

- `modern + standard`: local baseline confirmed; no bridge required.
- `modern + hero`: functional POC confirmed; bridge currently used for full Hero image positioning.
- `modern + two-column`: desktop and responsive baseline confirmed; no bridge required.

Two Column breakpoint: `@media (max-width: 899.98px)`.

At 900 px the two-column layout remains active; at 899 px and below it becomes one column with event data before the artwork.
