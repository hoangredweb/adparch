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
 * RedITEM explore Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Explore
 * @since       2.1.19
 *
 */
class ReditemModelExplore extends ReditemModelList
{
	/**
	 * Context for session
	 *
	 * @var  string
	 */
	protected $context = 'com_reditem.explore';

	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_explore';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'explore_limit';

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
			$filterFields = array(
				'title',
				'ordering',
				'i.ordering',
				'author',
				'template'
			);

			$config['filter_fields'] = $filterFields;
		}

		parent::__construct($config);
	}

	/**
	 * Method to get items and categories
	 *
	 * @return array
	 */
	public function getItems()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$categoryOrder = 'c.ordering';
		$itemOrder     = 'i.ordering';

		// Get category ID from url
		$categoryId = $app->getUserState('com_reditem.explore.parent_id', 0);
		$typeId     = $this->getState('filter.typeId', 0);
		$list       = $input->get('list', array(), 'array');
		$ordering   = $input->get('list.ordering', 'title');
		$direction  = $this->getState('list.direction', 'asc');

		switch ($ordering)
		{
			case 'author':
				$categoryOrder = 'c.created_id';
				$itemOrder = 'ua.name';
				break;

			case 'template':
				$categoryOrder = 'template_name';
				$itemOrder = 'template_name';
				break;

			case 'title':
				$categoryOrder = 'c.title';
				$itemOrder = 'i.title';
				break;

			default:
				break;
		}

		if (isset($list['items_limit']))
		{
			$limit = $list['items_limit'];
		}
		else
		{
			$limit = $input->getInt('limit', 0);
		}

		$limitStart = $input->getInt('limitstart', 0);

		// Get model categories
		$modelCategories = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');
		$modelItems      = RModel::getAdminInstance('items', array('ignore_request' => true), 'com_reditem');

		// Filter by categories and count
		$modelCategories->setState('list.ordering', $categoryOrder);
		$modelCategories->setState('list.direction', $direction);

		if (!$categoryId)
		{
			$modelCategories->setState('filter.parentid', '1');
			$modelCategories->setState('filter.isOrder', true);
			$modelItems->setState('filter.catid', -1);
		}
		else
		{
			$modelCategories->setState('filter.parentid', $categoryId);
			$modelItems->setState('filter.catid', $categoryId);
		}

		// Filter by items and count
		$modelItems->setState('filter.filter_types', $typeId);
		$modelItems->setState('list.ordering', $itemOrder);
		$modelItems->setState('list.direction', $direction);

		// Count categories item
		$countCategories = count($modelCategories->getItems());
		$countItems      = count($modelItems->getItems());

		if ((int) $limit != 0)
		{
			// Begin compare
			$limitCategories = $countCategories - $limitStart;

			if ($limitCategories >= $limit)
			{
				$modelCategories->setState('list.limit', $limit);
				$modelCategories->setState('list.start', $limitStart);

				$itemsCategories = $modelCategories->getItems();
				$itemsItems = array();
			}
			elseif ($limitCategories <= 0)
			{
				$limitT = $countCategories + $countItems - $limitStart;

				if ($limitT < $limit)
				{
					$limit = $limitT;
				}

				// Get only item
				$modelItems->setState('list.limit', $limit);
				$modelItems->setState('list.start', $limitStart - $countCategories);

				$itemsItems = $modelItems->getItems();
				$itemsCategories = array();
			}
			else
			{
				$modelCategories->setState('list.limit', $limitCategories);
				$modelCategories->setState('list.start', $limitStart);

				$itemsCategories = $modelCategories->getItems();

				// Begin items
				$modelItems->setState('list.limit', $limit - $limitCategories);
				$modelItems->setState('list.start', 0);

				$itemsItems = $modelItems->getItems();
			}
		}
		else
		{
			$itemsCategories = $modelCategories->getItems();
			$itemsItems      = $modelItems->getItems();
		}

		return array(
			'categories' => $itemsCategories,
			'items'      => $itemsItems,
			'total'      => (int) $countCategories + (int) $countItems
		);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   [description]
	 * @param   string  $direction  [description]
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'ordering', $direction = 'asc')
	{
		$app = JFactory::getApplication();

		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter_search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		$filterPlgSearchCategory = $this->getUserStateFromRequest($this->context . '.filter_plgSearchCategory', 'filter_plgSearchCategory');
		$this->setState('filter.plgSearchCategory', $filterPlgSearchCategory);

		$filterTypeId = $this->getUserStateFromRequest($this->context . '.filter_typeId', 'filter_typeId');
		$this->setState('filter.typeId', $filterTypeId);

		$filterIds = $this->getUserStateFromRequest($this->context . '.filter_ids', 'filter_ids');
		$this->setState('filter.ids', $filterIds);

		$parent = $this->getUserStateFromRequest($this->context . '.filter_parentid', 'filter_parentid');
		$this->setState('filter.parentid', $parent);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState($ordering, $direction);
	}

	/**
	 * This is copy process of categories from paste action
	 *
	 * @param   array    $catIds            This value include array id
	 * @param   int      $parentCategoryId  This value only have parent id
	 * @param   boolean  $move              Move item?
	 *
	 * @return void
	 */
	public function copyCategoriesProcess($catIds, $parentCategoryId, $move = false)
	{
		$catsModel = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');
		$catsModel->copy($catIds, $parentCategoryId, $move);
	}

	/**
	 * This is copy items process from paste action
	 *
	 * @param   array    $itemIds           this is array items id
	 * @param   int      $parentCategoryId  this is int value
	 * @param   boolean  $move              Move item?
	 *
	 * @return void
	 */
	public function copyItemsProcess($itemIds, $parentCategoryId, $move = false)
	{
		$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
		$itemsModel->copy($itemIds, array($parentCategoryId), $move);
	}

	/**
	 * Delete items override.
	 *
	 * @param   mixed  $pks  Id array of items and categories for deletion.
	 *
	 * @return  boolean  True on success, false on error.
	 */
	public function delete($pks = null)
	{
		if (is_null($pks))
		{
			return false;
		}

		$db          = $this->getDbo();
		$iModel      = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
		$cModel      = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
		$itemIds     = $pks['itemIds'];
		$categoryIds = $pks['catIds'];

		JArrayHelper::toInteger($itemIds);
		JArrayHelper::toInteger($categoryIds);

		$db->transactionStart();

		if (!empty($categoryIds) && !$cModel->delete($categoryIds))
		{
			$db->transactionRollback();

			return false;
		}

		if (!empty($itemIds) && !$iModel->delete($itemIds))
		{
			$db->transactionRollback();

			return false;
		}

		$db->transactionCommit();

		return true;
	}
}
