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
 * Category edit view
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class ReditemViewCategory extends ReditemViewAdmin
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
		$app                = JFactory::getApplication();
		$user               = ReditemHelperSystem::getUser();
		$this->useGmapField = true;
		$this->form         = $this->get('Form');
		$this->params       = $this->form->getGroup('params');
		$this->item         = $this->get('Item');
		$this->canConfig    = false;
		$this->fromExplore  = (int) $app->getUserState('com_reditem.global.fromExplore', 0);
		$this->parentId     = (int) $app->getUserState('com_reditem.global.parentId', 0);
		$this->customfields = $this->get('CustomFields');
		$fieldsTemplateId   = $app->getUserState('com_reditem.global.categoryFieldsTemplateEdit', null);
		$templateId         = $this->form->getValue('template_id', 0);

		if (isset($this->item->id) && $this->item->id > 0)
		{
			if (is_numeric($fieldsTemplateId))
			{
				if ($fieldsTemplateId > 0)
				{
					$templateModel      = RModel::getAdminInstance('Template', array('ignore_request' => true));
					$template           = $templateModel->getItem($fieldsTemplateId);
					$this->customfields = ReditemHelperCustomfield::getAvailableFields($template->content, $this->customfields);
					$this->form->setValue('fields_template_id', null, $fieldsTemplateId);
				}
				elseif ($fieldsTemplateId === 0)
				{
					$this->form->setValue('fields_template_id', null, $fieldsTemplateId);
				}
			}

			$this->form->setFieldAttribute('related_categories', 'ignoreCats', $this->item->id);
		}
		elseif ($templateId != 0)
		{
			$templateModel      = RModel::getAdminInstance('Template', array('ignore_request' => true));
			$template           = $templateModel->getItem($templateId);
			$this->customfields = ReditemHelperCustomfield::getAvailableFields($template->content, $this->customfields);
			$this->form->setValue('template_id', null, $templateId);
			$this->form->setValue('fields_template_id', null, $templateId);
		}

		if ($user->authorise('core.admin', 'com_reditem'))
		{
			$this->canConfig = true;
		}

		if ($this->parentId > 0)
		{
			$this->form->setValue('parent_id', null, $this->parentId);
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

		return JText::_('COM_REDITEM_CATEGORY_CATEGORY') . $subTitle;
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
		$group        = new RToolbarButtonGroup;
		$save         = RToolbarBuilder::createSaveButton('category.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('category.save');
		$saveAndNew   = RToolbarBuilder::createSaveAndNewButton('category.save2new');
		$save2Copy    = RToolbarBuilder::createSaveAsCopyButton('category.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		if (empty($this->item->category_id))
		{
			$cancel = RToolbarBuilder::createCancelButton('category.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('category.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
