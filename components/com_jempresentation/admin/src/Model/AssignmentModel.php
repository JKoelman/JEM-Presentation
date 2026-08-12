<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
class AssignmentModel extends AdminModel
{
    public function getTable($type = 'Assignment', $prefix = 'Administrator\\Table\\', $config = []) {return parent::getTable($type, $prefix, $config);}
    public function getForm($data = [], $loadData = true) { $form = $this->loadForm('com_jempresentation.assignment','assignment',['control'=>'jform','load_data'=>$loadData]); return $form ?: false; }
    protected function loadFormData() { $data = Factory::getApplication()->getUserState('com_jempresentation.edit.assignment.data',[]); if (empty($data)) {$data=$this->getItem();} return $data; }
    public function save($data)
    {
        $data['context_type']='event';
        if (!empty($data['context_id'])) {
            $db=$this->getDatabase();
            $query=$db->getQuery(true)->select($db->quoteName('id'))->from($db->quoteName('#__jempresentation_assignments'))->where($db->quoteName('context_type').' = '.$db->quote('event'))->where($db->quoteName('context_id').' = '.(int)$data['context_id']);
            if (!empty($data['id'])) {$query->where($db->quoteName('id').' <> '.(int)$data['id']);}
            $db->setQuery($query); if ($existing=(int)$db->loadResult()) {$data['id']=$existing;}
        }
        return parent::save($data);
    }
}
