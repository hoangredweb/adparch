<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Categories List View
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class ReditemViewCategories extends ReditemViewAdmin
{
	/**
	 * Display the category list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   0.9.1
	 */
	public function display($tpl = null)
	{
		$user = ReditemHelperSystem::getUser();

		$templates                          = ReditemHelperSystem::getTemplatesBySection('view_categorydetail');
		$this->items                        = $this->get('Items');
		$this->state                        = $this->get('State');
		$this->pagination                   = $this->get('Pagination');
		$this->filterForm                   = $this->get('Form');
		$this->activeFilters                = $this->get('ActiveFilters');
		$this->stoolsOptions['searchField'] = 'search';
		$this->stats                        = ReditemHelperSystem::getStats();
		$this->toType                       = JRoute::_('index.php?option=com_reditem&view=types');
		$this->templates                    = count($templates);
		$this->toTemplate                   = JRoute::_('index.php?option=com_reditem&view=templates');

		$this->ordering = array();

		foreach ($this->items as &$item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		// Edit State permission
		$this->canEditState = false;

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$this->canEditState = true;
		}

		// Edit permission
		$this->canEdit = false;

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$this->canEdit = true;
		}

		parent::display($tpl);
	}

	/**
	 * Get the page title
	 *
	 * @return  string  The title to display
	 *
	 * @since   0.9.1
	 */
	public function getTitle()
	{
		return JText::_('COM_REDITEM_CATEGORY_CATEGORIES');
	}

	/**
	 * Get the tool-bar to render.
	 *
	 * @todo	The commented lines are going to be implemented once we have setup ACL requirements for redITEM
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$user = ReditemHelperSystem::getUser();

		$firstGroup  = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup  = new RToolbarButtonGroup;
		$fourGroup   = new RToolbarButtonGroup;

		if ($user->authorise('core.create', 'com_reditem'))
		{
			$new = RToolbarBuilder::createNewButton('category.add');
			$firstGroup->addButton($new);
		}

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$edit = RToolbarBuilder::createEditButton('category.edit');
			$secondGroup->addButton($edit);

			$checkin = RToolbarBuilder::createCheckinButton('categories.checkin');
			$secondGroup->addButton($checkin);
		}

		if ($user->authorise('core.delete', 'com_reditem'))
		{
			$delete = RToolbarBuilder::createDeleteButton('categories.delete');
			$thirdGroup->addButton($delete);
		}

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$clearThumbnail = RToolbarBuilder::createStandardButton(
				'categories.cleanThumbnail',
				JText::_('COM_REDITEM_CATEGORIES_CLEAN_THUMBNAIL'),
				'',
				'icon-retweet');
			$thirdGroup->addButton($clearThumbnail);
		}

		if ($user->authorise('core.create', 'com_reditem') && $user->authorise('core.edit', 'com_reditem'))
		{
			$copy = RToolbarBuilder::createCopyButton('categories.copy');
			$secondGroup->addButton($copy);
		}

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$publish = RToolbarBuilder::createPublishButton('categories.publish');
			$thirdGroup->addButton($publish);

			$unPublish = RToolbarBuilder::createUnpublishButton('categories.unpublish');
			$thirdGroup->addButton($unPublish);

			$rebuildPermission = RToolbarBuilder::createStandardButton(
				'categories.rebuildPermission',
				JText::_('COM_REDITEM_CATEGORIES_REBUILD_PERMISSION'),
				'btn-primary',
				'icon-retweet',
				false
			);
			$fourGroup->addButton($rebuildPermission);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup)->addGroup($thirdGroup)->addGroup($fourGroup);

		return $toolbar;
	}
}
