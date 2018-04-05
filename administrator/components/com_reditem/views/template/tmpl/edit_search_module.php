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
			<a href="#tag_filter" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_VIEW_SEARCH_FILTER'); ?></a>
		</li>
		<?php if (!empty($this->extraTags)): ?>
		<li>
			<a href="#tag_extra" data-toggle="tab"><?php echo JText::_('COM_REDITEM_TEMPLATE_TAG_EXTRA'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tag_filter">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->filterTags)); ?>
		</div>
		<?php if (!empty($this->extraTags)): ?>
		<div class="tab-pane" id="tag_extra">
			<?php echo RLayoutHelper::render('template_tags', array('tags' => $this->extraTags)); ?>
		</div>
		<?php endif; ?>
	</div>
</div>
