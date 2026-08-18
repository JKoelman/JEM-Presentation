# Proposal: native JEM event-view presentation hook

## Goal

Add a small, generic extension point to JEM's single-event view so external extensions can adjust **request-local presentation state** after JEM has prepared the event and before JEM renders its own template.

This is not a JEM Presentation dependency. JEM continues to own event data, ACL, registration, actions and final template rendering.

## Minimal proposed API

```php
$dispatcher->triggerEvent('onJemPrepareEventView', [$this]);
```

A listener could then adjust request-local state:

```php
public function onJemPrepareEventView($view): void
{
    if (!isset($view->item) || !is_object($view->item)) return;
    $view->item->fullimage_layout = 'header';
}
```

## Placement

The hook should run after normal event-view preparation and access checks, and immediately before normal template rendering (`parent::display($tpl)`). Initially it only needs to cover the normal HTML event view.

## Contract

- presentation/view state only; no persistence implied;
- no bypass of JEM ACL, registration or business logic;
- no listener means unchanged JEM behavior;
- existing Joomla template overrides remain supported;
- no dependency on JEM Presentation.

## Motivation

JEM already uses a dispatcher and Joomla content events in the event view. Those events are content-oriented and do not provide a stable JEM-specific contract for final view presentation state. The current Hero Thin Override Bridge only sets `fullimage_layout = header` in memory and then includes JEM's original template. A native hook would remove that template coupling while preserving stored Presentation assignment IDs.

Tracked in JEM Presentation issue #1; direct upstream issue creation was blocked by the connected GitHub integration.
