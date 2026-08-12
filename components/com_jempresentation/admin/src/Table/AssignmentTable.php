<?php
namespace KoelmanLabs\Component\JemPresentation\Administrator\Table;
defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
class AssignmentTable extends Table
{
    public function __construct(DatabaseDriver $db) {parent::__construct('#__jempresentation_assignments','id',$db);}
}
