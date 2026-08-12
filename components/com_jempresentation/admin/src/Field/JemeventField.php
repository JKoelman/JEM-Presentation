<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class JemeventField extends ListField
{
    protected $type = 'Jemevent';

    protected function getOptions(): array
    {
        $options = [];
        $options[] = HTMLHelper::_('select.option', '', Text::_('COM_JEMPRESENTATION_SELECT_EVENT'));
        try {
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true)
                ->select([$db->quoteName('id'),$db->quoteName('title'),$db->quoteName('dates'),$db->quoteName('published')])
                ->from($db->quoteName('#__jem_events'))
                ->order(['CASE WHEN ' . $db->quoteName('dates') . ' IS NULL OR ' . $db->quoteName('dates') . " = '0000-00-00' THEN 1 ELSE 0 END ASC",$db->quoteName('dates') . ' DESC',$db->quoteName('title') . ' ASC']);
            $db->setQuery($query);
            foreach ($db->loadObjectList() ?: [] as $event) {
                $title = trim((string) $event->title);
                $label = $title !== '' ? $title : Text::_('COM_JEMPRESENTATION_UNTITLED_EVENT');
                if (!empty($event->dates) && $event->dates !== '0000-00-00') {$label .= ' — ' . $event->dates;}
                $label .= ' — #' . (int) $event->id;
                if ((int) $event->published !== 1) {$label .= ' [' . Text::_('COM_JEMPRESENTATION_EVENT_NOT_PUBLISHED') . ']';}
                $options[] = HTMLHelper::_('select.option',(string) (int) $event->id,$label);
            }
        } catch (\Throwable $e) {
            $options[] = HTMLHelper::_('select.option','',Text::_('COM_JEMPRESENTATION_JEM_EVENTS_UNAVAILABLE'),'value','text',true);
        }
        return array_merge(parent::getOptions(), $options);
    }
}
