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
 * RedITEM fields Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Fields
 * @since       2.0
 *
 */
class ReditemModelCategory_Fields extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_category_fields';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'category_fields_limit';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitstartField = 'auto';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  [description]
	 *
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name', 'cf.name',
				'ordering', 'cf.ordering',
				'state', 'cf,state',
				'id', 'cf.id',
				'type', 'cf.type',
				'fieldcode', 'cf.fieldcode'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   12.2
	 */
	public function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(
				$this->getState(
					'list.select',
					'cf.*'
				)
			)
			->from($db->qn('#__reditem_category_fields', 'cf'));

		$search = $this->getState('filter.search', '');

		if ($search != '')
		{
			$like = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where($db->qn('cf.name') . ' LIKE ' . $like);
		}

		$filterFieldType = $this->getState('filter.type', '');

		if (!empty($filterFieldType))
		{
			$query->where($db->qn('cf.type') . ' = ' . $db->quote($filterFieldType));
		}

		// Filter by published state
		$published = $this->getState('filter.published', '');

		if (is_numeric($published))
		{
			$query->where($db->qn('cf.state') . ' = ' . (int) $published);
		}

		// Filter by published state
		$catId = $this->getState('filter.catId', '');

		// Filter by field's fieldcode
		$filterFieldcode = $this->getState('filter.fieldcode', '');

		if ($filterFieldcode)
		{
			$query->where($db->qn('cf.fieldcode') . ' = ' . $db->quote($filterFieldcode));
		}

		if (is_numeric($catId) && $catId > 0)
		{
			$query->innerJoin(
				$db->qn('#__reditem_category_category_field_xref', 'ccfx') .
				' ON ' . $db->qn('cf.id') . ' = ' . $db->qn('ccfx.category_field_id')
			);
			$query->where($db->qn('ccfx.category_id') . ' = ' . (int) $catId);
			$query->select($db->qn('ccfx.value', 'value'));
		}

		// Get the ordering modifiers
		$orderCol = $this->state->get('list.ordering', 'cf.ordering');
		$orderDir = $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDir));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return	string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.type');
		$id	.= ':' . $this->getState('filter.published');
		$id	.= ':' . $this->getState('filter.catId');
		$id .= ':' . $this->getState('filter.fieldcode');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	public function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		$filterTypes = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type');
		$this->setState('filter.type', $filterTypes);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$catId = $this->getUserStateFromRequest($this->context . '.filter.catId', 'filter_catId', '');
		$this->setState('filter.catId', $catId);

		$filterFieldcode = $this->getUserStateFromRequest($this->context . '.filter.fieldcode', 'filter_fieldcode', '');
		$this->setState('filter.fieldcode', $filterFieldcode);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState('cf.ordering', 'asc');
	}

	/**
	 * Make connections between categories and fields.
	 *
	 * @param   array  $categories  Category ids.
	 * @param   array  $fields      Field ids.
	 *
	 * @return  bool  True on success connection, false otherwise.
	 */
	public function assign($categories, $fields)
	{
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$values = array();

		$query->select('*')
			->from($db->qn('#__reditem_category_category_field_xref'))
			->where($db->qn('category_id') . ' IN (' . implode(',', $categories) . ')');
		$xrefsDb = $db->setQuery($query)->loadAssocList();
		$xrefs   = array();

		foreach ($xrefsDb as $xref)
		{
			if (!isset($xrefs[$xref['category_id']]))
			{
				$xrefs[$xref['category_id']] = array($xref['category_field_id']);
			}
			else
			{
				$xrefs[$xref['category_id']][] = $xref['category_field_id'];
			}
		}

		foreach ($categories as $category)
		{
			foreach ($fields as $field)
			{
				if (!in_array($field, $xrefs[$category]))
				{
					$values[] = $category . ',' . $field;
				}
			}
		}

		if (!empty($values))
		{
			$query->clear()
				->insert($db->qn('#__reditem_category_category_field_xref'))
				->columns(
					array (
						$db->qn('category_id'),
						$db->qn('category_field_id')
					)
				)
				->values($values);

			if ($db->setQuery($query)->execute() === false)
			{
				return false;
			}
		}

		return true;
	}
}
