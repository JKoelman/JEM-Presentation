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
        $options = [];
        foreach (PresentationRegistry::layouts() as $id => $meta) {
            $label = PresentationRegistry::layoutLabel($id);
            if (($meta['status'] ?? '') === 'planned') {$label .= ' — ' . PresentationRegistry::statusLabel('planned');}
            $options[] = HTMLHelper::_('select.option', $id, $label);
        }
        return array_merge(parent::getOptions(), $options);
    }
}
