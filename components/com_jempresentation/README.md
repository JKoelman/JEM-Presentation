# JEM Presentation v0.1.6

## Scope

Management/registry release. No JEM core changes and no database schema changes.
Existing assignment values remain canonical and unchanged.

## Added

- Central `PresentationRegistry` for profile/layout metadata.
- Assignment profile and layout selectors now consume that registry.
- Component options consume the same registry fields.
- Dynamic assignment information panel with:
  - selected profile status;
  - layout description;
  - integration route;
  - bridge requirement;
  - schematic preview.
- Assignment list integration column and translated profile/layout labels.
- Read-only integration status for:
  - runtime plugin active/inactive/missing;
  - active site template;
  - Cassiopeia Hero bridge available/missing/incomplete/conflict;
  - native JEM presentation API status.
- Existing custom JEM overrides are never modified or overwritten.

## Registry status in this release

### Profile

- `modern` — available
- `sports` — planned
- `outdoor` — planned
- `festival` — planned

### Layouts

- `standard` — confirmed, native JEM markup, no bridge
- `hero` — functional POC, compatibility bridge for full image positioning
- `two-column` — confirmed, native JEM markup + CSS Grid, no bridge
- `route` — planned, not implemented by the runtime yet

The planned values remain visible because they already existed in v0.1.5 and may
already be stored. v0.1.6 therefore does not silently rewrite or delete them;
the UI makes their runtime status explicit instead.

## Important

JEM Presentation remains an extension layer. `com_jem` is not modified.
Events without an assignment remain outside JEM Presentation.
