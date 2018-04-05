<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The template edit controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Field
 * @since       2.0
 */
class ReditemControllerCategory_Field extends RControllerForm
{
	/**
	 * For auto-submit form when client choose field type
	 *
	 * @return void
	 */
	public function setFieldType()
	{
		$app      = JFactory::getApplication();
		$recordId = $app->input->getInt('id', 0);
		$data     = $app->input->get('jform', array(), 'array');

		$app->setUserState('com_reditem.global.category_field.type', $data['type']);
		$app->setUserState('com_reditem.edit.category_field.data', $data);

		$redirect = JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false);

		if (!empty($recordId))
		{
			$this->setMessage(JText::_('COM_REDITEM_FIELD_WARNING_CHANGE_FIELD_TYPE_LOST_DATA'), 'warning');
		}

		$this->setRedirect($redirect);
	}

	/**
	 * Method to add a new record.
	 *
	 * @return  mixed  True if the record can be added, a error object if not.
	 */
	public function add()
	{
		$app = JFactory::getApplication();
		$app->setUserState('com_reditem.global.category_field.type', null);

		return parent::add();
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app = JFactory::getApplication();
		$id  = $app->input->getInt('id', 0);

		if ($id)
		{
			$model = $this->getModel();
			$type  = $model->getItem($id)->type;
			$app->setUserState('com_reditem.global.category_field.type', $type);
		}

		return parent::edit($key, $urlVar);
	}
}
