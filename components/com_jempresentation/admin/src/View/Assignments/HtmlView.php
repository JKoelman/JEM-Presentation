<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\View\Assignments;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use KoelmanLabs\Component\JemPresentation\Administrator\Service\IntegrationStatus;

class HtmlView extends BaseHtmlView
{
    public $items = [];
    public $pagination;
    public bool $nativeHookAvailable = false;

    public function display($tpl = null)
    {
        $items = $this->get('Items');
        $this->items = is_array($items) ? $items : [];
        $this->pagination = $this->get('Pagination');

        $integrationStatus = IntegrationStatus::getStatus();
        $this->nativeHookAvailable = ($integrationStatus['native_api']['state'] ?? '') === 'detected';

        $user = Factory::getApplication()->getIdentity();

        ToolbarHelper::title(Text::_('COM_JEMPRESENTATION_ASSIGNMENTS'));

        if ($user->authorise('core.create', 'com_jempresentation')) {
            ToolbarHelper::addNew('assignment.add');
        }

        if ($user->authorise('core.edit', 'com_jempresentation')) {
            ToolbarHelper::editList('assignment.edit');
        }

        if ($user->authorise('core.delete', 'com_jempresentation')) {
            ToolbarHelper::deleteList('', 'assignments.delete');
        }

        if ($user->authorise('core.admin', 'com_jempresentation')) {
            ToolbarHelper::preferences('com_jempresentation');
        }

        parent::display($tpl);
    }
}
