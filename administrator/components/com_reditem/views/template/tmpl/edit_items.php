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
			<a href="#tag_default" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_DEFAULT_VIEW_ITEMDETAIL'); ?></a>
		</li>
		<?php if (!empty($this->fieldTags)): ?>
		<li>
			<a href="#tag_field" data-toggle="tab"><?php echo JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD', $this->fieldTags['name']); ?></a>
		</li>
		<?php endif; ?>
		<?php if (isset($this->itemRelated)) : ?>
		<li>
			<a href="#tag_related" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_DEFAULT_VIEW_ITEMDETAIL_RELATED_ITEMS'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tag_default">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->itemTags)); ?>
		</div>
		<?php if (!empty($this->fieldTags)): ?>
		<div class="tab-pane" id="tag_field">
			<p class="text-warning"><small><i><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_HINT_USE_CUSTOMFIELD_TAGS') ?></i></small></p>
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->fieldTags['tags'])); ?>
		</div>
		<?php endif; ?>
		<?php if (isset($this->itemRelated)) : ?>
		<div class="tab-pane" id="tag_related">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->itemRelated)); ?>
		</div>
		<?php endif; ?>
	</div>
</div>
