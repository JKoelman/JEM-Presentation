<?php
namespace KoelmanLabs\Plugin\System\JemPresentationRuntime\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

final class JemPresentationRuntime extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;

    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterRoute'        => 'onAfterRoute',
            'onBeforeCompileHead' => 'onBeforeCompileHead',
        ];
    }

    public function onAfterRoute(): void
    {
        $app = $this->getApplication();

        if (!$app->isClient('site')) {
            return;
        }

        $input = $app->getInput();

        if ($input->getCmd('option') !== 'com_jem' || $input->getCmd('view') !== 'event') {
            return;
        }

        $eventId = $this->resolveEventId((string) $input->getString('id', ''));

        if ($eventId <= 0) {
            $this->log('JEM event route detected but no valid event id could be resolved.');
            $this->debugMessage(
                Text::_('PLG_SYSTEM_JEMPRESENTATIONRUNTIME_DEBUG_INVALID_EVENT'),
                'warning'
            );
            return;
        }

        try {
            $db = Factory::getContainer()->get('DatabaseDriver');

            $query = $db->getQuery(true)
                ->select([
                    $db->quoteName('id'),
                    $db->quoteName('context_id'),
                    $db->quoteName('profile'),
                    $db->quoteName('layout'),
                    $db->quoteName('params'),
                ])
                ->from($db->quoteName('#__jempresentation_assignments'))
                ->where($db->quoteName('context_type') . ' = ' . $db->quote('event'))
                ->where($db->quoteName('context_id') . ' = ' . (int) $eventId);

            $db->setQuery($query);
            $assignment = $db->loadObject();

            if (!$assignment) {
                $this->log(sprintf('event=%d | assignment=NONE', $eventId));

                $this->debugMessage(
                    Text::sprintf(
                        'PLG_SYSTEM_JEMPRESENTATIONRUNTIME_DEBUG_NO_ASSIGNMENT',
                        $eventId
                    ),
                    'info'
                );
                return;
            }

            $app->set('jempresentation.event_id', $eventId);
            $app->set('jempresentation.assignment_id', (int) $assignment->id);
            $app->set('jempresentation.profile', (string) $assignment->profile);
            $app->set('jempresentation.layout', (string) $assignment->layout);
            $app->set('jempresentation.params', (string) ($assignment->params ?? ''));

            $this->log(sprintf(
                'event=%d | assignment=%d | profile=%s | layout=%s',
                $eventId,
                (int) $assignment->id,
                (string) $assignment->profile,
                (string) $assignment->layout
            ));

            $this->debugMessage(
                Text::sprintf(
                    'PLG_SYSTEM_JEMPRESENTATIONRUNTIME_DEBUG_RESOLVED',
                    $eventId,
                    (string) $assignment->profile,
                    (string) $assignment->layout
                ),
                'message'
            );
        } catch (\Throwable $e) {
            $this->log('resolver-error=' . $e->getMessage(), Log::ERROR);

            $this->debugMessage(
                Text::_('PLG_SYSTEM_JEMPRESENTATIONRUNTIME_DEBUG_ERROR'),
                'error'
            );
        }
    }

    public function onBeforeCompileHead(): void
    {
        $app = $this->getApplication();

        if (!$app->isClient('site')) {
            return;
        }

        $profile = (string) $app->get('jempresentation.profile', '');
        $layout  = (string) $app->get('jempresentation.layout', '');

        if ($profile !== 'modern') {
            return;
        }

        $document = $app->getDocument();

        if (!method_exists($document, 'getWebAssetManager')) {
            return;
        }

        try {
            $wa = $document->getWebAssetManager();

            $wa->registerAndUseStyle(
                'plg_system_jempresentationruntime.modern',
                'media/plg_system_jempresentationruntime/css/modern.css',
                ['version' => 'auto']
            );

            $this->log(sprintf(
                'event=%d | asset=modern.css | loaded=YES',
                (int) $app->get('jempresentation.event_id', 0)
            ));

            if ($layout === 'hero') {
                $wa->registerAndUseStyle(
                    'plg_system_jempresentationruntime.hero',
                    'media/plg_system_jempresentationruntime/css/hero.css',
                    ['version' => 'auto'],
                    [],
                    ['plg_system_jempresentationruntime.modern']
                );

                $this->log(sprintf(
                    'event=%d | asset=hero.css | loaded=YES',
                    (int) $app->get('jempresentation.event_id', 0)
                ));
            }

            if ($layout === 'two-column') {
                $wa->registerAndUseStyle(
                    'plg_system_jempresentationruntime.two-column',
                    'media/plg_system_jempresentationruntime/css/two-column.css',
                    ['version' => 'auto'],
                    [],
                    ['plg_system_jempresentationruntime.modern']
                );

                $this->log(sprintf(
                    'event=%d | asset=two-column.css | loaded=YES',
                    (int) $app->get('jempresentation.event_id', 0)
                ));
            }
        } catch (\Throwable $e) {
            $this->log('asset-error=' . $e->getMessage(), Log::ERROR);
        }
    }

    private function resolveEventId(string $rawId): int
    {
        $rawId = trim($rawId);

        if (preg_match('/^(\d+)/', $rawId, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    private function debugMessage(string $message, string $type = 'message'): void
    {
        if (!$this->getApplication()->getInput()->getBool('jempresentation_debug', false)) {
            return;
        }

        $this->getApplication()->enqueueMessage($message, $type);
    }

    private function log(string $message, int $priority = Log::INFO): void
    {
        try {
            Log::addLogger(
                ['text_file' => 'jempresentationruntime.php'],
                Log::ALL,
                ['jempresentationruntime']
            );

            Log::add(
                'JEM Presentation Runtime v0.1.8 | ' . $message,
                $priority,
                'jempresentationruntime'
            );
        } catch (\Throwable $e) {
        }
    }
}
