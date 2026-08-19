# JEM Presentation

Independent presentation and theme layer for **Joomla 6** and **JEM 5.0.1**.

JEM Presentation enhances individual JEM event presentation without taking ownership of JEM event data, ACL, registrations, actions, or normal event rendering.

## Current source baseline

- `com_jempresentation` **0.1.8** — management hardening plus native Joomla ACL configuration
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

The component uses Joomla component permissions for `core.admin`, `core.manage`, `core.create`, `core.edit` and `core.delete`. Version 0.1.8 adds the native Permissions panel in Component Options, exposes Options only to `core.admin`, and makes `core.manage` a required boundary for both direct component access and assignment add/edit/delete operations.

## Validation status

The latest fully confirmed release baseline remains **component v0.1.8 + runtime v0.1.8: 38/38 passed**. Runtime v0.1.9 additionally completed the dedicated **bridge-free native-hook POC: 5/5 passed** on 2026-08-19 with both Cassiopeia Thin Override Bridge files disabled during the run and restored afterwards.

A full runtime v0.1.9 release baseline still requires the existing 38 regressions to be rerun against v0.1.9.

See `docs/TEST_MATRIX.md`, `docs/COMPONENT_HARDENING_V0.1.7.md` and `docs/NATIVE_JEM_HOOK_PROPOSAL.md`.
