<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHTML::_('behavior.formvalidation');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

$object = JFactory::getApplication()->input->getString('object', '');
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

		form.submit();
	}
</script>

<form action="index.php?option=com_reditem&view=categories&layout=elements&tmpl=component&object=<?php echo $object; ?>" class="admin" id="adminForm" method="post" name="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<?php echo JText::_('COM_REDITEM_FILTER'); ?>
			<?php echo RLayoutHelper::render('search', array('view' => $this)); ?>
		</div>
		<div class="span6">
			<?php echo $this->filterForm->getLabel('filter_types'); ?>
			<?php echo $this->filterForm->getInput('filter_types'); ?>
		</div>
	</div>
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="30" align="center">
					<?php echo '#'; ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('rgrid.sort', 'COM_REDITEM_CATEGORY_CATEGORY', 'c.title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDITEM_CATEGORY_ASSIGNED_TEMPLATE'); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHTML::_('rgrid.sort', 'COM_REDITEM_ID', 'c.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="7">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php
		if (!empty($this->items))
		{
			$n = count($this->items);

			foreach ($this->items as $i => $row)
			{
			?>
				<tr>
					<td><?php echo $this->pagination->getRowOffset($i); ?></td>
					<td>
						<?php echo str_repeat('<span class="gi">|&mdash;</span>', $row->level - 1) ?>
						<?php $itemTitle = str_replace(array("'", "\""), array("\\'", ""), $row->title); ?>
						<a style="cursor: pointer;"
						onclick="window.parent.jRISelectCategory('<?php echo $row->id ?>', '<?php echo $itemTitle ?>', '<?php echo $object ?>');">
							<?php echo $this->escape($row->title); ?>
						</a>
					</td>
					<td>
						<?php echo $row->template_name; ?>
					</td>
					<td align="center" width="5%">
						<?php echo $row->id; ?>
					</td>
				</tr>
			<?php
			}
		}
		?>
		</tbody>
	</table>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
