<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Field;
defined('_JEXEC') or die;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use KoelmanLabs\Component\JemPresentation\Administrator\Registry\PresentationRegistry;
class PresentationlayoutField extends ListField
{
    protected $type = 'Presentationlayout';
    protected function getOptions(): array
    {
        $options = []; $current = (string) $this->value;
        foreach (PresentationRegistry::layouts() as $id => $meta) {
            $label = PresentationRegistry::layoutLabel($id); $selectable = (bool) ($meta['selectable'] ?? false);
            if (!$selectable) $label .= ' — ' . PresentationRegistry::statusLabel((string) ($meta['status'] ?? 'unknown'));
            $options[] = HTMLHelper::_('select.option', $id, $label, 'value', 'text', !$selectable && $current !== $id);
        }
        return array_merge(parent::getOptions(), $options);
    }
}
