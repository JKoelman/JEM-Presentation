# JEM Presentation — Cassiopeia Thin Override Bridge v0.1.0

## Status

Proof of concept.

This ZIP is intentionally **not** a Joomla extension installer.

It contains two small Joomla template overrides for Cassiopeia:

    templates/cassiopeia/html/com_jem/event/default.php
    templates/cassiopeia/html/com_jem/event/responsive/default.php

## What the bridge does

For a request resolved by JEM Presentation as:

    profile = modern
    layout  = hero

the bridge sets, in memory only:

    $this->item->fullimage_layout = 'header';

It then loads the currently installed original JEM event template.

For all other assignments, the bridge changes nothing and immediately delegates
to the original JEM template.

## What the bridge does NOT do

- it does not modify com_jem;
- it does not modify #__jem_events;
- it does not save `fullimage_layout`;
- it does not copy JEM's large event template;
- it does not use onAfterRender;
- it does not move DOM nodes with JavaScript.

## Future JEM compatibility

If JEM later adds a native presentation/plugin extension point, JEM Presentation
should prefer that native API.

The intended resolver order is:

    native JEM presentation API available?
        YES -> use native API
        NO  -> optional Thin Override Bridge

The bridge is therefore a compatibility fallback, not the permanent core API.

## CRITICAL conflict check

Before copying these files, check whether either destination already exists.

Do NOT overwrite an existing custom JEM override.

Existing destination examples:

    C:\wamp2\www\Joomla6T2\templates\cassiopeia\html\com_jem\event\default.php
    C:\wamp2\www\Joomla6T2\templates\cassiopeia\html\com_jem\event\responsive\default.php

If an existing override is present, stop and inspect it first.

## Manual POC install

1. Back up the Joomla site/template.
2. Extract this ZIP somewhere temporary.
3. Copy the `templates` directory into the Joomla root, preserving paths.
4. Clear Joomla/template cache if needed.
5. Keep the JEM Presentation Runtime plugin enabled.

## Test matrix

### A — Modern + Hero with image

Example:

    event #1289
    profile = modern
    layout = hero

Expected:

- runtime still resolves `modern + hero`;
- JEM itself renders the event image using its `header` image mode;
- the image is no longer rendered as the ordinary right-side detail image;
- Hero CSS remains active;
- no JEM database value is changed.

### B — Modern + Standard

Expected:

- bridge delegates immediately;
- JEM keeps the configured/global image position;
- only Modern CSS is active.

### C — Unassigned event

Expected:

- original JEM behavior;
- no Presentation CSS;
- no forced header image.

## Rollback

Delete only these bridge files:

    templates/cassiopeia/html/com_jem/event/default.php
    templates/cassiopeia/html/com_jem/event/responsive/default.php

If the directories then become empty, they may also be removed.

The JEM component itself does not need to be restored because it was never changed.
