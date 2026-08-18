# JEM Presentation v0.1.7

Management hardening release for Joomla 6 + JEM 5.0.1. No JEM core changes and no database schema changes.

## Added / hardened

- Server-side validation uses PresentationRegistry as the canonical contract.
- Referenced JEM event must exist; unknown or unsupported profile/layout changes are rejected.
- Existing legacy/planned values remain preservable only when event + profile + layout stay unchanged.
- Modern explicitly supports Standard, Hero and Two Column.
- Planned profiles/layouts remain visible but are disabled for new choices.
- Assigned JEM events are marked in the selector and disabled for normal duplicate selection.
- Database uniqueness remains the final duplicate guard; crafted duplicate-new saves reuse the existing assignment with an explicit message.
- Orphaned assignments are visible when the JEM event has been removed.
- Management actions follow core.create, core.edit and core.delete.
- Integration status identifies the default site template and reports each expected Hero bridge file separately.

## Deferred

- Remote/AJAX JEM event picker for very large event datasets.
- Optional pkg_jempresentation installer for component + runtime plugin; bridge remains opt-in and non-destructive.
- Native JEM event-view hook to replace the Thin Override Bridge when JEM supports it.
