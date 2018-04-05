<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$saveOrderLink = 'index.php?option=com_reditem&task=templates.saveOrderAjax&tmpl=component';
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
$saveOrder     = ($listOrder == 't.ordering' && strtolower($listDirn) == 'asc');
$user          = ReditemHelperSystem::getUser();
$userId        = $user->id;
$search        = $this->state->get('filter.search');

if ($saveOrder)
{
	JHtml::_('rsortablelist.sortable', 'table-templates', 'adminForm', strtolower($listDirn), $saveOrderLink, false, true);
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

		if (pressbutton == 'templates.delete')
		{
			var r = confirm('<?php echo JText::_("COM_REDITEM_TEMPLATE_DELETE_TEMPLATES")?>');
			if (r == true)    form.submit();
			else return false;
		}
		form.submit();
	};
</script>
<form action="index.php?option=com_reditem&view=templates" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_templates_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
	<hr />
	<?php if (empty($this->stats['types'])) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::sprintf('COM_REDITEM_NO_TYPE_EXISTS', $this->toType); ?></h3>
			</div>
		</div>
	<?php elseif (empty($this->items)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDITEM_NOTHING_TO_DISPLAY'); ?></h3>
			</div>
		</div>
	<?php else : ?>
	<table class="table table-striped" id="table-templates">
		<thead>
			<tr>
				<th width="10" align="center">
					<?php echo '#'; ?>
				</th>
				<th width="10">
					<?php if (version_compare(JVERSION, '3.0', 'lt')) : ?>
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					<?php else : ?>
						<?php echo JHTML::_('grid.checkall'); ?>
					<?php endif; ?>
				</th>
				<th width="30" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'JSTATUS', 't.published', $listDirn, $listOrder); ?>
				</th>
				<?php if (($search == '') && ($this->canEditState)) : ?>
					<th width="3%" style="text-align: center">
						<?php echo JHTML::_('rsearchtools.sort', '<i class=\'icon-sort\'></i>', 't.ordering', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<?php if ($this->canEdit) : ?>
				<th width="1" align="center">
				</th>
				<?php endif; ?>
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_TEMPLATE_NAME', 't.name', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_TEMPLATE_TYPE', 'type_name', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_TEMPLATE_FOR', 't.typecode', $listDirn, $listOrder); ?>
				</th>
				<th width="20%">
					<?php echo JText::_('COM_REDITEM_TEMPLATE_DESCRIPTION'); ?>
				</th>
				<th width="10" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ID', 't.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php $n = count($this->items); ?>
		<?php foreach ($this->items as $i => $row) : ?>
			<tr>
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td>
					<?php if ($this->canEditState) : ?>
						<?php echo JHtml::_('rgrid.published', $row->published, $i, 'templates.', true, 'cb'); ?>
					<?php else : ?>
						<?php if ($row->published) : ?>
							<a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
						<?php else : ?>
							<a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
						<?php endif; ?>
					<?php endif; ?>
				</td>
				<?php if (($search == '') && ($this->canEditState)) : ?>
					<td class="order nowrap center">
					<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
						<i class="icon-move"></i>
					</span>
						<input type="text" style="display:none" name="order[]" value="<?php echo $row->ordering;?>" class="text-area-order" />
					</td>
				<?php endif; ?>
				<?php if ($this->canEdit) : ?>
				<td>
					<?php if ($row->checked_out) : ?>
						<?php
						$editor = ReditemHelperSystem::getUser($row->checked_out);
						$canCheckin = $row->checked_out == $userId || $row->checked_out == 0;
						echo JHtml::_('rgrid.checkedout', $i, $editor->name, $row->checked_out_time, 'templates.', $canCheckin);
						?>
					<?php endif; ?>
				</td>
				<?php endif; ?>
				<td>
					<?php if (($row->checked_out) || (!$this->canEdit)) : ?>
						<?php echo $row->name; ?>
					<?php else : ?>
						<?php echo JHtml::_('link', 'index.php?option=com_reditem&task=template.edit&id=' . $row->id, $row->name); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $row->type_name; ?>
				</td>
				<td>
					<?php
					switch ($row->typecode) :
						case 'view_archiveditems':
							echo '<span class="badge badge-info">' . JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_ARCHIVEDITEMS') . '</span>';
							break;
						case 'view_itemdetail':
							echo '<span class="badge badge-info">' . JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_ITEMDETAIL') . '</span>';
							break;
						case 'view_itemedit':
							echo '<span class="badge badge-info">' . JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_ITEMEDIT') . '</span>';
							break;
						case 'view_categorydetail':
							echo '<span class="badge badge-info">' . JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_CATEGORYDETAIL') . '</span>';
							break;
						case 'view_categorydetailgmap':
							echo '<span class="badge badge-info">' . JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_CATEGORYDETAIL_GMAP') . '</span>';
							break;
						case 'view_search':
							echo '<span class="badge badge-info">' . JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_SEARCH') . '</span>';
							break;
						case 'module_items':
							echo '<span class="badge badge-success">' . JText::_('COM_REDITEM_TEMPLATE_TYPE_ITEMS_MODULE') . '</span>';
							break;
						case 'module_relateditems':
							echo '<span class="badge badge-success">' . JText::_('COM_REDITEM_TEMPLATE_TYPE_RELATED_ITEMS_MODULE') . '</span>';
							break;
						case 'module_search':
							echo '<span class="badge badge-success">' . JText::_('COM_REDITEM_TEMPLATE_TYPE_SEARCH_MODULE') . '</span>';
							break;
						default:
							break;
					endswitch;
					?>
				</td>
				<td>
					<?php echo JHTML::_('string.truncate', $row->description, 50, true, false); ?>
				</td>
				<td>
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
