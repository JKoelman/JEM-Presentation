# JEM Presentation v0.1.10

Adaptive Hero integration metadata release for Joomla 6 + JEM 5.0.1. No database schema changes and no change to stored assignment IDs or values.

## Added in v0.1.10

- Hero is no longer statically described as a Bridge-only POC.
- When JEM exposes `onJemPrepareEventView`, Hero is reported as using the native JEM event-view hook.
- The Cassiopeia Thin Override Bridge is then reported as a compatibility fallback rather than a requirement.
- When the native hook is unavailable, Hero automatically reports the compatibility bridge as its active integration route.
- The assignment editor exposes stable `data-integration` and `data-bridge-role` metadata for the selected layout.
- The assignment list uses the same environment-aware integration decision as the editor.
- Hero is promoted from `poc` to `confirmed`; the underlying stored layout ID remains `hero`.

## Existing v0.1.9 diagnostics retained

- Native JEM hook detection remains read-only and auditable.
- Existing custom or incomplete JEM overrides remain visible and are never overwritten automatically.
- The runtime plugin remains the component that actually consumes the native hook.

## Compatibility

Stored assignments remain unchanged:

- profile: `modern`
- layouts: `standard`, `hero`, `two-column`

No migration of existing assignments is required.

## Validation status

The last fully confirmed baseline before this source change is component v0.1.9 + runtime v0.1.9 with **38/38** normal regressions passing, plus **5/5** Integration Status and **5/5** bridge-free native-hook POC. Version 0.1.10 requires its dedicated adaptive-registry regression before promotion.
