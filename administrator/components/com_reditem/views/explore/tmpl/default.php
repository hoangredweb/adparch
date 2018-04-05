<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Types
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('rjquery.chosen', 'select');
JHtml::_('rholder.image', '50x50');
RHelperAsset::load('jquery-bootstrap-modal-steps.min.js', 'com_reditem');

// Load search tools
RHtml::_('rsearchtools.form', '#adminForm', array());
JLoader::import('helper', JPATH_COMPONENT . '/helpers');

$saveOrderUrl = JRoute::_('index.php?option=com_reditem&task=explore.ajaxSaveOrder&tmpl=component', false);
$listOrder    = $this->state->get('list.ordering');
$listDirn     = $this->state->get('list.direction');
$saveOrder    = ($listOrder == 'ordering' && strtolower($listDirn) == 'asc');
$search       = $this->state->get('filter.search');
$typeId       = $this->typeId;
$limit        = $this->limit;
$itemTypeXref = array();

if (($saveOrder) && ($this->canEditState))
{
	JHtml::_('rsortablelist.sortable', 'table-explore', 'adminForm', strtolower($listDirn), $saveOrderUrl, false, true);
}

$formUrl = 'index.php?option=com_reditem&view=explore';

if (!empty($this->parentId))
{
	$formUrl .= '&limit=' . $this->limit . '&parent_id=' . $this->parentId;
}

foreach ($this->items['items'] as $item)
{
	$itemTypeXref[$item->id] = $item->type_id;
}

