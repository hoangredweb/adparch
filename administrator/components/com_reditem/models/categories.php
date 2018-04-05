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
 * @subpackage  Models.Categories
 * @since       2.0
 *
 */
class ReditemModelCategories extends ReditemModelList
{
	/**
	 * Context for session
	 *
	 * @var  string
	 */
	protected $context = 'com_reditem.categories';

	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_categories';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'categories_limit';

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
				'title', 'c.title',
				'ordering', 'c.ordering',
				'published', 'c.published',
				'access', 'c.access', 'access_level',
				'id', 'c.id',
				'parent_id', 'c.parent_id', 'type_name',
				'template_id', 'c.template_id', 'template_name',
				'featured', 'c.featured', 'filter_types',
				'lft', 'c.lft'
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
		$db     = JFactory::getDbo();
		$user   = ReditemHelperSystem::getUser();
		$groups = $user->getAuthorisedViewLevels();

		$query = $db->getQuery(true)
			->select(
				$this->getState(
					'list.select',
					'c.*, t.name AS template_name, ag.title AS access_level'
				)
		);
		$query->from('#__reditem_categories AS c');
		$query->leftJoin($db->qn('#__reditem_templates', 't') . ' ON ' . $db->qn('c.template_id') . ' = ' . $db->qn('t.id'));

		// Join over the asset groups.
		$query->leftJoin($db->qn('#__viewlevels', 'ag') . ' ON ' . $db->qn('ag.id') . ' = ' . $db->qn('c.access'));

		// Remove "ROOT" item
		$query->where($db->qn('level') . ' > ' . $db->quote('0'));

		// Filter: ID
		$filterIds = $this->getState('filter.ids', array());

		if (is_array($filterIds) && !empty($filterIds))
		{
			$query->where($db->qn('c.id') . ' IN (' . implode(',', $filterIds) . ')');
		}

		// Filter: Parent ID
		$parent_id = $this->getState('filter.parentid');

		switch (gettype($parent_id))
		{
			case 'array':

				if (count($parent_id))
				{
					$parent_id = implode(',', $db->q($parent_id));
					$query->where($db->quoteName('c.parent_id') . ' IN(' . $parent_id . ')');
				}

				break;

			case 'string':
			case 'int':
			case 'integer':

				if ($parent_id)
				{
					$query->where($db->quoteName('c.parent_id') . ' = ' . (int) $parent_id);
				}
				break;

			default:
				break;
		}

		// Filter categories by "level"
		$level = (int) $this->getState('filter.level', 0);

		if (!empty($level))
		{
			$query->where($db->qn('c.level') . ' = ' . $level);
		}

		// Filter: Get deeper child or parent
		$lft = $this->getState('filter.lft', 0);
		$rgt = $this->getState('filter.rgt', 0);

		if (($lft) && ($rgt))
		{
			$query->where($db->qn('c.lft') . ' >= ' . (int) $lft);
			$query->where($db->qn('c.rgt') . ' <= ' . (int) $rgt);
		}

		// Filter: like / search
		$search = $this->getState('filter.search', '');

		if (!empty($search))
		{
			$like = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where($db->qn('c.title') . ' LIKE ' . $like);
		}

		// Filter: like / plugin Search Category
		$filterPlgSearchCategory = $this->getState('filter.plgSearchCategory', '');

		if (!empty($filterPlgSearchCategory))
		{
			$like = $db->quote('%' . $db->escape($filterPlgSearchCategory, true) . '%');

			$where = array(
				$db->qn('c.title') . ' LIKE ' . $like,
				$db->qn('c.introtext') . ' LIKE ' . $like,
				$db->qn('c.fulltext') . ' LIKE ' . $like
			);

			$query->where('((' . implode(') OR (', $where) . '))');
		}

		// Filter: types
		$filter_type = $this->getState('filter.filter_types', 0);

		if ($filter_type)
		{
			$query->where($db->qn('c.type_id') . ' = ' . $db->quote($filter_type));
		}

		// Filter: types
		$filterTypeExcludes = $this->getState('filter.type_excludes', null);

