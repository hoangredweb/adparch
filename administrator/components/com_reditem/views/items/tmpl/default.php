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
JHtml::_('rholder.image', '50x50');
RHelperAsset::load('jquery-bootstrap-modal-steps.min.js', 'com_reditem');

$saveOrderLink = 'index.php?option=com_reditem&task=items.saveOrderAjax&tmpl=component';
$listOrder     = $this->state->get('list.ordering');
$listDirn      = $this->state->get('list.direction');
$saveOrder     = ($listOrder == 'i.ordering' && strtolower($listDirn) == 'asc');
$search        = $this->state->get('filter.search');
$typeId        = JFactory::getApplication()->getUserState('com_reditem.global.tid', '0');
$user          = ReditemHelperSystem::getUser();
$userId        = $user->id;
$itemTypeXref  = array();

if (($saveOrder) && ($this->canEditState))
{
	JHtml::_('rsortablelist.sortable', 'table-items', 'adminForm', strtolower($listDirn), $saveOrderLink, false, true);
}

foreach ($this->items as $item)
{
	$itemTypeXref[$item->id] = $item->type_id;
}

$document = JFactory::getDocument();

$scripts [] = 'var itemTypeXref = ' . json_encode($itemTypeXref) . ';';
$scripts [] = 'Joomla.submitbutton = function (pressbutton)
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

		if (pressbutton == \'items.delete\')
		{
			var r = confirm(\'' . JText::_("COM_REDITEM_ITEM_DELETE_ITEMS") . '\');
			if (r == true)    form.submit();
			else return false;
		}

		form.submit();
	};
	';

// Click on next step button
$scripts [] = '
	function nextStep ()
	{
		jQuery(\'.js-btn-step[data-orientation="next"]\').trigger(\'click\');
	}
';

// Auto complete wizard step
$scripts [] = '
	function autoCompleteStep ()
	{
		// Step button
		var $button = jQuery(\'#item-wizard .modal-footer button.js-btn-step\');
		// Hook on click event
		jQuery ($button).on(\'click\', function (e){
			// Going to step 2
			if (jQuery(this).data(\'step\') == \'complete\')
			{
				var $templates = jQuery(\'#item-wizard .modal-body *[data-step="2"] select\');
				// Get list of templates
				var $templateOptions = jQuery(\'#item-wizard .modal-body *[data-step="2"] select option\');
				// Finish wizard if we have only one template
				if ($templateOptions.length == 1)
				{
					// And go to next step
					nextStep();
				}
				else if ($templateOptions.length == 2)
				{
					jQuery.each ($templateOptions, function (){
						// This\'s default template nothing need to do
						if (jQuery(this).attr(\'value\') != 0)
						{
							// Select this option
							jQuery(this).attr(\'selected\',\'selected\');
							// And go to next step
							nextStep();
						}
					})
				}
			}
		})
	}
';

// Complete wizard callback
$scripts [] = 'function wizardComplete()
	{
		Joomla.submitbutton(\'item.add\');
	};';

// Wizard init
$scripts [] = '
(function ($) {
		$(document).ready(function () {
			var wizard = $(\'#item-wizard\');

			wizard.modalSteps({
				btnCancelHtml: "' . JText::_('JCANCEL') . '",
				btnPreviousHtml: "' . JText::_('COM_REDITEM_ITEMS_PREVIOUS') . '",
				btnNextHtml: "' . JText::_('COM_REDITEM_ITEMS_NEXT') . '",
				btnLastStepHtml: "' . JText::_('COM_REDITEM_ITEMS_PROCEED') . '",
				completeCallback: wizardComplete
			});
			// Init auto complete step
			autoCompleteStep();
		});
	})(jQuery);
';

$document->addScriptDeclaration(implode(PHP_EOL, $scripts));

$document->addStyleDeclaration('.redcore .modal-body{overflow: visible !important;}');
?>

<form action="index.php?option=com_reditem&view=items" class="admin" id="adminForm" method="post" name="adminForm">
	<div class="modal fade" id="item-wizard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="js-title-step"></h4>
				</div>
				<div class="modal-body">
					<div class="hide" data-step="1" data-title="<?php echo JText::_('COM_REDITEM_ITEMS_CHOOSE_TYPE') ?>">
						<?php echo $this->filterForm->getInput('type', 'wizard'); ?>
					</div>
					<div class="hide" data-step="2" data-title="<?php echo JText::_('COM_REDITEM_ITEMS_CHOOSE_TEMPLATE') ?>">
						<?php echo $this->filterForm->getInput('template', 'wizard'); ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default js-btn-step pull-left" data-orientation="cancel" data-dismiss="modal"></button>
					<button type="button" class="btn btn-warning js-btn-step" data-orientation="previous"></button>
					<button type="button" class="btn btn-success js-btn-step" data-orientation="next"></button>
				</div>
			</div>
		</div>
	</div>
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_items_limit',
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
		<table class="table table-striped" id="table-items">
			<thead>
			<tr>
				<th width="1%" align="center">
					<?php echo '#'; ?>
				</th>
				<th width="1%">
					<?php if (version_compare(JVERSION, '3.0', 'lt')) : ?>
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					<?php else : ?>
						<?php echo JHTML::_('grid.checkall'); ?>
					<?php endif; ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
				</th>
				<?php if ($this->canEdit) : ?>
					<th width="1%" nowrap="nowrap">
					</th>
				<?php endif; ?>
				<?php if (($search == '') && ($this->canEditState)) : ?>
					<th width="3%">
						<?php echo JHTML::_('rsearchtools.sort', '<i class=\'icon-sort\'></i>', 'i.ordering', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ITEM_NAME', 'i.title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ITEM_TYPE', 'type_name', $listDirn, $listOrder); ?>
				</th>
				<?php if (($this->displayableFields) && (count($this->displayableFields) > 0)) : ?>
					<?php foreach ($this->displayableFields as $displayField) : ?>
						<th>
							<?php $fieldName = JHTML::_('string.truncate', $displayField->name, 20, true, false); ?>
							<?php if ($displayField->type != 'image') : ?>
								<?php echo JHTML::_('rsearchtools.sort', $fieldName, 'cfv_' . $displayField->fieldcode, $listDirn, $listOrder); ?>
							<?php else : ?>
								<?php echo $fieldName; ?>
							<?php endif; ?>
						</th>
					<?php endforeach; ?>
				<?php endif; ?>
				<th>
					<?php echo JText::_('COM_REDITEM_ITEM_CATEGORIES'); ?>
				</th>
				<!--<th>
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ITEM_TEMPLATE', 'template_name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rsearchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rsearchtools.sort', 'COM_REDITEM_ITEM_AUTHOR', 'i.created_user_id', $listDirn, $listOrder); ?>
				</th>-->
				<th nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ID', 'i.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php $n = count($this->items); ?>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php if ($item->blocked) : ?>
					<tr class="item-blocked">
				<?php else : ?>
					<tr>
				<?php endif; ?>
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
				<td align="center">
					<fieldset class="btn-group">
						<?php if ($this->canEditState) : ?>
							<?php echo JHtml::_('rgrid.published', $item->published, $i, 'items.', true, 'cb', $item->publish_up, $item->publish_down); ?>
						<?php else : ?>
							<?php if ($item->published) : ?>
								<a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
							<?php else : ?>
								<a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($item->featured) : ?>
							<?php if ($this->canEditState) : ?>
								<?php echo JHtml::_('rgrid.action', $i, 'setUnFeatured', 'items.', '', '', '', false, 'star featured', 'star featured', true, true, 'cb'); ?>
							<?php else : ?>
								<span class="btn btn-small disabled"><i class="icon-star featured"></i></span>
							<?php endif; ?>
						<?php else : ?>
							<?php if ($this->canEditState) : ?>
								<?php echo JHtml::_('rgrid.action', $i, 'setFeatured', 'items.', '', '', '', false, 'star-empty', 'star-empty', true, true, 'cb'); ?>
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
							echo JHtml::_('rgrid.checkedout', $i, $editor->name, $item->checked_out_time, 'items.', $canCheckin);
							?>
						<?php endif; ?>
					</td>
				<?php endif; ?>
				<?php if (($search == '') && ($this->canEditState)) : ?>
					<td class="order nowrap center">
					<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
						<i class="icon-move"></i>
					</span>
						<input type="text" style="display:none" name="order[]" value="<?php echo $item->ordering;?>" class="text-area-order" />
					</td>
				<?php endif; ?>
				<td class="item-title">
					<?php $itemTitle = JHTML::_('string.truncate', $item->title, 50, true, false); ?>
					<?php if (($item->checked_out) || (!$this->canEdit)) : ?>
						<?php echo $itemTitle; ?>
					<?php else : ?>
						<?php echo JHtml::_('link', 'index.php?option=com_reditem&task=item.edit&id=' . $item->id, $itemTitle); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $item->type_name; ?>
				</td>
				<!-- Add displayable fields data -->
				<?php if (($this->displayableFields) && (count($this->displayableFields) > 0)) : ?>
					<?php foreach ($this->displayableFields as $displayField) : ?>
						<td>
							<?php if ($displayField->type == 'image') : ?>
								<?php
								$image = json_decode($item->customfield_values[$displayField->fieldcode], true);
								$fileName = explode('/', $image[0]);
								$fileName = array_pop($fileName);
								$thumbnailPath = ReditemHelperImage::getImageLink($item, 'customfield', $fileName, 'thumbnail', 50, 50, true);
								?>
								<img src="<?php echo $thumbnailPath; ?>" />
							<?php elseif ($displayField->type == 'user') : ?>
								<?php
								$ids = json_decode($item->customfield_values[$displayField->fieldcode]);
								$html = array();

								foreach ($ids as $id)
								{
									$user = JFactory::getUser($id);

									if (isset($user->name) && !empty($user->name))
									{
										$html[] = $user->name;
									}
								}

								echo JHTML::_('string.truncate', strip_tags(implode("\n", $html)), 50, true, false);
								?>
							<?php else : ?>
								<?php $cfValue = $item->customfield_values[$displayField->fieldcode]; ?>
								<?php if ($cfValue) : ?>
									<?php if ($displayField->type == "checkbox") : ?>
										<?php $cfValue = implode(', ', json_decode($cfValue)); ?>
									<?php endif; ?>
									<?php echo JHTML::_('string.truncate', strip_tags($cfValue), 50, true, false); ?>
								<?php endif; ?>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				<?php endif; ?>
				<!-- End add displayable fields data -->
				<td>
					<?php
					if (!empty($item->categories)) :
						$categories = array();

						foreach ($item->categories As $cat) :
							if (!empty($cat)) :
								$categories[] = $cat->title;
							endif;
						endforeach;

						echo implode('<br />', $categories);
					endif;
					?>
				</td>
				<!--<td>
					<?php echo $item->template_name; ?>
				</td>
				<td>
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="hidden-phone">
					<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->created_user_id); ?>">
						<?php echo $this->escape($item->author_name); ?>
					</a>
				</td>-->
				<td align="center">
					<?php echo $item->id; ?>
				</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
	<?php endif; ?>

	<!-- Load the batch processing form. -->
	<!--<?php if ($user->authorise('core.create', 'com_reditem') && $user->authorise('core.edit', 'com_reditem') && $user->authorise('core.edit.state', 'com_reditem')): ?>
		<?php if ($typeId != 0): ?>
			<div id="batchForm">
				<?php echo $this->loadTemplate('batch'); ?>
			</div>
		<?php else: ?>
			<div class="alert alert-info">
				<?php echo JText::_("COM_REDITEM_ITEMS_BATCH_PROCESS_CHOOSE_TYPE"); ?>
			</div>
		<?php endif; ?>
	<?php endif;?>-->

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
	<?php echo RLayoutHelper::render('items.exportCsv', null, null, array('component' => 'com_reditem')); ?>
</form>
<?php echo RLayoutHelper::render('items.importCsv', null, null, array('component' => 'com_reditem')); ?>

<script>
	jQuery(document).ready(function(){
		jQuery('#filter_filter_time_from').on('blur', function(){
			jQuery('#adminForm').submit();
		});
		jQuery('#filter_filter_time_to').on('blur', function(){
			jQuery('#adminForm').submit();
		});
		jQuery('.js-stools-btn-filter').click();
	})
</script>