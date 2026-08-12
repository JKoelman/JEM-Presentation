<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Registry;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

final class PresentationRegistry
{
    public static function profiles(): array
    {
        return [
            'modern' => ['label'=>'COM_JEMPRESENTATION_PROFILE_MODERN','description'=>'COM_JEMPRESENTATION_PROFILE_MODERN_DESC','status'=>'available'],
            'sports' => ['label'=>'COM_JEMPRESENTATION_PROFILE_SPORTS','description'=>'COM_JEMPRESENTATION_PROFILE_SPORTS_DESC','status'=>'planned'],
            'outdoor' => ['label'=>'COM_JEMPRESENTATION_PROFILE_OUTDOOR','description'=>'COM_JEMPRESENTATION_PROFILE_OUTDOOR_DESC','status'=>'planned'],
            'festival' => ['label'=>'COM_JEMPRESENTATION_PROFILE_FESTIVAL','description'=>'COM_JEMPRESENTATION_PROFILE_FESTIVAL_DESC','status'=>'planned'],
        ];
    }
    public static function layouts(): array
    {
        return [
            'standard'=>['label'=>'COM_JEMPRESENTATION_LAYOUT_STANDARD','description'=>'COM_JEMPRESENTATION_LAYOUT_STANDARD_DESC','integration'=>'native','bridge'=>false,'status'=>'confirmed','preview'=>'standard'],
            'hero'=>['label'=>'COM_JEMPRESENTATION_LAYOUT_HERO','description'=>'COM_JEMPRESENTATION_LAYOUT_HERO_DESC','integration'=>'bridge','bridge'=>true,'status'=>'poc','preview'=>'hero'],
            'two-column'=>['label'=>'COM_JEMPRESENTATION_LAYOUT_TWO_COLUMN','description'=>'COM_JEMPRESENTATION_LAYOUT_TWO_COLUMN_DESC','integration'=>'native','bridge'=>false,'status'=>'confirmed','preview'=>'two-column'],
            'route'=>['label'=>'COM_JEMPRESENTATION_LAYOUT_ROUTE','description'=>'COM_JEMPRESENTATION_LAYOUT_ROUTE_DESC','integration'=>'planned','bridge'=>false,'status'=>'planned','preview'=>'route'],
        ];
    }
    public static function profile(string $id): array {return self::profiles()[$id] ?? ['label'=>$id!==''?$id:'-','description'=>'COM_JEMPRESENTATION_UNKNOWN_PROFILE_DESC','status'=>'unknown'];}
    public static function layout(string $id): array {return self::layouts()[$id] ?? ['label'=>$id!==''?$id:'-','description'=>'COM_JEMPRESENTATION_UNKNOWN_LAYOUT_DESC','integration'=>'unknown','bridge'=>false,'status'=>'unknown','preview'=>'unknown'];}
    public static function profileLabel(string $id): string {$meta=self::profile($id); return self::translate($meta['label']??$id);}
    public static function layoutLabel(string $id): string {$meta=self::layout($id); return self::translate($meta['label']??$id);}
    public static function integrationLabel(string $profileId,string $layoutId): string
    {
        $profile=self::profile($profileId); $layout=self::layout($layoutId);
        if (($profile['status']??'unknown')!=='available') {return Text::_('COM_JEMPRESENTATION_INTEGRATION_PLANNED');}
        return match ($layout['integration']??'unknown') {'native'=>Text::_('COM_JEMPRESENTATION_INTEGRATION_NATIVE'),'bridge'=>Text::_('COM_JEMPRESENTATION_INTEGRATION_BRIDGE'),'planned'=>Text::_('COM_JEMPRESENTATION_INTEGRATION_PLANNED'),default=>Text::_('COM_JEMPRESENTATION_INTEGRATION_UNKNOWN')};
    }
    public static function clientData(): array
    {
        $profiles=[]; foreach(self::profiles() as $id=>$meta){$profiles[$id]=['id'=>$id,'label'=>self::translate($meta['label']),'description'=>self::translate($meta['description']),'status'=>$meta['status'],'statusLabel'=>self::statusLabel($meta['status'])];}
        $layouts=[]; foreach(self::layouts() as $id=>$meta){$layouts[$id]=['id'=>$id,'label'=>self::translate($meta['label']),'description'=>self::translate($meta['description']),'integration'=>$meta['integration'],'integrationLabel'=>self::integrationTypeLabel($meta['integration']),'bridge'=>(bool)$meta['bridge'],'bridgeLabel'=>$meta['bridge']?Text::_('COM_JEMPRESENTATION_BRIDGE_REQUIRED'):Text::_('COM_JEMPRESENTATION_BRIDGE_NOT_REQUIRED'),'status'=>$meta['status'],'statusLabel'=>self::statusLabel($meta['status']),'preview'=>$meta['preview']];}
        return ['profiles'=>$profiles,'layouts'=>$layouts,'labels'=>['profile'=>Text::_('COM_JEMPRESENTATION_PROFILE'),'layout'=>Text::_('COM_JEMPRESENTATION_LAYOUT'),'integration'=>Text::_('COM_JEMPRESENTATION_INTEGRATION'),'bridge'=>Text::_('COM_JEMPRESENTATION_BRIDGE'),'status'=>Text::_('COM_JEMPRESENTATION_STATUS'),'unsupported'=>Text::_('COM_JEMPRESENTATION_RUNTIME_UNSUPPORTED_COMBINATION')]];
    }
    public static function statusLabel(string $status): string {return match($status){'available'=>Text::_('COM_JEMPRESENTATION_STATUS_AVAILABLE'),'confirmed'=>Text::_('COM_JEMPRESENTATION_STATUS_CONFIRMED'),'poc'=>Text::_('COM_JEMPRESENTATION_STATUS_POC'),'planned'=>Text::_('COM_JEMPRESENTATION_STATUS_PLANNED'),default=>Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN')};}
    private static function integrationTypeLabel(string $integration): string {return match($integration){'native'=>Text::_('COM_JEMPRESENTATION_INTEGRATION_NATIVE_MARKUP'),'bridge'=>Text::_('COM_JEMPRESENTATION_INTEGRATION_JEM_BRIDGE'),'planned'=>Text::_('COM_JEMPRESENTATION_INTEGRATION_PLANNED'),default=>Text::_('COM_JEMPRESENTATION_INTEGRATION_UNKNOWN')};}
    private static function translate(string $value): string {return str_starts_with($value,'COM_JEMPRESENTATION_')?Text::_($value):$value;}
}
