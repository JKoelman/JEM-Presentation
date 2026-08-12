<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;
class AssignmentsController extends AdminController
{
    public function getModel($name = 'Assignment', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}
