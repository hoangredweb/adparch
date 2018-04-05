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

$saveOrderUrl = 'index.php?option=com_reditem&task=categories.saveOrderAjax&tmpl=component';
$listOrder    = $this->state->get('list.ordering');
$listDirn     = $this->state->get('list.direction');
$saveOrder    = ($listOrder == 'c.lft' && strtolower($listDirn) == 'asc');
$search       = $this->state->get('filter.search');
$user         = ReditemHelperSystem::getUser();
$userId       = $user->id;

if (($saveOrder) && ($this->canEditState))
{
	JHTML::_('rsortablelist.sortable', 'table-categories', 'adminForm', strtolower($listDirn), $saveOrderUrl, false, true);
}

?>
<script type="text/javascript">
	Joomla.submitbutton = function (pressbutton)
	{
		submitbutton(pressbutton);
	}
	submitbutton = function (pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton)
		{
			form.task.value = pressbutton;
		}

		if (pressbutton == 'categories.delete')
		{
			var r = confirm('<?php echo JText::_("COM_REDITEM_CATEGORY_DELETE_CATEGORIES")?>');
			if (r == true)    form.submit();
			else return false;
		}
		form.submit();
	}
</script>

<form action="index.php?option=com_reditem&view=categories" class="adminForm" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_categories_limit',
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
	<?php elseif (empty($this->templates)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::sprintf('COM_REDITEM_NO_TEMPLATE_EXISTS', $this->toTemplate); ?></h3>
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
	<table class="table table-striped" id="table-categories">
		<thead>
			<tr>
				<th width="10">
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
					<?php echo JText::_('JSTATUS'); ?>
				</th>
				<?php if ($this->canEdit) : ?>
				<th width="30px">
				</th>
				<?php endif; ?>
				<?php if (($search == '') && ($this->canEditState)) : ?>
				<th width="40" class="center">
					<?php echo JHTML::_('rsearchtools.sort', '<i class=\'icon-sort\'></i>', 'c.lft', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_CATEGORY_CATEGORY', 'c.title', $listDirn, $listOrder); ?>
				</th>
				<th width="20%">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_CATEGORY_ASSIGNED_TEMPLATE', 'template_name', $listDirn, $listOrder); ?>
				</th>
				<th width="80">
					<?php echo JHtml::_('rsearchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="50" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ID', 'c.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php $n = count($this->items); ?>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php $orderkey = array_search($item->id, $this->ordering[$item->parent_id]); ?>
			<?php if ($item->level > 1) : ?>
				<?php
				$parentsStr = '';
				$_currentParentId = $item->parent_id;
				$parentsStr = ' ' . $_currentParentId;
				?>
				<?php for ($i2 = 0; $i2 < $item->level; $i2++) : ?>
					<?php foreach ($this->ordering as $k => $v) : ?>
						<?php
						$v = implode('-', $v);
						$v = '-' . $v . '-';
						?>
						<?php if (strpos($v, '-' . $_currentParentId . '-') !== false) : ?>
							<?php
							$parentsStr .= ' ' . $k;
							$_currentParentId = $k;
							break;
							?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endfor; ?>
			<?php else : ?>
				<?php $parentsStr = ''; ?>
			<?php endif; ?>
			<tr sortable-group-id="<?php echo $item->parent_id;?>" item-id="<?php echo $item->id?>" parents="<?php echo $parentsStr?>" level="<?php echo $item->level?>">
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
				<td align="center">
					<fieldset class="btn-group">
						<?php if ($this->canEditState) : ?>
							<?php echo JHtml::_('rgrid.published', $item->published, $i, 'categories.', true, 'cb', $item->publish_up, $item->publish_down); ?>
						<?php else : ?>
							<?php if ($item->published) : ?>
								<a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
							<?php else : ?>
								<a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ($item->featured) : ?>
							<?php if ($this->canEditState) : ?>
								<?php echo JHtml::_('rgrid.action', $i, 'setUnFeatured', 'categories.', '', '', '', false, 'star featured', 'star featured', true, true, 'cb'); ?>
							<?php else : ?>
								<span class="btn btn-small disabled"><i class="icon-star featured"></i></span>
							<?php endif; ?>
						<?php else : ?>
							<?php if ($this->canEditState) : ?>
								<?php echo JHtml::_('rgrid.action', $i, 'setFeatured', 'categories.', '', '', '', false, 'star-empty', 'star-empty', true, true, 'cb'); ?>
							<?php else : ?>
								<span class="btn btn-small disabled"><i class="icon-star-empty"></i></span>
							<?php endif; ?>
						<?php endif; ?>
					</fieldset>
				</td>
				<?php if ($this->canEdit) : ?>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php
						$editor = ReditemHelperSystem::getUser($item->checked_out);
						$canCheckin = $item->checked_out == $userId || $item->checked_out == 0;
						echo JHtml::_('rgrid.checkedout', $i, $editor->name, $item->checked_out_time, 'categories.', $canCheckin);
						?>
					<?php endif; ?>
				</td>
				<?php endif; ?>
				<?php if (($search == '') && ($this->canEditState)) : ?>
				<td class="order nowrap center">
					<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
						<i class="icon-move"></i>
					</span>
					<input type="text" style="display:none" name="order[]" value="<?php echo $orderkey + 1;?>" class="text-area-order" />
				</td>
				<?php endif; ?>
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1) ?>
					<?php if (($item->checked_out) || (!$this->canEdit)) : ?>
						<?php echo $this->escape($item->title); ?>
					<?php else : ?>
						<?php echo JHtml::_('link', 'index.php?option=com_reditem&task=category.edit&id=' . $item->id, $this->escape($item->title)); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $item->template_name; ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
	<?php endif; ?>
	<input type="hidden" name="view" value="categories" />
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
