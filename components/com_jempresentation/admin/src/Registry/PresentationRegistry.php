<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Registry;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

final class PresentationRegistry
{
    public static function profiles(): array
    {
        return [
            'modern' => [
                'label' => 'COM_JEMPRESENTATION_PROFILE_MODERN',
                'description' => 'COM_JEMPRESENTATION_PROFILE_MODERN_DESC',
                'status' => 'available',
                'layouts' => ['standard', 'hero', 'two-column'],
            ],
            'sports' => [
                'label' => 'COM_JEMPRESENTATION_PROFILE_SPORTS',
                'description' => 'COM_JEMPRESENTATION_PROFILE_SPORTS_DESC',
                'status' => 'planned',
                'layouts' => [],
            ],
            'outdoor' => [
                'label' => 'COM_JEMPRESENTATION_PROFILE_OUTDOOR',
                'description' => 'COM_JEMPRESENTATION_PROFILE_OUTDOOR_DESC',
                'status' => 'planned',
                'layouts' => [],
            ],
            'festival' => [
                'label' => 'COM_JEMPRESENTATION_PROFILE_FESTIVAL',
                'description' => 'COM_JEMPRESENTATION_PROFILE_FESTIVAL_DESC',
                'status' => 'planned',
                'layouts' => [],
            ],
        ];
    }

    public static function layouts(): array
    {
        return [
            'standard' => [
                'label' => 'COM_JEMPRESENTATION_LAYOUT_STANDARD',
                'description' => 'COM_JEMPRESENTATION_LAYOUT_STANDARD_DESC',
                'integration' => 'native',
                'bridge' => false,
                'status' => 'confirmed',
                'preview' => 'standard',
                'selectable' => true,
            ],
            'hero' => [
                'label' => 'COM_JEMPRESENTATION_LAYOUT_HERO',
                'description' => 'COM_JEMPRESENTATION_LAYOUT_HERO_DESC',
                'integration' => 'adaptive',
                'bridge' => true,
                'status' => 'confirmed',
                'preview' => 'hero',
                'selectable' => true,
            ],
            'two-column' => [
                'label' => 'COM_JEMPRESENTATION_LAYOUT_TWO_COLUMN',
                'description' => 'COM_JEMPRESENTATION_LAYOUT_TWO_COLUMN_DESC',
                'integration' => 'native',
                'bridge' => false,
                'status' => 'confirmed',
                'preview' => 'two-column',
                'selectable' => true,
            ],
            'route' => [
                'label' => 'COM_JEMPRESENTATION_LAYOUT_ROUTE',
                'description' => 'COM_JEMPRESENTATION_LAYOUT_ROUTE_DESC',
                'integration' => 'planned',
                'bridge' => false,
                'status' => 'planned',
                'preview' => 'route',
                'selectable' => false,
            ],
        ];
    }

    public static function hasProfile(string $id): bool
    {
        return array_key_exists($id, self::profiles());
    }

    public static function hasLayout(string $id): bool
    {
        return array_key_exists($id, self::layouts());
    }

    public static function profile(string $id): array
    {
        return self::profiles()[$id] ?? [
            'label' => $id !== '' ? $id : '-',
            'description' => 'COM_JEMPRESENTATION_UNKNOWN_PROFILE_DESC',
            'status' => 'unknown',
            'layouts' => [],
        ];
    }

    public static function layout(string $id): array
    {
        return self::layouts()[$id] ?? [
            'label' => $id !== '' ? $id : '-',
            'description' => 'COM_JEMPRESENTATION_UNKNOWN_LAYOUT_DESC',
            'integration' => 'unknown',
            'bridge' => false,
            'status' => 'unknown',
            'preview' => 'unknown',
            'selectable' => false,
        ];
    }

    public static function isProfileAvailable(string $id): bool
    {
        return self::hasProfile($id) && (self::profile($id)['status'] ?? '') === 'available';
    }

    public static function isLayoutSelectable(string $id): bool
    {
        return self::hasLayout($id) && (bool) (self::layout($id)['selectable'] ?? false);
    }

    public static function supports(string $profileId, string $layoutId): bool
    {
        if (!self::isProfileAvailable($profileId) || !self::isLayoutSelectable($layoutId)) {
            return false;
        }

        return in_array($layoutId, self::profile($profileId)['layouts'] ?? [], true);
    }

    public static function profileLabel(string $id): string
    {
        $meta = self::profile($id);
        return self::translate($meta['label'] ?? $id);
    }

    public static function layoutLabel(string $id): string
    {
        $meta = self::layout($id);
        return self::translate($meta['label'] ?? $id);
    }

    public static function integrationKey(string $profileId, string $layoutId, bool $nativeHookAvailable = false): string
    {
        $profile = self::profile($profileId);

        if (($profile['status'] ?? 'unknown') !== 'available') {
            return 'planned';
        }

        return self::effectiveIntegration(self::layout($layoutId), $nativeHookAvailable);
    }

