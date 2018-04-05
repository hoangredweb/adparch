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
 * Types List View
 *
 * @package     RedITEM.Backend
 * @subpackage  View.Types
 * @since       0.9.1
 */
class ReditemViewTypes extends ReditemViewAdmin
{
	/**
	 * Display the template list
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

		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('Form');
		$this->activeFilters = $this->get('ActiveFilters');

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
		return JText::_('COM_REDITEM_TYPE_TYPES');
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

		$firstGroup		= new RToolbarButtonGroup;
		$secondGroup	= new RToolbarButtonGroup;
		$thirdGroup		= new RToolbarButtonGroup;

		if ($user->authorise('core.create', 'com_reditem'))
		{
			$new = RToolbarBuilder::createNewButton('type.add');
			$firstGroup->addButton($new);
		}

		if ($user->authorise('core.delete', 'com_reditem'))
		{
			$delete = RToolbarBuilder::createDeleteButton('types.delete');
			$secondGroup->addButton($delete);
		}

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$edit = RToolbarBuilder::createEditButton('type.edit');
			$secondGroup->addButton($edit);
		}

		if ($user->authorise('core.create', 'com_reditem') && $user->authorise('core.edit', 'com_reditem'))
		{
			$copy = RToolbarBuilder::createCopyButton('types.copy');
			$secondGroup->addButton($copy);
		}

		if ($user->authorise('core.create', 'com_reditem'))
		{
			$copyOverrideBtn = RToolbarBuilder::createModalButton(
				'#copyOverrideTemplate',
				JText::_('COM_REDITEM_TYPE_COPY_OVERRIDE_TEMPLATE'),
				'btn btn-info',
				'icon-arrow-right'
			);

			$thirdGroup->addButton($copyOverrideBtn);
		}

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$rebuildPermission = RToolbarBuilder::createStandardButton(
				'types.rebuildPermission',
				JText::_('COM_REDITEM_TYPES_REBUILD_PERMISSION'),
				'btn-primary',
				'icon-retweet',
				false
			);

			$thirdGroup->addButton($rebuildPermission);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup)->addGroup($thirdGroup);

		return $toolbar;
	}
}
