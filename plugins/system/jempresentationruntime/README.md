# JEM Presentation Runtime v0.1.9

## Native JEM event-view hook POC

Runtime v0.1.9 subscribes to the proposed native JEM event-view hook:

`onJemPrepareEventView`

The listener uses Joomla's `SubscriberInterface` event contract, reads the JEM view from the generic Event arguments, and only changes request-local presentation state when all of these are true:

- site client
- Presentation assignment resolved for the same JEM event
- profile `modern`
- layout `hero`

For that case only, it sets:

```php
$view->item->fullimage_layout = 'header';
```

Standard and Two Column are not mutated by the hook.

The runtime emits a debug message containing `onJemPrepareEventView` when `jempresentation_debug=1` and the Hero hook is actually applied. This is intended for the local bridge-free POC.

The previously confirmed responsive Two Column behaviour remains unchanged: 900px is two columns, 899px and below stack details before media.
