<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
class AssignmentController extends FormController
{
    protected function allowAdd($data = []) { return Factory::getApplication()->getIdentity()->authorise('core.create', 'com_jempresentation'); }
    protected function allowEdit($data = [], $key = 'id') { return Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_jempresentation'); }
}
