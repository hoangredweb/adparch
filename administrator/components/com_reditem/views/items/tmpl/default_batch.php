<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;
$published = $this->state->get('filter.published');
?>
<script type="text/javascript">
	batchFormReset = function(){
		location.reload();
	};
</script>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_REDITEM_ITEMS_BATCH_TITLE');?></legend>
	<div class="row-fluid">
		
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->filterForm->getLabel('access'); ?>
			</div>
			<div class="controls">
				<?php echo $this->filterForm->getInput('access'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->filterForm->getLabel('batchCategories'); ?>
			</div>
			<div class="controls">
				<?php echo $this->filterForm->getInput('batchCategories'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->filterForm->getLabel('copyMove'); ?>
			</div>
			<div class="controls">
				<?php echo $this->filterForm->getInput('copyMove'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->filterForm->getLabel('removeOrigin'); ?>
			</div>
			<div class="controls">
				<?php echo $this->filterForm->getInput('removeOrigin'); ?>
			</div>
		</div>

		<button class="btn" type="submit" onclick="Joomla.submitbutton('items.batch');">
			<?php echo JText::_('COM_REDITEM_BATCH_FORM_PROCESS'); ?>
		</button>

		<button class="btn" id="batchButtonClear" onclick="batchFormReset();return false;">
			<?php echo JText::_('COM_REDITEM_BATCH_FORM_RESET'); ?>
		</button>
	</div>
</fieldset>

