# JEM Presentation

Independent presentation and theme layer for **Joomla 6** and **JEM 5.0.1**.

JEM Presentation enhances individual JEM event presentation without taking ownership of JEM event data, ACL, registrations, actions, or normal event rendering.

## Current source baseline

- `com_jempresentation` **0.1.8** — management hardening plus native Joomla ACL configuration
- `plg_system_jempresentationruntime` **0.1.8** — runtime resolver and conditional presentation assets
- Cassiopeia Thin Override Bridge **0.1.0** — optional Hero compatibility fallback

## Principles

- JEM remains owner of event data and business logic.
- `com_jem` is not modified by JEM Presentation.
- Events without an assignment retain normal JEM output.
- CSS/layout is preferred where existing JEM markup is sufficient.
- Compatibility overrides are minimal, opt-in and never overwrite user overrides automatically.
- A native JEM event-view presentation hook should replace the Hero bridge when available.

## Current registry

- `modern` — available; supports `standard`, `hero`, `two-column`
- `sports`, `outdoor`, `festival` — planned
- `route` — planned and not selectable for new assignments

## Management / ACL

The component uses Joomla component permissions for `core.admin`, `core.manage`, `core.create`, `core.edit` and `core.delete`. Version 0.1.8 adds the native Permissions panel in Component Options, exposes Options only to `core.admin`, and guards the direct administrator component entry with `core.manage`.

## Validation status

The latest fully confirmed local baseline is component v0.1.7 with **29/29** regression tests passing. The v0.1.8 ACL configuration changes are pending their dedicated restricted-user regression.

See `docs/TEST_MATRIX.md`, `docs/COMPONENT_HARDENING_V0.1.7.md` and `docs/NATIVE_JEM_HOOK_PROPOSAL.md`.
