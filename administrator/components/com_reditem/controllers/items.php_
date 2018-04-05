<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

/**
 * The templates controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Items
 * @since       2.0
 */
class ReditemControllerItems extends RControllerAdmin
{
	/**
	 * constructor (registers additional tasks to methods)
	 */
	public function __construct()
	{
		parent::__construct();

		// Write this to make two tasks use the same method (in this example the add method uses the edit method)
		$this->registerTask('add', 'edit');
	}

	/**
	 * display the add and the edit form
	 *
	 * @return void
	 */
	public function edit()
	{
		$jInput = JFactory::getApplication()->input;
		$jInput->set('view', 'item');
		$jInput->set('layout', 'default');
		$jInput->set('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * Method to set item to "Featured" item.
	 *
	 * @return  void
	 */
	public function setFeatured()
	{
		$app       = JFactory::getApplication();
		$input     = $app->input;
		$itemModel = RModel::getAdminInstance('Item', array('ignore_request' => true));

		$cids   = $input->get('cid', array(), 'array');
		$return = $input->getBase64('return', null);

		if (!empty($cids))
		{
			$cid = $cids[0];

			if (!$itemModel->featured($cid, 1))
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_ITEMS_SET_FEATURED_ERROR'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_ITEMS_SET_FEATURED_SUCCESS'));
			}
		}

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JURI::base() . 'index.php?option=com_reditem&view=items');
		}

		$this->redirect();
	}

	/**
	 * Method to set "Featured" item to item.
	 *
	 * @return  void
	 */
	public function setUnFeatured()
	{
		$app = JFactory::getApplication();
		$itemModel = RModel::getAdminInstance('Item', array('ignore_request' => true));

		$cids   = $app->input->get('cid', array(), 'array');
		$return = $app->input->getBase64('return', null);

		if (!empty($cids))
		{
			$cid = $cids[0];

			if (!$itemModel->featured($cid, 0))
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_ITEMS_SET_UN_FEATURED_ERROR'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_ITEMS_SET_UN_FEATURED_SUCCESS'));
			}
		}

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JURI::base() . 'index.php?option=com_reditem&view=items');
		}

		$this->redirect();
	}

	/**
	 * Copy items function
	 *
	 * @return  void
	 */
	public function copy()
	{
		$this->copyItemsProcess(false);
		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=items', false));
	}

	/**
	 * Copy items using batch option function
	 *
	 * @return  void
	 */
	public function batch()
	{
		$input  = JFactory::getApplication()->input;
		$isMove = $input->getString('copyMove', 'copy');

		if ($isMove === 'copy')
		{
			$this->copyItemsProcess(true);
		}
		elseif ($isMove === 'move')
		{
			$this->moveItemsProcess();
		}

		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=items', false));
	}

	/**
	 * Move items process function
	 *
	 * @return  void
	 */
	public function moveItemsProcess()
	{
		$input  = JFactory::getApplication()->input;
		$cids   = $input->get('cid', array(), 'array');
		$cats   = $input->get('batchCategories', array(), 'array');
		$remOrg = (boolean) $input->getInt('removeOrigin', 0);
		$access = $input->getInt('access', 0);
		$model  = $this->getModel('Items');

		$model->copy($cids, $cats, true, $access, $remOrg);

		JFactory::getApplication()->enqueueMessage(JText::_('COM_REDITEM_ITEMS_MOVE_SUCCESSFUL'));
	}

	/**
	 * Copy items process function
	 *
	 * @param   bool  $isBatch  if batch copy, it is true value
	 *
	 * @return  void
	 */
	public function copyItemsProcess($isBatch = false)
	{
		$input  = JFactory::getApplication()->input;
		$cids   = $input->get('cid', array(), 'array');
		$cats   = $input->get('batchCategories', array(), 'array');
		$remOrg = (boolean) $input->getInt('removeOrigin', 0);
		$access = $input->getInt('access', 0);
		$model  = $this->getModel('Items');

		if ($isBatch)
		{
			$model->copy($cids, $cats, false, $access);
		}
		else
		{
			$model->copy($cids, array(), false, 0, $remOrg);
		}

		JFactory::getApplication()->enqueueMessage(JText::_('COM_REDITEM_ITEMS_COPY_SUCCESSFUL'));
	}

	/**
	 * Clear thumbnail files which has created in custom fields of items
	 *
	 * @return   void
	 */
	public function cleanThumbnail()
	{
		$cid				= JFactory::getApplication()->input->get('cid', array(), 'array');
		$itemModel			= RModel::getAdminInstance('Item', array(), 'com_reditem');
		$deletedImagesCount	= 0;

		// Clean temporary folder
		$temporaryFolder = JPATH_ROOT . '/media/com_reditem/files/customfield/temporary/';
		JFolder::delete($temporaryFolder);
		JFolder::create($temporaryFolder);

		if (!$cid)
		{
			return;
		}

		foreach ($cid as $itemId)
		{
			$item = $itemModel->getItem($itemId);
			$imageValues = array();

			// Get all image fields
			$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
			$fieldsModel->setState('list.select', 'f.fieldcode');
			$fieldsModel->setState('filter.types', $item->type_id);
			$fieldsModel->setState('filter.fieldtypes', 'image');
			$imagefields = $fieldsModel->getItems();

			if ($imagefields)
			{
				foreach ($imagefields as $imagefield)
				{
					$fieldcode = $imagefield->fieldcode;

					if (isset($item->customfield_values[$fieldcode]) && !empty($item->customfield_values[$fieldcode]))
					{
						$imageValue = $item->customfield_values[$fieldcode];
						$imageValue = json_decode($imageValue);

						// Put image value into Image Values array
						$imageValues[] = $imageValue[0];
					}
				}
			}

			// Get all gallery fields
			$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
			$fieldsModel->setState('list.select', 'f.fieldcode');
			$fieldsModel->setState('filter.types', $item->type_id);
			$fieldsModel->setState('filter.fieldtypes', 'gallery');
			$galleryfields = $fieldsModel->getItems();

			if ($galleryfields)
			{
				foreach ($galleryfields as $galleryfield)
				{
					$fieldcode = $galleryfield->fieldcode;

					if (isset($item->customfield_values[$fieldcode]) && !empty($item->customfield_values[$fieldcode]))
					{
						$galleryValue = $item->customfield_values[$fieldcode];
						$galleryValue = json_decode($galleryValue);

						if ($galleryValue)
						{
							foreach ($galleryValue as $imageInGallery)
							{
								// Put image value into Image Values array
								$imageValues[] = $imageInGallery;
							}
						}
					}
				}
			}

			$imageFolder = JPATH_ROOT . '/media/com_reditem/images/customfield/' . $item->id;

			if (JFolder::exists($imageFolder))
			{
				$files = JFolder::files($imageFolder);

				if (count($files))
				{
					// Get all files in image folder
					foreach ($files as $file)
					{
						$tmpFileName = $item->id . '/' . $file;

						// Check if file is not in Image Values (this is generated image)
						if (!in_array($tmpFileName, $imageValues))
						{
							JFile::delete($imageFolder . '/' . $file);
							$deletedImagesCount++;
						}
					}
				}
			}
		}

		JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDITEM_ITEMS_CLEAN_THUMBNAIL_SUCCESSFUL', count($cid), $deletedImagesCount));

		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=items', false));
	}

	/**
	 * Method for rebuild permission of list items given
	 *
	 * @return  void
	 */
	public function rebuildPermission()
	{
		$user = ReditemHelperSystem::getUser();
		$pks  = $this->input->post->get('cid', array(), 'array');

		$this->setRedirect($this->getRedirectToListRoute());

		if (!$user->authorise('core.edit.state', 'com_reditem'))
		{
			$this->setMessage(JText::_('COM_REDITEM_EDIT_PERMISSION_NOT_GRANTED'), 'error');

			return;
		}

		try
		{
			ReditemHelperAssets::rebuildItems($pks);
		}
		catch (RuntimeException $e)
		{
			$this->setMessage(JText::sprintf('COM_REDITEM_ITEMS_ERROR_ASSET_REBUILD', $e->getMessage()), 'error');

			return;
		}

		$this->setMessage(JText::_('COM_REDITEM_ITEMS_REBUILD_PERMISSION_SUCCESS'));
	}

	/**
	 * Import csv file for updating items and creating new ones.
	 *
	 * @return  void
	 */
	public function importCsv()
	{
		$app   = JFactory::getApplication();
		$csv   = $app->input->get('csv', array(), 'array');
		$files = $app->input->files->get('csv', array(), 'array');
		$del   = isset($csv['delimiter']) ? $csv['delimiter'] : ',';
		$model = $this->getModel('Items', 'ReditemModel');

		if ($model->importCsv($files['import'], $del))
		{
			$msg  = JText::_('COM_REDITEM_ITEMS_CSV_IMPORT_SUCCESS');
			$type = 'message';
		}
		else
		{
			$msg  = JText::_('COM_REDITEM_ITEMS_CSV_IMPORT_FAIL');
			$type = 'error';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=items', false), $msg, $type);
		$this->redirect();
	}

	/**
	 * Export csv file with items selected.
	 *
	 * @return  void
	 */
	public function exportCsv()
	{
		$app      = JFactory::getApplication();
		$ids      = $app->input->get('cid', array(), 'array');
		$csv      = $app->input->get('csv', array(), 'array');
		$del      = isset($csv['delimiter']) ? $csv['delimiter'] : ',';
		$model    = $this->getModel('Items', 'ReditemModel');
		$date     = new JDate;
		$filename = $date->format('H-i-s_d-m-Y') . '.csv';

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Pragma: no-cache");
		header("Expires: 0");
		$model->exportCsv($ids, $del);

		$app->close();
	}

	/**
	 * Method for getting list of edit fields.
	 *
	 * @return  void
	 */
	public function ajaxGetEditFields()
	{
		$app       = JFactory::getApplication();
		$fromTypes = $app->input->get('typesFrom', array(), 'array');
		$toType    = $app->input->getInt('typeTo', 0);
		$html      = '';

		foreach ($fromTypes as $fromType)
		{
			$fromFields = ReditemHelperType::getCustomFieldList($fromType);
			$toFields   = ReditemHelperType::getCustomFieldList($toType);
			$type       = ReditemHelperType::getType($fromType);
			$html      .= '<fieldset><legend><span>' . JText::_('COM_REDITEM_ITEMS_CONVERT_ITEM_TYPE') . $type->title . '</span></legend>';
			$html      .= '<table class="table table-striped"><thead><tr><th style="width: 50%;">' . JText::_('COM_REDITEM_ITEMS_CONVERT_FROM_ITEM_FIELDS') .
				'</th><th style="width: 50%;">' . JText::_('COM_REDITEM_ITEMS_CONVERT_TO_ITEM_FIELDS') . '</th></tr></thead><tbody>';

			foreach ($fromFields as $fromField)
			{
				$html   .= '<tr><td>' . $fromField->name . '</td>';
				$fields  = ReditemHelperCustomfield::getPossibleConvertFields($fromField->type, $toFields);
				$options = array(
					JHtml::_('select.option', -1, JText::_('COM_REDITEM_ITEMS_CONVERT_SKIP_FIELD')),
					JHtml::_('select.option', 0, JText::_('COM_REDITEM_ITEMS_CONVERT_CREATE_FIELD_COPY'))
				);

				foreach ($fields as $field)
				{
					$options[] = JHtml::_('select.option', (int) $field->id, $field->name);
				}

				$html .= '<td>' . JHtml::_(
						'select.genericlist',
						$options,
						'convert[fields][' . $fromType . '][' . $fromField->id . ']',
						array (
							'list.attr' => array('class' => 'convert-fields field-' . $fromField->type . '-' . $fromType)
						),
						'value',
						'text',
						0
					) . '</td></tr>';
			}

			$html .= '</tbody></table></fieldset>';
		}

		echo $html;
		$app->close();
	}

	/**
	 * Ajax function for getting list of possible related items.
	 *
	 * @return  void
	 */
	public function ajaxGetRelatedItems()
	{
		$app     = JFactory::getApplication();
		$search  = $app->input->getString('search', '');
		$page    = $app->input->getInt('page', 1);
		$limit   = $app->input->getInt('limit', 20);
		$exclude = $app->input->getInt('exclude', 0);
		$model   = $this->getModel('Items', 'ReditemModel');
		$list    = $model->getRelatedItems($search, $exclude, $page, $limit);

		echo json_encode($list);

		$app->close();
	}
}
