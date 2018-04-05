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
 * Custom CategoryField table
 *
 * @package     RedITEM.Backend
 * @subpackage  Table
 * @since       2.2.0
 */
class ReditemTableCategory_Field extends RTable
{
	/**
	 * Category field id.
	 *
	 * @var  int
	 */
	public $id;

	/**
	 * Category field default value.
	 *
	 * @var  mixed
	 */
	public $default;

	/**
	 * Category field ordering value.
	 *
	 * @var  int
	 */
	public $ordering;

	/**
	 * Category field published status.
	 *
	 * @var  int
	 */
	public $published;

	/**
	 * Category field name.
	 *
	 * @var  string
	 */
	public $name;

	/**
	 * Category field options.
	 * Used for radio, select & checkbox options.
	 * Formatted as JSON array.
	 *
	 * @var  string
	 */
	public $options;

	/**
	 * Category field code for template usage.
	 *
	 * @var  string
	 */
	public $fieldcode;

	/**
	 * Category field checked out user id.
	 *
	 * @var  int
	 */
	public $checked_out;

	/**
	 * Category field checked out datetime value.
	 *
	 * @var  string
	 */
	public $checked_out_time;

	/**
	 * Category field bit value for allowing
	 * search by its value in frontend.
	 *
	 * @var  int
	 */
	public $searchable_in_frontend;

	/**
	 * Category field bit value for allowing
	 * search by its value in backend.
	 *
	 * @var  int
	 */
	public $searchable_in_backend;

	/**
	 * Category field params. Used for other settings.
	 * JParams formatted string.
	 *
	 * @var  string
	 */
	public $params;

	/**
	 * Categories array to connect to the field.
	 *
	 * @var  array
	 */
	protected $categories;

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		$this->_tableName = 'reditem_category_fields';
		$this->_tbl_key   = 'id';

		parent::__construct($db);
	}

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		$isNew  = false;
		$db     = $this->getDbo();
		$query  = $db->getQuery(true)
				->select($db->qn('fieldcode'))
				->from($db->qn('#__reditem_category_fields'));
		$fieldCodes = $db->setQuery($query)->loadColumn();

		if (empty($this->id))
		{
			$isNew = true;
		}

		if (empty($this->fieldcode) || $isNew)
		{
			$fieldCode = trim($this->name);
			$fieldCode = JFilterOutput::stringURLSafe($fieldCode);

			while (in_array($fieldCode, $fieldCodes))
			{
				$fieldCode = JString::increment($fieldCode, 'dash');
			}

			$this->fieldcode = str_replace('-', '_', $fieldCode);
		}

		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		if (!empty($this->categories) && is_array($this->categories))
		{
			$query->delete($db->qn('#__reditem_category_category_field_xref'))
				->where($db->qn('category_field_id') . ' = ' . (int) $this->id);

			if ($db->setQuery($query)->execute())
			{
				$return = parent::store($updateNulls);
				$values = array();

				foreach ($this->categories as $category)
				{
					$values[] = $db->q($category) . ',' . $db->q($this->id);
				}

				$query->clear()
					->insert($db->qn('#__reditem_category_category_field_xref'))
					->columns(
						array (
							$db->qn('category_id'),
							$db->qn('category_field_id')
						)
					)
					->values($values);

				$db->setQuery($query)->execute();

				return $return;
			}
		}
		else
		{
			return parent::store($updateNulls);
		}

		return false;
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
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			$query->select($db->qn('category_id'))
				->from($db->qn('#__reditem_category_category_field_xref'))
				->where($db->qn('category_field_id') . ' = ' . (int) $this->id);
			$this->categories = json_encode($db->setQuery($query)->loadColumn());

			return true;
		}

		return false;
	}
}
