<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
class AssignmentsModel extends ListModel
{
    protected function getListQuery()
    {
        $db = $this->getDatabase();
        return $db->getQuery(true)->select([$db->quoteName('a.id'),$db->quoteName('a.context_id'),$db->quoteName('a.profile'),$db->quoteName('a.layout'),$db->quoteName('e.id', 'event_id'),$db->quoteName('e.title', 'event_title'),$db->quoteName('e.dates', 'event_date'),$db->quoteName('e.published', 'event_published')])->from($db->quoteName('#__jempresentation_assignments', 'a'))->join('LEFT', $db->quoteName('#__jem_events', 'e') . ' ON ' . $db->quoteName('e.id') . ' = ' . $db->quoteName('a.context_id'))->where($db->quoteName('a.context_type') . ' = ' . $db->quote('event'))->order($db->quoteName('a.id') . ' DESC');
    }
}
