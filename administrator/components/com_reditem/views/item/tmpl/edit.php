<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Item
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

JHtml::_('behavior.formvalidation');
JHtml::_('rjquery.chosen', '.chosen');
JHtml::_('behavior.modal', 'a.modal-thumb');
JHtml::_('behavior.keepalive');
JHtml::_('rjquery.ui');

$editor    = JFactory::getEditor();
$user      = ReditemHelperSystem::getUser();
$previewId = 0;

if (isset($this->item->id))
{
	$previewId = (int) $this->item->id;
}

$previewId = md5($previewId . $user->id);

// Make sure we have declared jinput before use it
$jinput      = JFactory::getApplication()->input;
$previewLink = JRoute::_(JUri::root() . 'index.php?option=com_reditem&view=itemlook&id=' . $previewId);

$script [] = 'jQuery(document).ready (function () {';
$script [] = 'jQuery(\'#categoryTab\').on(\'shown\', function (e) {';
$script [] = 'localStorage.setItem(\'last_tab_' . $jinput->getInt('id') . '\', jQuery(e.target).attr(\'href\'));';
$script [] = '});';
$script [] = 'var lastTab = localStorage.getItem(\'last_tab_' . $jinput->getInt('id') . '\');';
$script [] = 'if (lastTab) {jQuery(\'a[href=\' + lastTab + \']\').tab(\'show\');}';
$script [] = 'jQuery(".btn-group").each(function(index){';
$script [] = '  if (jQuery(this).hasClass(\'disabled\')) { jQuery(this).find("label").off(\'click\'); }';
$script [] = '});';

if ($this->item->id)
{
	$script [] = 'jQuery(\'#jform_type_id\').prop(\'disabled\', true).trigger("liszt:updated").prop(\'disabled\', false);';
}

if (!$this->typeId)
{
	$script [] = 'jQuery(\'#jform_categories\').prop(\'disabled\', true).trigger("liszt:updated").prop(\'disabled\', false);';
	$script [] = 'jQuery(\'#jform_template_id\').prop(\'disabled\', true).trigger("liszt:updated").prop(\'disabled\', false);';
}

$script [] = 'function updateRelatedItems() {';
$script [] = 'var order = [];';
$script [] = 'var elements = [];';
$script [] = 'var rSelect = jQuery(\'#jform_related_items_select\');';
$script [] = 'jQuery(\'.related-items\').find(\'.ajax-option\').each(function(){order.push(jQuery(this).attr(\'id\'));});';
$script [] = 'order.each(function(val,i){elements.push(jQuery(\'#jform_related_items_select\').find(\'option[value="\' + val + \'"]\'));});';
$script [] = 'rSelect.html(\'\');';
$script [] = 'elements.each(function(val,i){rSelect.append(val);});';
$script [] = 'rSelect.trigger(\'change\');';
$script [] = '}';
$script [] = 'setTimeout(function(){';
$script [] = 'jQuery(\'.related-items\').find(\'.select2-selection__rendered\').sortable({containment: \'parent\', update: updateRelatedItems });';
$script [] = 'jQuery(\'#jform_related_items_select\').on(\'select2:select\', function(e) {';
$script [] = 'var ele = jQuery(this);';
$script [] = 'var element = ele.find(\'[value="\' + e.params.data.id + \'"]\');';
$script [] = 'ele.append(element);';
$script [] = 'ele.trigger(\'change\');';
$script [] = '});';
$script [] = 'jQuery(\'#jform_related_items_select\').on(\'select2:unselect\', function(e) {';
$script [] = 'var ele = jQuery(this);';
$script [] = 'ele.find(\'option[value="\' + e.params.data.id + \'"]\').remove();';
$script [] = 'ele.trigger(\'change\');';
$script [] = '});';
$script [] = 'jQuery(\'#jform_related_items_select\').on(\'change\', function(e) {';
$script [] = 'var val = [];';
$script [] = 'jQuery(this).find(\'option\').each(function() {';
$script [] = 'var ele   = jQuery(this);';
$script [] = 'var value = ele.attr(\'value\');';
$script [] = 'if (value.length > 0) { val.push(value); }';
$script [] = '});';
$script [] = 'jQuery(\'#jform_params_related_items\').val(JSON.stringify(val));';
$script [] = '});';
$script [] = '}, 200);';
$script [] = '});';

