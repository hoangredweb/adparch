<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * RedITEM category Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Category
 * @since       2.0
 *
 */
class ReditemModelCategory extends RModelAdmin
{
	public $category = null;

	/**
	 * Method to save the form data for TableNested
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		// Initialise variables;
		$table       = $this->getTable();
		$pk          = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		$task        = $this->getState('task', 'save');
		$files       = JFactory::getApplication()->input->files->get('jform', array());
		$fieldFiles  = (!empty($files['fields'])) ? $files['fields'] : array();
		$isNew       = true;
		$oldCatImage = '';

		// Process dragndrop uploads array
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

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Load the row if saving an existing category.
		if ($pk > 0)
		{
			$table->load($pk);
			$isNew = false;

			if (!empty($table->category_image))
			{
				$oldCatImage = $table->category_image;
			}
		}

		// Set the new parent id if parent id not matched OR while New/Save as Copy .
		if ($table->parent_id != $data['parent_id'] || $data['id'] == 0)
		{
			$table->setLocation($data['parent_id'], 'last-child');
		}

		// Alter the title for save as copy
		if ($task == 'save2copy')
		{
			list($title, $alias) = $this->generateNewTitle($data['parent_id'], $data['alias'], $data['title']);
			$data['title'] = $title;
			$data['alias'] = $alias;
			$pk            = JFactory::getApplication()->input->getInt('id', 0);

			if (!$this->dataSave($data, $table, $isNew))
			{
				return false;
			}

			// Copy category fields which we didn't save
			$this->copyFields($pk, $table->id);

			// Copy field images
			ReditemHelperCustomfield::copyFiles($pk, $table->id, 'images', 'category');

			// Copy field files
			ReditemHelperCustomfield::copyFiles($pk, $table->id, 'files', 'category');

			// Store files from the request
			ReditemHelperCustomfield::storeFilesFromRequest($fieldFiles, $data, $table->id, 'category', true);

			if (!empty($drags))
			{
				ReditemHelperCustomfield::processDragNDrop($table->id, 0, $drags, $data, 'category');
			}
		}
		// Task update
		elseif (!$isNew)
		{
			// Store files from the request
			$data = ReditemHelperCustomfield::storeFilesFromRequest($fieldFiles, $data, $pk, 'category');

			if (!$this->dataSave($data, $table, $isNew))
			{
				return false;
			}

			if (!empty($drags))
			{
				ReditemHelperCustomfield::processDragNDrop($pk, 0, $drags, $data, 'category');
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
			ReditemHelperCustomfield::storeFilesFromRequest($fieldFiles, $data, $table->id, 'category', true);

			if (!empty($drags))
			{
				ReditemHelperCustomfield::processDragNDrop($table->id, 0, $drags, $data, 'category');
			}
		}

		// Save category image
		$this->saveCategoryImage($table->id, $data, $files, $oldCatImage);

		// Rebuild the path for the category:
		if (!$table->rebuildPath($table->id))
		{
			$this->setError($table->getError());

			return false;
		}

		// Rebuild the paths of the category's children:
		if (!$table->rebuild($table->id, $table->lft, $table->level, $table->path))
		{
			$this->setError($table->getError());

			return false;
		}

		$this->setState($this->getName() . '.id', $table->id);

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Copies all source field xrefs to dest category.
	 *
	 * @param   int  $from  Category id to copy fields from.
	 * @param   int  $to    Category id to field to.
	 *
	 * @return  void
	 */
	public function copyFields($from, $to)
	{
		$table = RTable::getAdminInstance('Category_Category_Field_Xref', array(), 'com_reditem');
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(
			array(
				$db->qn('category_field_id', 'id'),
				$db->qn('value')
			)
		)
			->from($db->qn('#__reditem_category_category_field_xref'))
			->where($db->qn('category_id') . ' = ' . (int) $from);
		$db->setQuery($query);
		$fields = $db->loadObjectList() ?: array();

		foreach ($fields as $field)
		{
			$table->reset();

			if (!$table->load(array('category_id' => $to, 'category_field_id' => $field->id)))
			{
				$table->save(array('category_id' => $to, 'category_field_id' => $field->id, 'value' => $field->value));
			}
		}
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   int  $pk  Primary key
	 *
	 * @return    object
	 */
	public function getItem($pk = null)
	{
		$app            = JFactory::getApplication();
		$this->category = parent::getItem($pk);

		if (!empty($this->category))
		{
			if ($app->isAdmin())
			{
				// Load related categories if in Admin.
				$this->category->related_categories = ReditemHelperCategory::getRelatedCategories($this->category->id, true);
			}
			else
			{
				// Define null and now dates
				$nullDate = JFactory::getDbo()->getNullDate();
				$nowDate  = ReditemHelperSystem::getDateWithTimezone()->toSql();

				if (($this->category->published == 1) && (($this->category->publish_up > $nowDate)
					|| (($this->category->publish_down != $nullDate) && ($this->category->publish_down < $nowDate))))
				{
					return null;
				}
			}
		}

		return $this->category;
	}

	/**
	 * Same as getItem but not using method to get related categories to avoid endless loop.
	 *
	 * @param   int  $pk  Primary key
	 *
	 * @return    object
	 */
	public function getItemNoRelated($pk = null)
	{
		$app            = JFactory::getApplication();
		$this->category = parent::getItem($pk);

		if (!empty($this->category))
		{
			if (!$app->isAdmin())
			{
				// Define null and now dates
				$nullDate = JFactory::getDbo()->getNullDate();
				$nowDate  = ReditemHelperSystem::getDateWithTimezone()->toSql();

				if (($this->category->published == 1) && (($this->category->publish_up > $nowDate)
					|| (($this->category->publish_down != $nullDate) && ($this->category->publish_down < $nowDate))))
				{
					return null;
				}
			}
		}

		return $this->category;
	}

	/**
	 * Method to save the reordered nested set tree.
	 * First we save the new order values in the lft values of the changed ids.
	 * Then we invoke the table rebuild to implement the new ordering.
	 *
	 * @param   array  $idArray    Id's of rows to be reordered
	 * @param   array  $lft_array  Lft values of rows to be reordered
	 *
	 * @return   boolean  false on failuer or error, true otherwise
	 */
	public function saveorder($idArray = null, $lft_array = null)
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->saveorder($idArray, $lft_array))
		{
			$this->setError($table->getError());

			return false;
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to set featured of category.
	 *
	 * @param   int  $id     Id of category
	 * @param   int  $state  Featured state of category
	 *
	 * @return  boolean
	 */
	public function featured($id = null, $state = 0)
	{
		$db = JFactory::getDbo();

		if ($id)
		{
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__reditem_categories', 'c'))
				->set($db->quoteName('c.featured') . ' = ' . (int) $state)
				->where($db->quoteName('c.id') . ' = ' . (int) $id);
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
	 * Method to change the title & alias.
	 *
	 * @param   integer  $category_id  The id of the category.
	 * @param   string   $alias        The alias.
	 * @param   string   $title        The title.
	 *
	 * @return    array  Contains the modified title and alias.
	 *
	 * @since    12.2
	 */
	protected function generateNewTitle($category_id, $alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();

		while ($table->load(array('alias' => $alias, 'parent_id' => $category_id)))
		{
			$title = JString::increment($title);
			$alias = JString::increment($alias, 'dash');
		}

		return array($title, $alias);
	}

	/**
	 * Method to get custom field.
	 *
	 * @param   int      $catId       Category id.
	 * @param   boolean  $valuesOnly  Get only values instead of Cutomfield object.
	 *
	 * @return  array
	 */
	public function getCustomFields($catId = 0, $valuesOnly = false)
	{
		$app = RFactory::getApplication();

		if (!$catId)
		{
			$catId = $app->input->getInt('id', 0);
		}

		if ($catId)
		{
			$fieldsModel = RModel::getAdminInstance('Category_Fields', array('ignore_request' => true), 'com_reditem');
			$fieldsModel->setState('filter.catId', (int) $catId);
			$fieldsModel->setState('filter.published', 1);
			$rows   = $fieldsModel->getItems() ?: array();
			$fields = array();

			foreach ($rows as $row)
			{
				if ($row->state == 1)
				{
					if ($valuesOnly)
					{
						$fields[$row->fieldcode] = $row->value;
					}
					else
					{
						$field = ReditemHelperCustomfield::getCustomField($row->type);
						$field->bind($row);
						$fields[] = $field;
					}
				}
			}

			return $fields;
		}

		return array();
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
		$dispatcher = RFactory::getDispatcher();

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		// Bind the rules.
		if (isset($data['rules']))
		{
			$rules = new JAccessRules($data['rules']);
			$table->setRules($rules);
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));

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
		// Trigger the onContentAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));

		return true;
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
			// Remove category images
			$imageFolder = JPATH_REDITEM_MEDIA . 'images/category/' . $pk;

			if (JFolder::exists($imageFolder))
			{
				JFolder::delete($imageFolder);
			}

			// Remove category fields images
			$imageFolder = JPATH_REDITEM_MEDIA . 'images/categoryfield/' . $pk;

			if (JFolder::exists($imageFolder))
			{
				JFolder::delete($imageFolder);
			}

			// Remove files
			$fileFolder = JPATH_REDITEM_MEDIA . 'files/categoryfield/' . $pk;

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
		$query->update($db->qn('#__reditem_category_category_field_xref'))
			->set($db->qn('value') . ' = ' . $db->q($value))
			->where($db->qn('category_id') . ' = ' . (int) $id)
			->where(
				$db->qn('category_field_id') . ' = (' .
				'SELECT ' . $db->qn('id') .
				' FROM ' . $db->qn('#__reditem_category_fields') .
				' WHERE ' . $db->qn('fieldcode') . ' = ' . $db->q($fieldCode) . ')'
			);
		$db->setQuery($query)->execute();
	}

	/**
	 * Function for saving category image.
	 *
	 * @param   int     $catId        Category id.
	 * @param   array   $data         Input data.
	 * @param   array   $files        Input files.
	 * @param   string  $oldCatImage  Old category image.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function saveCategoryImage($catId, $data, $files, $oldCatImage)
	{
		$catImageFile  = $files['category_image_file'];
		$catImageValue = isset($data['category_image']) ? $data['category_image'] : '';
		$catImageMedia = isset($data['category_image_media']) ? $data['category_image_media'] : '';
		$doDelete      = false;
		$catLocation   = JPath::clean(JPATH_REDITEM_CATEGORY_IMAGES . $catId . '/');

		if (!JFolder::exists($catLocation))
		{
			JFolder::create($catLocation);
		}

		// Check if media file is set
		if (!empty($catImageMedia))
		{
			// Move media file to new location
			$image = basename($catImageMedia);

			if (JFile::copy(JPath::clean(JPATH_ROOT . '/' . $catImageMedia), $catLocation . $image))
			{
				$tmp           = explode('/', $catImageMedia);
				$catImageValue = array_pop($tmp);
				$doDelete      = true;
			}
		}
		// Check if file is uploaded instead
		elseif (!empty($catImageFile) && is_array($catImageFile) && $catImageFile['error'] == 0)
		{
			// Save file to new location
			$file = ReditemHelperFile::upload($catImageFile, $catLocation, 2, null, null, false);

			if ($file)
			{
				$catImageValue = $file['name'];
				$doDelete      = true;
			}
		}
		elseif (empty($catImageValue))
		{
			$doDelete = true;
		}

		// Delete old file if exist
		if ($doDelete)
		{
			$this->deleteCategoryImage($catId, $oldCatImage);
		}

		// Update category value
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->update($db->qn('#__reditem_categories'))
			->set($db->qn('category_image') . ' = ' . $db->q($catImageValue))
			->where($db->qn('id') . ' = ' . (int) $catId);

		return (boolean) $db->setQuery($query)->execute();
	}

	/**
	 * Function for deleting current category image.
	 *
	 * @param   int     $catId        Category id.
	 * @param   string  $oldCatImage  Old category image.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	private function deleteCategoryImage($catId, $oldCatImage)
	{
		$path = JPATH_REDITEM_CATEGORY_IMAGES . $catId . '/';

		if (empty($oldCatImage) || !JFile::exists($path . $oldCatImage) || JFile::delete($path . $oldCatImage))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
