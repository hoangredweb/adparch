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
 * Category table
 *
 * @package     RedITEM.Backend
 * @subpackage  Table
 * @since       0.9.1
 */
class ReditemTableCategory extends RTableNested
{
	/**
	 * The name of the table with category
	 *
	 * @var   string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reditem_categories';

	/**
	 * The primary key of the table
	 *
	 * @var   string
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
	 * Category id.
	 *
	 * @var  int
	 * @since 2.5.0
	 */
	public $id;

	/**
	 * Category asset id.
	 * Foreign key value to assets table.
	 *
	 * @var  int
	 * @since 2.5.0
	 */
	public $asset_id;

	/**
	 * Category title.
	 *
	 * @var  string
	 * @since 2.5.0
	 */
	public $title;

	/**
	 * Category image.
	 *
	 * @var   string
	 * @since 2.5.1
	 */
	public $category_image;

	/**
	 * Category path.
	 * Used in link determination.
	 *
	 * @var  string
	 * @since 2.5.0
	 */
	public $path;

	/**
	 * Category intro text.
	 *
	 * @var  string
	 * @since 2.5.0
	 */
	public $introtext;

	/**
	 * Category full text.
	 *
	 * @var  string
	 * @since 2.5.0
	 */
	public $fulltext;

	/**
	 * Category template id.
	 * Foreign key value to templates table.
	 *
	 * @var  int
	 * @since 2.5.0
	 */
	public $template_id;

	/**
	 * Category featured state.
	 *
	 * @var  int  0|1
	 * @since 2.5.0
	 */
	public $featured;

	/**
	 * Category ordering value.
	 *
	 * @var  int
	 * @since 2.5.0
	 */
	public $ordering;

	/**
	 * Category published state.
	 *
	 * @var  int  0|1
	 * @since 2.5.0
	 */
	public $published;

	/**
	 * Published up (until) date.
	 *
	 * @var  string  datetime
	 * @since 2.5.0
	 */
	public $publish_up;

	/**
	 * Published down date.
	 *
	 * @var  string datetime
	 * @since 2.5.0
	 */
	public $publish_down;

	/**
	 * User which checked out the category.
	 * Foreign key value to users table.
	 *
	 * @var  int
	 * @since 2.5.0
	 */
	public $checked_out;

	/**
	 * Checked out time.
	 *
	 * @var  string  datetime
	 * @since 2.5.0
	 */
	public $checked_out_time;

	/**
	 * User which created the category.
	 * Foreign key value to users table.
	 *
	 * @var  int
	 * @since 2.5.0
	 */
	public $created_user_id;

	/**
	 * Time when category was created.
	 *
	 * @var  string  datetime
	 * @since 2.5.0
	 */
	public $created_time;

	/**
	 * Last user which modified the category.
	 *
	 * @var  int
	 * @since 2.5.0
	 */
	public $modified_user_id;

	/**
	 * Last modification time.
	 *
	 * @var  string  datetime
	 * @since 2.5.0
	 */
	public $modified_time;

	/**
	 * Category params.
	 *
	 * @var  array
	 * @since 2.5.0
	 */
	public $params;

	/**
	 * Related categories.
	 *
	 * @var  array
	 * @since 2.5.0
	 */
	protected $related_categories = array();

	/**
	 * Category custom fields array.
	 *
	 * @var  array
	 * @since 2.5.0
	 */
	protected $fields = array();

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		$date  = ReditemHelperSystem::getDateWithTimezone();
		$user  = ReditemHelperSystem::getUser();
		$isNew = false;

		if (!$this->id)
		{
			$isNew = true;
		}

		// Prepare for publish_up & publish_down fields
		if ($this->published == 1 && (int) $this->publish_up == 0)
		{
			$this->publish_up = ReditemHelperSystem::getDateWithTimezone()->toSql();
		}

		if ($this->published == 1 && intval($this->publish_down) == 0)
		{
			$this->publish_down = '0000-00-00 00:00:00';
		}

		// Generate category alias
		$this->alias = $this->generateAlias();

		if ($isNew)
		{
			// New category
			$this->created_time    = $date->toSql();
			$this->created_user_id = $user->get('id');
		}
		else
		{
			// Existing category
			$this->modified_time    = $date->toSql();
			$this->modified_user_id = $user->get('id');
		}

		if (!parent::store($updateNulls))
		{
			return false;
		}

		if (!empty($this->fields) && !$this->storeFields())
		{
			return false;
		}

		// Add related categories
		if (!$this->storeRelatedCategories())
		{
			return false;
		}

