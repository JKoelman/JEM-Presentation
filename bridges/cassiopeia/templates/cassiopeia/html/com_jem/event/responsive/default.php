<?php
/**
 * JEM Presentation Thin Override Bridge
 *
 * IMPORTANT:
 * - This is NOT a copy of the JEM event template.
 * - It changes only in-memory presentation state for the current request.
 * - It then includes JEM's currently installed original event template.
 * - No JEM database value is changed.
 *
 * Compatibility fallback:
 * If JEM gains a native presentation extension point in the future,
 * JEM Presentation should prefer that native API and this bridge can be removed.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$app = Factory::getApplication();

$profile = (string) $app->get('jempresentation.profile', '');
$layout  = (string) $app->get('jempresentation.layout', '');

// Modern + Hero asks JEM to use its own existing header-image renderer.
// This changes the view item in memory only for this page request.
if (
    $profile === 'modern'
    && $layout === 'hero'
    && isset($this->item)
    && is_object($this->item)
) {
    $this->item->fullimage_layout = 'header';
}

$original = JPATH_SITE . '/components/com_jem/views/event/tmpl/responsive/default.php';

if (!is_file($original)) {
    throw new RuntimeException(
        'JEM Presentation bridge: original responsive JEM event template not found: ' . $original
    );
}

require $original;
