<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
    protected $default_view = 'assignments';

    public function display($cachable = false, $urlparams = [])
    {
        if (!Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_jempresentation')) {
            throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        return parent::display($cachable, $urlparams);
    }
}
