# JEM Presentation Test Matrix

This file records public regression coverage and locally confirmed results. The Playwright implementation, local configuration, traces, screenshots and reports are intentionally kept outside this public repository.

## Source baseline

| Part | Version | Purpose |
|---|---:|---|
| `com_jempresentation` | 0.1.10 | Management/ACL hardening plus adaptive native-first Hero integration metadata |
| `plg_system_jempresentationruntime` | 0.1.9 | Event resolver/assets plus native `onJemPrepareEventView` Hero-hook support |
| Cassiopeia Thin Override Bridge | 0.1.0 | Compatibility fallback for JEM installations without the native event-view hook |

## Latest confirmed local baseline

The current fully confirmed normal release baseline is **component v0.1.10 + runtime v0.1.9**.

- 2026-08-20: management/runtime + frontend + hardening + ACL recheck — **38 passed**, 1 worker, 5.1 minutes.
- 2026-08-19: updated management/registry/runtime regression against component v0.1.10 + runtime v0.1.9 — **13 passed**, 1 worker, 1.3 minutes.
- 2026-08-19: component v0.1.10 adaptive Hero registry regression — **5 passed**, 1 worker, 18.2 seconds.
- 2026-08-19: component v0.1.9 Integration Status regression — **5 passed**, 1 worker, 20.0 seconds.
- 2026-08-19: bridge-free runtime v0.1.9 native-hook POC — **5 passed**.

The 38-test release regression confirms that the adaptive v0.1.10 management metadata did not regress existing management, runtime, frontend, hardening or ACL behavior. The separate adaptive-registry, Integration Status and bridge-free hook regressions confirm the native-first Hero contract and compatibility fallback behavior.

## Normal release regression — component v0.1.10 + runtime v0.1.9

The suite names retain their historical version labels, but the complete set was rerun against the installed v0.1.10 component and v0.1.9 runtime.

| Group | Tests | Scope | Local result |
|---|---:|---|---|
| Management / registry / runtime | 13 | Assignment health, registry metadata, planned choices, duplicate prevention, Integration Status, runtime asset isolation, native-hook Hero list marker | PASS 13/13 |
| Frontend layout | 8 | Standard, Hero, Two Column, breakpoints, details toggle, functional JEM preservation | PASS 8/8 |
| Component hardening | 8 | Registry capabilities, selector state, crafted saves, invalid values, orphan handling, integration diagnostics | PASS 8/8 |
| ACL configuration / enforcement | 9 | Permissions UI, `core.manage` boundary, create/edit/delete authorization and crafted-task denial | PASS 9/9 |
| **Total** | **38** | **Normal release regression baseline** | **PASS 38/38** |

### Frontend contract retained

- Standard retains JEM right-image markup and the native event toolbar.
- Two Column renders as two columns at 900 px and stacks details above media below the 899.98 px breakpoint.
- Hero remains contained on mobile and preserves the same JEM functional core contract.
- JEM details/compact toggle remains functional.
- Standard, Hero and Two Column preserve toolbar, online-meeting action and registration/attendee visibility behavior.

### Hardening contract retained

| ID | Test | Local result |
|---|---|---|
| HARD-001 | Registry capabilities and planned choices | PASS |
| HARD-002 | Assigned event selector state | PASS |
| HARD-003 | Crafted duplicate-new save reuses existing assignment | PASS |
| HARD-004 | Unknown profile rejected server-side | PASS |
| HARD-005 | Unsupported profile/layout combination rejected | PASS |
| HARD-006 | Missing JEM event rejected | PASS |
| HARD-007 | Removed JEM event remains visible as orphan assignment | PASS |
| HARD-008 | Integration diagnostics remain healthy | PASS |

### ACL contract retained

| ID | Test | Local result |
|---|---|---|
| ACL-001 | Native Permissions configuration for all five actions | PASS |
| ACL-002 | `core.manage` Denied dominates direct list/add/edit routes | PASS |
| ACL-003 | Manage-only toolbar omits New/Edit/Delete/Options as configured | PASS |
| ACL-004 | `core.create` permits normal add flow | PASS |
| ACL-005 | `core.edit` permits editing an existing assignment | PASS |
| ACL-006 | `core.delete` permits deleting a dedicated assignment | PASS |
| ACL-007 | Crafted create without `core.create` is blocked | PASS |
| ACL-008 | Crafted edit without `core.edit` is blocked | PASS |
| ACL-009 | Crafted delete without `core.delete` is blocked | PASS |

## Component v0.1.10 adaptive Hero regression

Version 0.1.10 makes Hero environment-aware without changing its stored canonical ID. The same registry feeds both the assignment editor and the assignment list.

Confirmed **5/5 on 2026-08-19**, 1 worker, 18.2 seconds.

