<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\View\Assignments;
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
class HtmlView extends BaseHtmlView
{
    public $items=[]; public $pagination;
    public function display($tpl=null)
    {
        $items=$this->get('Items'); $this->items=is_array($items)?$items:[]; $this->pagination=$this->get('Pagination');
        ToolbarHelper::title(Text::_('COM_JEMPRESENTATION_ASSIGNMENTS')); ToolbarHelper::addNew('assignment.add'); ToolbarHelper::editList('assignment.edit'); ToolbarHelper::deleteList('', 'assignments.delete');
        parent::display($tpl);
    }
}
