<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('rjquery.chosen', 'select');
JHtml::_('rbootstrap.tooltip');
JHtml::_('behavior.keepalive');

?>
<script type="text/javascript">
	jQuery(document).ready(function()
	{
		var templateType = jQuery('#jform_typecode');
		var type         = jQuery('#jform_type_id');
		<?php if ($this->item->id) : ?>
		type.prop('disabled', true).trigger("liszt:updated").prop('disabled', false);
		templateType.prop('disabled', true).trigger("liszt:updated").prop('disabled', false);
		<?php endif; ?>

		// Disable click function on btn-group
		jQuery(".btn-group").each(function(index){
			if (jQuery(this).hasClass('disabled'))
			{
				jQuery(this).find("label").off('click');
			}
		});

		if (templateType.val() == 'view_categorydetail' || templateType.val() == 'view_categorydetailgmap')
		{
			type.val('').removeClass('required').removeAttr('required').removeAttr('aria-required').trigger("liszt:updated");
			jQuery('#template-type-id').hide();
		}

		jQuery('.tab-pane .btn-tag').on('click', function (e) {
			var $button = jQuery(this);
			var tag = $button.html().trim().replace(/<em>|<\/em>/g, "");
			var cm = jQuery('.CodeMirror')[0].CodeMirror;
			var doc = cm.getDoc();
			var cursor = doc.getCursor();
			var pos = {
				line: cursor.line,
				ch: cursor.ch
			}
			doc.replaceRange(tag, pos);
			cm.focus();
			doc.setSelection(pos, {line: cursor.line, ch: cursor.ch + tag.length});
		});
	});

	function redITEMtemplateChange(template)
	{
		var view = jQuery(template).val();
		var type = jQuery('#jform_type_id');

		if (view == 'view_categorydetail' || view == 'view_categorydetailgmap')
		{
			type.val('').removeClass('required').removeAttr('required').removeAttr('aria-required').trigger("liszt:updated");
			jQuery('#template-type-id').hide();
		}
		else
		{
			type.val('').addClass('required').attr('required', '').attr('aria-required', 'true').trigger("liszt:updated");
			jQuery('#template-type-id').show();
		}
	}
</script>
<form enctype="multipart/form-data"
	action="index.php?option=com_reditem&task=template.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm">
	<div class="row-fluid">
		<div class="span7">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('typecode'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('typecode'); ?>
				</div>
			</div>
			<div class="control-group" id="template-type-id">
				<div class="control-label">
					<?php echo $this->form->getLabel('type_id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('type_id'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('name'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('name'); ?>
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
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('published'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('published'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('content'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('content'); ?>
				</div>
			</div>
		</div>
		<div class="span5">
			<div class='template_tags'>
			<?php if ($this->item->id) : ?>
				<?php
				switch ($this->item->typecode)
				{
					case 'view_archiveditems':
					case 'view_itemdetail':
					case 'module_items':
					case 'module_relateditems':
						echo $this->loadTemplate('items');
						break;

					case 'view_itemedit':
						echo $this->loadTemplate('itemedit');
						break;

					case 'view_search':
						echo $this->loadTemplate('search');
						break;

					case 'view_categorydetail':
					case 'view_categorydetailgmap':
						echo $this->loadTemplate('category');
						break;

					case 'module_search':
						echo $this->loadTemplate('search_module');
						break;

					default:
						break;
				}
				?>
			<?php else : ?>
			<div class="alert alert-info">
				<p><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_NOTICE_SAVE_TEMPLATE_FIRST'); ?></p>
			</div>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
