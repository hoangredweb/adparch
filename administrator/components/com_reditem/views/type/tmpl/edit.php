<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('rbootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('rjquery.chosen', 'select');
JHtml::_('behavior.keepalive');

?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Disable click function on btn-group
		jQuery(".btn-group").each(function(index){
			if (jQuery(this).hasClass('disabled'))
			{
				jQuery(this).find("label").off('click');
			}
		});

		<?php if (!$this->canConfig) : ?>
		jQuery('#jform_params_default_itemdetail_template').prop('disabled', true).trigger("liszt:updated").prop('disabled', false);
		jQuery('#jform_params_default_categorydetail_template').prop('disabled', true).trigger("liszt:updated").prop('disabled', false);
		<?php endif; ?>
	});
</script>

<form enctype="multipart/form-data"
	action="index.php?option=com_reditem&task=type.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm"
>
	<ul class="nav nav-tabs" id="categoryTab">
		<li class="active">
			<a href="#infor" data-toggle="tab">
				<strong><?php echo JText::_('COM_REDITEM_GENERAL_INFORMATION'); ?></strong>
			</a>
		</li>
		<li>
			<a href="#config" data-toggle="tab">
				<strong><?php echo JText::_('COM_REDITEM_CONFIGURATION'); ?></strong>
			</a>
		</li>
		<?php if ($this->canConfig) : ?>
		<li>
			<a href="#permission" data-toggle="tab">
				<strong><?php echo JText::_('COM_REDITEM_PERMISSIONS'); ?></strong>
			</a>
		</li>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="infor">
			<div class="row-fluid">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('title'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('title'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('description'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('description'); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="config">
			<div class="row-fluid">
				<?php $firstSpacer = true; ?>
				<?php foreach ($this->form->getGroup('params') as $field) : ?>
				<div class="control-group">
					<?php if ($field->type == 'Spacer') : ?>
						<?php if (!$firstSpacer) : ?>
							<hr />
						<?php else : ?>
							<?php $firstSpacer = false; ?>
						<?php endif; ?>
						<?php echo $field->label; ?>
					<?php elseif ($field->hidden) : ?>
						<?php echo $field->input; ?>
					<?php else : ?>
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php if ($this->canConfig) : ?>
		<div class="tab-pane" id="permission">
			<div class="row-fluid">
				<?php echo $this->form->getInput('rules'); ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php echo $this->form->getInput('id'); ?>
	<?php echo $this->form->getInput('table_name'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
