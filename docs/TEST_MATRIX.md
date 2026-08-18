# JEM Presentation Test Matrix

This file records public regression coverage and locally confirmed results. The Playwright implementation, local configuration, traces, screenshots and reports are intentionally kept outside this public repository.

## Source baseline

| Part | Version | Purpose |
|---|---:|---|
| `com_jempresentation` | 0.1.7 | Management hardening, registry capabilities, assignment validation and integration diagnostics |
| `plg_system_jempresentationruntime` | 0.1.8 | Event resolver and conditional runtime assets |
| Cassiopeia Thin Override Bridge | 0.1.0 | Optional Hero compatibility POC |

## Latest confirmed local baseline

- 2026-08-18: v0.1.7 management hardening regression — **8 passed**, 1 worker, 1.2 minutes.
- 2026-08-17: management + runtime baseline — **13 passed**, 1 worker, 1.4 minutes, component 0.1.6.1.
- The 13-test management/runtime baseline remains PENDING recheck on component 0.1.7.

## Local regression — v0.1.7 hardening

| ID | Test | Expected | Local result |
|---|---|---|---|
| HARD-001 | Registry capabilities | Modern supports Standard/Hero/Two Column; planned profile/layout disabled | PASS |
| HARD-002 | Assigned event selector state | Existing assigned event marked and disabled | PASS |
| HARD-003 | Crafted duplicate-new save | Existing assignment reused, explicit message, no duplicate | PASS |
| HARD-004 | Unknown profile POST | Server rejects unknown profile | PASS |
| HARD-005 | Unsupported combination POST | Server rejects Modern + Route | PASS |
| HARD-006 | Missing event POST | Server rejects non-existing JEM event ID | PASS |
| HARD-007 | Orphan assignment | Deleted JEM event leaves explicit warning row and retained assignment | PASS |
| HARD-008 | Integration diagnostics | Default site template label + per-file Hero bridge states | PASS |

## ACL follow-up

`core.create`, `core.edit` and `core.delete` are applied in component controllers/toolbars. A dedicated restricted-user permission matrix is planned.

## Regression history

- 0.1.6 lacked Joomla's hidden `boxchecked` field on the assignment list. 0.1.6.1 fixed the form contract; the full local management/runtime baseline then passed 13/13.
- The first 0.1.7 orphan verification exposed a local test-fixture cleanup mismatch (`events.remove` versus JEM's current `events.delete`). The test helper was corrected; the component orphan rendering was unchanged. The complete v0.1.7 hardening regression then passed 8/8 on 2026-08-18.

## Repository policy

- No Playwright specs or helpers in this repository.
- No traces, screenshots, HTML reports, test-results or authentication state in this repository.
- No local Joomla credentials, passwords, tokens or session data in this repository.
- Only compact, sanitized regression outcomes are recorded here.
