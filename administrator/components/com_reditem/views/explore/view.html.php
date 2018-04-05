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
 * Explore List View
 *
 * @package     RedITEM.Backend
 * @subpackage  View.Explore
 * @since       2.1.19
 */
class ReditemViewExplore extends ReditemViewAdmin
{
	/**
	 * ID of type
	 *
	 * @var  integer
	 */
	protected $typeId = 0;

	/**
	 * Id of parent category
	 *
	 * @var  integer
	 */
	protected $parentId = 0;
	/**
	 * Display the template list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   2.1.19
	 */
	public function display($tpl = null)
	{
		$user  = ReditemHelperSystem::getUser();
		$app   = JFactory::getApplication();
		$input = $app->input;

		$list = $input->get('list', array(), 'array');

		// We do not use session here to prevent locked in past breadcrumb
		$this->parentId = $input->getInt('parent_id', 0);
		$this->typeId   = $app->getUserState('com_reditem.explore.filter_typeId', 0);

		if (!isset($list['items_limit']))
		{
			$limit = $input->getInt('limit');
		}
		else
		{
			$limit = $list['items_limit'];
		}

		$limitStart = $input->getInt('limitstart', 0);

		// Set it to filter
		$app->setUserState('com_reditem.explore.parent_id', $this->parentId);
		$app->setUserState('com_reditem.edit.category.data.type_id', $this->typeId);

		$this->items      = $this->get('Items');
		$this->state      = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('Form');
		$this->limit      = $limit;
		$this->limitStart = $limitStart;
		$this->total      = $this->items['total'];

		/*
		 * Get displayable fields
		 */
		$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
		$fieldsModel->setState('filter.searchableInBackend', 1);
		$fieldsModel->setState('filter.types', $this->typeId);
		$displayableFields = $fieldsModel->getItems();

		foreach ($this->items['items'] as $item)
		{
			$item->customfield_values = ReditemHelperItem::getCustomFieldValues($item->id);
		}

		$this->displayableFields = $displayableFields;

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
	 * @since   2.1.19
	 */
	public function getTitle()
	{
		return JText::_('COM_REDITEM_EXPLORE_EXPLORE');
	}

	/**
	 * Get the tool-bar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$user        = ReditemHelperSystem::getUser();
		$firstGroup  = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup  = new RToolbarButtonGroup;
		$forthGroup  = new RToolbarButtonGroup;

		if ($user->authorise('core.create', 'com_reditem'))
		{
			$newItem = RToolbarBuilder::createModalButton(
				'item-wizard',
				JText::_('COM_REDITEM_EXPLORE_TOOLBAR_NEW_ITEMS'),
				'btn-success',
				'icon-file-text');

			$firstGroup->addButton($newItem);

			$newCategories = RToolbarBuilder::createStandardButton(
				'category.add',
				JText::_('COM_REDITEM_EXPLORE_TOOLBAR_NEW_CATEGORIES'),
				'btn-success',
				'icon-folder-open',
				'',
				false
			);

			$firstGroup->addButton($newCategories);
		}

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$edit    = RToolbarBuilder::createEditButton('explore.edit');
			$checkin = RToolbarBuilder::createCheckinButton('explore.checkIn');

			$secondGroup->addButton($edit)
				->addButton($checkin);
		}

		if ($user->authorise('core.create', 'com_reditem') && $user->authorise('core.edit', 'com_reditem'))
		{
			$copy    = RToolbarBuilder::createStandardButton('explore.copy', JText::_('COM_REDITEM_EXPLORE_TOOLBAR_COPY'), '', 'icon-copy');
			$move    = RToolbarBuilder::createStandardButton('explore.move', JText::_('COM_REDITEM_EXPLORE_TOOLBAR_MOVE'), '', 'icon-remove-circle');
			$past    = RToolbarBuilder::createStandardButton('explore.paste', JText::_('COM_REDITEM_EXPLORE_TOOLBAR_PASTE'), '', 'icon-file', false);
			$convert = RToolbarBuilder::createModalButton(
				'item-convert',
				JText::_('COM_REDITEM_EXPLORE_TOOLBAR_CONVERT_ITEMS'),
				'btn-default',
				'icon-refresh',
				true
			);

			$thirdGroup->addButton($copy)
				->addButton($move)
				->addButton($past)
				->addButton($convert);
		}

		if ($user->authorise('core.delete', 'com_reditem'))
		{
			$delete    = RToolbarBuilder::createDeleteButton('explore.delete');
			$publish   = RToolbarBuilder::createPublishButton('explore.publish');
			$unPublish = RToolbarBuilder::createUnpublishButton('explore.unpublish');

			$forthGroup->addButton($delete)
				->addButton($publish)
				->addButton($unPublish);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)
			->addGroup($secondGroup)
			->addGroup($thirdGroup)
			->addGroup($forthGroup);

		return $toolbar;
	}
}
