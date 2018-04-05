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
 * Custom Field table
 *
 * @package     RedITEM.Backend
 * @subpackage  Table
 * @since       0.9.1
 */
class ReditemTableField extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reditem_fields';

	/**
	 * The primary key of the table
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableKey = 'id';

	/**
	 * Field name to publish/unpublish table registers. Ex: state
	 *
	 * @var  string
	 */
	protected $_tableFieldState = 'published';

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		parent::__construct($db);

		JObserverMapper::addObserverClassToClass(
			'JTableObserverContenthistory',
			'ReditemTableField',
			array('typeAlias' => 'com_reditem.field')
		);
	}

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		$isNew           = false;
		$fieldClass      = ReditemHelperCustomfield::getCustomField($this->type);
		$this->fieldcode = str_replace('-', '_', JFilterOutput::stringURLSafe($this->name));

		if (!$this->id)
		{
			$isNew = true;
		}

		if ($isNew)
		{
			if ($fieldClass->checkColumnExist($this->type_id, $this->fieldcode))
			{
				$this->setError(JText::_('COM_REDITEM_FIELD_ERROR_ANOTHER_FIELDNAME_EXIST'));

				return false;
			}
			elseif (!$fieldClass->insertColumn($this->type_id, $this->fieldcode))
			{
				$this->setError(JText::_('COM_REDITEM_FIELD_ERROR_CAN_NOT_ADD_COLUMN'));

				return false;
			}
		}
		else
		{
			$oldField = clone $this;
			$oldField->load($this->id);

			if ($fieldClass->checkColumnExist($this->type_id, $oldField->fieldcode))
			{
				if ($oldField->type != $this->type || $oldField->fieldcode != $this->fieldcode)
				{
					if (!$fieldClass->changeColumn($this->type_id, $this->fieldcode, $oldField->fieldcode))
					{
						$this->setError(JText::_('COM_REDITEM_FIELD_ERROR_CAN_NOT_CHANGE_COLUMN'));

						return false;
					}
				}
			}
		}

		if (!parent::store($updateNulls))
		{
			return false;
		}

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
		$db = JFactory::getDBO();
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem');

		// Run event 'onBeforeFieldDelete'
		$dispatcher->trigger('onBeforeFieldDelete', array($this));

		// Get fieldcode of field
		$q = $db->getQuery(true);

		$q->select($db->qn('fieldcode') . ', ' . $db->qn('type_id'));
		$q->from($db->qn('#__reditem_fields'));
		$q->where($db->qn('id') . ' = ' . $db->quote($this->id));
		$db->setQuery($q);

		$rs = $db->loadObject();

		$fieldcode = $rs->fieldcode;
		$type_id = $rs->type_id;

		// If "type" table exists
		if ($type_id > 0)
		{
			// Get "type" table columns
			$query = $db->getQuery(true);
			$query->select($db->qn('table_name'))
			->from('#__reditem_types')
			->where($db->qn('id') . ' = ' . $db->quote($type_id));
			$db->setQuery($query);

			$rs = $db->loadObject();

			// Check if columns exists
			$tb = '#__reditem_types_' . $rs->table_name;
			$db->setQuery('SHOW COLUMNS FROM ' . $tb);
			$cols = $db->loadObjectList('Field');

			if (array_key_exists($fieldcode, $cols))
			{
				$q = "ALTER TABLE " . $tb . " DROP " . $db->qn($fieldcode);
				$db->setQuery($q);

				if (!$db->execute())
				{
					return false;
				}
			}
		}

		if (!parent::delete($pk))
		{
			return false;
		}

		// Run event 'onAfterFieldDelete'
		$dispatcher->trigger('onAfterFieldDelete', array($this));

		return true;
	}
}
