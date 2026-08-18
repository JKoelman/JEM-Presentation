# JEM Presentation

Independent presentation and theme layer for **Joomla 6** and **JEM 5.0.1**.

JEM Presentation enhances individual JEM event presentation without taking ownership of JEM event data, ACL, registrations, actions, or normal event rendering.

## Current source baseline

- `com_jempresentation` **0.1.7** — management hardening, assignments, registry/capabilities, preview and integration diagnostics
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

## v0.1.7 hardening

The component validates assignments server-side against the registry, preserves unchanged legacy values without allowing new unsupported combinations, marks already assigned JEM events, exposes orphaned assignments, applies Joomla ACL to management actions, and provides per-file Hero bridge diagnostics.

See `docs/COMPONENT_HARDENING_V0.1.7.md` and `docs/NATIVE_JEM_HOOK_PROPOSAL.md`.
