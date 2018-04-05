<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Type table
 *
 * @package     RedITEM.Backend
 * @subpackage  Table
 * @since       0.9.1
 */
class ReditemTableType extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reditem_types';

	/**
	 * The primary key of the table
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableKey = 'id';

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		$db         = RFactory::getDBO();
		$input      = RFactory::getApplication()->input;
		$dispatcher = RFactory::getDispatcher();
		$isNew      = false;
		JPluginHelper::importPlugin('reditem');

		// Run event 'onBeforeTypeSave'
		$dispatcher->trigger('onBeforeTypeSave', array($this, $input));

		if (!$this->id)
		{
			$isNew = true;
		}

		if (!parent::store($updateNulls))
		{
			return false;
		}

		// Create table for this type if this is new type
		if ($isNew)
		{
			$tableName = str_replace('-', '_', JFilterOutput::stringURLSafe($this->title) . '_' . $this->id);

			// Update table name
			$query = $db->getQuery(true);
			$query->update($db->qn('#__reditem_types', 't'))
				->set($db->qn('t.table_name') . '=' . $db->quote($tableName))
				->where($db->qn('t.id') . '=' . (int) $this->id);
			$db->setQuery($query);
			$db->execute();

			// Create table for this type
			$queryTable = 'CREATE TABLE IF NOT EXISTS ' . $db->qn('#__reditem_types_' . $tableName) . ' ( ';
			$queryTable .= $db->qn('id') . ' int(11) NOT NULL DEFAULT ' . $db->quote(0) . ',';
			$queryTable .= 'PRIMARY KEY (' . $db->qn('id') . '),';
			$queryTable .= 'CONSTRAINT ' . $db->qn('#__reditem_types_' . $tableName . '_fk') . ' ';
			$queryTable .= 'FOREIGN KEY (' . $db->qn('id') . ') REFERENCES ' . $db->qn('#__reditem_items') . ' (' . $db->qn('id') . ') ';
			$queryTable .= 'ON DELETE CASCADE ';
			$queryTable .= 'ON UPDATE CASCADE ';
			$queryTable .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';

			$db->setQuery($queryTable);

			if (!$db->execute())
			{
				return false;
			}
		}

		// Run event 'onAfterTypeSave'
		$dispatcher->trigger('onAfterTypeSave', array($this, $isNew));

		return true;
	}

	/**
	 * Deletes this row in database (or if provided, the row of key $pk)
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null)
	{
		$db = RFactory::getDBO();

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_reditem/tables');
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem');

		// Run event 'onBeforeTypeDelete'
		$dispatcher->trigger('onBeforeTypeDelete', array($this));

		// Delete all custom fields belong to this type
		$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
		$fieldsModel->setState('list.select', 'f.id');
		$fieldsModel->setState('filter.types', $this->id);
		$fields = $fieldsModel->getItems();

		if ($fields)
		{
			$fieldTable = JTable::getInstance('Field', 'ReditemTable', array('ignore_request' => true));

			foreach ($fields as $field)
			{
				$fieldTable->load($field->id);
				$fieldTable->delete();
			}
		}

		// Delete all items belong to this type
		$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
		$itemsModel->setState('list.select', 'i.id');
		$itemsModel->setState('filter.filter_types', $this->id);
		$items = $itemsModel->getItems();

		if ($items)
		{
			$itemTable = JTable::getInstance('Item', 'ReditemTable', array('ignore_request' => true));

			foreach ($items as $item)
			{
				$itemTable->load($item->id);
				$itemTable->delete();
			}
		}

		// Delete all templates belong to this type
		$templatesModel = RModel::getAdminInstance('Templates', array('ignore_request' => true), 'com_reditem');
		$templatesModel->setState('list.select', 't.id');
		$templatesModel->setState('filter.filter_types', $this->id);
		$templates = $templatesModel->getItems();

		if ($templates)
		{
			$templateTable = JTable::getInstance('Template', 'ReditemTable', array('ignore_request' => true));

			foreach ($templates as $template)
			{
				$templateTable->load($template->id);
				$templateTable->delete();
			}
		}

		// Delete table of this type
		$q = 'DROP TABLE IF EXISTS ' . $db->qn('#__reditem_types_' . $this->table_name) . ';';
		$db->setQuery($q);

		if (!$db->execute())
		{
			$this->setError('Cannot delete table: ' . $db->qn('#__reditem_types_' . $this->table_name));

			return false;
		}

		if (!parent::delete($pk))
		{
			return false;
		}

		// Run event 'onAfterTypeDelete'
		$dispatcher->trigger('onAfterTypeDelete', array($this));

		return true;
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		$key = $this->_tbl_key;

		return 'com_reditem.type.' . (int) $this->$key;
	}

	/**
	 * Method to get the parent asset under which to register this one.
	 * By default, all assets are registered to the ROOT node with ID,
	 * which will default to 1 if none exists.
	 * The extended class can define a table and id to lookup.  If the
	 * asset does not exist it will be created.
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     Id to look up
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');

		if ($asset->loadByName('com_reditem'))
		{
			return $asset->id;
		}

		return parent::_getAssetParentId($table, $id);
	}
}
