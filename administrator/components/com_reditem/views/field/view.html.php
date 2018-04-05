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
class ReditemViewField extends ReditemViewAdmin
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
		$app       = JFactory::getApplication();
		$fieldType = $app->getUserState('com_reditem.global.field.type', '');
		$editData  = $app->getUserState('com_reditem.edit.field.data', array());

		$configuration = JComponentHelper::getParams('com_reditem');
		$this->versioningEnable = (boolean) $configuration->get('save_history', 1);

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

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

		if ($fieldType && !empty($editData['params']) && is_array($editData['params']))
		{
			$this->item->type = $fieldType;

			foreach ($editData['params'] as $key => $value)
			{
				$this->form->setValue($key, 'params', $value);
			}
		}

		// Prepare version modal link
		if ($this->versioningEnable && $this->item->id)
		{
			$typeAlias = $this->getModel()->get('typeAlias');
			$contentTypeTable = JTable::getInstance('Contenttype');
			$typeId = $contentTypeTable->getTypeId($typeAlias);

			$this->versionModalLink = 'index.php?option=com_contenthistory&view=history&layout=modal&tmpl=component&item_id=' . $this->item->id;
			$this->versionModalLink .= '&type_id=' . $typeId . '&amp;type_alias=' . $typeAlias . '&' . JSession::getFormToken() . '=1';
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
		$group = new RToolbarButtonGroup;

		$save = RToolbarBuilder::createSaveButton('field.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('field.save');
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('field.save2new');
		$save2Copy = RToolbarBuilder::createSaveAsCopyButton('field.save2copy');

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
			$cancel = RToolbarBuilder::createCancelButton('field.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('field.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
