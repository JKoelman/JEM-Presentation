# JEM Presentation Test Matrix

This file records public regression coverage and locally confirmed results. The Playwright implementation, local configuration, traces, screenshots and reports are intentionally kept outside this public repository.

## Source baseline

| Part | Version | Purpose |
|---|---:|---|
| `com_jempresentation` | 0.1.8 | Management hardening, assignment validation, integration diagnostics and native Joomla ACL configuration |
| `plg_system_jempresentationruntime` | 0.1.9 | Event resolver/assets plus native `onJemPrepareEventView` Hero-hook support |
| Cassiopeia Thin Override Bridge | 0.1.0 | Compatibility fallback; no longer technically required when the native JEM hook is present |

## Latest confirmed local baseline

The latest fully confirmed release baseline remains **component v0.1.8 + runtime v0.1.8**:

- 2026-08-19: management/runtime + frontend + hardening recheck — **29 passed**, 1 worker, 4.6 minutes.
- 2026-08-19: ACL configuration + enforcement regression — **9 passed**, 1 worker, 2.6 minutes.
- Combined confirmed v0.1.8 release baseline: **38/38 passed**.

In addition, runtime v0.1.9 with a local JEM `onJemPrepareEventView` trigger completed the dedicated **bridge-free native-hook POC: 5/5 passed** on 2026-08-19. During that run both Cassiopeia Thin Override Bridge files were temporarily disabled and automatically restored afterwards.

A full v0.1.9 release baseline still requires the existing 38 regressions to be rerun against runtime v0.1.9.

## Local regression — v0.1.8 hardening recheck

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

Confirmed 8/8 again on v0.1.8 as part of the 29-test release recheck on 2026-08-19.

## Local regression — v0.1.8 management + runtime recheck

Confirmed 13/13 again on v0.1.8 as part of the 29-test release recheck on 2026-08-19. Coverage includes assignment list health, canonical Modern/Standard values, Standard/Hero/Two Column registry metadata and previews, planned Sports/Route visibility without selection, duplicate prevention, integration status, runtime asset isolation, and no Presentation assets on an unassigned event.

## Local regression — v0.1.8 frontend layout recheck

| ID | Test | Expected | Local result |
|---|---|---|---|
| FRONT-001 | Standard frontend structure | Native right-image JEM structure and event toolbar remain intact | PASS |
| FRONT-002 | Two Column at 900 px | Details and media render in separate desktop columns | PASS |
| FRONT-003 | Two Column at 899 px | Details stack above media at the responsive breakpoint | PASS |
| FRONT-004 | Two Column at 390 px | Single-column detail/media flow stays contained without Presentation overflow | PASS |
| FRONT-005 | JEM details/compact toggle | Native JEM detail-mode toggle remains functional under Two Column | PASS |
| FRONT-006 | Hero bridge/fallback | Available bridge uses JEM header-image position; missing bridge degrades without technical failure | PASS |
| FRONT-007 | Hero at 390 px | Header image or fallback media remains contained on mobile | PASS |
| FRONT-008 | Functional preservation | Standard/Hero/Two Column preserve JEM toolbar, online-meeting action and the same registration/attendee visibility contract | PASS |

Confirmed 8/8 again on v0.1.8 as part of the 29-test release recheck on 2026-08-19. The frontend fixture uses JEM's own administrator UI to create the event, upload/select its image and remove all test data. No direct database or Joomla filesystem mutation is part of the regression workflow.

## Local regression — v0.1.8 ACL configuration and enforcement

Version 0.1.8 completes the missing Joomla ACL configuration layer: the actions declared in `access.xml` are configurable through Component Options, the Options toolbar action requires `core.admin`, and `core.manage` is required for direct component access as well as assignment add/edit/delete authorization.

Confirmed **9/9 on 2026-08-19**.

| ID | Test | Expected | Local result |
|---|---|---|---|
| ACL-001 | Native Permissions configuration | Options exposes rules for core.admin/manage/create/edit/delete | PASS |
| ACL-002 | Manage boundary | core.manage Denied blocks direct list/add/edit access even when mutation actions are Allowed | PASS |
| ACL-003 | Restricted toolbar | With manage only, New/Edit/Delete/Options follow their denied permissions | PASS |
| ACL-004 | Create allowed | core.create permits normal add flow while edit/delete stay unavailable | PASS |
| ACL-005 | Edit allowed | core.edit permits editing an existing assignment while create/delete stay unavailable | PASS |
| ACL-006 | Delete allowed | core.delete permits deleting a dedicated assignment while create/edit stay unavailable | PASS |
| ACL-007 | Crafted create denied | Direct/crafted save cannot create an assignment without core.create | PASS |
| ACL-008 | Crafted edit denied | Direct/crafted save cannot mutate an assignment without core.edit | PASS |
| ACL-009 | Crafted delete denied | Direct list-task submission cannot delete an assignment without core.delete | PASS |

