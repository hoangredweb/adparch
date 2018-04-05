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
			<a href="#tag_category" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_DEFAULT_VIEW_CATEGORYDETAIL'); ?></a>
		</li>
		<?php if (!empty($this->categoryFieldTags)): ?>
			<li>
				<a href="#tag_category_fields" data-toggle="tab">
					<?php echo $this->categoryFieldTags['name']; ?>
				</a>
			</li>
		<?php endif; ?>
		<?php if (!empty($this->categoryGmapTags)) : ?>
		<li>
			<a href="#tag_gmap" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_VIEW_CATEGORYDETAIL_GMAP_TAG'); ?></a>
		</li>
		<?php endif; ?>
		<li>
			<a href="#tag_filter" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_VIEW_CATEGORYDETAIL_FILTER'); ?></a>
		</li>

		<li>
			<a href="#tag_filter_extra" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_VIEW_CATEGORYDETAIL_FILTER_SUB_CATEGORIES'); ?></a>
		</li>

		<?php if (!empty($this->fieldTags)): ?>
			<?php foreach ($this->fieldTags as $index => $fieldTag): ?>
			<li>
				<a href="#tag_field<?php echo $index ?>" data-toggle="tab">
					<?php echo JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD', $fieldTag['name']); ?>
				</a>
			</li>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if (!empty($this->extraTags)): ?>
		<li>
			<a href="#tag_extra" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_EXTRA'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tag_category">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->categoryTags)); ?>
		</div>
		<?php if (!empty($this->categoryFieldTags)): ?>
		<div class="tab-pane" id="tag_category_fields">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->categoryFieldTags['tags'])); ?>
		</div>
		<?php endif; ?>
		<?php if (!empty($this->categoryGmapTags)) : ?>
		<div class="tab-pane" id="tag_gmap">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->categoryGmapTags)); ?>
		</div>
		<?php endif; ?>
		<div class="tab-pane" id="tag_filter">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->filterTags)); ?>
		</div>

		<div class="tab-pane" id="tag_filter_extra">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->filterCategoryExtraTags)); ?>
		</div>

		<?php if (!empty($this->fieldTags)): ?>
			<?php foreach ($this->fieldTags as $index => $fieldTag): ?>
			<div class="tab-pane" id="tag_field<?php echo $index ?>">
				<?php echo RLayoutHelper::render('template_tags', array('tags' => $fieldTag['tags'])); ?>
			</div>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if (!empty($this->extraTags)): ?>
		<div class="tab-pane" id="tag_extra">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->extraTags)); ?>
		</div>
		<?php endif; ?>
	</div>
</div>
