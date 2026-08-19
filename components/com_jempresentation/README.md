# JEM Presentation v0.1.9

Integration diagnostics release for Joomla 6 + JEM 5.0.1. No JEM data ownership changes and no database schema changes.

## Added in v0.1.9

- Integration Status detects the proposed native JEM `onJemPrepareEventView` hook in `components/com_jem/views/event/view.html.php`.
- A detected hook is shown as the preferred integration path for Hero.
- An installed Cassiopeia Thin Override Bridge is reported as a compatibility fallback when the native hook exists.
- When the native hook exists and no bridge is installed, Integration Status reports that the bridge is not required.
- Existing custom or incomplete JEM overrides remain visible as diagnostics instead of being overwritten.
- The native-hook source path is displayed so the detected integration point is auditable.

## Existing v0.1.8 ACL hardening retained

- Native Joomla Permissions configuration for `core.admin`, `core.manage`, `core.create`, `core.edit` and `core.delete`.
- `core.manage` remains the administrator boundary for direct component and assignment mutation routes.
- Assignment validation, duplicate prevention, orphan handling and registry safeguards remain unchanged.

## Current registry

- `modern` — available; supports `standard`, `hero`, `two-column`
- `sports`, `outdoor`, `festival` — planned
- `route` — planned and not selectable for new assignments

## Integration model

- Preferred when available: native JEM `onJemPrepareEventView` hook.
- Compatibility fallback: Cassiopeia Thin Override Bridge.
- JEM Presentation never installs or modifies the JEM hook automatically.

## Validation status

The last fully confirmed normal release baseline before this component change is component v0.1.8 + runtime v0.1.9 with **38/38** regressions passing. The dedicated bridge-free native-hook POC also passed **5/5**. Component v0.1.9 requires its own Integration Status regression before it is promoted to the confirmed baseline.
