<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
JLoader::import('helper', JPATH_ROOT . '/administrator/components/com_reditem/helpers');

/**
 * Category edit view
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class ReditemViewItem extends ReditemViewAdmin
{
	/**
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display the category edit page
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @todo Check the extra fields once implemented
	 *
	 * @since   0.9.1
	 */
	public function display($tpl = null)
	{
		$app  = JFactory::getApplication();
		$user = ReditemHelperSystem::getUser();
		JPluginHelper::importPlugin('reditem');

		$configuration = JComponentHelper::getParams('com_reditem');

		$this->versioningEnable = (boolean) $configuration->get('save_history', 1);
		$itemTitleLimitChars    = 50;
		$this->item             = $this->get('Item');
		$this->useGmapField     = false;
		$this->canConfig        = false;

		if ($user->authorise('core.admin', 'com_reditem'))
		{
			$this->canConfig = true;
		}

		// Get customfield values if item is in edit mode
		if (isset($this->item->id))
		{
			$this->typeId = (int) $this->item->type_id;
			$app->setUserState('com_reditem.global.tid', $this->typeId);

			// Prepare versioning modal link
			if ($this->versioningEnable)
			{
				$typeAlias        = $this->getModel()->get('typeAlias');
				$contentTypeTable = JTable::getInstance('Contenttype');
				$typeId           = $contentTypeTable->getTypeId($typeAlias);

				$this->versionModalLink  = 'index.php?option=com_contenthistory&view=history&layout=modal&tmpl=component&item_id=' . $this->item->id;
				$this->versionModalLink .= '&type_id=' . $typeId . '&amp;type_alias=' . $typeAlias . '&' . JSession::getFormToken() . '=1';
			}
		}
		else
		{
			$this->typeId = (int) $app->getUserState('com_reditem.global.tid', 0);
		}

		$this->categoryId      = (int) $app->getUserState('com_reditem.global.parentId', 0);
		$this->fromExplore     = (int) $app->getUserState('com_reditem.global.fromExplore', 0);
		$this->form            = $this->get('Form');
		$this->templateId      = (int) $this->form->getValue('template_id');

		if (!$this->templateId)
		{
			$this->templateId = $app->getUserState('com_reditem.global.templateEdit');
		}

		$this->canonicalEnable = true;
		$fieldsTemplateId      = $app->getUserState('com_reditem.global.fieldsTemplateEdit', null);

		if (!JPluginHelper::isEnabled('reditem', 'canonical'))
		{
			$this->canonicalEnable = false;
			$this->form->setFieldAttribute('canonical_url', 'disabled', 'true', 'params');
		}

		if ($this->typeId > 0)
		{
			$typeModel          = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
			$this->item->type   = $typeModel->getItem($this->typeId);
			$typeParams         = new JRegistry($this->item->type->params);
			$this->useGmapField = (boolean) $typeParams->get('item_gmap_field', false);

			// Process for set Limit of item title
			$itemTitleLimitChars = (int) $typeParams->get('itemTitleLimit', 50);
		}

		$this->form         = $this->get('Form');
		$this->customfields = $this->get('CustomFields');
		$this->form->setFieldAttribute('title', 'maxlength', $itemTitleLimitChars);

		// Exclude data to prevent choose self item
		$this->form->setFieldAttribute('related_items_select', 'exclude', $this->item->id);
		$this->form->setValue('related_items_select', '', json_decode($this->form->getValue('related_items', 'params')));

		if (isset($this->item->id) && $this->item->id > 0 && is_numeric($fieldsTemplateId))
		{
			if ($fieldsTemplateId > 0)
			{
				$templateModel      = RModel::getAdminInstance('Template', array('ignore_request' => true));
				$this->template     = $templateModel->getItem($fieldsTemplateId);
				$this->customfields = ReditemHelperCustomfield::getAvailableFields($this->template->content, $this->customfields);
				$this->form->setValue('fields_template_id', null, $fieldsTemplateId);
			}
		}
		elseif ($this->templateId != 0)
		{
			$templateModel      = RModel::getAdminInstance('Template', array('ignore_request' => true));
			$this->template     = $templateModel->getItem($this->templateId);
			$this->customfields = ReditemHelperCustomfield::getAvailableFields($this->template->content, $this->customfields);
			$this->form->setValue('template_id', null, $this->templateId);
			$this->form->setValue('fields_template_id', null, $this->templateId);
		}

		$displayedFields = array();

		foreach ($this->customfields as $field)
		{
			$displayedFields[] = $field->fieldcode;
		}

		$this->form->setValue('fields_to_edit', null, json_encode($displayedFields));

		if (!empty($this->categoryId))
		{
			$this->form->setValue('categories', null, array($this->categoryId));
		}

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		$subTitle = ' <small>' . JText::_('COM_REDITEM_NEW') . '</small>';

		if ($this->item->id)
		{
			$subTitle = ' <small>' . JText::_('COM_REDITEM_EDIT') . '</small>';
		}

		return JText::_('COM_REDITEM_ITEM_ITEM') . $subTitle;
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @todo	We have setup ACL requirements for redITEM
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;

		$save = RToolbarBuilder::createSaveButton('item.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('item.save');
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('item.save2new');
		$save2Copy = RToolbarBuilder::createSaveAsCopyButton('item.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		// Check if version feature enable
		$user = ReditemHelperSystem::getUser();

		if ($this->versioningEnable && $user->authorise('core.edit', 'com_reditem'))
		{
			$version = RToolbarBuilder::createModalButton(
				'#versionModal',
				JText::_('JTOOLBAR_VERSIONS'),
				'btn btn-default',
				'icon-archive'
			);

			$group->addButton($version);
		}

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('item.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('item.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
