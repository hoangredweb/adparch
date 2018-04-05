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
class ReditemModelFields extends ReditemModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_fields';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'fields_limit';

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
				'name', 'f.name',
				'ordering', 'f.ordering',
				'published', 'f.published',
				'id', 'f.id',
				'type_id', 'f.type_id',
				'type', 'f.type',
				'fieldcode', 'f.fieldcode',
				'type_name'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is constructed only once for a given state of the model.
	 *
	 * @param   bool  $newQuery  use new query or not to improve performance
	 *
	 * @return JDatabaseQuery A JDatabaseQuery object
	 */
	public function getListQuery($newQuery = true)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery($newQuery);

		// Clear old query
		if (!$newQuery)
		{
			$query->clear();
		}

		$query->select(
			$this->getState(
				'list.select',
				'f.*, ty.title AS type_name, ty.table_name as table_name'
			)
		);
		$query->from($db->qn('#__reditem_fields', 'f'));
		$query->leftJoin($db->qn('#__reditem_types', 'ty') . ' ON ' . $db->qn('f.type_id') . ' = ' . $db->qn('ty.id'));

		// Filter: like / search
		$search = $this->getState('filter.search', '');

		if ($search != '')
		{
			$like = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where($db->qn('f.name') . ' LIKE ' . $like);
		}

		// Filter: types
		$filterTypes = $this->getState('filter.types', 0);

		if (!empty($filterTypes))
		{
			if (is_string($filterTypes))
			{
				$query->where($db->qn('f.type_id') . ' IN (' . $db->q($filterTypes) . ')');
			}
			else
			{
				$query->where($db->qn('f.type_id') . ' = ' . (int) $filterTypes);
			}
		}

		// Filter: fieldtypes
		$filter_fieldtype = $this->getState('filter.fieldtypes');

		if ($filter_fieldtype)
		{
			$query->where($db->qn('f.type') . ' = ' . $db->quote($filter_fieldtype));
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->qn('f.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(' . $db->qn('f.published') . ' IN (0, 1))');
		}

		// Filter by field params
		$fieldParams = $this->getState('filter.params', null);

		if (($fieldParams) && is_array($fieldParams) && (is_numeric($filterType)))
		{
			foreach ($fieldParams as $paramKey => $paramValue)
			{
				$query->where($db->qn('f.params') . ' LIKE ' . $db->quote('%"' . $paramKey . '":"' . $paramValue . '"%'));
			}
		}

		// Filter by field's fieldcode
		$filterFieldcode = $this->getState('filter.fieldcode', '');

		if ($filterFieldcode)
		{
			$query->where($db->qn('f.fieldcode') . ' = ' . $db->quote($filterFieldcode));
		}

		// Filter by field's searchable in backend
		$filterSearchableInBackend = $this->getState('filter.searchableInBackend', null);

		if (is_numeric($filterSearchableInBackend))
		{
			$query->where($db->qn('searchable_in_backend') . ' = ' . (int) $filterSearchableInBackend);
		}
		else
		{
			$query->where('(' . $db->qn('searchable_in_backend') . ' IN (0, 1))');
		}

		// Filter by field's searchable in frontend
		$filterSearchableInFrontend = $this->getState('filter.searchableInFrontend', null);

		if (is_numeric($filterSearchableInFrontend))
		{
			$query->where($db->qn('searchable_in_frontend') . ' = ' . (int) $filterSearchableInFrontend);
		}
		else
		{
			$query->where('(' . $db->qn('searchable_in_frontend') . ' IN (0, 1))');
		}

		// Filter by field's backend filter
		$backendFilter = $this->getState('filter.backendFilter', null);

		if (is_numeric($backendFilter))
		{
			$query->where($db->qn('backend_filter') . ' = ' . (int) $backendFilter);
		}
		else
		{
			$query->where('(' . $db->qn('backend_filter') . ' IN (0, 1))');
		}

		// Get the ordering modifiers
		$orderCol	= $this->state->get('list.ordering', 'f.ordering');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

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
		$id .= ':' . $this->getState('filter.types');
		$id	.= ':' . $this->getState('filter.fieldtypes');
		$id	.= ':' . $this->getState('filter.fieldcode');
		$id	.= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.searchableInBackend');
		$id .= ':' . $this->getState('filter.searchableInFrontend');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   [description]
	 * @param   string  $direction  [description]
	 *
	 * @return  void
	 */
	public function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		$filterTypes = $this->getUserStateFromRequest($this->context . '.filter.types', 'filter_types');
		$this->setState('filter.types', $filterTypes);

		$filterFieldTypes = $this->getUserStateFromRequest($this->context . '.filter.fieldtypes', 'filter_fieldtypes');
		$this->setState('filter.fieldtypes', $filterFieldTypes);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$filterParams = $this->getUserStateFromRequest($this->context . '.filter.params', 'filter_params');
		$this->setState('filter.params', $filterParams);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState('f.ordering', 'asc');
	}
}
