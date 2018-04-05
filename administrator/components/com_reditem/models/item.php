<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('helper', JPATH_ROOT . '/administrator/components/com_reditem/helpers');
jimport('joomla.filesystem.folder');

/**
 * RedITEM Item Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Item
 * @since       0.9.1
 *
 */
class ReditemModelItem extends RModelAdmin
{
	public $item = null;

	protected $typeAlias = 'com_reditem.item';

	/**
	 * Method to get the row form.
	 *
	 * @param   int  $pk  Primary key
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		$app        = JFactory::getApplication();
		$this->item = parent::getItem($pk);
		$this->item->customfield_values = array();

		if (!empty($this->item->params['related_items']))
		{
			$this->item->params['related_items'] = json_encode($this->item->params['related_items']);
		}
		else
		{
			$this->item->params['related_items'] = '';
		}

		if (isset($this->item->id) && $app->isAdmin())
		{
			$categories = ReditemHelperItem::getCategories($this->item->id);

			if (isset($categories[$this->item->id]))
			{
				$this->item->categories = $categories[$this->item->id];
			}

			if (empty($this->item->customfield_values))
			{
				$cfValues = ReditemHelperItem::getCustomFieldValues($this->item->id);

				if (isset($cfValues[$this->item->type_id][$this->item->id]))
				{
					$this->item->customfield_values = $cfValues[$this->item->type_id][$this->item->id];
				}
			}
		}

		return $this->item;
	}

	/**
	 * Method to get custom field.
	 *
	 * @return  array
	 */
	public function getCustomFields()
	{
		$app    = RFactory::getApplication();
		$typeId = $app->input->getInt('tid', 0);

		if (!$typeId)
		{
			$typeId = $app->getUserState('com_reditem.global.tid', 0);
		}

		if (!$typeId)
		{
			return false;
		}

		$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
		$fieldsModel->setState('filter.types', $typeId);
		$fieldsModel->setState('filter.published', 1);
		$fieldsModel->setState('list.ordering', 'f.ordering');
		$fieldsModel->setState('list.direction', 'asc');

		$rows   = $fieldsModel->getItems() ?: array();
		$fields = array();

		foreach ($rows as $row)
		{
			if ($row->published == 1)
			{
				$field = ReditemHelperCustomfield::getCustomField($row->type);
				$field->bind($row);

				if ((isset($this->item->customfield_values)) && isset($this->item->customfield_values[$row->fieldcode]))
				{
					$field->value = $this->item->customfield_values[$row->fieldcode];
				}

				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Method to set featured of item.
	 *
	 * @param   int  $id     Id of item
	 * @param   int  $state  featured state of item
	 *
	 * @return  boolean
	 */
	public function featured($id = null, $state = 0)
	{
		$db = JFactory::getDbo();

		if ($id)
		{
			$query = $db->getQuery(true);

			$query->update($db->qn('#__reditem_items', 'i'))
				->set($db->qn('i.featured') . ' = ' . (int) $state)
				->where($db->qn('i.id') . ' = ' . (int) $id);

			$db->setQuery($query);

			if (!$db->execute())
			{
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = parent::getForm($data, $loadData);
		$user = ReditemHelperSystem::getUser();

		if (!$user->authorise('core.edit.state', 'com_reditem'))
		{
			// Disable change publish state
			$form->setFieldAttribute('published', 'readonly', true);
			$form->setFieldAttribute('published', 'class', 'btn-group disabled');

			// Disable change feature state
			$form->setFieldAttribute('featured', 'readonly', true);
			$form->setFieldAttribute('featured', 'class', 'btn-group disabled');

			// Disable change access state
			$form->setFieldAttribute('access', 'disabled', true);
		}

		return $form;
	}

	/**
	 * Method for save Preview data
	 *
	 * @return  void
	 */
	public function savePreviewData()
	{
		$app                    = JFactory::getApplication();
		$db                     = JFactory::getDbo();
		$input                  = $app->input;
		$jform                  = $input->get('jform', array(), 'array');
		$fields                 = $jform['fields'];
		$id                     = $input->getRaw('previewId', '');
		$imageFiles             = $input->files->get('jform');
		$customFieldUploadFiles = $imageFiles['fields'];

		// Remove [Image] customfield
		if (isset($jform['customfield_image_rm']))
		{
			foreach ($jform['customfield_image_rm'] as $customFieldImageRemove)
			{
				// Remove this image from values array
				$fields['image'][$customFieldImageRemove] = '';
			}
		}

		// Remove [gallery] custom field - Checked
		if (isset($jform['customfield_gallery_rm']))
		{
			foreach ($jform['customfield_gallery_rm'] as $cfGallery => $cfImagesRemove)
			{
				if ($cfImagesRemove)
				{
					foreach ($cfImagesRemove as $cfImage)
					{
						if ($cfImage)
						{
							// Remove this image from values array
							$key = array_search($cfImage, $fields['gallery'][$cfGallery]);
							unset($fields['gallery'][$cfGallery][$key]);
						}
					}
				}
			}
		}

		// [Image] custom field - Folder process
		$imageFolder = JPATH_ROOT . '/media/com_reditem/images/customfield/' . $id . '/preview/';

		if (!JFolder::exists($imageFolder))
		{
			JFolder::create($imageFolder);
		}

		// [Image] custom field - Media field process
		if (isset($fields['image_media']))
		{
			foreach ($fields['image_media'] as $imageField => $imageLink)
			{
				if (!empty($imageLink))
				{
					// Choose from media manager
					$mediaImageSource      = JPATH_SITE . '/' . $imageLink;
					$mediaTmp              = explode("/", $imageLink);
					$mediaTmp              = array_reverse($mediaTmp);
					$mediaImageFilename    = array_shift($mediaTmp);
					$mediaImageDestination = $imageFolder . $mediaImageFilename;

					JFile::copy($mediaImageSource, $mediaImageDestination);

					if (isset($imageFilesCustomField['image'][$imageField]))
					{
						unset($imageFilesCustomField['image'][$imageField]);
					}

					$fields['image'][$imageField] = json_encode(array($id . '/preview/' . $mediaImageFilename));
				}
			}

			unset($fields['image_media']);
		}
		// [Image] custom field - Drag & Drop function
		if (!empty($fields['image']))
		{
			$temporaryFolder = JPATH_ROOT . '/media/com_reditem/files/customfield/temporary/';

			foreach ($fields['image'] as $imageFieldName => $imageFieldData)
			{
				if (count($fields['dragndrop'][$imageFieldName])
					&& JString::trim($fields['dragndrop'][$imageFieldName][0])
					&& JFile::exists($temporaryFolder . JString::trim($fields['dragndrop'][$imageFieldName][0])))
				{
					$imagePathValue                  = $id . '/preview/' . JString::trim($fields['dragndrop'][$imageFieldName][0]);
					$fields['image'][$imageFieldName] = json_encode(array($imagePathValue));

					// Move file from temporary folder to file folder
					$sourceFile      = $temporaryFolder . JString::trim($fields['dragndrop'][$imageFieldName][0]);
					$destinationFile = $imageFolder . JString::trim($fields['dragndrop'][$imageFieldName][0]);

					JFile::copy($sourceFile, $destinationFile);
				}
			}
		}

		// [File] custom field - Folder process
		$fileFolder = JPATH_ROOT . '/media/com_reditem/files/customfield/' . $this->id . '/';

		if (!JFolder::exists($fileFolder))
		{
			JFolder::create($fileFolder);
		}

		// [File] custom field - Drag & Drop function
		if (!empty($fields['file']))
		{
			$temporaryFolder = JPATH_ROOT . '/media/com_reditem/files/customfield/temporary/';

			foreach ($fields['file'] as $fileFieldName => $fileFieldData)
			{
				$fileFieldFileName = $fields['file'][$fileFieldName];

				if (count($fields['dragndrop'][$fileFieldName])
					&& JString::trim($fields['dragndrop'][$fileFieldName][0])
					&& JFile::exists($temporaryFolder . JString::trim($fields['dragndrop'][$fileFieldName][0])))
				{
					$filePath                      = $id . '/preview/' . JString::trim($fields['dragndrop'][$fileFieldName][0]);
					$fields['file'][$fileFieldName] = json_encode(array($filePath, $fileFieldFileName));

					// Move file from temporary folder to file folder
					$sourceFile = $temporaryFolder . JString::trim($fields['dragndrop'][$fileFieldName][0]);
					$destFile   = $fileFolder . JString::trim($fields['dragndrop'][$fileFieldName][0]);

					JFile::move($sourceFile, $destFile);
				}
			}
		}

		// [Gallery] custom field - Remove images
		if (isset($jform['customfield_gallery_rm']))
		{
			foreach ($jform['customfield_gallery_rm'] as $cfGallery => $cfImagesRemove)
			{
				if ($cfImagesRemove)
				{
					foreach ($cfImagesRemove as $cfImage)
					{
						if ($cfImage)
						{
							// Remove this image from values array
							$key = array_search($cfImage, $fields['gallery'][$cfGallery]);
							unset($fields['gallery'][$cfGallery][$key]);
						}
					}
				}
			}
		}

		// [Gallery] custom field - On use Media field
		if (isset($fields['gallery_media']))
		{
			foreach ($fields['gallery_media'] as $galleryMediaField => $galleryMediaData)
			{
				$galleryMediaFieldName = substr($galleryMediaField, 0, -5);
				$tmpGalleryMediaImages = array();

				if (is_array($galleryMediaData) && !empty($galleryMediaData))
				{
					foreach ($galleryMediaData as $key => $galleryMediaImage)
					{
						if (!empty($galleryMediaImage))
						{
							$tmpMediaSource        = explode("/", $galleryMediaImage);
							$tmpMediaSource        = array_reverse($tmpMediaSource);
							$tmpMediaImageFilename = array_shift($tmpMediaSource);
							$oldImageMedia         = JPATH_SITE . '/' . $galleryMediaImage;
							$newImageMedia         = $imageFolder . $tmpMediaImageFilename;

							// Remove old file if exist
							if (JFile::exists($newImageMedia))
							{
								JFile::delete($newImageMedia);
							}

							JFile::copy($oldImageMedia, $newImageMedia);

							$tmpGalleryMediaImages[] = $id . '/preview/' . $tmpMediaImageFilename;

							// Remove field
							if (isset($customFieldUploadFiles['gallery'][$galleryMediaField][$key]))
							{
								unset($customFieldUploadFiles['gallery'][$galleryMediaField][$key]);
							}
						}

						unset($galleryMediaData[$key]);
					}
				}

				// Check if there are any images exist in this gallery.
				$oldGalleryImages = $fields['gallery'][$galleryMediaFieldName];

				if (is_array($oldGalleryImages) && !empty($oldGalleryImages))
				{
					// Merge old image with new upload images.
					$tmpGalleryMediaImages = array_merge($oldGalleryImages, $tmpGalleryMediaImages);
				}

				$fields['gallery'][$galleryMediaFieldName] = $tmpGalleryMediaImages;
				unset($fields['gallery_media'][$galleryMediaField]);
			}

			unset($fields['gallery_media']);
		}
		// [Gallery] custom field - Prepare value
		foreach ($fields['gallery'] as $col => $value)
		{
			if (count($fields['dragndrop'][$col]) && JString::trim($fields['dragndrop'][$col][0]))
			{
				$temporaryFolder = JPATH_ROOT . '/media/com_reditem/files/customfield/temporary/';
				$dragndropData   = explode(',', JString::trim($fields['dragndrop'][$col][0]));

				if (count($dragndropData))
				{
					foreach ($dragndropData as $dragKey => $dragValue)
					{
						if (JString::trim($dragValue))
						{
							if (JFile::exists($temporaryFolder . $dragValue))
							{
								$value[] = $id . '/preview/' . $dragValue;
								JFile::move($temporaryFolder . $dragValue, $imageFolder . $dragValue);
							}
						}
					}
				}
			}

			if (is_array($value))
			{
				// Remove empty value
				foreach ($value as $key => $val)
				{
					if (empty($val))
					{
						unset($value[$key]);
					}
				}
				// Reset key for array values
				$value                   = array_values($value);
				$fields['gallery'][$col] = json_encode($value);
			}
		}

		// [Url] custom field - Process
		if (isset($fields['url']) && is_array($fields['url']))
		{
			foreach ($fields['url'] as $fieldName => $linkUrl)
			{
				$linkTitle = htmlspecialchars(JStringPunycode::urlToUTF8($linkUrl), ENT_COMPAT, 'UTF-8');

				if (isset($fields['url'][$fieldName]) && ($fields['url'][$fieldName]))
				{
					$linkTitle = $fields['url'][$fieldName];
				}

				$linkValue          = array($linkUrl, $linkTitle);
				$fields[$fieldName] = json_encode($linkValue);
			}
		}

		unset($fields['dragndrop']);

		// Prepare custom fields value
		$customfieldValues = array();

		foreach ($fields as $groupType => $group)
		{
			foreach ($group as $column => $value)
			{
				// Remove empty value in Checkbox custom field
				if ($groupType == 'checkbox')
				{
					$value = array_filter($value);
				}

				$value                      = (is_array($value)) ? json_encode($value) : $value;
				$customfieldValues[$column] = $value;
			}
		}

		$jform['customfield_values'] = $customfieldValues;
		$data                        = new JRegistry($jform);

		// Delete old preview data
		$query = $db->getQuery(true)
			->delete($db->qn('#__reditem_item_preview'))
			->where($db->qn('id') . ' = ' . $db->quote($id));
		$db->setQuery($query);
		$db->execute();

		// Insert new preview data
		$query->clear()
			->insert($db->qn('#__reditem_item_preview'))
			->columns($db->qn(array('id', 'data')))
			->values($db->quote($id) . ', ' . $db->quote($data->toString()));
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   12.2
	 */
	public function save($data)
	{
		$table      = $this->getTable();
		$files      = JFactory::getApplication()->input->files->get('jform', array());
		$fieldFiles = (!empty($files['fields'])) ? $files['fields'] : array();
		$task       = $this->getState('task', 'save');

		// Process dragndrop uploads
		$drags = !empty($data['fields']['dragndrop']) ? $data['fields']['dragndrop'] : array();

		if (!empty($drags))
		{
			foreach ($drags as $field => $file)
			{
				if (isset($files['fields']['files'][$field]) && $files['fields']['files'][$field]['error'] == 0)
				{
					unset($drags[$field]);
				}

				if (isset($files['fields']['images'][$field]) && $files['fields']['images'][$field]['error'] == 0)
				{
					unset($drags[$field]);
				}
			}
		}

		if ((!empty($data['tags']) && $data['tags'][0] != ''))
		{
			$table->newTags = $data['tags'];
		}

		if (empty($data['categories']))
		{
			$data['categories'] = array();
		}

		$key   = $table->getKeyName();
		$pk    = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the plugins for the save events.
		JPluginHelper::importPlugin($this->events_map['save']);

		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}

			// Task save2copy
			if ($task == 'save2copy')
			{
				list($title, $alias) = $this->generateItemTitle($data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
				$pk            = JFactory::getApplication()->input->getInt('id', 0);

				if (!$this->dataSave($data, $table, $isNew))
				{
					return false;
				}

				// Copy field images
				ReditemHelperCustomfield::copyFiles($pk, $table->id, 'images', 'item');

				// Copy field files
				ReditemHelperCustomfield::copyFiles($pk, $table->id, 'files', 'item');

				// Store files from the request
				ReditemHelperCustomfield::storeFilesFromRequest($fieldFiles, $data, $table->id, 'item', true);

				if (!empty($drags))
				{
					ReditemHelperCustomfield::processDragNDrop($table->id, $table->type_id, $drags, $data);
				}
			}
			// Task update
			elseif (!$isNew)
			{
				// Store files from the request
				$data = ReditemHelperCustomfield::storeFilesFromRequest($fieldFiles, $data, $pk);

				if (!$this->dataSave($data, $table, $isNew))
				{
					return false;
				}

				if (!empty($drags))
				{
					ReditemHelperCustomfield::processDragNDrop($pk, $data['type_id'], $drags, $data);
				}
			}
			// Task new
			else
			{
				if (!$this->dataSave($data, $table, $isNew))
				{
					return false;
				}

				// Store files from the request
				ReditemHelperCustomfield::storeFilesFromRequest($fieldFiles, $data, $table->id, 'item', true);

				if (!empty($drags))
				{
					ReditemHelperCustomfield::processDragNDrop($table->id, $table->type_id, $drags, $data);
				}
			}
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		if (isset($table->$key))
		{
			$this->setState($this->getName() . '.id', $table->$key);
			$previewId = md5($table->$key . ReditemHelperSystem::getUser()->id);

			// Remove preview data
			$db    = $this->_db;
			$query = $db->getQuery(true)
				->delete($db->qn('#__reditem_item_preview'))
				->where($db->qn('id') . ' = ' . $db->quote($previewId));
			$db->setQuery($query);
			$db->execute();
		}

		$this->setState($this->getName() . '.new', $isNew);

		if ($this->associationsContext && JLanguageAssociations::isEnabled())
		{
			$associations = $data['associations'];

			// Unset any invalid associations
			foreach ($associations as $tag => $id)
			{
				if (!(int) $id)
				{
					unset($associations[$tag]);
				}
			}
			// Show a notice if the item isn't assigned to a language but we have associations.
			if ($associations && ($table->language == '*'))
			{
				JFactory::getApplication()->enqueueMessage(
					JText::_(strtoupper($this->option) . '_ERROR_ALL_LANGUAGE_ASSOCIATED'),
					'notice'
				);
			}
			// Adding self to the association
			$associations[$table->language] = (int) $table->$key;

			// Deleting old association for these items
			$db    = $this->getDbo();
			$query = $db->getQuery(true)
				->delete($db->qn('#__associations'))
				->where($db->qn('context') . ' = ' . $db->quote($this->associationsContext))
				->where($db->qn('id') . ' IN (' . implode(',', $associations) . ')');
			$db->setQuery($query);
			$db->execute();

			if ((count($associations) > 1) && ($table->language != '*'))
			{
				// Adding new association for these items
				$key   = md5(json_encode($associations));
				$query = $db->getQuery(true)
					->insert('#__associations');

				foreach ($associations as $id)
				{
					$query->values($id . ',' . $db->quote($this->associationsContext) . ',' . $db->quote($key));
				}

				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}

	/**
	 * Data save function.
	 *
	 * @param   array    $data   Data to store to database.
	 * @param   JTable   $table  Table to store data.
	 * @param   boolean  $isNew  Is row new.
	 *
	 * @return  bool  True on success, false otherwise.
	 *
	 * @throws Exception
	 */
	private function dataSave($data, $table, $isNew)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$context    = $this->option . '.' . $this->name;

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		// Prepare the row for saving
		$this->prepareTable($table);

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Trigger the before save event.
		$result = $dispatcher->trigger($this->event_before_save, array($context, $table, $isNew));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}
		// Clean the cache.
		$this->cleanCache();

		// Trigger the after save event.
		$dispatcher->trigger($this->event_after_save, array($context, $table, $isNew));

		return true;
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   string  $alias  The alias.
	 * @param   string  $title  The title.
	 *
	 * @return    array  Contains the modified title and alias.
	 *
	 * @since    12.2
	 */
	protected function generateItemTitle($alias, $title)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(' . $db->qn('id') . ')')
			->from($db->qn('#__reditem_items'))
			->where($db->qn('alias') . ' = ' . $db->q($alias))
			->where($db->qn('title') . ' = ' . $db->q($title));
		$db->setQuery($query);

		while ($db->loadResult())
		{
			$query->clear('where');
			$title = JString::increment($title);
			$alias = JString::increment($alias, 'dash');
			$query->where($db->qn('alias') . ' = ' . $db->q($alias))
				->where($db->qn('title') . ' = ' . $db->q($title));
			$db->setQuery($query);
		}

		return array($title, $alias);
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   12.2
	 */
	public function delete(&$pks)
	{
		$pks = (array) $pks;

		// Remove item files
		foreach ($pks as $pk)
		{
			// Remove images
			$imageFolder = JPATH_REDITEM_MEDIA . 'images/customfield/' . $pk;

			if (JFolder::exists($imageFolder))
			{
				JFolder::delete($imageFolder);
			}

			// Remove files
			$fileFolder = JPATH_REDITEM_MEDIA . 'files/customfield/' . $pk;

			if (JFolder::exists($fileFolder))
			{
				JFolder::delete($fileFolder);
			}
		}

		return parent::delete($pks);
	}

	/**
	 * Function for updating item value.
	 *
	 * @param   int     $id         Item id.
	 * @param   string  $fieldCode  Field fieldcode to update.
	 * @param   string  $value      Value to set.
	 *
	 * @return  void
	 */
	public function updateValue($id, $fieldCode, $value)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$table = ReditemHelperItem::getTableName($id);
		$query->update($table)
			->set($db->qn($fieldCode) . ' = ' . $db->q($value))
			->where($db->qn('id') . ' = ' . (int) $id);
		$db->setQuery($query)->execute();
	}
}
