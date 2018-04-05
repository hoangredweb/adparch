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
class ReditemControllerField extends RControllerForm
{
	/**
	 * Edit field
	 *
	 * @param   int     $key     [description]
	 * @param   string  $urlVar  [description]
	 *
	 * @return  boolean
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app = JFactory::getApplication();
		$fieldModel = RModel::getAdminInstance('Field');

		$field = $fieldModel->getItem();
		$app->setUserState('com_reditem.global.field.type', $field->type);
		$app->setUserState('com_reditem.global.field.RITypeId', $field->type_id);

		return parent::edit($key, $urlVar);
	}

	/**
	 * For auto-submit form when client choose type
	 *
	 * @return void
	 */
	public function setType()
	{
		$app = JFactory::getApplication();
		$recordId = $app->input->getInt('id', 0);
		$data     = $app->input->get('jform', array(), 'array');

		$app->setUserState('com_reditem.edit.field.data', $data);
		$app->setUserState('com_reditem.global.field.type', $data['type']);

		$redirect = JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false);

		$this->setRedirect($redirect);
	}

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

		$app->setUserState('com_reditem.edit.field.data', $data);
		$app->setUserState('com_reditem.global.field.type', $data['type']);
		$app->setUserState('com_reditem.global.field.RITypeId', $data['type_id']);

		$redirect = JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false);

		if (!empty($recordId))
		{
			$this->setMessage(JText::_('COM_REDITEM_FIELD_WARNING_CHANGE_FIELD_TYPE_LOST_DATA'), 'warning');
		}

		$this->setRedirect($redirect);
	}

	/**
	 * Ajax function for cropping custom field images.
	 *
	 * @return  void
	 */
	public function ajaxCropImage()
	{
		$result    = false;
		$app       = RFactory::getApplication();
		$imageName = $app->input->getString('image_name');
		$top       = $app->input->getInt('top');
		$left      = $app->input->getInt('left');
		$width     = $app->input->getInt('width');
		$height    = $app->input->getInt('height');
		$path      = JPATH_REDITEM_MEDIA . $app->input->getString('path', '') . '/';

		if (JFile::exists($path . $imageName))
		{
			$result = ReditemHelperCropimage::cropImage($imageName, $path, $top, $left, $width, $height);
		}

		echo (int) $result;

		$app->close();
	}

	/**
	 * Method for upload an file using AJAX method.
	 *
	 * @return  void  Outputs path to new file location.
	 */
	public function ajaxUpload()
	{
		$app        = JFactory::getApplication();
		$files      = $app->input->files->get('dragFile');
		$uploadType = $app->input->getString('uploadType', '');
		$fieldType  = $app->input->getString('fieldType', 'item');

		if (!in_array($uploadType, array('file', 'image', 'gallery')))
		{
			$app->close();
		}

		$uploadTarget = $app->input->getString('uploadTarget', '');

		if (trim($uploadTarget) == '')
		{
			$app->close();
		}

		echo ReditemHelperFile::dragNDropUpload($files, $fieldType, $uploadTarget);

		$app->close();
	}
}