$script [] = 'Joomla.submitbutton = function (pressbutton) { submitbutton(pressbutton); };';
$script [] = 'submitbutton = function (pressbutton) {';
$script [] = 'var form = document.adminForm;';
$script [] = 'if (pressbutton) { form.task.value = pressbutton; }';
$script [] = 'if ((pressbutton != \'item.close\') && (pressbutton != \'item.cancel\')) {';
$script [] = 'if (document.formvalidator.isValid(form)) { form.submit(); }';
$script [] = '} else { form.submit(); }';
$script [] = '};';
$script [] = 'function previewItem() {';
$script [] = 'var form = jQuery("#adminForm");';
$script [] = 'if (!document.formvalidator.isValid(form)) { return false; }';
$script [] = 'var editorContent = "";';
$script [] = 'var oldAction = form.attr("action");';
$script [] = 'form.attr("action", "index.php?option=com_reditem&task=item.preview");';
$script [] = 'form.find(\'input#task\').val("item.preview");';

if (!empty($this->customfields))
{
	foreach ($this->customfields as $customfield)
	{
		if ($customfield->type == "editor")
		{
			$script [] = 'editorContent = ' . $editor->getContent('jform[fields][editor][' . $customfield->fieldcode . ']') . ';';
			$script [] = 'form.find("textarea[name=\'jform[fields][editor][' . $customfield->fieldcode . ']\']").val(editorContent)';
		}
	}
}

$script [] = 'jQuery.ajax({ type: "POST", url: "index.php?option=com_reditem&task=item.preview", data: form.serialize() })';
$script [] = '.success(function(data) {';
$script [] = 'form.attr("action", oldAction);';
$script [] = 'form.find(\'input#task\').val("");';
$script [] = 'var modalWidth = jQuery(window).width() * 0.85;';
$script [] = 'var modalHeight = jQuery(window).height() * 0.85;';
$script [] = 'SqueezeBox.open("' . $previewLink . '", {handler: "iframe", size: {x: modalWidth, y: modalHeight}})';
$script [] = '});';
$script [] = 'return false;';
$script [] = '}';

