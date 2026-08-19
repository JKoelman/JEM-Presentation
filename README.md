# JEM Presentation

Independent presentation and theme layer for **Joomla 6** and **JEM 5.0.1**.

JEM Presentation enhances individual JEM event presentation without taking ownership of JEM event data, ACL, registrations, actions, or normal event rendering.

## Current source baseline

- `com_jempresentation` **0.1.10** — management/ACL hardening plus adaptive native-first Hero integration metadata
- `plg_system_jempresentationruntime` **0.1.9** — runtime resolver/assets plus native `onJemPrepareEventView` Hero-hook support
- Cassiopeia Thin Override Bridge **0.1.0** — compatibility fallback for JEM installations without the native event-view hook

## Principles

- JEM remains owner of event data and business logic.
- JEM Presentation does not own or persist JEM event data.
- Events without an assignment retain normal JEM output.
- CSS/layout is preferred where existing JEM markup is sufficient.
- Compatibility overrides are minimal, opt-in and never overwrite user overrides automatically.
- When JEM exposes the native event-view presentation hook, Hero uses that native path and the bridge becomes fallback only.

## Current registry

- `modern` — available; supports `standard`, `hero`, `two-column`
- `sports`, `outdoor`, `festival` — planned
- `route` — planned and not selectable for new assignments

Hero has an adaptive registry contract:

- native JEM hook detected → `native-hook` + bridge role `fallback`;
- native JEM hook absent → `bridge` + bridge role `required`.

The canonical stored layout ID remains `hero` in both cases.

## Management / ACL

The component uses Joomla component permissions for `core.admin`, `core.manage`, `core.create`, `core.edit` and `core.delete`. Version 0.1.8 added the native Permissions panel and the `core.manage` component boundary. Version 0.1.9 added native-hook diagnostics. Version 0.1.10 makes the assignment editor and assignment list use those diagnostics to report Hero's effective integration route.

## Validation status

The current fully confirmed normal release baseline is **component v0.1.10 + runtime v0.1.9: 38/38 passed** on 2026-08-20 (1 worker, 5.1 minutes).

The v0.1.10 adaptive Hero contract also passed its dedicated **5/5 regression** on 2026-08-19 (1 worker, 18.2 seconds), and the updated management/registry/runtime group passed **13/13** on 2026-08-19 (1 worker, 1.3 minutes). Runtime v0.1.9 additionally retains the confirmed **5/5 bridge-free native-hook POC**, while the Integration Status contract remains confirmed **5/5**.

Together these results confirm the adaptive native-first Hero management contract, the runtime native-hook path, bridge fallback compatibility, and the complete normal regression baseline.

See `docs/TEST_MATRIX.md`, `docs/COMPONENT_HARDENING_V0.1.7.md` and `docs/NATIVE_JEM_HOOK_PROPOSAL.md`.