?>
<script type="text/javascript">
	var itemTypeXref = <?php echo json_encode($itemTypeXref);?>;
	var typesFrom    = null;

	jQuery(document).ready(function(){
		Joomla.submitform = function (task, form)
		{
			form.action = '<?php echo JRoute::_($formUrl, false); ?>';
			form.submit();
		}
	});

	Joomla.submitbutton = function (pressbutton)
	{
		submitbutton(pressbutton);
	};

	function sendAjax(pressbutton,form)
	{
		var jmsgs = [];

		// Send form to this controller
		jQuery.ajax({
	          type: "POST",
	          dataType: "json",
	          url: '<?php echo JRoute::_("index.php?option=com_reditem", false)?>',
	          data: jQuery(form).serialize(), // serializes the form's elements.
	          success: function(data)
	          {
	        	  jmsgs = [data.message];
		          Joomla.renderMessages({'info': jmsgs });

		          // Remove task from form
		          jQuery('#adminForm input[name=task]').val("");
	          }
	    });
	}

	submitbutton = function (pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton)
		{
			form.task.value = pressbutton;
		}

		switch (pressbutton)
		{
			case "explore.edit":
				var checks = jQuery('input[type=checkbox][name^="ritem"]:checked');
				var ele    = jQuery(checks[0]);

				if (ele.attr('name') == 'ritem[itemIds][]')
				{
					jQuery.ajax({
						url  : 'index.php?option=com_reditem&task=item.ajaxGetEditTemplates',
						data : {
							id   : itemTypeXref[ele.val()],
							showHtml : 1
						}
					}).done(function(data) {
						jQuery('#item-edit-tmp').html(data);
						jQuery('#editTemplate').chosen();
						jQuery('#item-edit').modal('show');
					});

					return false;
				}

				break;
			case "explore.copy":
			case "explore.move":
					// Send ajax copy to this controller
					sendAjax(pressbutton, form);

					return;

				break;
			case "items.delete":
					var r = confirm('<?php echo JText::_("COM_REDITEM_ITEM_DELETE_ITEMS")?>');

					if (r == true)
					{
						form.submit();
					}
					else
					{
						return false;
					}

				break;
			default:
				break;
		}

		form.submit();
	};

	function submitLimit()
	{
		var form = document.adminForm;
		form.task = "";
		form.action = '<?php echo JRoute::_($formUrl, false); ?>';
		form.submit();
	}

	(function($){
		$(document).ready(function() {
			var wizard    = $("#item-wizard");
			var convert   = $("#item-convert");
			var next      = $(convert.find('button[data-orientation="next"]:first'));
			var typeField = $('#convert_type');
			var tplField  = $('#convert_template');

			wizard.modalSteps({
				btnCancelHtml: "<?php echo JText::_('JCANCEL')?>",
				btnPreviousHtml: "<?php echo JText::_('COM_REDITEM_ITEMS_PREVIOUS')?>",
				btnNextHtml: "<?php echo JText::_('COM_REDITEM_ITEMS_NEXT')?>",
				btnLastStepHtml: "<?php echo JText::_('COM_REDITEM_ITEMS_PROCEED')?>",
				completeCallback: wizardComplete
			});

			convert.modalSteps({
				btnCancelHtml: "<?php echo JText::_('JCANCEL')?>",
				btnPreviousHtml: "<?php echo JText::_('COM_REDITEM_ITEMS_PREVIOUS')?>",
				btnNextHtml: "<?php echo JText::_('COM_REDITEM_ITEMS_NEXT')?>",
				btnLastStepHtml: "<?php echo JText::_('COM_REDITEM_ITEMS_PROCEED')?>",
				callbacks: {
					'1': function (){
						if (!typeField.val() || typeField.val() == 0)
						{
							next.attr('disabled', 'disabled');
						}

						typeField.on('change', function() {
							if (!$(this).val() || $(this).val() == 0)
							{
								next.attr('disabled', 'disabled');
							}
							else
							{
								next.removeAttr('disabled');
							}
						});
					},
					'2': function (){
						if (!tplField.val() || tplField.val() == 0)
						{
							next.attr('disabled', 'disabled');
						}

						tplField.on('change', function() {
							if (!$(this).val() || $(this).val() == 0)
							{
								next.attr('disabled', 'disabled');
							}
							else
							{
								next.removeAttr('disabled');
							}
						});
					},
					'3': function (){
						var typeTo = $('#convert_type').val();

						$.ajax({
							url  : 'index.php?option=com_reditem&task=items.ajaxGetEditFields',
							data : {
								typeTo    : typeTo,
								typesFrom : typesFrom
							},
							beforeSend : function() {
								$('#item-convert-fields').html('<p><?php echo JText::_('COM_REDITEM_ITEMS_CONVERT_LOADING_FIELDS');?></p>');
							}
						}).done(function(data) {
							$('#item-convert-fields').html(data);
							$('.convert-fields').each(function() {
								var field = $(this);
								field.chosen();
								field.data('pre', field.val());

								field.on('change', function() {
									var name      = field.attr('name');
									var fClasses  = field.attr('class').split(' ');
									var fieldType = fClasses[1];
									var val       = field.val();
									var bChange   = field.data('pre');

									if (bChange > 0)
									{
										$('.' + fieldType).each(function() {
											var iField = $(this);

											if (iField.attr('name') != name)
											{
												iField.find('option[value="' + bChange + '"]').removeAttr('disabled');
												iField.trigger('liszt:updated');
											}
										});
									}

									if (val > 0)
									{
										$('.' + fieldType).each(function() {
											var iField = $(this);

											if (iField.attr('name') != name)
											{
												if (iField.val() == val)
												{
													iField.val('0');
												}

												iField.find('option[value="' + val + '"]').attr('disabled', 'disabled');
												iField.trigger('liszt:updated');
											}
										});
									}

									field.data('pre', val);
								});
							});
						});
					}
				},
				completeCallback: convertComplete
			});

			convert.on('show', function() {
				var checks = $('input[type=checkbox][name="ritem[itemIds][]"]:checked');
				typesFrom  = [];

				checks.each(function() {
					var val = jQuery(this).val();
					typesFrom.push(itemTypeXref[val]);
				});

				typesFrom = typesFrom.filter(function(val, index, self) {
					return self.indexOf(val) === index;
				});

				jQuery(typesFrom).each(function() {
					typeField.find('option[value="' + this + '"]').attr('disabled', 'disabled');
				});

				typeField.trigger('liszt:updated');
			});

			convert.on('shown', function() {
				var items = $('input[type=checkbox][name="ritem[itemIds][]"]:checked');
				var jmsgs = ["<?php echo JText::_('COM_REDITEM_ITEMS_CONVERT_ONLY_ITEMS');?>"];

				if (items.length <= 0)
				{
					convert.modal('hide');
					Joomla.renderMessages({'warning': jmsgs });
				}

				setTimeout(function() {
					Joomla.removeMessages();
				}, 4500);
			});
		});
	})(jQuery);

	function wizardComplete()
	{
		Joomla.submitbutton('item.add');
	}

	function convertComplete()
	{
		jQuery('#convert_typesFrom').val(JSON.stringify(typesFrom));
		Joomla.submitbutton('explore.convert');
	}
</script>
<style type="text/css">
	.redcore .modal-body
	{
		overflow: inherit;
	}