## Local regression — runtime v0.1.9 native JEM hook POC

The local JEM event view contains the proposed `onJemPrepareEventView` trigger immediately before template rendering. Runtime v0.1.9 subscribes through Joomla's `SubscriberInterface`, extracts the JEM view from the Event arguments and applies `fullimage_layout = header` only for the resolved `modern + hero` assignment of the same event.

The POC deliberately disabled only files carrying the exact `JEM Presentation Thin Override Bridge` marker during the run and restored them afterwards. This proves Hero can work through the native hook without a template override.

Confirmed **5/5 on 2026-08-19**.

| ID | Test | Expected | Local result |
|---|---|---|---|
| HOOK-001 | Bridge-free precondition | Cassiopeia Thin Override Bridge is absent during the POC | PASS |
| HOOK-002 | Standard isolation | Standard keeps native right-image markup and receives no Hero hook mutation | PASS |
| HOOK-003 | Native Hero | Hero emits native hook debug evidence and renders the JEM header image without the bridge | PASS |
| HOOK-004 | Two Column isolation | Two Column keeps native 900px two-column behaviour and receives no Hero hook mutation | PASS |
| HOOK-005 | Hero preservation/mobile | Native Hero stays contained at 390px and preserves JEM toolbar, registration and online-meeting contracts | PASS |

## Regression history

- 0.1.6 lacked Joomla's hidden `boxchecked` field on the assignment list. 0.1.6.1 fixed the form contract; the full local management/runtime baseline then passed 13/13.
- The first 0.1.7 orphan verification exposed a local test-fixture cleanup mismatch (`events.remove` versus JEM's current `events.delete`). The test helper was corrected; the component orphan rendering was unchanged. The complete v0.1.7 hardening regression then passed 8/8 on 2026-08-18.
- The first v0.1.7 management/runtime rerun exposed three outdated v0.1.6 UI assumptions: planned Sports and Route values were still actively selected and an already assigned event was reselected. The regression suite was aligned with the v0.1.7 contract; the component was unchanged. The full management/runtime regression then passed 13/13 on 2026-08-18.
- The first frontend-layout run exposed a fixture mismatch: JEM sanitized/renamed the uploaded event image while the local helper still assigned the unsanitized source filename. The component and runtime were unchanged. The helper was corrected to resolve JEM's canonical stored filename through its own image gallery; the complete frontend layout regression then passed 8/8 on 2026-08-18.
- Preparing the ACL regression exposed a real component gap in v0.1.7: `access.xml` declared component actions, but `config.xml` did not expose Joomla's Rules field, and the direct administrator/mutation routes did not consistently treat `core.manage` as the component boundary. Version 0.1.8 completes that configuration/enforcement layer.
- The first v0.1.8 ACL run exposed a local Playwright helper mismatch: Joomla 6.1.2 persisted permission-select changes through `com_config&task=application.store&format=json`, while the helper waited for a `permissions.apply` response. The trace showed successful HTTP 200 ACL store calls. The helper was corrected; JEM Presentation production code was unchanged. The corrected ACL regression then passed 9/9 on 2026-08-19.
- The existing 29 management/runtime, frontend and hardening regressions were rerun unchanged against the installed v0.1.8 component and all passed on 2026-08-19, establishing the complete 38/38 v0.1.8 release baseline.
- Runtime v0.1.9 adds a subscriber for the proposed local JEM `onJemPrepareEventView` extension point. The dedicated bridge-free POC passed 5/5 on 2026-08-19. Both Cassiopeia Thin Override Bridge files were absent during the tests and restored afterwards. Hero therefore no longer requires the bridge when the native JEM hook is available.

## Repository policy

- No Playwright specs or helpers in this repository.
- No traces, screenshots, HTML reports, test-results or authentication state in this repository.
- No local Joomla credentials, passwords, tokens or session data in this repository.
- Only compact, sanitized regression outcomes are recorded here.
