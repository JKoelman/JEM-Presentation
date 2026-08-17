# JEM Presentation — Playwright batch 01

Status: **PASS — 13/13 on local Joomla 6 test environment**

## Baseline

- Joomla 6
- JEM 5.0.1
- `com_jempresentation` 0.1.6.1
- `plg_system_jempresentationruntime` 0.1.8
- Cassiopeia primary reference template
- JEM Playwright structure aligned with the v0.68.20.223 reference package

## Confirmed local result

- Date: 2026-08-17
- Workers: 1
- Result: `13 passed`
- Duration: 1.4 minutes
- No `[JEM Presentation cleanup]` warning was present in the supplied run output.

## First local finding and fix

The initial run on component 0.1.6 failed PW-ADM-001 because the assignment list form did not contain Joomla's standard hidden `boxchecked` field. The Edit and Delete toolbar buttons use list-selection state and both raised the same `joomla-toolbar-button` page error during initialization.

Component 0.1.6.1 restored:

```html
<input type="hidden" name="boxchecked" value="0">
```

No debug-monitor ignore was added. The real list-form contract was fixed instead. The complete batch was then rerun and passed 13/13.

## Test files

- `tests/playwright/jem/helpers-jempresentation.js`
- `tests/playwright/jem/jem-presentation-v0-1-6-admin-runtime.spec.js`

## Scope — 13 tests

1. assignment list health and Integration column — PASS;
2. existing canonical `modern + standard` assignment values — PASS;
3. Standard registry metadata and schematic preview — PASS;
4. Hero live metadata/preview change without submit — PASS;
5. Two Column live metadata/preview and no bridge requirement — PASS;
6. planned Sports profile warning — PASS;
7. planned Route layout warning and preview — PASS;
8. canonical Hero ID persistence and Bridge list route — PASS;
9. duplicate-new-form attempt keeps one event assignment — PASS;
10. read-only runtime/template/bridge/native-API status — PASS;
11. Standard runtime assets plus strict unassigned-event isolation — PASS;
12. Hero runtime assets — PASS;
13. Two Column runtime assets — PASS.

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

All covered runtime routes passed the batch health checks.

## Safety

- no JEM core changes;
- no database schema changes;
- no fixed production event IDs;
- no usernames, passwords, API keys or tokens in this package;
- local administrator credentials remain owned by the existing PlanjeSuiteTests configuration;
- Event Hub/uddeIM/Community Builder implementation assumptions are not dependencies of this batch.

## Batch conclusion

The v0.1.6 management/registry functionality plus the v0.1.6.1 assignment-list toolbar hotfix now form a confirmed green regression baseline. The next development batch can build on this state.
