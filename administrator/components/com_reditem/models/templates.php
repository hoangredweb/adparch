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
 * RedITEM templates Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.templates
 * @since       0.9.1
 *
 */
class ReditemModelTemplates extends ReditemModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_templates';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'templates_limit';

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
				't.name', 'name',
				't.ordering', 'ordering',
				't.published', 'published',
				't.typecode', 'typecode',
				't.id', 'id',
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
	 * @return JDatabaseQuery A JDatabaseQuery object
	 */
	public function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
				'list.select',
				't.*, ty.title AS type_name'
			)
		);
		$query->from($db->qn('#__reditem_templates', 't'));
		$query->leftJoin($db->qn('#__reditem_types', 'ty') . ' ON ' . $db->qn('t.type_id') . ' = ' . $db->qn('ty.id'));

		// Filter: like / search
		$search = $this->getState('filter.search', '');

		if ($search != '')
		{
			$like = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where($db->qn('t.name') . ' LIKE ' . $like);
		}

		// Filter: types
		$filter_type = $this->getState('filter.filter_types', 0);

		if ($filter_type)
		{
			$query->where($db->qn('t.type_id') . ' = ' . $db->quote($filter_type));
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->qn('t.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(' . $db->qn('t.published') . ' IN (0, 1))');
		}

		// Filter: section
		$filterSection = $this->getState('filter.section', '');

		if (!empty($filterSection))
		{
			$query->where($db->qn('t.typecode') . ' = ' . $db->quote($filterSection));
		}

		// Get the ordering modifiers
		$orderCol	= $this->state->get('list.ordering', 't.name');
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
		$id	.= ':' . $this->getState('filter.published');
		$id	.= ':' . $this->getState('filter.filter_types');
		$id	.= ':' . $this->getState('filter.section');

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
	protected function populateState($ordering = 't.ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		$filterTypes = $this->getUserStateFromRequest($this->context . '.filter.types', 'filter_types');
		$this->setState('filter.filter_types', $filterTypes);

		$filterSection = $this->getUserStateFromRequest($this->context . '.filter.section', 'filter_section');
		$this->setState('filter.section', $filterSection);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState($ordering, $direction);
	}
}
