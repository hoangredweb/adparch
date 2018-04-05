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
 * RedITEM categories Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Types
 * @since       0.9.1
 *
 */
class ReditemModelTypes extends ReditemModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_types';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'types_limit';

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
				'ty.title', 'ty.id', 'ty.ordering'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is constructed only once for a given state of the model.
	 *
	 * @return JDatabaseQuery A JDatabaseQuery object
	 */
	public function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
				'list.select',
				'ty.*'
			)
		);
		$query->from($db->qn('#__reditem_types', 'ty'));

		// Filter: like / search
		$search = $this->getState('filter.search', '');

		if ($search != '')
		{
			$like = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where($db->qn('ty.title') . ' LIKE ' . $like);
		}

		// Filter by type ID
		$id = $this->getState('filter.id', 0);

		if ($id)
		{
			$query->where($db->qn('ty.id') . ' = ' . $db->quote($id));
		}

		// Filter by type exclude
		$excludes = $this->getState('filter.exclude');

		if ($excludes)
		{
			if (!is_array($excludes))
			{
				$excludes = array($excludes);
			}

			JArrayHelper::toInteger($excludes);
			$query->where($db->qn('ty.id') . ' NOT IN (' . implode(',', $excludes) . ')');
		}

		// Filter by field params
		$typeParams = $this->getState('filter.params', null);

		if (!empty($typeParams) && is_array($typeParams))
		{
			foreach ($typeParams as $paramKey => $paramValue)
			{
				$query->where($db->qn('ty.params') . ' LIKE ' . $db->quote('%"' . $paramKey . '":"' . $paramValue . '"%'));
			}
		}

		// Get the ordering modifiers
		$orderCol	= $this->state->get('list.ordering', 'ty.ordering');
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
	protected function populateState($ordering = 'ty.ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter_search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState($ordering, $direction);
	}
}
