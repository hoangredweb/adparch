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
class ReditemTableItem extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reditem_items';

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
	 * Event to run before Item store.
	 *
	 * @var  string
	 */
	protected $_eventBeforeStore = 'onBeforeItemSave';

	/**
	 * Event name to trigger after store().
	 *
	 * @var  string
	 */
	protected $_eventAfterStore = 'onAfterItemSave';

	/**
	 * Event name to trigger after delete().
	 *
	 * @var  string
	 */
	protected $_eventAfterDelete = 'onAfterItemDelete';

	/**
	 * An array of plugin types to import.
	 *
	 * @var  array
	 */
	protected $_pluginTypesToImport = array('reditem');

	/**
	 * Item id.
	 *
	 * @var  int
	 */
	public $id;

	/**
	 * Item asset id.
	 * Foreign key value to assets table.
	 *
	 * @var  int
	 */
	public $asset_id;

	/**
	 * Item title.
	 *
	 * @var  string
	 */
	public $title;

	/**
	 * Item alias.
	 *
	 * @var  string.
	 */
	public $alias;

	/**
	 * Ordering value.
	 *
	 * @var  int
	 */
	public $ordering;

	/**
	 * Item published state.
	 *
	 * @var  int  0|1
	 */
	public $published;

	/**
	 * Published up (until) date.
	 *
	 * @var  string  datetime
	 */
	public $publish_up;

	/**
	 * Published down date.
	 *
	 * @var  string datetime
	 */
	public $publish_down;

	/**
	 * Item blocking state.
	 *
	 * @var  int
	 */
	public $blocked;

	/**
	 * Item featured state.
	 *
	 * @var  int
	 */
	public $featured;

	/**
	 * Item type id.
	 * Foreign key value to types table.
	 *
	 * @var  int
	 */
	public $type_id;

	/**
	 * Item template id.
	 * Foreign key value to template table.
	 *
	 * @var  int
	 */
	public $template_id;

	/**
	 * User which checked out the item.
	 * Foreign key value to users table.
	 *
	 * @var  int
	 */
	public $checked_out;

	/**
	 * Checked out time.
	 *
	 * @var  string  datetime
	 */
	public $checked_out_time;

	/**
	 * User which created the item.
	 * Foreign key value to users table.
	 *
	 * @var  int
	 */
	public $created_user_id;

	/**
	 * Time when category was created.
	 *
	 * @var  string  datetime
	 */
	public $created_time;

	/**
	 * Last user which modified the item.
	 *
	 * @var  int
	 */
	public $modified_user_id;

	/**
	 * Last modification time.
	 *
	 * @var  string  datetime
	 */
	public $modified_time;

	/**
	 * Item params.
	 *
	 * @var  array
	 */
	public $params;

	/**
	 * Item custom fields array.
	 *
	 * @var  array
	 */
	protected $fields = array();

	/**
	 * Item categories array.
	 *
	 * @var  array
	 */
	protected $categories;

	/**
	 * Current displayed fields for edit.
	 *
	 * @var  array
	 */
	protected $fields_to_edit;

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', 'ReditemTableItem', array('typeAlias' => 'com_reditem.item'));

		parent::__construct($db);
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
		$date  = ReditemHelperSystem::getDateWithTimezone();
		$user  = ReditemHelperSystem::getUser();
		$isNew = false;

		if (!$this->id)
		{
			$isNew = true;
		}

		// Fix related items storing
		$params = new JRegistry($this->params);
		$rItems = $params->get('related_items', '');

		if (!empty($rItems) && is_string($rItems))
		{
			$params->set('related_items', json_decode($rItems));
			$this->params = $params->toString();
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

		return parent::store($updateNulls);
	}

	/**
	 * Called after store().
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterStore($updateNulls = false)
	{
		if (!empty($this->fields) && !$this->storeFields())
		{
			return false;
		}

		// Add item-categories xref
		if (!empty($this->categories) && !$this->saveCategoriesXref())
		{
			return false;
		}
		elseif (empty($this->categories) && $this->id && !$this->deleteCategoriesXref())
		{
			return false;
		}

		return parent::afterStore($updateNulls);
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.
	 *                            If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success; false if $pks is empty.
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem');

		foreach ($pks as $pk)
		{
			$this->reset();

			// Run event 'onAfterItemPublished'
			if ($this->load($pk) && ($state == 1))
			{
				$dispatcher->trigger('onAfterItemPublished', array($this));
			}

			// Run event 'onAfterItemUnpublished'
			if ($this->load($pk) && ($state == 0))
			{
				$dispatcher->trigger('onAfterItemUnpublished', array($this));
			}
		}

		return parent::publish($pks, $state, $userId);
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
			->from($db->qn('#__reditem_items', 'i'))
			->where($db->qn('i.alias') . ' = ' . $db->quote($alias));

		if ($id)
		{
			$query->where($db->qn('i.id') . ' <> ' . $db->quote($id));
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
	 * Create categories xref for the item.
	 *
	 * @return  boolean  True on success.
	 */
	public function saveCategoriesXref()
	{
		if (!empty($this->categories) && $this->deleteCategoriesXref())
		{
			$table = RTable::getInstance('Item_Category_Xref', 'ReditemTable');

			foreach ($this->categories as $categoryId)
			{
				// Don't allow storing ROOT category
				if ($categoryId > 1)
				{
					$table->reset();
					$src = array(
						'item_id'     => $this->id,
						'category_id' => $categoryId,
					);

					if (!$table->save($src))
					{
						return false;
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Delete all item - category xrefs function.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function deleteCategoriesXref()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->delete($db->qn('#__reditem_item_category_xref'))
			->where($db->qn('item_id') . ' = ' . (int) $this->id);

		$db->setQuery($query)->execute();

		if (!$db->setQuery($query)->execute())
		{
			return false;
		}

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

		return 'com_reditem.item.' . (int) $this->$key;
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
		$asset  = JTable::getInstance('Asset');
		$typeId = (int) $this->type_id;

		// 1. Get parent asset from type of this item
		if ($typeId && $asset->loadByName('com_reditem.type.' . $typeId))
		{
			return $asset->id;
		}

		// 2. Get root id
		return parent::_getAssetParentId($table, $id);
	}

	/**
	 * Store item custom fields.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	private function storeFields()
	{
		if (empty($this->fields))
		{
			return true;
		}

		$db         = $this->getDbo();
		$query      = $db->getQuery(true);
		$tableName  = ReditemHelperType::getTableName($this->type_id);
		$newValues  = ReditemHelperCustomfield::processFieldsDataForStore($this->fields);
		$editFields = json_decode($this->fields_to_edit, true);

		// Get old values
		$query->select('*')
			->from($db->qn($tableName))
			->where($db->qn('id') . ' = ' . (int) $this->id);
		$values = $db->setQuery($query, 0, 1)->loadAssoc();

		if (!empty($values))
		{
			unset($values['id']);

			// Merge values
			foreach ($values as $fieldCode => $value)
			{
				// Check if field is sent for edit
				if (in_array($fieldCode, $editFields))
				{
					if (empty($newValues[$fieldCode]))
					{
						$values[$fieldCode] = '';
					}
					else
					{
						$values[$fieldCode] = $newValues[$fieldCode];
					}
				}
			}

			$query->clear()
				->update($db->qn($tableName))
				->where($db->qn('id') . ' = ' . (int) $this->id);

			foreach ($values as $column => $value)
			{
				$query->set($db->qn($column) . ' = ' . $db->q($value));
			}

			if (!$db->setQuery($query)->execute())
			{
				return false;
			}
		}
		else
		{
			$newValues['id'] = (int) $this->id;
			$columns         = array();
			$values          = array();

			foreach ($newValues as $column => $value)
			{
				$columns[] = $db->qn($column);
				$values[]  = $db->q($value);
			}

			$query->clear()
				->insert($db->qn($tableName))
				->columns($columns)
				->values(implode(',', $values));

			if (!$db->setQuery($query)->execute())
			{
				return false;
			}
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
		parent::load($keys, $reset);

		$this->categories = array();

		if (!class_exists('ReditemHelperItem', true))
		{
			JLoader::import('reditem.helper.item');
		}

		$categories = ReditemHelperItem::getCategories($this->id);

		if (isset($categories[$this->id]))
		{
			$this->categories = $categories[$this->id];
		}

		return true;
	}
}
