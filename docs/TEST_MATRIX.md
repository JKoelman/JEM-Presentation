# JEM Presentation Test Matrix

This file records public regression coverage and locally confirmed results. The Playwright implementation, local configuration, traces, screenshots and reports are intentionally kept outside this public repository.

## Source baseline

| Part | Version | Purpose |
|---|---:|---|
| `com_jempresentation` | 0.1.10 | Management/ACL hardening plus adaptive native-first Hero integration metadata |
| `plg_system_jempresentationruntime` | 0.1.9 | Event resolver/assets plus native `onJemPrepareEventView` Hero-hook support |
| Cassiopeia Thin Override Bridge | 0.1.0 | Compatibility fallback for JEM installations without the native event-view hook |

## Latest confirmed local baseline

The latest fully confirmed normal release baseline remains **component v0.1.9 + runtime v0.1.9**.

- 2026-08-19: management/runtime + frontend + hardening + ACL recheck — **38 passed**, 1 worker, 4.9 minutes.
- 2026-08-19: component v0.1.9 Integration Status regression — **5 passed**, 1 worker, 20.0 seconds.
- 2026-08-19: bridge-free runtime v0.1.9 native-hook POC — **5 passed**.
- 2026-08-19: component v0.1.10 adaptive Hero registry regression — **5 passed**, 1 worker, 18.2 seconds.

Component v0.1.10 is the current source baseline. Its dedicated adaptive-registry contract is confirmed, but the normal release baseline is not promoted until the historical management assertion is aligned with the native-first Hero contract and the complete 38-test release regression is rerun.

## Normal release regression — component v0.1.9 + runtime v0.1.9

The existing suite names retain their historical version labels, but the complete set was rerun unchanged against the installed v0.1.9 component and v0.1.9 runtime.

| Group | Tests | Scope | Local result |
|---|---:|---|---|
| Management / registry / runtime | 13 | Assignment health, registry metadata, planned choices, duplicate prevention, Integration Status, runtime asset isolation | PASS 13/13 |
| Frontend layout | 8 | Standard, Hero, Two Column, breakpoints, details toggle, functional JEM preservation | PASS 8/8 |
| Component hardening | 8 | Registry capabilities, selector state, crafted saves, invalid values, orphan handling, integration diagnostics | PASS 8/8 |
| ACL configuration / enforcement | 9 | Permissions UI, `core.manage` boundary, create/edit/delete authorization and crafted-task denial | PASS 9/9 |
| **Total** | **38** | **Normal release regression baseline** | **PASS 38/38** |

## Component v0.1.10 adaptive Hero regression

Version 0.1.10 makes Hero environment-aware without changing its stored canonical ID. The same registry feeds both the assignment editor and the assignment list.

Confirmed **5/5 on 2026-08-19**, 1 worker, 18.2 seconds.

| ID | Test | Expected | Local result |
|---|---|---|---|
| ADAPT-001 | Hero native-first metadata | With native JEM hook detected, Hero publishes `data-integration=native-hook` | PASS |
| ADAPT-002 | Hero bridge role | With native hook detected, Hero publishes `data-bridge-role=fallback`, not required | PASS |
| ADAPT-003 | Hero confirmed status | Hero is `confirmed` rather than `poc` while keeping canonical layout ID `hero` | PASS |
| ADAPT-004 | Standard / Two Column isolation | Standard and Two Column remain `native` with bridge role `none` | PASS |
| ADAPT-005 | Layout switching stability | Standard → Hero → Two Column updates only the selected layout metadata and keeps Integration Status healthy | PASS |

Before the next normal-release rerun, the historical management assertion that expected Hero to display `Bridge` in the assignment list must be aligned with the new stable contract. On the tested hook-capable JEM installation the intended assignment-list marker is now `data-jempresentation-integration="native-hook"`.

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
- The full 38-test normal release regression was rerun against **component v0.1.9 + runtime v0.1.9** and passed **38/38** on 2026-08-19.
- Component v0.1.10 changes the Hero registry from static Bridge/POC metadata to adaptive native-first metadata while preserving all canonical assignment values. Its dedicated adaptive Hero regression passed **5/5** on 2026-08-19.

## Repository policy

- No Playwright specs or helpers in this repository.
- No traces, screenshots, HTML reports, test-results or authentication state in this repository.
- No local Joomla credentials, passwords, tokens or session data in this repository.
- Only compact, sanitized regression outcomes are recorded here.
