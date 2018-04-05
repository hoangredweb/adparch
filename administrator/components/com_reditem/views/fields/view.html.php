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
 * Custom Fields List View
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class ReditemViewFields extends ReditemViewAdmin
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

		$this->items         = $this->get('Items');
		$this->state         = $this->get('State');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('Form');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->stats         = ReditemHelperSystem::getStats();
		$this->toType        = JRoute::_('index.php?option=com_reditem&view=types');

		// Fields ordering
		$this->ordering = array();

		if ($this->items)
		{
			foreach ($this->items as &$item)
			{
				$this->ordering[0][] = $item->id;
			}
		}

		// Edit permission
		$this->canEdit = false;

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$this->canEdit = true;
		}

		// Edit state permission
		$this->canEditState = false;

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$this->canEditState = true;
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
		return JText::_('COM_REDITEM_FIELD_FIELDS');
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

		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup = new RToolbarButtonGroup;

		if ($user->authorise('core.create', 'com_reditem'))
		{
			$new = RToolbarBuilder::createNewButton('field.add');
			$firstGroup->addButton($new);
		}

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$edit = RToolbarBuilder::createEditButton('field.edit');
			$secondGroup->addButton($edit);

			$checkin = RToolbarBuilder::createCheckinButton('fields.checkin');
			$secondGroup->addButton($checkin);
		}

		if ($user->authorise('core.create', 'com_reditem') && $user->authorise('core.edit', 'com_reditem'))
		{
			$copy = RToolbarBuilder::createCopyButton('fields.copy');
			$secondGroup->addButton($copy);
		}

		if ($user->authorise('core.delete', 'com_reditem'))
		{
			$delete = RToolbarBuilder::createDeleteButton('fields.delete');
			$thirdGroup->addButton($delete);
		}

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$publish = RToolbarBuilder::createPublishButton('fields.publish');
			$thirdGroup->addButton($publish);

			$unPublish = RToolbarBuilder::createUnpublishButton('fields.unpublish');
			$thirdGroup->addButton($unPublish);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup)->addGroup($thirdGroup);

		return $toolbar;
	}
}
