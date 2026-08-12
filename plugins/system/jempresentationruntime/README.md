# JEM Presentation Runtime v0.1.8

## Two Column v0.3 — responsive hardening

Firefox Responsive Design Mode results before this fix:

- 1280 × 800: PASS
- 900 × 800: PASS (two columns)
- 899 × 800: PASS (single column)
- 390 × 844: layout collapsed, but JEM responsive CSS could still show the image before the facts

The current JEM responsive template itself emits:

1. `.jem-event-overview-details`
2. `.jem-event-overview-media`

so the desired semantic source order is already correct.

v0.1.8 explicitly forces the presentation panel to CSS Grid below 900px and
pins:
- details to row 1
- image to row 2

with sufficient specificity/`!important` to survive JEM's later responsive CSS.

## Retest

Only:
- 899 × 800
- 390 × 844

Expected:
facts first, image second, no horizontal overflow.