		if (is_numeric($filterTypeExcludes))
		{
			$query->where($db->qn('c.type_id') . ' <> ' . (int) $filterTypeExcludes);
		}
		elseif (is_array($filterTypeExcludes))
		{
			JArrayHelper::toInteger($filterTypeExcludes);
			$query->where($db->qn('c.type_id') . ' NOT IN (' . implode(',', $filterTypeExcludes) . ')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		// Define null and now dates
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(ReditemHelperSystem::getDateWithTimezone()->toSql());

		if (is_numeric($published))
		{
			$query->where($db->qn('c.published') . ' = ' . (int) $published);

			if (($published == 1) && (!$user->authorise('core.edit.state', 'com_reditem')) && (!$user->authorise('core.edit', 'com_reditem')))
			{
				$query->where('(' . $db->qn('c.publish_up') . ' = ' . $nullDate . ' OR ' . $db->qn('c.publish_up') . ' <= ' . $nowDate . ')')
					->where('(' . $db->qn('c.publish_down') . ' = ' . $nullDate . ' OR ' . $db->qn('c.publish_down') . ' >= ' . $nowDate . ')');
			}
		}
		elseif (($published === '') || (!isset($published)))
		{
			$query->where('(c.published IN (0, 1))');
		}

		// Filter by featured state
		$featured = $this->getState('filter.featured');

		if (is_numeric($featured))
		{
			$query->where('c.featured = ' . (int) $featured);
		}
		elseif ($featured === '')
		{
			$query->where('(c.featured IN (0, 1))');
		}

		// Check access level
		$query->where($db->qn('c.access') . ' IN (' . implode(',', $groups) . ')');

		// Get the ordering modifiers
		if ($this->getState('filter.isOrder', true))
		{
			$orderCol = $this->state->get('list.ordering', 'c.lft');
			$orderDirn = $this->state->get('list.direction', 'asc');
			$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		}

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
		$id .= ':' . $this->getState('filter.plgSearchCategory');
		$id	.= ':' . $this->getState('filter.published');
		$id	.= ':' . $this->getState('filter.featured');
		$id	.= ':' . $this->getState('filter.lft');
		$id	.= ':' . $this->getState('filter.rgt');

		$ids = $this->getState('filter.ids');

		if (isset($ids))
		{
			if (is_array($ids))
			{
				$id .= ':' . implode(',', $ids);
			}
			else
			{
				$id .= ':' . $ids;
			}
		}

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
	protected function populateState($ordering = 'c.lft', $direction = 'asc')
	{
		$app = JFactory::getApplication();

		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		$filterPlgSearchCategory = $this->getUserStateFromRequest($this->context . '.filter.plgSearchCategory', 'filter_plgSearchCategory');
		$this->setState('filter.plgSearchCategory', $filterPlgSearchCategory);

		$filterTypes = $this->getUserStateFromRequest($this->context . '.filter.types', 'filter_types');
		$this->setState('filter.filter_types', $filterTypes);

		$filterIds = $this->getUserStateFromRequest($this->context . '.filter.ids', 'filter_ids');
		$this->setState('filter.ids', $filterIds);

		$parent = $this->getUserStateFromRequest($this->context . '.filter.parentid', 'filter_parentid');
		$this->setState('filter.parentid', $parent);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$featured = $this->getUserStateFromRequest($this->context . '.filter.featured', 'featured', '');
		$this->setState('filter.featured', $featured);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState($ordering, $direction);
	}

	/**
	 * Copy categories function. Allowing move option as well.
	 *
	 * @param   array    $catIds            Array of categories ids.
	 * @param   int      $parentCategoryId  Parent category id.
	 * @param   boolean  $move              Move category flag.
	 *
	 * @return void
	 */
	public function copy($catIds, $parentCategoryId, $move = false)
	{
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem_categories');

		if (count($catIds))
		{
			foreach ($catIds as $catId)
			{
				$categoryTable = $this->getTable('Category', 'ReditemTable');

				if ($move)
				{
					$categoryTable->load($catId);
					$categoryTable->moveByReference($parentCategoryId, 'last-child', $catId);
					$categoryTable->rebuildPath();
				}
				else
				{
					$categoryTable->load($catId);

					$categoryTable->id    = null;
					$categoryTable->title = JString::increment($categoryTable->title);

					if ($parentCategoryId)
					{
						$categoryTable->parent_id = $parentCategoryId;
					}

					$oldCImage = $categoryTable->category_image;
					$categoryTable->setLocation($parentCategoryId, 'last-child');

					unset($categoryTable->path);
					unset($categoryTable->lft);
					unset($categoryTable->rgt);
					unset($categoryTable->level);
					unset($categoryTable->alias);
					unset($categoryTable->asset_id);

					if (!$categoryTable->check())
					{
						continue;
					}

					if ($categoryTable->store())
					{
						$dispatcher->trigger('onAfterCopy', array($catId, $categoryTable->id));
						$model = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
						$model->copyFields($catId, $categoryTable->id);

						// Copy field images
						ReditemHelperCustomfield::copyFiles($catId, $categoryTable->id, 'images', 'category');

						// Copy field files
						ReditemHelperCustomfield::copyFiles($catId, $categoryTable->id, 'files', 'category');

						// Copy category's image
						$imageFolder = JPATH_ROOT . '/media/com_reditem/images/category/';

						if (JFile::exists($imageFolder . $catId . '/' . $oldCImage))
						{
							JFile::copy(
								$imageFolder . $catId . '/' . $oldCImage,
								$imageFolder . $categoryTable->id . '/' . $categoryTable->category_image
							);
						}

						// Rebuild new category path
						$categoryTable->rebuildPath();
					}
				}
			}
		}
	}
}
