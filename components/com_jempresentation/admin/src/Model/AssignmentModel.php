<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use KoelmanLabs\Component\JemPresentation\Administrator\Registry\PresentationRegistry;

class AssignmentModel extends AdminModel
{
    public function getTable($type = 'Assignment', $prefix = 'Administrator\\Table\\', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_jempresentation.assignment',
            'assignment',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        return $form ?: false;
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState(
            'com_jempresentation.edit.assignment.data',
            []
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    protected function canDelete($record)
    {
        $user = Factory::getApplication()->getIdentity();

        return $user->authorise('core.manage', 'com_jempresentation')
            && $user->authorise('core.delete', 'com_jempresentation');
    }

    public function save($data)
    {
        $data['context_type'] = 'event';
        $data['context_id'] = (int) ($data['context_id'] ?? 0);
        $data['profile'] = trim((string) ($data['profile'] ?? ''));
        $data['layout'] = trim((string) ($data['layout'] ?? ''));

        $current = null;
        if (!empty($data['id'])) {
            $current = $this->getItem((int) $data['id']);
        }

        if ($data['context_id'] <= 0) {
            $this->setError(Text::_('COM_JEMPRESENTATION_ERROR_EVENT_REQUIRED'));
            return false;
        }

        if (!$this->eventExists($data['context_id'])) {
            $this->setError(Text::sprintf('COM_JEMPRESENTATION_ERROR_EVENT_NOT_FOUND', $data['context_id']));
            return false;
        }

        $legacySelectionUnchanged = $this->isLegacySelectionUnchanged($current, $data);

        if (!$legacySelectionUnchanged) {
            if (!PresentationRegistry::hasProfile($data['profile'])) {
                $this->setError(Text::sprintf('COM_JEMPRESENTATION_ERROR_UNKNOWN_PROFILE', $data['profile']));
                return false;
            }

            if (!PresentationRegistry::hasLayout($data['layout'])) {
                $this->setError(Text::sprintf('COM_JEMPRESENTATION_ERROR_UNKNOWN_LAYOUT', $data['layout']));
                return false;
            }

            if (!PresentationRegistry::supports($data['profile'], $data['layout'])) {
                $this->setError(Text::sprintf(
                    'COM_JEMPRESENTATION_ERROR_UNSUPPORTED_COMBINATION',
                    PresentationRegistry::profileLabel($data['profile']),
                    PresentationRegistry::layoutLabel($data['layout'])
                ));
                return false;
            }
        }

        $requestedId = (int) ($data['id'] ?? 0);
        $existing = $this->findAssignmentForEvent($data['context_id'], $requestedId);
        if ($existing > 0) {
            if ($requestedId > 0) {
                $this->setError(Text::_('COM_JEMPRESENTATION_ERROR_EVENT_ALREADY_ASSIGNED'));
                return false;
            }

            $data['id'] = $existing;
            Factory::getApplication()->enqueueMessage(
                Text::_('COM_JEMPRESENTATION_ASSIGNMENT_REUSED'),
                'info'
            );
        }

        return parent::save($data);
    }

    private function eventExists(int $eventId): bool
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__jem_events'))
            ->where($db->quoteName('id') . ' = ' . $eventId);

        $db->setQuery($query);
        return (int) $db->loadResult() === 1;
    }

    private function findAssignmentForEvent(int $eventId, int $excludeId = 0): int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__jempresentation_assignments'))
            ->where($db->quoteName('context_type') . ' = ' . $db->quote('event'))
            ->where($db->quoteName('context_id') . ' = ' . $eventId);

        if ($excludeId > 0) {
            $query->where($db->quoteName('id') . ' <> ' . $excludeId);
        }

        $db->setQuery($query, 0, 1);
        return (int) $db->loadResult();
    }

    private function isLegacySelectionUnchanged($current, array $data): bool
    {
        if (!is_object($current) || empty($current->id)) {
            return false;
        }

        return (int) ($current->context_id ?? 0) === $data['context_id']
            && (string) ($current->profile ?? '') === $data['profile']
            && (string) ($current->layout ?? '') === $data['layout'];
    }
}
