# JEM Presentation — Playwright batch 01

Status: PREPARED / LOCAL RUN PENDING

## Baseline

- Joomla 6
- JEM 5.0.1
- `com_jempresentation` 0.1.6
- `plg_system_jempresentationruntime` 0.1.8
- Cassiopeia primary reference template
- JEM Playwright structure aligned with the v0.68.20.223 reference package

## Test files

- `tests/playwright/jem/helpers-jempresentation.js`
- `tests/playwright/jem/jem-presentation-v0-1-6-admin-runtime.spec.js`

## Scope — 13 tests

1. assignment list health and Integration column;
2. existing canonical `modern + standard` assignment values;
3. Standard registry metadata and schematic preview;
4. Hero live metadata/preview change without submit;
5. Two Column live metadata/preview and no bridge requirement;
6. planned Sports profile warning;
7. planned Route layout warning and preview;
8. canonical Hero ID persistence and Bridge list route;
9. duplicate-new-form attempt keeps one event assignment and reuses the existing record;
10. read-only runtime/template/bridge/native-API status;
11. Standard runtime assets plus strict unassigned-event isolation;
12. Hero runtime assets;
13. Two Column runtime assets.

## Autonomous fixtures

The batch creates:

- one JEM category;
- one assigned JEM event;
- one unassigned JEM event;
- one JEM Presentation assignment.

The fixture uses the existing JEM administrator helpers rather than database shortcuts.

## Cleanup contract

Cleanup follows the mature JEM/Event Hub pattern:

1. restore/confirm administrator session;
2. delete the JEM Presentation assignment;
3. trash -> remove the unassigned event;
4. trash -> remove the assigned event;
5. trash -> remove the JEM category;
6. close the temporary administrator context.

Each step runs through `cleanupStep()` with an explicit timeout. Cleanup failures are reported as warnings using the prefix `[JEM Presentation cleanup]`; one cleanup failure does not prevent later cleanup steps from being attempted.

## Runtime health

Frontend runtime tests use the existing JEM debug monitor and validate the conditional stylesheet set:

- Standard: `modern.css` only;
- Hero: `modern.css` + `hero.css`;
- Two Column: `modern.css` + `two-column.css`;
- unassigned event: no JEM Presentation runtime stylesheet.

## Safety

- no JEM core changes;
- no database schema changes;
- no fixed production event IDs;
- no usernames, passwords, API keys or tokens in this package;
- local administrator credentials remain owned by the existing PlanjeSuiteTests configuration;
- Event Hub/uddeIM/Community Builder implementation assumptions are not dependencies of this batch.

## Expected current result

`PENDING` until run locally against the installed v0.1.6 / runtime v0.1.8 baseline.

Expected healthy run: `13 passed` with no `[JEM Presentation cleanup]` warnings.