</style>
<?php
	$layout     = new JLayoutFile('breadcrumb', JPATH_ADMINISTRATOR . '/components/com_reditem/layouts/explore');
	$categories = ReditemHelperHelper::getParentCategories($this->parentId);

	echo $layout->render(array(
		"categoriesList" => $categories,
		"limit"          => $this->limit
	));
?>
<p></p>

<form action="<?php echo JRoute::_("index.php?option=com_reditem&view=explore"); ?>" class="admin" id="adminForm" method="post" name="adminForm">
	<div class="modal fade" id="item-wizard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="js-title-step"></h4>
				</div>
				<div class="modal-body">
					<div class="hide" data-step="1" data-title="<?php echo JText::_('COM_REDITEM_ITEMS_CHOOSE_TYPE')?>">
						<?php echo $this->filterForm->getInput('type', 'wizard'); ?>
					</div>
					<div class="hide" data-step="2" data-title="<?php echo JText::_('COM_REDITEM_ITEMS_CHOOSE_TEMPLATE')?>">
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
	<div class="modal fade" id="item-convert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="js-title-step"></h4>
				</div>
				<div class="modal-body">
					<div class="hide" data-step="1" data-title="<?php echo JText::_('COM_REDITEM_ITEMS_CHOOSE_TYPE')?>">
						<?php echo $this->filterForm->getInput('type', 'convert'); ?>
					</div>
					<div class="hide" data-step="2" data-title="<?php echo JText::_('COM_REDITEM_ITEMS_CHOOSE_TEMPLATE')?>">
						<?php echo $this->filterForm->getInput('template', 'convert'); ?>
					</div>
					<div class="hide" data-step="3" data-title="<?php echo JText::_('COM_REDITEM_ITEMS_CONVERT_MAP_FIELDS')?>">
						<div id="item-convert-fields" style="overflow-y: scroll; max-height: 400px; width: 530px"></div>
					</div>
					<div class="hide" data-step="4" data-title="<?php echo JText::_('COM_REDITEM_ITEMS_CONVERT_ADVANCE_OPTIONS')?>">
						<?php echo $this->filterForm->getField('categories', 'convert')->renderField(); ?>
						<?php echo $this->filterForm->getField('keeporg', 'convert')->renderField(); ?>
						<input type="hidden" name="convert[typesFrom]" id="convert_typesFrom" value="" />
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
		if (!$typeId)
		{
			$this->filterForm->setValue('typeId', 'filter', '');
		}

		$group = $this->filterForm->getGroup("filter");

		echo $group["filter_typeId"]->input;
	?>
	<?php
		if (!$this->limit)
		{
			$this->filterForm->setValue('items_limit', 'list', 0);
		}

		$groupList = $this->filterForm->getGroup("list");

		echo $groupList["list_items_limit"]->input;
	?>
	<hr />
	<?php if (empty($this->items['categories']) && empty($this->items['items'])) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDITEM_NOTHING_TO_DISPLAY'); ?></h3>
			</div>
		</div>
	<?php else : ?>
		<table class="table table-striped" id="table-explore">
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
						<?php echo JText::_('JSTATUS'); ?>
					</th>
					<?php if (($search == '') && ($this->canEditState)) : ?>
					<th width="3%">
						<?php echo JHTML::_('rsearchtools.sort', '<i class=\'icon-sort\'></i>', 'i.ordering', $listDirn, $listOrder); ?>
					</th>
					<?php endif; ?>
					<th class="title checktitle" width="auto">
						<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_EXPLORE_TITLE', 'title', $listDirn, $listOrder); ?>
					</th>
					<th width="auto">
						<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_EXPLORE_AUTHOR', 'author', $listDirn, $listOrder); ?>
					</th>
					<th width="auto">
						<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_EXPLORE_TEMPLATE', 'template', $listDirn, $listOrder); ?>
					</th>
					<?php if (($this->displayableFields) && (count($this->displayableFields) > 0)) : ?>
						<?php foreach ($this->displayableFields as $displayField) : ?>
							<th width="auto">
							<?php $fieldName = JHTML::_('string.truncate', $displayField->name, 20, true, false); ?>
							<?php if ($displayField->type != 'image') : ?>
								<?php echo JHTML::_('rsearchtools.sort', $fieldName, 'cfv_' . $displayField->fieldcode, $listDirn, $listOrder); ?>
							<?php else : ?>
								<?php echo $fieldName; ?>
							<?php endif; ?>
							</th>
						<?php endforeach; ?>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
			<?php
				$index = 0;

				if ($this->limitStart != 0)
				{
					$index = $this->limitStart;
				}
			?>
			<?php foreach ($this->items["categories"] as $i => $item) : ?>
				<tr sortable-group-id="<?php echo $item->parent_id;?>">
					<td><?php echo $this->pagination->getRowOffset($index++); ?></td>
					<td><?php echo JHtml::_('grid.id', $i, $item->id, false, "ritem[catIds]"); ?></td>
					<td align="center">
						<fieldset class="btn-group">
							<?php if ($this->canEditState) : ?>
								<?php echo JHtml::_('rgrid.published', $item->published, $i, 'explore.', true, 'cb', $item->publish_up, $item->publish_down); ?>
							<?php else : ?>
								<?php if ($item->published) : ?>
									<a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
								<?php else : ?>
									<a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
								<?php endif; ?>
							<?php endif; ?>
						</fieldset>
					</td>
					<?php if (($search == '') && ($this->canEditState)) : ?>
					<td class="order nowrap center drag">
						<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
							<i class="icon-move"></i>
						</span>
						<input type="text" style="display:none" name="order[]" value="<?php echo $item->ordering;?>" class="text-area-order" />
					</td>
					<?php endif; ?>
					<td>
						<i class="icon-folder-open"></i>
						<?php if (($item->checked_out) || (!$this->canEdit)) : ?>
							<?php echo $this->escape($item->title); ?>
						<?php else : ?>
							<?php echo JHtml::_('link', 'index.php?option=com_reditem&view=explore&limit=' . $this->limit . '&parent_id=' . $item->id, $this->escape($item->title)); ?>
						<?php endif; ?>
					</td>
					<td>
						<?php
							$user = RFactory::getUser($item->created_user_id);
							echo $user->name;
						?>
					</td>
					<td>
						<?php echo $item->template_name; ?>
					</td>
					<?php if (($this->displayableFields) && (count($this->displayableFields) > 0)) : ?>
						<?php foreach ($this->displayableFields as $displayField) : ?>
							<td></td>
						<?php endforeach; ?>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			<?php foreach ($this->items["items"] as $i => $item) : ?>
				<tr class="dndlist-sortable" sortable-group-id="<?php echo (int) $this->parentId + 1;?>">
					<td><?php echo $this->pagination->getRowOffset($index++); ?></td>
					<td><?php echo JHtml::_('grid.id', $i, $item->id, false, "ritem[itemIds]"); ?></td>
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
						</fieldset>
					</td>
					<?php if (($search == '') && ($this->canEditState)) : ?>
					<td class="order nowrap center">
						<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
							<i class="icon-move"></i>
						</span>
						<input type="text" style="display:none" name="order[]" value="<?php echo $item->ordering;?>" class="text-area-order" />
					</td>
					<?php endif; ?>
					<td>
						<i class="icon-file"></i>
						<?php if (($item->checked_out) || (!$this->canEdit)) : ?>
							<?php echo $this->escape($item->title); ?>
						<?php else : ?>
							<?php echo JHtml::_('link', 'index.php?option=com_reditem&task=item.edit&id=' . $item->id . '&fromExplore=1&parent_id=' . $this->parentId, $this->escape($item->title)); ?>
						<?php endif; ?>
					</td>
					<td>
						<?php
							$user = RFactory::getUser($item->created_user_id);
							echo $user->name;
						?>
					</td>
					<td>
						<?php echo $item->template_name; ?>
					</td>
					<!-- Add displayable fields data -->
					<?php if (($this->displayableFields) && (count($this->displayableFields) > 0)) : ?>
						<?php foreach ($this->displayableFields as $displayField) : ?>
							<td>
							<?php if ($displayField->type == 'user') : ?>
								<?php
									$ids = json_decode($item->customfield_values[$displayField->fieldcode]);
									$html = array();

									if ($ids == null) continue;

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
							<?php endif; ?>
							</td>
						<?php endforeach; ?>
				<?php endif; ?>
				<!-- End add displayable fields data -->

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
			<?php
				$page = new RPagination($this->total, $this->limitStart, $this->limit);

				echo $page->getListFooter();
			?>
	<?php endif;?>
	<input type="hidden" name="fromExplore" value="1" />
	<input type="hidden" name="parent_id" value="<?php echo $this->parentId; ?>" />
	<input type="hidden" name="type_id" value="<?php echo $this->typeId; ?>" />
	<input type="hidden" name="view" value="explore" />
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>