# JEM Presentation Test Matrix

This file tracks the regression baseline for JEM Presentation. New or changed Playwright tests and locally confirmed results should be recorded here.

## Source baseline

| Part | Version | Purpose |
|---|---:|---|
| `com_jempresentation` | 0.1.6 | Management, assignments, registry, preview and integration status |
| `plg_system_jempresentationruntime` | 0.1.8 | Event resolver and conditional runtime assets |
| Cassiopeia Thin Override Bridge | 0.1.0 | Optional Hero compatibility POC |

## Existing manual baseline

| ID | Area | Expected | Status |
|---|---|---|---|
| MAN-STD-001 | Modern + Standard | JEM keeps its normal event HTML; presentation assets only | PASS (local baseline) |
| MAN-HERO-001 | Modern + Hero | Runtime resolves Hero and the bridge can place the JEM image in header mode without database changes | PASS (functional POC) |
| MAN-TWO-1280 | Modern + Two Column | Two columns at 1280 × 800 | PASS |
| MAN-TWO-900 | Modern + Two Column | Two columns at 900 × 800 | PASS |
| MAN-TWO-899 | Modern + Two Column | One column at 899 × 800; event data before artwork | PASS |
| MAN-TWO-390 | Modern + Two Column | Responsive one-column layout at 390 × 844 | PASS |

## Playwright — v0.1.6 management regression

| ID | Test | Expected | Local result |
|---|---|---|---|
| PW-ADM-001 | Assignment list opens | No technical/PHP/JS/console error | PENDING |
| PW-ADM-002 | Existing assignment opens | Existing canonical values load correctly | PENDING |
| PW-ADM-003 | Standard metadata | Description, Native integration and `Bridge not required` shown | PENDING |
| PW-ADM-004 | Hero metadata | Hero preview and current bridge requirement shown | PENDING |
| PW-ADM-005 | Two Column metadata | Two-column preview, Native integration and no bridge requirement shown | PENDING |
| PW-ADM-006 | Layout switch | Preview and metadata update without form submit | PENDING |
| PW-ADM-007 | Canonical ID persistence | Saving preserves `standard`, `hero` and `two-column` IDs | PENDING |
| PW-ADM-008 | Assignment uniqueness | Existing event assignment remains unique | PENDING |
| PW-ADM-009 | Integration column | Assignment list shows Native/Bridge route from registry | PENDING |
| PW-ADM-010 | Integration status | Runtime/template/bridge status renders read-only and safely | PENDING |

## Playwright — runtime regression

| ID | Test | Expected | Local result |
|---|---|---|---|
| PW-RUN-001 | Assigned Standard event | Modern/Standard assets only; normal JEM rendering remains functional | PENDING |
| PW-RUN-002 | Assigned Hero event | Hero runtime resolves without PHP/JS/console error | PENDING |
| PW-RUN-003 | Assigned Two Column event | Two-column runtime resolves and remains responsive | PENDING |
| PW-RUN-004 | Unassigned event | Completely normal JEM output; no Presentation assets | PENDING |
| PW-RUN-005 | Runtime health | No PHP, pageerror, console or failed-resource regression | PENDING |

## Test data policy

Playwright tests should create their own data where practical and clean it up afterwards. Tests must not depend on one permanent JEM event ID when the required fixture can be created by the test itself.
