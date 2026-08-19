<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

final class IntegrationStatus
{
    private const BRIDGE_MARKER = 'JEM Presentation Thin Override Bridge';
    private const NATIVE_HOOK = 'onJemPrepareEventView';
    private const JEM_EVENT_VIEW = 'components/com_jem/views/event/view.html.php';

    public static function getStatus(): array
    {
        $template = self::getDefaultSiteTemplate();
        $nativeApi = self::getNativeApiStatus();
        $nativeDetected = ($nativeApi['state'] ?? '') === 'detected';

        return [
            'runtime' => self::getRuntimeStatus(),
            'template' => [
                'state' => $template !== '' ? 'detected' : 'unknown',
                'label' => $template !== '' ? $template : Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN'),
            ],
            'native_api' => $nativeApi,
            'bridge' => self::getBridgeStatus($template, $nativeDetected),
        ];
    }

    private static function getRuntimeStatus(): array
    {
        try {
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true)
                ->select($db->quoteName('enabled'))
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('jempresentationruntime'));

            $db->setQuery($query);
            $enabled = $db->loadResult();

            if ($enabled === null) {
                return ['state' => 'missing', 'label' => Text::_('COM_JEMPRESENTATION_RUNTIME_MISSING')];
            }

            return (int) $enabled === 1
                ? ['state' => 'active', 'label' => Text::_('COM_JEMPRESENTATION_RUNTIME_ACTIVE')]
                : ['state' => 'inactive', 'label' => Text::_('COM_JEMPRESENTATION_RUNTIME_INACTIVE')];
        } catch (\Throwable $e) {
            return ['state' => 'unknown', 'label' => Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN')];
        }
    }

    private static function getDefaultSiteTemplate(): string
    {
        try {
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true)
                ->select($db->quoteName('template'))
                ->from($db->quoteName('#__template_styles'))
                ->where($db->quoteName('client_id') . ' = 0')
                ->where($db->quoteName('home') . ' = 1');

            $db->setQuery($query, 0, 1);
            return (string) ($db->loadResult() ?: '');
        } catch (\Throwable $e) {
            return '';
        }
    }

    private static function getNativeApiStatus(): array
    {
        $file = JPATH_SITE . '/' . self::JEM_EVENT_VIEW;
        $base = [
            'source' => self::JEM_EVENT_VIEW,
            'hook' => self::NATIVE_HOOK,
        ];

        if (!is_file($file)) {
            return $base + [
                'state' => 'not-detected',
                'role' => 'unavailable',
                'label' => Text::_('COM_JEMPRESENTATION_NATIVE_API_NOT_DETECTED'),
                'help' => Text::_('COM_JEMPRESENTATION_NATIVE_API_NOT_DETECTED_DESC'),
            ];
        }

        $contents = @file_get_contents($file);

        if (!is_string($contents)) {
            return $base + [
                'state' => 'unknown',
                'role' => 'unknown',
                'label' => Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN'),
                'help' => Text::_('COM_JEMPRESENTATION_NATIVE_API_READ_ERROR_DESC'),
            ];
        }

        $pattern = '~->\s*triggerEvent\s*\(\s*([\'\"])' . preg_quote(self::NATIVE_HOOK, '~') . '\\1\s*,~';
        $detected = preg_match($pattern, $contents) === 1;

        if ($detected) {
            return $base + [
                'state' => 'detected',
                'role' => 'preferred',
                'label' => Text::_('COM_JEMPRESENTATION_NATIVE_API_DETECTED'),
                'help' => Text::_('COM_JEMPRESENTATION_NATIVE_API_DETECTED_DESC'),
            ];
        }

        return $base + [
            'state' => 'not-detected',
            'role' => 'unavailable',
            'label' => Text::_('COM_JEMPRESENTATION_NATIVE_API_NOT_DETECTED'),
            'help' => Text::_('COM_JEMPRESENTATION_NATIVE_API_NOT_DETECTED_DESC'),
        ];
    }

    private static function getBridgeStatus(string $template, bool $nativeDetected): array
    {
        if ($template === '') {
            return [
                'state' => 'unknown',
                'role' => 'unknown',
                'label' => Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN'),
                'files' => [],
            ];
        }

        if ($template !== 'cassiopeia') {
            if ($nativeDetected) {
                return [
                    'state' => 'not-required',
                    'role' => 'not-required',
                    'label' => Text::_('COM_JEMPRESENTATION_BRIDGE_NOT_REQUIRED_NATIVE'),
                    'help' => Text::_('COM_JEMPRESENTATION_BRIDGE_NOT_REQUIRED_NATIVE_DESC'),
                    'files' => [],
                ];
            }

            return [
                'state' => 'unsupported-template',
                'role' => 'required',
                'label' => Text::sprintf('COM_JEMPRESENTATION_BRIDGE_TEMPLATE_UNSUPPORTED', $template),
                'files' => [],
            ];
        }

        $base = JPATH_SITE . '/templates/' . $template . '/html/com_jem/event';
        $expected = [
            'default.php' => $base . '/default.php',
            'responsive/default.php' => $base . '/responsive/default.php',
        ];

        $bridgeFiles = 0;
        $customFiles = 0;
        $existing = 0;
        $files = [];

        foreach ($expected as $relative => $file) {
            $state = 'missing';

            if (is_file($file)) {
                $existing++;
                $contents = @file_get_contents($file);

                if (is_string($contents) && str_contains($contents, self::BRIDGE_MARKER)) {
                    $bridgeFiles++;
                    $state = 'bridge';
                } else {
                    $customFiles++;
                    $state = 'custom';
                }
            }

            $files[] = [
                'path' => 'html/com_jem/event/' . $relative,
                'state' => $state,
                'label' => self::bridgeFileStateLabel($state),
            ];
        }

        if ($customFiles > 0) {
            return [
                'state' => 'conflict',
                'role' => $nativeDetected ? 'fallback' : 'required',
                'label' => Text::_('COM_JEMPRESENTATION_BRIDGE_CONFLICT'),
                'help' => $nativeDetected
                    ? Text::_('COM_JEMPRESENTATION_BRIDGE_CONFLICT_NATIVE_DESC')
                    : Text::_('COM_JEMPRESENTATION_BRIDGE_CONFLICT_DESC'),
                'files' => $files,
            ];
        }

        if ($bridgeFiles === count($expected)) {
            return [
                'state' => 'available',
                'role' => $nativeDetected ? 'fallback' : 'required',
                'label' => $nativeDetected
                    ? Text::_('COM_JEMPRESENTATION_BRIDGE_AVAILABLE_FALLBACK')
                    : Text::_('COM_JEMPRESENTATION_BRIDGE_AVAILABLE'),
                'help' => $nativeDetected
                    ? Text::_('COM_JEMPRESENTATION_BRIDGE_AVAILABLE_FALLBACK_DESC')
                    : '',
                'files' => $files,
            ];
        }

        if ($existing > 0 || $bridgeFiles > 0) {
            return [
                'state' => 'incomplete',
                'role' => $nativeDetected ? 'fallback' : 'required',
                'label' => Text::_('COM_JEMPRESENTATION_BRIDGE_INCOMPLETE'),
                'help' => $nativeDetected
                    ? Text::_('COM_JEMPRESENTATION_BRIDGE_INCOMPLETE_NATIVE_DESC')
                    : Text::_('COM_JEMPRESENTATION_BRIDGE_INCOMPLETE_DESC'),
                'files' => $files,
            ];
        }

        if ($nativeDetected) {
            return [
                'state' => 'not-required',
                'role' => 'not-required',
                'label' => Text::_('COM_JEMPRESENTATION_BRIDGE_NOT_REQUIRED_NATIVE'),
                'help' => Text::_('COM_JEMPRESENTATION_BRIDGE_NOT_REQUIRED_NATIVE_DESC'),
                'files' => $files,
            ];
        }

        return [
            'state' => 'missing',
            'role' => 'required',
            'label' => Text::_('COM_JEMPRESENTATION_BRIDGE_MISSING'),
            'files' => $files,
        ];
    }

    private static function bridgeFileStateLabel(string $state): string
    {
        return match ($state) {
            'bridge' => Text::_('COM_JEMPRESENTATION_BRIDGE_FILE_PRESENT'),
            'custom' => Text::_('COM_JEMPRESENTATION_BRIDGE_FILE_CUSTOM'),
            default => Text::_('COM_JEMPRESENTATION_BRIDGE_FILE_MISSING'),
        };
    }
}
