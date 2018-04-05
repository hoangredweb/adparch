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

$saveOrderUrl = 'index.php?option=com_reditem&task=category_fields.saveOrderAjax&tmpl=component';
$listOrder    = $this->escape($this->state->get('list.ordering'));
$listDirn     = $this->escape($this->state->get('list.direction'));
$saveOrder    = ($listOrder == 'cf.ordering' && strtolower($listDirn) == 'asc');
$user         = ReditemHelperSystem::getUser();
$userId       = $user->id;
$search       = $this->state->get('filter.search');

if (($saveOrder) && ($this->canEditState))
{
	JHTML::_('rsortablelist.sortable', 'table-category-fields', 'adminForm', strtolower($listDirn), $saveOrderUrl, false, true);
}

$document = JFactory::getDocument();
$document->addStyleDeclaration('.redcore .modal-body{overflow: visible !important;}');
$script = '
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

		if (pressbutton == \'category_fields.delete\')
		{
			var r = confirm(\'<?php echo JText::_("COM_REDITEM_FIELD_DELETE_FIELDS")?>\');
			if (r == true)    form.submit();
			else return false;
		}

		form.submit();
	};
	
	jQuery(document).ready(function() {
		var categories = jQuery(\'#modal_categories\');

		function checkCategoriesAssign() {
			if (categories.val())
			{
				jQuery(\'#btn-assign-categories\').removeAttr(\'disabled\').on(\'click\', function() {
					Joomla.submitbutton(\'category_fields.assign\')
				});
			}
			else
			{
				jQuery(\'#btn-assign-categories\').attr(\'disabled\', \'disabled\').off(\'click\');
			}
		}

		categories.on(\'change\', checkCategoriesAssign);
	});
';
$document->addScriptDeclaration($script);
?>
<form action="index.php?option=com_reditem&view=category_fields" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_category_fields_limit',
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
	<table class="table table-striped table-hover" id="table-category-fields">
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
					<?php echo JHTML::_('rsearchtools.sort', 'JSTATUS', 'cf.state', $listDirn, $listOrder); ?>
				</th>
				<?php if ($this->canEdit) : ?>
				<th width="1" nowrap="nowrap">
				</th>
				<?php endif; ?>
				<?php if (($search == '') && ($this->canEditState)) : ?>
				<th width="40">
					<?php echo JHTML::_('rsearchtools.sort', '<i class=\'icon-sort\'></i>', 'cf.ordering', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_FIELD_NAME', 'cf.name', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JText::_('COM_REDITEM_FIELD_GROUP'); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_FIELD_FIELDCODE', 'cf.fieldcode', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_FIELD_FIELDTYPE', 'cf.type', $listDirn, $listOrder); ?>
				</th>
				<th width="10">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ID', 'cf.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php $n = count($this->items); ?>
			<?php foreach ($this->items as $i => $row) : ?>
				<tr>
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<?php echo JHtml::_('grid.id', $i, $row->id); ?>
					</td>
					<td>
						<?php if ($this->canEditState) : ?>
							<?php echo JHtml::_('rgrid.published', $row->state, $i, 'category_fields.', true, 'cb'); ?>
						<?php else : ?>
							<?php if ($row->state) : ?>
								<a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
							<?php else : ?>
								<a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<?php if ($this->canEdit) : ?>
					<td>
						<?php if ($row->checked_out) : ?>
							<?php
							$editor = ReditemHelperSystem::getUser($row->checked_out);
							$canCheckin = $row->checked_out == $userId || $row->checked_out == 0;
							echo JHtml::_('rgrid.checkedout', $i, $editor->name, $row->checked_out_time, 'category_fields.', $canCheckin);
							?>
						<?php endif; ?>
					</td>
					<?php endif; ?>
					<?php if (($search == '') && ($this->canEditState)) : ?>
					<td class="order nowrap center">
						<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
							<i class="icon-move"></i>
						</span>
						<input type="text" style="display:none" name="order[]" value="<?php echo $row->ordering;?>" class="text-area-order" />
					</td>
					<?php endif; ?>
					<td>
						<?php $itemTitle = JHTML::_('string.truncate', $row->name, 50, true, false); ?>
						<?php if (($row->checked_out) || (!$this->canEdit)) : ?>
							<?php echo $itemTitle; ?>
						<?php else : ?>
							<?php echo JHtml::_('link', 'index.php?option=com_reditem&task=category_field.edit&id=' . $row->id, $itemTitle); ?>
						<?php endif; ?>
					</td>
					<td>
						<?php $params = new JRegistry($row->params); ?>
						<?php echo $params->get('group', JText::_('COM_REDITEM_FIELDS_UNGROUP')); ?>
					</td>
					<td>
						<?php echo $row->fieldcode; ?>
					</td>
					<td>
						<?php echo JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_' . strtoupper($row->type)) ?>
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
	<div class="modal hide fade" id="assign">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo JText::_('COM_REDITEM_CATEGORY_FIELDS_ASSIGN_CATEGORIES_MODAL_HEADER');?></h3>
		</div>
		<div class="modal-body" style="overflow-y: visible; width: initial;">
			<p><?php echo JText::_('COM_REDITEM_CATEGORY_FIELDS_ASSIGN_CATEGORIES_MODAL_INTRO');?></p>
			<?php echo $this->filterForm->getField('categories', 'modal')->renderField();?>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('COM_REDITEM_CLOSE');?></a>
			<a href="#" disabled="disabled" id="btn-assign-categories" class="btn btn-primary"><?php echo JText::_('COM_REDITEM_CONFIRM');?></a>
		</div>
	</div>
</form>
