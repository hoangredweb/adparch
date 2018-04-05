<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('rjquery.chosen', 'select');
JHtml::_('behavior.formvalidation');
JHtml::_('rbootstrap.tooltip', '.hasTooltip', array('placement' => 'right'));
JHtml::_('behavior.keepalive');
RHelperAsset::load('daterangepicker/moment.min.js', 'com_reditem');
RHelperAsset::load('daterangepicker/daterangepicker.min.js', 'com_reditem');
RHelperAsset::load('daterangepicker/daterangepicker-bs2.min.css', 'com_reditem');

$optionAvailable = explode(',', $this->form->getFieldAttribute('options', 'section', ''));
$fieldType       = $this->form->getField('type')->__get('value');
?>
<script type="text/javascript">
	jQuery(document).ready(function()
	{
		// Disable click function on btn-group
		jQuery(".btn-group").each(function(index){
			if (jQuery(this).hasClass('disabled'))
			{
				jQuery(this).find("label").off('click');
			}
		});

		<?php if ($this->form->getField('default')) :?>
			<?php if ($fieldType == 'daterange') :?>
			var dateRange = jQuery('.ridaterange');

			if (dateRange)
			{
				var format     = jQuery('#jform_params_format');
				var options    = {};
				options.format = format.val();
				options.drops  = 'up';
				options.ranges = {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				};
				options.showDropdowns     = true;
				options.showWeekNumbers   = true;
				options.timePicker        = true;
				options.timePickerSeconds = true;
				dateRange.daterangepicker(options);

				format.on('change', function() {
					var val    = dateRange.val();
					var format = jQuery(this);

					if (val.length > 0)
					{
						var tmp = val.split(' - ');
						dateRange.val(moment(tmp[0]).format(format) + ' - ' + moment(tmp[1]).format(format));
					}
				});
			}
			<?php endif; ?>

			<?php if (in_array($fieldType, array('radio', 'select', 'checkbox'))) :?>
			// Add custom events
			var def  = jQuery('#jform_default');
			var dVal = '<?php echo $this->item->default;?>';

			refreshList();

			if (dVal != '')
			{
				def.val(dVal);
				def.trigger('liszt:updated');
			}

			var table = jQuery('#reditem-dynamic-table');
			table.on('row-dynamic-added', function() {
				refreshList();
			});
			table.on('row-dynamic-removed', function() {
				refreshList();
			});
			<?php endif; ?>
		<?php endif;?>
	});

	<?php if ($this->form->getField('default') && in_array($fieldType, array('radio', 'select', 'checkbox'))):?>
	function refreshList()
	{
		var def  = jQuery('#jform_default');
		var rows = jQuery('.dynamic-row');
		def.html('');

		if (rows.length > 0)
		{
			def.removeClass('disabled').removeAttr('disabled');
			rows.each(function() {
				var input = jQuery(this).find('.dynamic-value');
				input.attr('onchange', 'refreshList()');
				var value = jQuery(input[0]).val();
				input     = jQuery(this).find('.dynamic-text');
				input.attr('onchange', 'refreshList()');
				var text  = jQuery(input[0]).val();

				if (text && value)
				{
					def.append(jQuery('<option>').attr('value', value).html(text));
				}
				else if (value)
				{
					def.append(jQuery('<option>').attr('value', value).html(value));
				}
			});
		}
		else
		{
			def.addClass('disabled').attr('disabled', 'disabled');
		}

		def.trigger('liszt:updated');
	}
	<?php endif;?>
</script>

<form enctype="multipart/form-data"
	action="index.php?option=com_reditem&task=category_field.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm">
	<div class="row-fluid">
		<div class="span6">
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
					<?php echo $this->form->getLabel('type'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('type'); ?>
				</div>
			</div>
			<?php echo $this->form->getField('categories')->renderField();?>
			<?php if ($this->form->getField('default')):?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('default'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('default'); ?>
				</div>
			</div>
			<?php endif;?>
			<?php if (in_array($this->form->getValue('type'), $optionAvailable)) : ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('options'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('options'); ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('published'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('published'); ?>
				</div>
			</div>
		</div>
		<div class="span6">
			<?php foreach ($this->form->getGroup('params') as $field) : ?>
				<div class="control-group">
					<?php if ($field->type == 'Spacer') : ?>
						<hr />
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
	<?php echo $this->form->getInput('id'); ?>
	<?php echo $this->form->getInput('fieldcode'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="oldtype" value="<?php echo $this->item->type ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
