# JEM Presentation

Independent presentation and theme layer for **Joomla 6** and **JEM 5.0.1**.

JEM Presentation is designed to enhance the presentation of individual JEM events without taking ownership of JEM event data, ACL, registrations, actions, or normal event rendering.

## Status

Early development / proof-of-concept.

Current source baseline:

- `com_jempresentation` **0.1.6** — management, assignments and central profile/layout registry
- `plg_system_jempresentationruntime` **0.1.8** — runtime resolver and conditional presentation assets
- Cassiopeia Thin Override Bridge **0.1.0** — optional compatibility fallback for the current Hero image position

## Principles

- JEM remains the owner of event data and business logic.
- `com_jem` is not modified by this project.
- Events without a JEM Presentation assignment keep the normal JEM output.
- CSS/layout is preferred where the existing JEM markup is sufficient.
- A minimal compatibility bridge is used only where JEM render state currently needs to be influenced.
- Existing user/template overrides must never be overwritten automatically.
- A future native JEM presentation/render extension point should replace the compatibility bridge without changing stored assignment IDs.

## Repository structure

```text
components/
  com_jempresentation/                Joomla administrator component

plugins/
  system/
    jempresentationruntime/           Runtime resolver + conditional assets

bridges/
  cassiopeia/                         Optional Thin Override Bridge (POC)

docs/
  ARCHITECTURE.md                     Architecture and integration levels
  TEST_MATRIX.md                      Regression and Playwright matrix
```

## Current registry

### Profiles

- `modern` — available
- `sports` — planned
- `outdoor` — planned
- `festival` — planned

### Layouts

- `standard` — confirmed; native JEM markup; no bridge
- `hero` — functional POC; compatibility bridge currently needed for full image positioning
- `two-column` — confirmed; native JEM markup + CSS Grid; no bridge
- `route` — planned; not yet implemented by the runtime

Stored profile/layout IDs are canonical and language-independent.

## Compatibility target

- Joomla 6
- JEM 5.0.1
- Cassiopeia as the primary reference template

## Development rule

Before adding a feature, determine whether it belongs to a theme, a layout within existing JEM markup, a render-position integration, or true content composition. Avoid turning a presentation layer into a replacement JEM renderer.
