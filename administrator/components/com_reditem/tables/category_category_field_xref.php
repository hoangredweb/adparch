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
 * Xref table between an category and category field.
 *
 * @package     RedITEM.Backend
 * @subpackage  Table
 * @since       2.2.0
 */
class ReditemTableCategory_Category_Field_Xref extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 */
	protected $_tableName = 'reditem_category_category_field_xref';
	/**
	 * The primary key of the table
	 *
	 * @var string
	 */
	protected $_tableKey = array('category_id', 'category_field_id');

	/**
	 * Category id.
	 *
	 * @var  int
	 */
	public $category_id;

	/**
	 * Category field id.
	 *
	 * @var int
	 */
	public $category_field_id;

	/**
	 * Field value.
	 *
	 * @var  mixed
	 */
	public $value;

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		if (is_array($this->value))
		{
			$this->value = json_encode(array_values($this->value));
		}

		return parent::store($updateNulls);
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	public function load($keys = null, $reset = true)
	{
		if (parent::load($keys, $reset))
		{
			if ($value = ReditemHelperCustomfield::isJsonValue($this->value))
			{
				$this->value = $value;
			}

			return true;
		}

		return false;
	}

	/**
	 * Method to reset class properties to the defaults set in the class
	 * definition. It will ignore the primary key as well as any private class
	 * properties (except $_errors).
	 *
	 * @return  void
	 *
	 * @link    https://docs.joomla.org/JTable/reset
	 * @since   11.1
	 */
	public function reset()
	{
		$this->category_id       = null;
		$this->category_field_id = null;
		$this->value             = null;

		// Reset table errors
		$this->_errors = array();
	}
}
