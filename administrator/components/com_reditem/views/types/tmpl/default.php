<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Types
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
$search        = $this->state->get('filter.search');
$saveOrder     = ($listOrder == 'ty.ordering' && strtolower($listDirn) == 'asc');
$saveOrderLink = 'index.php?option=com_reditem&task=types.saveOrderAjax&tmpl=component';

if (($saveOrder) && ($this->canEdit))
{
	JHTML::_('rsortablelist.sortable', 'table-types', 'adminForm', strtolower($listDirn), $saveOrderLink, false, true);
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function (pressbutton)
	{
		submitbutton(pressbutton);
	};

	submitbutton = function (pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton)
		{
			form.task.value = pressbutton;
		}

		if (pressbutton == 'types.delete')
		{
			var r = confirm('<?php echo JText::_("COM_REDITEM_TYPE_DELETE_TYPES")?>');
			if (r == true)    form.submit();
			else return false;
		}
		form.submit();
	};
</script>
<form action="index.php?option=com_reditem&view=types" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'filterButton' => false,
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_type_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn,
				'chosenSupport' => false
			)
		)
	);
	?>
	<hr />
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<div class="pagination-centered">
			<h3><?php echo JText::_('COM_REDITEM_NOTHING_TO_DISPLAY'); ?></h3>
		</div>
	</div>
	<?php else : ?>
	<table class="table table-striped" id="table-types">
		<thead>
			<tr>
				<th width="30">
					<?php echo '#'; ?>
				</th>
				<th width="20">
					<?php if (version_compare(JVERSION, '3.0', 'lt')) : ?>
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					<?php else : ?>
						<?php echo JHTML::_('grid.checkall'); ?>
					<?php endif; ?>
				</th>
				<?php if (($search == '') && ($this->canEdit)) : ?>
				<th width="40" class="center">
					<?php echo JHTML::_('rsearchtools.sort', '<i class=\'icon-sort\'></i>', 'ty.ordering', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>
				<th class="title">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_TYPE_NAME', 'ty.title', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JText::_('COM_REDITEM_TYPE_OVERRIDE_FOLDER'); ?>
				</th>
				<th width="200">
					<?php echo JText::_('COM_REDITEM_TYPE_DESCRIPTION'); ?>
				</th>
				<th width="5%" nowrap="nowrap" class="text-center">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ID', 'ty.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php $n = count($this->items); ?>
		<?php foreach ($this->items as $i => $row) : ?>
			<tr>
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<?php if (($search == '') && ($this->canEdit)) : ?>
				<td class="order nowrap center">
					<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
						<i class="icon-move"></i>
					</span>
					<input type="text" style="display:none" name="order[]" value="<?php echo $row->ordering;?>" class="text-area-order" />
				</td>
				<?php endif; ?>
				<td>
					<?php if ($this->canEdit) : ?>
						<?php echo JHtml::_('link', 'index.php?option=com_reditem&task=type.edit&id=' . $row->id, $row->title); ?>
					<?php else : ?>
						<?php echo $this->escape($row->title); ?>
					<?php endif; ?>
				</td>
				<td>
					template/&lt;template_name&gt;/html/layouts/com_reditem/<?php echo 'type_' . $row->table_name ?>
				</td>
				<td>
					<?php echo $row->description; ?>
				</td>
				<td align="center" width="5%">
					<?php echo $row->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#copyOverrideTemplate').on('show', function(){
				if ($('#adminForm input[name="cid[]"]:checked').length > 0) {
					var typeIds = '';
					$('#adminForm input[name="cid[]"]:checked').each(function(index){
						typeIds += $(this).val() + ",";
					});

					var src = $('#copyOverrideTemplateIframe').attr('src');
					src += '&typeIds=' + typeIds;
					$('#copyOverrideTemplateIframe').attr('src', src);

					return true;
				}
			});
		});
	})(jQuery);
</script>
<div id="copyOverrideTemplate" class="modal fade hide" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-body">
		<?php $modalLink = 'index.php?option=com_reditem&layout=modal&tmpl=component&task=types.copyOverrideTemplate'; ?>
		<iframe id="copyOverrideTemplateIframe" src="<?php echo $modalLink ?>" frameborder="0" width="100%"></iframe>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JTOOLBAR_CLOSE') ?></button>
	</div>
</div>
