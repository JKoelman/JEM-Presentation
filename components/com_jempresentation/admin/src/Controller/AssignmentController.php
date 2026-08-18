<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

class AssignmentController extends FormController
{
    protected function allowAdd($data = [])
    {
        $user = Factory::getApplication()->getIdentity();

        return $user->authorise('core.manage', 'com_jempresentation')
            && $user->authorise('core.create', 'com_jempresentation');
    }

    protected function allowEdit($data = [], $key = 'id')
    {
        $user = Factory::getApplication()->getIdentity();

        return $user->authorise('core.manage', 'com_jempresentation')
            && $user->authorise('core.edit', 'com_jempresentation');
    }
}
