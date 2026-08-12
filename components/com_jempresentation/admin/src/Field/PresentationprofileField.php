<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Field;
defined('_JEXEC') or die;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use KoelmanLabs\Component\JemPresentation\Administrator\Registry\PresentationRegistry;
class PresentationprofileField extends ListField
{
    protected $type = 'Presentationprofile';
    protected function getOptions(): array
    {
        $options = [];
        foreach (PresentationRegistry::profiles() as $id => $meta) {
            $label = PresentationRegistry::profileLabel($id);
            if (($meta['status'] ?? '') !== 'available') {$label .= ' — ' . PresentationRegistry::statusLabel((string) ($meta['status'] ?? 'unknown'));}
            $options[] = HTMLHelper::_('select.option', $id, $label);
        }
        return array_merge(parent::getOptions(), $options);
    }
}
