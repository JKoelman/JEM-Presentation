# JEM Presentation Test Matrix

This file records public regression coverage and locally confirmed results. The Playwright implementation, local configuration, traces, screenshots and reports are intentionally kept outside this public repository.

## Source baseline

| Part | Version | Purpose |
|---|---:|---|
| `com_jempresentation` | 0.1.7 | Management hardening, registry capabilities, assignment validation and integration diagnostics |
| `plg_system_jempresentationruntime` | 0.1.8 | Event resolver and conditional runtime assets |
| Cassiopeia Thin Override Bridge | 0.1.0 | Optional Hero compatibility POC |

## Latest confirmed local baseline

- 2026-08-18: v0.1.7 management + runtime regression — **13 passed**, 1 worker, 1.4 minutes.
- 2026-08-18: v0.1.7 management hardening regression — **8 passed**, 1 worker, 1.2 minutes.
- Combined confirmed v0.1.7 baseline: **21/21 passed**.

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

## Local regression — v0.1.7 management + runtime

Confirmed 13/13 on 2026-08-18. Coverage includes assignment list health, canonical Modern/Standard values, Standard/Hero/Two Column registry metadata and previews, planned Sports/Route visibility without selection, duplicate prevention, integration status, runtime asset isolation, and no Presentation assets on an unassigned event.

## Planned local regression — v0.1.7 frontend layout contract

| ID | Test | Expected | Local result |
|---|---|---|---|
| FRONT-001 | Standard frontend structure | Native right-image JEM structure and event toolbar remain intact | PENDING |
| FRONT-002 | Two Column at 900 px | Details and media render in separate desktop columns | PENDING |
| FRONT-003 | Two Column at 899 px | Details stack above media at the responsive breakpoint | PENDING |
| FRONT-004 | Two Column at 390 px | Single-column detail/media flow stays contained without Presentation overflow | PENDING |
| FRONT-005 | JEM details/compact toggle | Native JEM detail-mode toggle remains functional under Two Column | PENDING |
| FRONT-006 | Hero bridge/fallback | Available bridge uses JEM header-image position; missing bridge degrades without technical failure | PENDING |
| FRONT-007 | Hero at 390 px | Header image or fallback media remains contained on mobile | PENDING |
| FRONT-008 | Functional preservation | Standard/Hero/Two Column preserve JEM toolbar, online-meeting action and the same registration/attendee visibility contract | PENDING |

The frontend fixture uses JEM's own administrator UI to create the event, upload/select its image and remove all test data. No direct database or Joomla filesystem mutation is part of the regression workflow.

## ACL follow-up

`core.create`, `core.edit` and `core.delete` are applied in component controllers/toolbars. A dedicated restricted-user permission matrix is planned.

## Regression history

- 0.1.6 lacked Joomla's hidden `boxchecked` field on the assignment list. 0.1.6.1 fixed the form contract; the full local management/runtime baseline then passed 13/13.
- The first 0.1.7 orphan verification exposed a local test-fixture cleanup mismatch (`events.remove` versus JEM's current `events.delete`). The test helper was corrected; the component orphan rendering was unchanged. The complete v0.1.7 hardening regression then passed 8/8 on 2026-08-18.
- The first v0.1.7 management/runtime rerun exposed three outdated v0.1.6 UI assumptions: planned Sports and Route values were still actively selected and an already assigned event was reselected. The regression suite was aligned with the v0.1.7 contract; the component was unchanged. The full management/runtime regression then passed 13/13 on 2026-08-18.
- The first frontend-layout run exposed a fixture mismatch: JEM sanitized/renamed the uploaded event image while the local helper still assigned the unsanitized source filename. The component and runtime were unchanged. The local helper now resolves the canonical stored filename through JEM's own image gallery before linking it to the event; frontend layout recheck remains pending.

## Repository policy

- No Playwright specs or helpers in this repository.
- No traces, screenshots, HTML reports, test-results or authentication state in this repository.
- No local Joomla credentials, passwords, tokens or session data in this repository.
- Only compact, sanitized regression outcomes are recorded here.