| ID | Test | Expected | Local result |
|---|---|---|---|
| ADAPT-001 | Hero native-first metadata | With native JEM hook detected, Hero publishes `data-integration=native-hook` | PASS |
| ADAPT-002 | Hero bridge role | With native hook detected, Hero publishes `data-bridge-role=fallback`, not required | PASS |
| ADAPT-003 | Hero confirmed status | Hero is `confirmed` rather than `poc` while keeping canonical layout ID `hero` | PASS |
| ADAPT-004 | Standard / Two Column isolation | Standard and Two Column remain `native` with bridge role `none` | PASS |
| ADAPT-005 | Layout switching stability | Standard → Hero → Two Column updates only selected layout metadata and keeps Integration Status healthy | PASS |

## Updated management / registry / runtime regression — component v0.1.10

The historical Hero assignment-list assertion was updated from localized visible text `Bridge` to the stable v0.1.10 contract `data-jempresentation-integration="native-hook"`. No production behavior was changed by this test update.

Confirmed **13/13 on 2026-08-19**, 1 worker, 1.3 minutes.

## Component v0.1.9 Integration Status regression

Component v0.1.9 detects a real `triggerEvent('onJemPrepareEventView', ...)` call in the JEM event view, exposes stable status/role markers in the administrator UI, and treats the Thin Override Bridge as a compatibility fallback when the native hook exists.

Confirmed **5/5 on 2026-08-19**, 1 worker, 20.0 seconds.

| ID | Test | Expected | Local result |
|---|---|---|---|
| INT-001 | Component/runtime status health | Assignment edit opens without technical error; runtime remains active | PASS |
| INT-002 | Native hook detection | Native API reports `data-state=detected` and `data-role=preferred` | PASS |
| INT-003 | Auditable hook source | Diagnostics expose `components/com_jem/views/event/view.html.php` | PASS |
| INT-004 | Bridge fallback classification | Restored bridge reports `data-state=available` and `data-role=fallback` with both file diagnostics | PASS |
| INT-005 | Status stability across layouts | Standard/Hero/Two Column do not mutate environment-level native/bridge diagnostics | PASS |

## Runtime v0.1.9 native JEM hook POC

The local JEM event view contains the proposed `onJemPrepareEventView` trigger immediately before template rendering. Runtime v0.1.9 subscribes through Joomla's `SubscriberInterface`, extracts the JEM view from the Event arguments and applies `fullimage_layout = header` only for the resolved `modern + hero` assignment of the same event.

The POC deliberately disabled only files carrying the exact `JEM Presentation Thin Override Bridge` marker during the run and restored them afterwards.

Confirmed **5/5 on 2026-08-19**.

| ID | Test | Expected | Local result |
|---|---|---|---|
| HOOK-001 | Bridge-free precondition | Cassiopeia Thin Override Bridge is absent during the POC | PASS |
| HOOK-002 | Standard isolation | Standard keeps native right-image markup and receives no Hero mutation | PASS |
| HOOK-003 | Native Hero | Hero emits hook evidence and renders JEM header image without the bridge | PASS |
| HOOK-004 | Two Column isolation | Two Column retains native 900 px behavior and receives no Hero mutation | PASS |
| HOOK-005 | Hero preservation/mobile | Hero remains contained at 390 px and preserves JEM functional contracts | PASS |

## Integration interpretation

With the tested local JEM hook present:

- native `onJemPrepareEventView` is the preferred Hero integration path;
- Hero's effective management integration is `native-hook`;
- the Cassiopeia Thin Override Bridge is a compatibility fallback, not a technical requirement;
- Standard and Two Column remain native and unaffected;
- stored assignments remain `profile=modern` plus canonical layout IDs `standard`, `hero`, or `two-column`;
- JEM Presentation does not install or modify the JEM hook automatically.

## Regression history

- v0.1.6.1 fixed Joomla list-form `boxchecked` compatibility.
- v0.1.7 hardened registry validation, duplicate prevention, orphan visibility and integration diagnostics.
- v0.1.8 completed native Joomla ACL configuration and enforced `core.manage` as the administrator boundary.
- Runtime v0.1.9 added the `onJemPrepareEventView` subscriber. The dedicated bridge-free POC passed 5/5.
- Component v0.1.9 added native-hook detection, stable `data-state`/`data-role` diagnostics and bridge-fallback classification. Its dedicated Integration Status regression passed 5/5.
- Component v0.1.9 + runtime v0.1.9 established the previous full 38/38 baseline.
- Component v0.1.10 changed Hero from static Bridge/POC metadata to adaptive native-first metadata while preserving all canonical assignment values. Its dedicated adaptive Hero regression passed 5/5.
- The historical management regression was aligned with the v0.1.10 native-hook assignment-list marker and passed 13/13.
- The complete release regression was then rerun against **component v0.1.10 + runtime v0.1.9** and passed **38/38** on 2026-08-20, establishing the current confirmed release baseline.

## Repository policy

- No Playwright specs or helpers in this repository.
- No traces, screenshots, HTML reports, test-results or authentication state in this repository.
- No local Joomla credentials, passwords, tokens or session data in this repository.
- Only compact, sanitized regression outcomes are recorded here.
