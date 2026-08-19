# Architecture

## Goal

JEM Presentation is an independent presentation layer above JEM. It does not replace JEM and does not take ownership of JEM event data or business logic.

A native JEM event-view hook is the preferred integration point when available. The optional Thin Override Bridge remains a compatibility fallback for JEM installations without that hook.

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
|     + native onJemPrepareEventView listener
|
+-- presentation profiles/layouts
|     modern / standard / hero / two-column / ...
|
+-- optional Template Bridge
      compatibility fallback for JEM installations
      without the native event-view hook
```

## Runtime contract

A public JEM event route is resolved to its JEM event ID. If an assignment exists, the runtime resolves the canonical profile/layout IDs and conditionally loads only the required presentation assets.

For `modern + hero`, runtime v0.1.9 listens to `onJemPrepareEventView`. When JEM exposes that hook, the runtime adjusts only request-local view state (`fullimage_layout = header`) before the normal JEM event template is rendered.

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

Preferred and tested implementation: native JEM `onJemPrepareEventView` hook.

Compatibility implementation: optional Cassiopeia Thin Override Bridge when the native hook is not available.

### Level 4 — True content composition

Examples include profile-specific labels, composite hero content with date/location/CTA, fundamental block reordering, and presentation-specific content blocks.

This level should preferably use native JEM extension points rather than a large template override or a replacement renderer.

## Native hook and bridge fallback

Resolver preference:

```text
native JEM onJemPrepareEventView available?
    YES -> native hook is preferred
           Hero integration = native-hook
           bridge role = fallback

    NO  -> compatibility bridge can provide Hero image positioning
           Hero integration = bridge
           bridge role = required
```

The canonical stored assignment does not change between these environments:

```text
profile = modern
layout  = hero
```

Component v0.1.10 makes the management registry environment-aware so the assignment editor and assignment list report the effective route rather than statically describing Hero as Bridge-only.

## Thin Override Bridge

The bridge remains intentionally small and optional. On JEM versions without the native hook it can influence the JEM image render state required by Hero and then delegate to the installed original JEM event template.

It must not:

- change JEM database values;
- copy or replace the complete JEM event renderer;
- manipulate final HTML in `onAfterRender`;
- reorganize presentation-critical DOM structure with JavaScript;
- overwrite an existing user/template override.

When the native hook is present, the bridge is not technically required for Hero.

## Central registry

Profile/layout IDs and their metadata belong in one canonical registry. Dropdowns, management metadata, integration labels, and preview information consume that registry instead of maintaining separate hardcoded value lists.

Hero is an adaptive registry entry:

```text
hero:
    status: confirmed
    integration: adaptive
    native hook present -> native-hook
    native hook absent  -> bridge
```

Canonical IDs remain stable and language-independent.

## Current confirmed layouts

- `modern + standard`: confirmed; no bridge required.
- `modern + hero`: native-hook path confirmed; bridge-free POC passed; bridge retained as compatibility fallback.
- `modern + two-column`: desktop and responsive baseline confirmed; no bridge required.

Two Column breakpoint: `@media (max-width: 899.98px)`.

At 900 px the two-column layout remains active; at 899 px and below it becomes one column with event data before the artwork.