JFactory::getDocument()->addScriptDeclaration(implode(PHP_EOL, $script));
?>
<form enctype="multipart/form-data"
	action="index.php?option=com_reditem&task=item.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate"
	id="adminForm">
	<ul class="nav nav-tabs" id="categoryTab">
		<li class="active">
			<a href="#item-information" data-toggle="tab"><strong><?php echo JText::_('COM_REDITEM_GENERAL_INFORMATION'); ?></strong></a>
		</li>
		<?php if ($this->useGmapField) : ?>
		<li id="item-gmap-wrapper">
			<a href="#item-gmap" data-toggle="tab"><strong><?php echo JText::_('COM_REDITEM_ITEM_LOCATION_INFORMATION'); ?></strong></a>
		</li>
		<?php endif; ?>
		<li>
			<a href="#item-customfields" data-toggle="tab" id="additional-link"><strong><?php echo JText::_('COM_REDITEM_ADDITIONAL_INFORMATION'); ?></strong></a>
		</li>
		<li>
			<a href="#item-seo" data-toggle="tab"><strong><?php echo JText::_('COM_REDITEM_SEO_INFORMATION'); ?></strong></a>
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
		<div class="tab-pane active" id="item-information">
			<div class="row-fluid">
				<div class="span8">
					<fieldset class="form-horizontal">
						<?php if (empty($this->typeId)): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('type_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('type_id'); ?>
							</div>
						</div>
						<?php endif;?>
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
								<?php echo $this->form->getLabel('alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('alias'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('categories'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('categories'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('access'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('access'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('template_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('template_id'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('blocked'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('blocked'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('featured'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('featured'); ?>
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
						<?php if ($this->versioningEnable): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('version_note'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('version_note'); ?>
							</div>
						</div>
						<?php endif; ?>
						<?php if ($this->item->id) : ?>
						<div class="control-group">
							<div class="control-label">
								<label><?php echo JText::_('COM_REDITEM_ITEM_DIRECT_LINK'); ?></label>
							</div>
							<div class="controls">
								<?php $viewLink = JRoute::_(JUri::root() . 'index.php?option=com_reditem&view=itemdetail&id=' . $this->item->id); ?>
								<a href="<?php echo $viewLink; ?>" target="_blank"><?php echo $viewLink; ?></a>
							</div>
						</div>
						<?php endif; ?>
						<div class="control-group">
							<div class="controls">
								<a id="previewLink" onClick="javascript:previewItem();" class="btn"
									href="javascript:void(0);">Preview</a>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="span4">
					<fieldset class="form-vertical">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('created_user_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('created_user_id'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('publish_up'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('publish_up'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('publish_down'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('publish_down'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('created_time'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('created_time'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('modified_user_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('modified_user_id'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('modified_time'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('modified_time'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group related-items">
							<div class="control-label">
								<?php echo $this->form->getLabel('related_items_select'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('related_items_select'); ?>
								<?php echo $this->form->getField('related_items', 'params')->input; ?>
							</div>
							<div class="clearfix"></div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
		<?php if ($this->useGmapField) : ?>
		<div class="tab-pane" id="item-gmap">
			<?php echo $this->loadTemplate('gmap'); ?>
		</div>
		<?php endif; ?>
		<div class="tab-pane" id="item-customfields">
			<?php echo $this->loadTemplate('customfields'); ?>
		</div>
		<?php $tooltip = RHelperAsset::load('tooltip.png', 'com_reditem'); ?>
		<div class="tab-pane" id="item-seo">
			<fieldset class="form-horizontal">
				<div class="control-group">
					<?php $field = $this->form->getField('append_to_global_seo', 'params'); ?>
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
						<div class="hasTooltip"
							data-original-title="<?php echo JText::_($field->getAttribute('description')); ?>"
							style="display:inline">
							<?php echo $tooltip; ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="control-group">
					<?php $field = $this->form->getField('page_title', 'params'); ?>
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
						<div class="hasTooltip"
							data-original-title="<?php echo JText::_($field->getAttribute('description')); ?>"
							style="display:inline">
							<?php echo $tooltip; ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="control-group">
					<?php $field = $this->form->getField('page_heading', 'params'); ?>
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
						<div class="hasTooltip"
							data-original-title="<?php echo JText::_($field->getAttribute('description')); ?>"
							style="display:inline">
							<?php echo $tooltip; ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="control-group">
					<?php $field = $this->form->getField('meta_description', 'params'); ?>
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
						<div class="hasTooltip"
							data-original-title="<?php echo JText::_($field->getAttribute('description')); ?>"
							style="display:inline">
							<?php echo $tooltip; ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="control-group">
					<?php $field = $this->form->getField('canonical_url', 'params'); ?>
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
						<div class="hasTooltip"
							data-original-title="<?php echo JText::_($field->getAttribute('description')); ?>"
							style="display:inline">
							<?php echo $tooltip; ?>
						</div>
					</div>
					<?php if (!$this->canonicalEnable): ?>
					<div class="controls">
						<span class="label label-important red"><?php echo JText::_('COM_REDITEM_ITEM_CANONICAL_URL_WARNING')?></span>
					</div>
					<?php endif; ?>
					<div class="clearfix"></div>
				</div>
				<div class="control-group">
					<?php $field = $this->form->getField('meta_keywords', 'params'); ?>
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
						<div class="hasTooltip"
							data-original-title="<?php echo JText::_($field->getAttribute('description')); ?>"
							style="display:inline">
							<?php echo $tooltip; ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="control-group">
					<?php $field = $this->form->getField('meta_language', 'params'); ?>
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
						<div class="hasTooltip"
							data-original-title="<?php echo JText::_($field->getAttribute('description')); ?>"
							style="display:inline">
							<?php echo $tooltip; ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="control-group">
					<?php $field = $this->form->getField('meta_robots', 'params'); ?>
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
						<div class="hasTooltip"
							data-original-title="<?php echo JText::_($field->getAttribute('description')); ?>"
							style="display:inline">
							<?php echo $tooltip; ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</fieldset>
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
	<input type="hidden" name="previewId" value="<?php echo $previewId; ?>" />
	<?php if (!empty($this->typeId)): ?>
		<input type="hidden" name="jform[type_id]" value="<?php echo $this->typeId;?>" />
	<?php endif;?>

	<input id="task" type="hidden" name="task" value="" />
	<?php if ($this->fromExplore) :?>
	<input id="return" name="return" type="hidden" value="<?php echo base64_encode(RRoute::_('index.php?option=com_reditem&view=explore&parent_id=' . $this->categoryId, false)); ?>" />
	<?php endif;?>
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $this->form->getInput('fields_to_edit'); ?>
</form>

<?php if ($this->versioningEnable && $this->item->id): ?>
<div id="versionModal" class="modal fade hide" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-body">
		<iframe src="<?php echo $this->versionModalLink ?>" frameborder="0" width="100%"></iframe>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JTOOLBAR_CLOSE') ?></button>
	</div>
</div>
<?php endif; ?>
