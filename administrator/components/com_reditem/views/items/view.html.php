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
 * Items List View
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class ReditemViewItems extends ReditemViewAdmin
{
	/**
	 * @var  boolean
	 */
	protected $items;

	/**
	 * @var  boolean
	 */
	protected $displaySidebar = true;

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

		$templates = ReditemHelperSystem::getTemplatesBySection();
		$this->items         = $this->get('Items');
		$this->state         = $this->get('State');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('Form');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->stats         = ReditemHelperSystem::getStats();
		$this->templates     = count($templates);
		$this->toType        = JRoute::_('index.php?option=com_reditem&view=types');
		$this->toTemplate    = JRoute::_('index.php?option=com_reditem&view=templates');

		// Load categories for each of item
		$itemIds = ReditemHelperItem::getItemIds($this->items);
		$categories = ReditemHelperItem::getCategories($itemIds, false);

		foreach ($this->items as $item)
		{
			if (isset($categories[$item->id]))
			{
				$item->categories = $categories[$item->id];
			}
		}

		// Load fields for batch template
		$this->filterForm->loadFile('items_batch', false);

		/*
		 * Get displayable fields
		 */
		$displayableFields = array();
		$filterType = $this->getModel()->getState('filter.filter_types', 0);

		// Make sure user has choose filter by type
		if (is_numeric($filterType) && ($filterType > 0))
		{
			$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
			$fieldsModel->setState('filter.searchableInBackend', 1);
			$fieldsModel->setState('filter.types', $filterType);

			$displayableFields = $fieldsModel->getItems();

			// Get filters by custom fields
			$fieldFilters = ReditemHelperCustomfield::getFieldFilters();

			if ($fieldFilters)
			{
				foreach ($fieldFilters as $field)
				{
					switch ($field->type)
					{
						case 'user':
							$element = new SimpleXMLElement('<field />');
							$element->addAttribute('type', 'RedItemUser');
							$element->addAttribute('name', $field->fieldcode);
							$element->addAttribute('onchange', 'this.form.submit()');
							$element->addChild('option', 'COM_REDITEM_ITEMS_FILTER_FIELD_USER_SELECT');
							$this->filterForm->setField($element, 'filter');
							$value = $this->state->get('filter.' . $field->fieldcode, 0);
							$this->filterForm->setValue($field->fieldcode, 'filter', $value);

							break;

						default:
							break;
					}
				}
			}

			$cfValues = ReditemHelperItem::getCustomFieldValues($itemIds);

			if (!empty($cfValues))
			{
				foreach ($this->items as $item)
				{
					if (isset($cfValues[$item->type_id][$item->id]))
					{
						$item->customfield_values = $cfValues[$item->type_id][$item->id];
					}
				}
			}
		}

		$this->displayableFields = $displayableFields;

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
		return JText::_('COM_REDITEM_ITEM_ITEMS');
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
			$new = RToolbarBuilder::createModalButton(
				'item-wizard',
				JText::_('JTOOLBAR_NEW'),
				'btn-success',
				'icon-file-text'
			);

			$firstGroup->addButton($new);
		}

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$edit    = RToolbarBuilder::createEditButton('item.edit');
			$checkin = RToolbarBuilder::createCheckinButton('items.checkin');

			$secondGroup->addButton($edit)
				->addButton($checkin);
		}

		if ($user->authorise('core.create', 'com_reditem') && $user->authorise('core.edit', 'com_reditem'))
		{
/*			$copy = RToolbarBuilder::createCopyButton('items.copy');
			$secondGroup->addButton($copy);*/

			$export = RToolbarBuilder::createModalButton(
				'#exportCsvModal',
				JText::_('COM_REDITEM_TOOLBAR_EXPORT_CSV'),
				'',
				'icon-download-alt'
			);
			$secondGroup->addButton($export);

			/*$import = RToolbarBuilder::createModalButton(
				'#importCsvModal',
				JText::_('COM_REDITEM_TOOLBAR_IMPORT_CSV'),
				'',
				'icon-upload-alt'
			);
			$secondGroup->addButton($import);*/
		}

		if ($this->state->get('filter.published') == -2 && $user->authorise('core.delete', 'com_reditem'))
		{
			$delete = RToolbarBuilder::createDeleteButton('items.delete', 'btn-danger');
			$thirdGroup->addButton($delete);
		}
		elseif ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$trash = RToolbarBuilder::createTrashButton('items.trash', 'btn-danger');
			$thirdGroup->addButton($trash);
		}

		/*if ($user->authorise('core.edit', 'com_reditem'))
		{
			$clearThumbnail = RToolbarBuilder::createStandardButton('items.cleanThumbnail', JText::_('COM_REDITEM_ITEMS_CLEAN_THUMBNAIL'), '', 'icon-retweet');
			$thirdGroup->addButton($clearThumbnail);
		}*/

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$publish = RToolbarBuilder::createPublishButton('items.publish');
			$thirdGroup->addButton($publish);

			$unPublish = RToolbarBuilder::createUnpublishButton('items.unpublish');
			$thirdGroup->addButton($unPublish);

			/*$archive = RToolbarBuilder::createArchiveButton('items.archive');
			$thirdGroup->addButton($archive);*/
		}

		/*if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$rebuildPermission = RToolbarBuilder::createStandardButton(
				'items.rebuildPermission',
				JText::_('COM_REDITEM_ITEMS_REBUILD_PERMISSION'),
				'btn-primary',
				'icon-retweet',
				false
			);
			$fourGroup->addButton($rebuildPermission);
		}*/

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup)->addGroup($thirdGroup)->addGroup($fourGroup);

		return $toolbar;
	}
}
