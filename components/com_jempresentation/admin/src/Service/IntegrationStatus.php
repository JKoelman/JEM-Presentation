<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

final class IntegrationStatus
{
    private const BRIDGE_MARKER = 'JEM Presentation Thin Override Bridge';
    public static function getStatus(): array
    {
        $template=self::getSiteTemplate();
        return ['runtime'=>self::getRuntimeStatus(),'template'=>['state'=>$template!==''?'detected':'unknown','label'=>$template!==''?$template:Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN')],'bridge'=>self::getBridgeStatus($template),'native_api'=>['state'=>'not-detected','label'=>Text::_('COM_JEMPRESENTATION_NATIVE_API_NOT_DETECTED'),'help'=>Text::_('COM_JEMPRESENTATION_NATIVE_API_NOT_DETECTED_DESC')]];
    }
    private static function getRuntimeStatus(): array
    {
        try {
            $db=Factory::getContainer()->get('DatabaseDriver');
            $query=$db->getQuery(true)->select($db->quoteName('enabled'))->from($db->quoteName('#__extensions'))->where($db->quoteName('type').' = '.$db->quote('plugin'))->where($db->quoteName('folder').' = '.$db->quote('system'))->where($db->quoteName('element').' = '.$db->quote('jempresentationruntime'));
            $db->setQuery($query); $enabled=$db->loadResult();
            if($enabled===null){return ['state'=>'missing','label'=>Text::_('COM_JEMPRESENTATION_RUNTIME_MISSING')];}
            return (int)$enabled===1?['state'=>'active','label'=>Text::_('COM_JEMPRESENTATION_RUNTIME_ACTIVE')]:['state'=>'inactive','label'=>Text::_('COM_JEMPRESENTATION_RUNTIME_INACTIVE')];
        } catch(\Throwable $e){return ['state'=>'unknown','label'=>Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN')];}
    }
    private static function getSiteTemplate(): string
    {
        try {$db=Factory::getContainer()->get('DatabaseDriver');$query=$db->getQuery(true)->select($db->quoteName('template'))->from($db->quoteName('#__template_styles'))->where($db->quoteName('client_id').' = 0')->where($db->quoteName('home').' = 1');$db->setQuery($query,0,1);return (string)($db->loadResult()?:'');} catch(\Throwable $e){return '';}
    }
    private static function getBridgeStatus(string $template): array
    {
        if($template===''){return ['state'=>'unknown','label'=>Text::_('COM_JEMPRESENTATION_STATUS_UNKNOWN')];}
        if($template!=='cassiopeia'){return ['state'=>'unsupported-template','label'=>Text::sprintf('COM_JEMPRESENTATION_BRIDGE_TEMPLATE_UNSUPPORTED',$template)];}
        $base=JPATH_SITE.'/templates/'.$template.'/html/com_jem/event'; $files=[$base.'/default.php',$base.'/responsive/default.php']; $existing=0;$bridgeFiles=0;$customFiles=0;
        foreach($files as $file){if(!is_file($file)){continue;} $existing++;$contents=@file_get_contents($file); if(is_string($contents)&&str_contains($contents,self::BRIDGE_MARKER)){$bridgeFiles++;}else{$customFiles++;}}
        if($customFiles>0){return ['state'=>'conflict','label'=>Text::_('COM_JEMPRESENTATION_BRIDGE_CONFLICT'),'help'=>Text::_('COM_JEMPRESENTATION_BRIDGE_CONFLICT_DESC')];}
        if($bridgeFiles===count($files)){return ['state'=>'available','label'=>Text::_('COM_JEMPRESENTATION_BRIDGE_AVAILABLE')];}
        if($existing>0||$bridgeFiles>0){return ['state'=>'incomplete','label'=>Text::_('COM_JEMPRESENTATION_BRIDGE_INCOMPLETE'),'help'=>Text::_('COM_JEMPRESENTATION_BRIDGE_INCOMPLETE_DESC')];}
        return ['state'=>'missing','label'=>Text::_('COM_JEMPRESENTATION_BRIDGE_MISSING')];
    }
}
