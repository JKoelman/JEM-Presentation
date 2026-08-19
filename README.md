# JEM Presentation

Independent presentation and theme layer for **Joomla 6** and **JEM 5.0.1**.

JEM Presentation enhances individual JEM event presentation without taking ownership of JEM event data, ACL, registrations, actions, or normal event rendering.

## Current source baseline

- `com_jempresentation` **0.1.9** — management/ACL hardening plus native JEM hook and bridge-fallback diagnostics
- `plg_system_jempresentationruntime` **0.1.9** — runtime resolver/assets plus native `onJemPrepareEventView` Hero-hook support
- Cassiopeia Thin Override Bridge **0.1.0** — compatibility fallback for JEM installations without the native event-view hook

## Principles

- JEM remains owner of event data and business logic.
- JEM Presentation does not own or persist JEM event data.
- Events without an assignment retain normal JEM output.
- CSS/layout is preferred where existing JEM markup is sufficient.
- Compatibility overrides are minimal, opt-in and never overwrite user overrides automatically.
- When JEM exposes the native event-view presentation hook, Hero no longer requires a template override.

## Current registry

- `modern` — available; supports `standard`, `hero`, `two-column`
- `sports`, `outdoor`, `festival` — planned
- `route` — planned and not selectable for new assignments

## Management / ACL

The component uses Joomla component permissions for `core.admin`, `core.manage`, `core.create`, `core.edit` and `core.delete`. Version 0.1.8 added the native Permissions panel and the `core.manage` component boundary. Version 0.1.9 keeps that behavior and adds integration diagnostics for the native JEM event-view hook.

## Integration diagnostics

Component v0.1.9 inspects `components/com_jem/views/event/view.html.php` for a real `triggerEvent('onJemPrepareEventView', ...)` call. When detected, the native hook is reported as the **preferred** integration path. The Cassiopeia Thin Override Bridge is then reported as a **compatibility fallback** rather than a primary requirement. Existing custom/incomplete overrides remain visible and are never overwritten automatically.

## Validation status

The current fully confirmed normal release baseline is **component v0.1.9 + runtime v0.1.9: 38/38 passed** on 2026-08-19 (1 worker, 4.9 minutes).

Runtime v0.1.9 also completed the dedicated **bridge-free native-hook POC: 5/5 passed**. Component v0.1.9 Integration Status diagnostics completed their dedicated **5/5 local regression**. Together these results confirm both the normal 0.1.9/0.1.9 regression baseline and the native-hook/bridge-fallback integration contract.

See `docs/TEST_MATRIX.md`, `docs/COMPONENT_HARDENING_V0.1.7.md` and `docs/NATIVE_JEM_HOOK_PROPOSAL.md`.
