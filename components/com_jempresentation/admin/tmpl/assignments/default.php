<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use KoelmanLabs\Component\JemPresentation\Administrator\Registry\PresentationRegistry;
?>
<form action="<?php echo Route::_('index.php?option=com_jempresentation&view=assignments'); ?>"
      method="post" name="adminForm" id="adminForm">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th style="width:1%"><?php echo HTMLHelper::_('grid.checkall'); ?></th><th><?php echo Text::_('COM_JEMPRESENTATION_EVENT'); ?></th><th><?php echo Text::_('COM_JEMPRESENTATION_PROFILE'); ?></th><th><?php echo Text::_('COM_JEMPRESENTATION_LAYOUT'); ?></th><th><?php echo Text::_('COM_JEMPRESENTATION_INTEGRATION'); ?></th><th>ID</th></tr></thead>
            <tbody>
            <?php foreach (($this->items ?: []) as $i => $item) : ?>
                <?php $profileId=(string)$item->profile; $layoutId=(string)$item->layout; $layoutMeta=PresentationRegistry::layout($layoutId); ?>
                <tr>
                    <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                    <td><a href="<?php echo Route::_('index.php?option=com_jempresentation&task=assignment.edit&id=' . (int) $item->id); ?>"><?php echo $this->escape($item->event_title ?: ('JEM event #' . $item->context_id)); ?></a><?php if (!empty($item->event_date)) : ?><div class="small text-muted"><?php echo $this->escape($item->event_date); ?></div><?php endif; ?></td>
                    <td><?php echo $this->escape(PresentationRegistry::profileLabel($profileId)); ?><?php if ((PresentationRegistry::profile($profileId)['status'] ?? '') !== 'available') : ?><div class="small text-warning-emphasis"><?php echo $this->escape(PresentationRegistry::statusLabel((string) (PresentationRegistry::profile($profileId)['status'] ?? 'unknown'))); ?></div><?php endif; ?></td>
                    <td><?php echo $this->escape(PresentationRegistry::layoutLabel($layoutId)); ?><div class="small text-muted"><?php echo $this->escape(PresentationRegistry::statusLabel((string) ($layoutMeta['status'] ?? 'unknown'))); ?></div></td>
                    <td><?php echo $this->escape(PresentationRegistry::integrationLabel($profileId, $layoutId)); ?></td>
                    <td><?php echo (int) $item->context_id; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($this->pagination) : ?><?php echo $this->pagination->getListFooter(); ?><?php endif; ?>
    <input type="hidden" name="boxchecked" value="0">
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