    public static function integrationLabel(string $profileId, string $layoutId, bool $nativeHookAvailable = false): string
    {
        return self::integrationTypeLabel(self::integrationKey($profileId, $layoutId, $nativeHookAvailable));
    }

    public static function clientData(bool $nativeHookAvailable = false): array
    {
        $profiles = [];
        foreach (self::profiles() as $id => $meta) {
            $profiles[$id] = [
                'id' => $id,
                'label' => self::translate($meta['label']),
                'description' => self::translate($meta['description']),
                'status' => $meta['status'],
                'statusLabel' => self::statusLabel($meta['status']),
                'supportedLayouts' => array_values($meta['layouts'] ?? []),
                'selectable' => ($meta['status'] ?? '') === 'available',
            ];
        }

        $layouts = [];
        foreach (self::layouts() as $id => $meta) {
            $integration = self::effectiveIntegration($meta, $nativeHookAvailable);
            $bridgeRole = self::bridgeRole($meta, $nativeHookAvailable);

            $layouts[$id] = [
                'id' => $id,
                'label' => self::translate($meta['label']),
                'description' => self::translate($meta['description']),
                'integration' => $integration,
                'integrationLabel' => self::integrationTypeLabel($integration),
                'bridge' => (bool) ($meta['bridge'] ?? false),
                'bridgeRole' => $bridgeRole,
                'bridgeLabel' => self::bridgeRoleLabel($bridgeRole),
                'status' => $meta['status'],
                'statusLabel' => self::statusLabel($meta['status']),
                'preview' => $meta['preview'],
                'selectable' => (bool) ($meta['selectable'] ?? false),
            ];
        }

        return [
            'environment' => [
                'nativeHookAvailable' => $nativeHookAvailable,
            ],
            'profiles' => $profiles,
            'layouts' => $layouts,
            'labels' => [
                'profile' => Text::_('COM_JEMPRESENTATION_PROFILE'),
                'layout' => Text::_('COM_JEMPRESENTATION_LAYOUT'),
                'integration' => Text::_('COM_JEMPRESENTATION_INTEGRATION'),
                'bridge' => Text::_('COM_JEMPRESENTATION_BRIDGE'),
                'status' => Text::_('COM_JEMPRESENTATION_STATUS'),
                'unsupported' => Text::_('COM_JEMPRESENTATION_RUNTIME_UNSUPPORTED_COMBINATION'),
            ],
        ];
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'available' => Text::_('COM_JEMPRESENTATION_STATUS_AVAILABLE'),
            'confirmed' => Text::_('COM_JEMPRESENTATION_STATUS_CONFIRMED'),
            'poc' => Text::_('COM_JEMPRESENTATION_STATUS_POC'),
            'planned' => Text::_('COM_JEMPRESENTATION_STATUS_PLANNED'),
            default => Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN'),
        };
    }

    private static function effectiveIntegration(array $layout, bool $nativeHookAvailable): string
    {
        $integration = (string) ($layout['integration'] ?? 'unknown');

        if ($integration !== 'adaptive') {
            return $integration;
        }

        return $nativeHookAvailable ? 'native-hook' : 'bridge';
    }

    private static function bridgeRole(array $layout, bool $nativeHookAvailable): string
    {
        if (!(bool) ($layout['bridge'] ?? false)) {
            return 'none';
        }

        if (($layout['integration'] ?? '') === 'adaptive') {
            return $nativeHookAvailable ? 'fallback' : 'required';
        }

        return 'required';
    }

    private static function integrationTypeLabel(string $integration): string
    {
        return match ($integration) {
            'native' => Text::_('COM_JEMPRESENTATION_INTEGRATION_NATIVE_MARKUP'),
            'native-hook' => Text::_('COM_JEMPRESENTATION_INTEGRATION_NATIVE_HOOK'),
            'bridge' => Text::_('COM_JEMPRESENTATION_INTEGRATION_COMPATIBILITY_BRIDGE'),
            'planned' => Text::_('COM_JEMPRESENTATION_INTEGRATION_PLANNED'),
            default => Text::_('COM_JEMPRESENTATION_INTEGRATION_UNKNOWN'),
        };
    }

    private static function bridgeRoleLabel(string $role): string
    {
        return match ($role) {
            'fallback' => Text::_('COM_JEMPRESENTATION_BRIDGE_FALLBACK'),
            'required' => Text::_('COM_JEMPRESENTATION_BRIDGE_REQUIRED'),
            default => Text::_('COM_JEMPRESENTATION_BRIDGE_NOT_REQUIRED'),
        };
    }

    private static function translate(string $value): string
    {
        return str_starts_with($value, 'COM_JEMPRESENTATION_') ? Text::_($value) : $value;
    }
}
