<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDCORE') or die;
$model = RModelList::getInstance('Items', 'ReditemModel');
$form  = $model->getFilterForm();
$field = $form->getField('delimiter', 'csv');
?>
<script type="text/javascript">
	function exportCsv()
	{
		Joomla.submitbutton('items.exportCsv');
		jQuery('#exportCsvModal').modal('hide');
		jQuery('input[name="task"]').val('');
	}
</script>
<div class="modal hide fade" id="exportCsvModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3><?php echo JText::_('COM_REDITEM_ITEMS_MODAL_CSV_EXPORT_HEADER'); ?></h3>
	</div>
	<div class="modal-body" style="overflow: visible">
		<?php echo $field->renderField();?>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></a>
		<a href="#" class="btn btn-primary" onclick="exportCsv();"><?php echo JText::_('COM_REDITEM_EXPORT'); ?></a>
	</div>
</div>