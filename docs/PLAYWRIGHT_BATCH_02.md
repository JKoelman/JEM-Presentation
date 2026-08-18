# JEM Presentation — Playwright batch 02

Status: PREPARED / LOCAL RUN PENDING

Baseline: Joomla 6, JEM 5.0.1, component 0.1.7, runtime 0.1.8, Cassiopeia reference template.

## Scope — 8 hardening tests

1. Registry capabilities + planned options disabled.
2. Assigned JEM events marked/disabled for normal duplicate selection.
3. Crafted duplicate-new save reuses existing assignment with explicit message.
4. Unknown profile POST rejected server-side.
5. Modern + Route POST rejected server-side.
6. Non-existing JEM event ID rejected server-side.
7. Removed JEM event leaves explicit orphan assignment.
8. Default site template label + per-file Hero bridge diagnostics.

After this batch passes, rerun batch 01. Healthy v0.1.7 target: **13 + 8 = 21 passed**.

ACL is implemented but should receive a dedicated restricted-user permission matrix rather than a superficial simulation in this batch.
