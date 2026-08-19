<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\View\Assignment;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use KoelmanLabs\Component\JemPresentation\Administrator\Registry\PresentationRegistry;
use KoelmanLabs\Component\JemPresentation\Administrator\Service\IntegrationStatus;

class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;
    public array $registryData = [];
    public array $integrationStatus = [];

    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->integrationStatus = IntegrationStatus::getStatus();
        $nativeHookAvailable = ($this->integrationStatus['native_api']['state'] ?? '') === 'detected';
        $this->registryData = PresentationRegistry::clientData($nativeHookAvailable);

        $wa = Factory::getApplication()
            ->getDocument()
            ->getWebAssetManager();

        $wa->useScript('form.validate');
        $wa->registerAndUseStyle(
            'com_jempresentation.admin',
            'com_jempresentation/admin.css',
            ['version' => 'auto']
        );
        $wa->registerAndUseScript(
            'com_jempresentation.assignment',
            'com_jempresentation/assignment.js',
            ['version' => 'auto'],
            ['defer' => true]
        );

        $user = Factory::getApplication()->getIdentity();
        $isNew = empty($this->item->id);
        $canSave = $user->authorise($isNew ? 'core.create' : 'core.edit', 'com_jempresentation');

        ToolbarHelper::title(Text::_('COM_JEMPRESENTATION_ASSIGNMENT'));

        if ($canSave) {
            ToolbarHelper::apply('assignment.apply');
            ToolbarHelper::save('assignment.save');
        }

        ToolbarHelper::cancel('assignment.cancel');

        parent::display($tpl);
    }
}
