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
$file  = $form->getField('import', 'csv');
$del   = $form->getField('delimiter', 'csv');
?>
<script type="text/javascript">
	function csvImport()
	{
		var filename = jQuery('#csv_import').val();

		if (filename.length > 0)
		{
			jQuery('#csvImportForm').submit();
		}
		else
		{
			alert('<?php echo JText::_('COM_REDITEM_SELECT_FILE_FIRST'); ?>');
		}
	}
</script>
<form action="index.php?option=com_reditem&task=items.importCsv" enctype="multipart/form-data" method="post" id="csvImportForm" class="admin form-horizontal" name="csvImportForm">
	<div class="modal hide fade" id="importCsvModal">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo JText::_('COM_REDITEM_ITEMS_MODAL_CSV_IMPORT_HEADER'); ?></h3>
		</div>
		<div class="modal-body" style="overflow: visible">
			<p><?php echo JText::_('COM_REDITEM_ITEMS_MODAL_CSV_IMPORT_INTRO'); ?></p>
			<?php echo $file->renderField();?>
			<?php echo $del->renderField();?>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></a>
			<a href="#" class="btn btn-primary" onclick="csvImport()"><?php echo JText::_('COM_REDITEM_IMPORT'); ?></a>
		</div>
	</div>
</form>