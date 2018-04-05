<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
?>
<div class="well">
	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#tag_required" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_REQUIRED_VIEW_ITEM_EDIT'); ?></a>
		</li>

		<li>
			<a href="#tag_edit" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_DEFAULT_VIEW_ITEM_EDIT'); ?></a>
		</li>

		<?php if (!empty($this->fieldTags)): ?>
		<li>
			<a href="#tag_field" data-toggle="tab"><?php echo JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD', $this->fieldTags['name']); ?></a>
		</li>
		<?php endif; ?>

		<?php if (!empty($this->extraTags)): ?>
		<li>
			<a href="#tag_extra" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_EXTRA'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tag_required">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->requiredTags)); ?>
		</div>
		<div class="tab-pane" id="tag_edit">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->itemTags)); ?>
		</div>
		<?php if (!empty($this->fieldTags)): ?>
		<div class="tab-pane" id="tag_field">
			<p class="text-warning"><small><i><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_HINT_USE_CUSTOMFIELD_TAGS') ?></i></small></p>
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->fieldTags['tags'])); ?>
		</div>
		<?php endif; ?>

		<?php if (!empty($this->extraTags)): ?>
		<div class="tab-pane" id="tag_extra">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->extraTags)); ?>
		</div>
		<?php endif; ?>
	</div>
</div>
