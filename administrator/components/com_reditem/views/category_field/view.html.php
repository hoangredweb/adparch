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
 * Field edit view
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class ReditemViewCategory_Field extends ReditemViewAdmin
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
		$app        = JFactory::getApplication();
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$editData   = $app->getUserState('com_reditem.edit.category_field.data', array());
		$fieldType  = $app->getUserState('com_reditem.global.category_field.type', '');

		if (!empty($this->item->params))
		{
			$params = new JRegistry($this->item->params);
			$params = $params->toArray();

			foreach ($params as $key => $value)
			{
				$this->form->setValue($key, 'params', $value);
			}

			if (isset($this->item->default))
			{
				$this->form->setValue('default', null, $this->item->default);
			}
		}

		if (empty($fieldType) && !empty($editData['params']) && is_array($editData['params']))
		{
			$this->item->type = $fieldType;

			foreach ($editData['params'] as $key => $value)
			{
				$this->form->setValue($key, 'params', $value);
			}
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

		return JText::_('COM_REDITEM_FIELD_FIELD') . $subTitle;
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
		$save         = RToolbarBuilder::createSaveButton('category_field.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('category_field.save');
		$saveAndNew   = RToolbarBuilder::createSaveAndNewButton('category_field.save2new');
		$save2Copy    = RToolbarBuilder::createSaveAsCopyButton('category_field.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('category_field.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('category_field.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