		return true;
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
			// After successful load, load related categories as well.
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn('related_id'))
				->from($db->qn('#__reditem_category_related'))
				->where($db->qn('parent_id') . ' = ' . (int) $this->id);
			$this->relatedCategories = $db->setQuery($query, 0, 1)->loadColumn();

			return true;
		}

		return false;
	}

	/**
	 * Process related categories
	 *
	 * @return  boolean
	 */
	private function storeRelatedCategories()
	{
		$db    = RFactory::getDbo();
		$query = $db->getQuery(true)
			->delete($db->qn('#__reditem_category_related'))
			->where(
				array(
					$db->qn('parent_id') . ' =  ' . (int) $this->id,
					$db->qn('related_id') . ' = ' . (int) $this->id
				),
				'OR'
			);
		$db->setQuery($query);

		if ($db->execute())
		{
			if (empty($this->related_categories))
			{
				return true;
			}

			// Insert new related categories reference
			$query->clear()
				->insert($db->qn('#__reditem_category_related'))
				->columns($db->qn(array('related_id', 'parent_id')));

			foreach ($this->related_categories as $rid)
			{
				$query->values($db->q($this->id) . ',' . $db->q($rid));
				$query->values($db->q($rid) . ',' . $db->q($this->id));
			}

			$db->setQuery($query);

			if ($db->execute())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Function to check and generate new category alias.
	 *
	 * @return string
	 */
	private function generateAlias()
	{
		// Remove spaces from alias
		if (!empty($this->alias))
		{
			$alias = trim($this->alias);
		}
		// If alias is empty. Create alias from the title
		else
		{
			$alias = trim($this->title);
		}

		// Make sef URL for alias
		$alias       = JFilterOutput::stringURLSafe($alias);
		$aliasExists = $this->checkAlias($alias, $this->id);

		// If alias exists, generate new one (increment)
		while ($aliasExists)
		{
			$alias       = JString::increment($alias, 'dash');
			$aliasExists = $this->checkAlias($alias, $this->id);
		}

		return $alias;
	}

	/**
	 * Check alias exist in database
	 *
	 * @param   string  $alias  Alias string.
	 * @param   int     $id     Id of category to skip in results.
	 *
	 * @return  boolean  True if exist. False otherwise.
	 */
	private function checkAlias($alias, $id = null)
	{
		$db    = RFactory::getDbo();
		$query = $db->getQuery(true)
			->select('count(*) AS ' . $db->qn('count'))
			->from($db->qn('#__reditem_categories', 'c'))
			->where($db->qn('c.alias') . ' = ' . $db->quote($alias));

		if ($id)
		{
			$query->where($db->qn('c.id') . ' <> ' . $db->quote($id));
		}

		$db->setQuery($query);
		$result = $db->loadObject();

		if (($result) && ($result->count))
		{
			return true;
		}

		return false;
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

		return 'com_reditem.category.' . (int) $this->$key;
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
		$asset    = JTable::getInstance('Asset');
		$parentId = (int) $this->parent_id;

		// Try to inherit permissions from parent category
		if ($parentId && $asset->loadByName('com_reditem.category.' . $parentId))
		{
			return $asset->id;
		}

		// Try to inherit permissions from component
		if ($asset->loadByName('com_reditem'))
		{
			return $asset->id;
		}

		return parent::_getAssetParentId($table, $id);
	}

	/**
	 * Store category custom fields.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since 2.5.0
	 */
	private function storeFields()
	{
		$categoryId = $this->id;
		$db         = $this->getDbo();
		$query      = $db->getQuery(true);
		$values     = ReditemHelperCustomfield::processFieldsDataForStore($this->fields);
		$fieldCodes = array_keys($values);
		$table      = RTable::getInstance('Category_Category_Field_Xref', 'ReditemTable');
		$query->select($db->qn('xref.category_field_id'))
			->from($db->qn('#__reditem_category_category_field_xref', 'xref'))
			->innerJoin($db->qn('#__reditem_category_fields', 'cf') . ' ON ' . $db->qn('xref.category_field_id') . ' = ' . $db->qn('cf.id'))
			->where($db->qn('xref.category_id') . ' = ' . (int) $categoryId)
			->where($db->qn('cf.type') . ' IN (\'checkbox\', \'radio\', \'select\')');
		$clearableFields = $db->setQuery($query)->loadColumn();

		if (empty($this->fields))
		{
			return true;
		}

		$escapedFieldCodes = ReditemHelperDatabase::filterString($fieldCodes);
		$query->clear()
			->select(
				array(
					$db->qn('id'),
					$db->qn('fieldcode')
				)
			)
			->from($db->qn('#__reditem_category_fields'))
			->where($db->qn('fieldcode') . ' IN (' . implode(',', $escapedFieldCodes) . ')');
		$ids     = $db->setQuery($query)->loadAssocList('fieldcode');
		$updated = array();

		foreach ($fieldCodes as $fieldCode)
		{
			$table->reset();
			$src = array(
				'category_id'       => $categoryId,
				'category_field_id' => $ids[$fieldCode]['id'],
				'value'             => $values[$fieldCode]
			);

			if (!$table->save($src))
			{
				return false;
			}

			$updated[] = $ids[$fieldCode]['id'];
		}

		// Clear missing values if we are updating the category
		if (!empty($clearableFields))
		{
			$diff = array_diff($clearableFields, $updated);

			foreach ($diff as $fieldId)
			{
				$table->reset();
				$src = array(
					'category_id'       => $categoryId,
					'category_field_id' => $fieldId,
					'value'             => ''
				);

				if (!$table->save($src))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to recursive update published column for children rows.
	 *
	 * this method is override for fix category not be unpublished evertime we save + children
	 *
	 * @param   integer  $pk        Id number of row which published column was changed.
	 * @param   integer  $newState  An optional value for published column of row identified by $pk.
	 *
	 * @return  boolean  True on success.
	 */
	protected function recursiveUpdatePublishedColumn($pk, $newState = null)
	{
		return true;
	}
}
