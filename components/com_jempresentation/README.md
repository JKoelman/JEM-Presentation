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

Component v0.1.10 adaptive Hero metadata passed its dedicated **5/5** regression on 2026-08-19 (1 worker, 18.2 seconds). After aligning the historical Hero assignment-list assertion with the stable `data-jempresentation-integration="native-hook"` contract, the management/registry/runtime group passed **13/13** on 2026-08-19 (1 worker, 1.3 minutes).

The complete normal release regression was then rerun against **component v0.1.10 + runtime v0.1.9** and passed **38/38** on 2026-08-20 (1 worker, 5.1 minutes). This establishes v0.1.10 as the current fully confirmed component baseline.

The previously confirmed **5/5 Integration Status** regression and **5/5 bridge-free native-hook POC** remain part of the supporting integration evidence.
