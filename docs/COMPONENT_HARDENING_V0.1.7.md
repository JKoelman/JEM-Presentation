# JEM Presentation v0.1.7 hardening

## Implemented

1. Server-side registry validation for JEM event, profile, layout and supported combinations.
2. Planned profiles/layouts visible but disabled for new choices; unchanged legacy values remain preservable.
3. Existing event assignments are marked in the event selector and disabled for normal duplicate selection.
4. Database uniqueness remains the final duplicate guard; crafted duplicate-new saves reuse the existing assignment with an explicit message.
5. Orphaned assignments are visible when their JEM event no longer exists.
6. Management toolbars/controllers follow Joomla `core.create`, `core.edit` and `core.delete` ACL.
7. Integration status now calls the detected template the **default site template** rather than the active route template.
8. Hero bridge diagnostics show both expected override files and whether each is Presentation bridge, custom/conflicting or missing.
9. Registry capabilities explicitly define which layouts an available profile supports.

## Deliberately deferred

- Remote/AJAX JEM event picker for installations with hundreds/thousands of events.
- Future `pkg_jempresentation` installer for component + runtime plugin; bridge stays opt-in and non-destructive.
- Native JEM event-view presentation hook; see `NATIVE_JEM_HOOK_PROPOSAL.md`.
