<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Item
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();
JHtml::_('rbootstrap.tooltip', '.hasTooltip', array('placement' => 'right'));

$customfields        = array();
$customfieldGroups   = array();
$customfieldUnGroups = array();
$active              = false;
$activeId            = "";

// Group custom fields by group name.
if (!empty($this->customfields)) :
	foreach ($this->customfields as $customfield) :
		$fieldParams     = new JRegistry($customfield->params);
		$fieldGroup      = $fieldParams->get('group');
		$fieldGroupAlias = JFilterOutput::stringURLSafe($fieldGroup);

		if (!$fieldGroup) :
			$customfieldUnGroups[] = $customfield;
		else :
			if (!isset($customfields[$fieldGroupAlias])) :
				$customfields[$fieldGroupAlias] = array();
				$customfieldGroups[$fieldGroupAlias] = $fieldGroup;
			endif;

			$customfields[$fieldGroupAlias][] = $customfield;
		endif;
	endforeach;

	ksort($customfieldGroups);
endif;

?>
<?php if (isset($this->item->id) && $this->item->id > 0) :?>
	<div class="js-stools clearfix">
		<div class="clearfix">
			<div class="js-stools-container-list">
				<div class="well well-small form-horizontal pull-right">
					<div class="row-fluid">
						<div class="span5" style="padding-top: 3px"><?php echo $this->form->getLabel('fields_template_id'); ?></div>
						<div class="span7"><?php echo $this->form->getInput('fields_template_id'); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<div class="row-fluid reditem-admin">
	<?php if ($customfieldGroups) : ?>
		<div class="span2">
			<ul class="nav nav-pills nav-stacked">
				<?php if (!empty($customfieldUnGroups)) : ?>
					<?php $active = true; ?>
					<li class="active">
						<a href="#ungroup" data-toggle="tab"><i><?php echo JText::_('COM_REDITEM_FIELDS_UNGROUP'); ?></i></a>
					</li>
				<?php endif; ?>
				<?php foreach ($customfieldGroups as $id => $name) : ?>
					<?php if (empty($customfieldUnGroups) && !$active) : ?>
						<?php $active = true; ?>
						<?php $activeId = $id; ?>
						<li class="active">
					<?php else : ?>
						<li>
					<?php endif; ?>
					<a href="#<?php echo $id; ?>" data-toggle="tab"><?php echo $name; ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<?php if ($customfieldGroups) : ?>
	<div class="span10">
		<?php endif; ?>
		<fieldset class="form-horizontal">
			<div class="tab-content">
				<?php if (!empty($customfieldUnGroups)) : ?>
					<div class="tab-pane active" id="ungroup">
						<?php foreach ($customfieldUnGroups as $customfield) : ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $customfield->getLabel(); ?>
								</div>
								<div class="controls">
									<?php if (in_array($customfield->type, array('image', 'gallery', 'file'))) :?>
										<?php echo $customfield->render(array(), 'categoryfield'); ?>
									<?php else :?>
										<?php echo $customfield->render(); ?>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php foreach ($customfieldGroups as $id => $name) : ?>
					<?php $class = ''; ?>
					<?php if (empty($customfieldUnGroups) && ($id == $activeId)) : ?>
						<?php $class = 'active'; ?>
					<?php endif; ?>
					<div class="tab-pane <?php echo $class; ?>" id="<?php echo $id; ?>">
						<?php if (isset($customfields[$id]) && !empty($customfields[$id])) : ?>
							<?php foreach ($customfields[$id] as $customfield) : ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo $customfield->getLabel(); ?>
									</div>
									<div class="controls">
										<?php if (in_array($customfield->type, array('image', 'gallery', 'file'))) :?>
											<?php echo $customfield->render(array(), 'categoryfield'); ?>
										<?php else :?>
											<?php echo $customfield->render(); ?>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</fieldset>
		<?php if ($customfieldGroups) : ?>
	</div>
<?php endif; ?>
</div>
