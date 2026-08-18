# JEM Presentation Test Matrix

## Source baseline

| Part | Version | Purpose |
|---|---:|---|
| `com_jempresentation` | 0.1.7 | Management hardening, registry capabilities, assignment validation and integration diagnostics |
| `plg_system_jempresentationruntime` | 0.1.8 | Event resolver and conditional runtime assets |
| Cassiopeia Thin Override Bridge | 0.1.0 | Optional Hero compatibility POC |

## Latest confirmed local baseline

- 2026-08-17: batch 01 `jem-presentation-v0-1-6-admin-runtime.spec.js` — **13 passed**, 1 worker, 1.4 minutes, component 0.1.6.1.
- v0.1.7 changes management hardening, so batch 01 is PENDING recheck on 0.1.7.

## Playwright — batch 02 v0.1.7 hardening

| ID | Test | Expected | Local result |
|---|---|---|---|
| PW-HARD-001 | Registry capabilities | Modern supports Standard/Hero/Two Column; planned profile/layout disabled | PENDING |
| PW-HARD-002 | Assigned event selector state | Existing assigned event marked and disabled | PENDING |
| PW-HARD-003 | Crafted duplicate-new save | Existing assignment reused, explicit message, no duplicate | PENDING |
| PW-HARD-004 | Unknown profile POST | Server rejects unknown profile | PENDING |
| PW-HARD-005 | Unsupported combination POST | Server rejects Modern + Route | PENDING |
| PW-HARD-006 | Missing event POST | Server rejects non-existing JEM event ID | PENDING |
| PW-HARD-007 | Orphan assignment | Deleted JEM event leaves explicit warning row and retained assignment | PENDING |
| PW-HARD-008 | Integration diagnostics | Default site template label + per-file Hero bridge states | PENDING |

## ACL follow-up

`core.create`, `core.edit` and `core.delete` are now applied in component controllers/toolbars. A dedicated restricted-user permission matrix is planned.

## Regression history

0.1.6 lacked Joomla's hidden `boxchecked` field on the assignment list. 0.1.6.1 fixed the form contract; full batch 01 then passed 13/13.
