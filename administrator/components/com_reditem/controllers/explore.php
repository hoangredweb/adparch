<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * The explore controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Explore
 * @since       2.1.19
 */
class ReditemControllerExplore extends RControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Write this to make two tasks use the same method (in this example the add method uses the edit method)
		$this->registerTask('add', 'edit');

		// Move function use the same method with copy function
		$this->registerTask('move', 'copy');

		// Register items convert task
		$this->registerTask('convert', 'convert');
	}

	/**
	 * Method for clear state of Explore
	 *
	 * @return  void
	 */
	public function clear()
	{
		$app = JFactory::getApplication();
		$app->setUserState('com_reditem.explore.parent_id', 0);

		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=explore', false));
	}

	/**
	 * This method will redirect edit item or categories item
	 *
	 * @return void
	 */
	public function edit()
	{
		$app      = JFactory::getApplication();
		$input    = $app->input;
		$catIds   = array();
		$itemIds  = array();
		$parentId = $input->get('parent_id', 0, 'int');
		$post     = $input->get('ritem', array(), 'array');

		// Get item Categories id
		if (isset($post['catIds']))
		{
			$catIds = $post['catIds'];
		}

		// Get item items id
		if (isset($post['itemIds']))
		{
			$itemIds = $post['itemIds'];
		}

		$countCategories = count($catIds);

		if ((int) $countCategories == 0)
		{
			$edit = $itemIds[0];
			$task = 'item.edit';
		}
		else
		{
			$edit = $catIds[0];
			$task = 'category.edit';
		}

		$this->setRedirect(
			JRoute::_(
				'index.php?option=com_reditem&task=' . $task . '&id=' . $edit . '&fromExplore=1&parent_id=' . $parentId,
				false
			)
		);
	}

	/**
	 * Get limit from request
	 *
	 * @return int
	 */
	public function getLimit()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$list  = $input->get('list', array(), 'array');
		$limit = $list['items_limit'];

		if (empty($list))
		{
			$limit = $input->get('limit');
		}

		return (int) $limit;
	}

	/**
	 * Delete item in lists
	 *
	 * @return void
	 */
	public function delete()
	{
		$app     = JFactory::getApplication();
		$input   = $app->input;
		$catIds  = array();
		$itemIds = array();
		$data    = $input->get('ritem', array(), 'array');
		$typeId  = $input->getInt('type_id', 0);
		$parent  = $input->getInt('parent_id', 0);
		$model   = $this->getModel('Explore', 'ReditemModel');
		$limit   = $this->getLimit();

		if ($typeId == null)
		{
			$typeId = $app->input->get('typeId', null);
		}

		if (isset($data['catIds']))
		{
			$catIds = $data['catIds'];
		}

		if (isset($data['itemIds']))
		{
			$itemIds = $data['itemIds'];
		}

		if (!$model->delete($data))
		{
			$this->setMessage(JText::_('COM_REDITEM_EXPLORE_DELETE_ERROR'), 'error');
		}
		else
		{
			$countDeleted = (int) count($catIds) + (int) count($itemIds);
			$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', $countDeleted));
		}

		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=explore&limit=' . $limit . '&typeId=' . $typeId . '&parent_id=' . $parent, false));
	}

	/**
	 * This method will sort Categories or items list
	 *
	 * @return string
	 */
	public function ajaxSaveOrder()
	{
		// Get the input
		$pks     = $this->input->get('ritem', array(), 'array');
		$order   = $this->input->get('order', array(), 'array');
		$sort    = null;
		$catIds  = array();
		$itemIds = array();

		if (isset($pks['catIds']))
		{
			$catIds = $pks['catIds'];
		}

		if (isset($pks['itemIds']))
		{
			$itemIds = $pks['itemIds'];
		}

		// Sanitize the input
		JArrayHelper::toInteger($order);

		if (count($itemIds) > 0)
		{
			$sort = 'items';
		}

		if (count($catIds) > 0)
		{
			$sort = 'categories';
		}

		switch ($sort)
		{
			case 'items':

				JArrayHelper::toInteger($itemIds);

				// Get model categories
				$model = RModel::getAdminInstance('item', array('ignore_request' => true), 'com_reditem');

				// Save the ordering
				$return = $model->saveorder($itemIds, $order);

				if ($return)
				{
					echo '1';
				}
				else
				{
					echo '0';
				}

				break;
			case 'categories':

				JArrayHelper::toInteger($catIds);

				// Get model categories
				$model = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');

				// Save the ordering
				$return = $model->saveorder($catIds, $order);

				if ($return)
				{
					echo '1';
				}
				else
				{
					echo '0';
				}

				break;
			default:

				echo '0';
				break;
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	/**
	 * Checkin method for categories and items
	 *
	 * @return void|boolean
	 */
	public function checkIn()
	{
		$app     = JFactory::getApplication();
		$input   = $app->input;
		$catIds  = array();
		$itemIds = array();
		$post    = $input->get('ritem', array(), 'array');
		$typeId  = $post['typeId'];
		$limit   = $this->getLimit();
		$message = '';

		if ($typeId == null || !isset($typeId))
		{
			$typeId = $app->input->getInt('typeId', 0);
		}

		$current = $post['parentId'];

		if (isset($post['catIds']))
		{
			$catIds = $post['catIds'];
		}

		if (isset($post['itemIds']))
		{
			$itemIds = $post['itemIds'];
		}

		// Get model categories
		$modelCategories = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');

		// Get model items
		$modelItems = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');

		$countCheckin = (int) count($catIds) + (int) count($itemIds);

		if (count($catIds) > 0)
		{
			$return = $modelCategories->checkin($catIds);

			if ($return === false)
			{
				// Checkin failed.
				$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $modelCategories->getError());

				// Set redirect
				$this->setRedirect($this->getRedirectToListRoute(), $message, 'error');

				return false;
			}
			else
			{
				// Checkin succeeded.
				$message = JText::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($catIds));
			}
		}

		if (count($itemIds) > 0)
		{
			$return = $modelItems->checkin($itemIds);

			if ($return === false)
			{
				// Checkin failed.
				$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $modelItems->getError());

				if ($current)
				{
					$this->setRedirect(
						JRoute::_(
							'index.php?option=com_reditem&view=explore&limit='
							. $limit
							. '&typeId='
							. $typeId
							. '&cid='
							. $current, false
						),
						$message
					);
				}
				else
				{
					$this->setRedirect(JRoute::_('index.php?option=com_reditem&limit=' . $limit . '&typeId=' . $typeId . '&view=explore', false), $message);
				}

				return false;
			}
			else
			{
				// Checkin succeeded.
				$message = JText::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', $countCheckin);
			}
		}

		if ($current != 0)
		{
			$this->setRedirect(
				JRoute::_(
					'index.php?option=com_reditem&view=explore&limit='
					. $limit
					. '&typeId='
					. $typeId
					. '&cid='
					. $current, false
				),
				$message
			);
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=explore&limit=' . $limit . '&typeId=' . $typeId, false), $message);
		}
	}

	/**
	 * This is publish and unpublish method for both items and categories
	 *
	 * @return void
	 */
	public function publish()
	{
		$app     = JFactory::getApplication();
		$input   = $app->input;
		$catIds  = array();
		$itemIds = array();
		$post    = $input->get('ritem', array(), 'array');
		$typeId  = $post['typeId'];
		$limit   = $this->getLimit();

		if ($typeId == null || !isset($typeId))
		{
			$typeId = $app->input->getInt('typeId', 0);
		}

		$current = $post['parentId'];

		// Get item Categories id
		if (isset($post['catIds']))
		{
			$catIds = $post['catIds'];
		}

		// Get item items id
		if (isset($post['itemIds']))
		{
			$itemIds = $post['itemIds'];
		}

		// Get model categories
		$modelCategory = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');

		// Get model items
		$modelItem = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');

		$value = JArrayHelper::getValue($this->states, $this->getTask(), 0, 'int');

		$countCheckin = (int) count($catIds) + (int) count($itemIds);

		$task = $this->getTask();

		if (count($catIds) > 0)
		{
			// Make sure the item ids are integers
			JArrayHelper::toInteger($catIds);

			if ($modelCategory->publish($catIds, $value))
			{
				if ($task === 'publish')
				{
					$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
				}
				else
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				}

				$this->setMessage(JText::plural($ntext, count($catIds)));
			}
		}

		if (count($itemIds) > 0)
		{
			// Make sure the item ids are integers
			JArrayHelper::toInteger($itemIds);

			if ($modelItem->publish($itemIds, $value))
			{
				if ($task === 'publish')
				{
					$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
				}
				else
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				}

				$this->setMessage(JText::plural($ntext, $countCheckin));
			}
		}

		if ($current != 0)
		{
			$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=explore&limit=' . $limit . '&typeId=' . $typeId . '&cid=' . $current, false));
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=explore&limit=' . $limit . '&typeId=' . $typeId, false));
		}
	}

	/**
	 * This copy function will copy and move both items and categories in the same type by ajax
	 *
	 * @return void
	 */
	public function copy()
	{
		// Process save item copy to global variable
		$app     = JFactory::getApplication();
		$input   = $app->input;
		$catIds  = array();
		$itemIds = array();
		$rItem   = $input->get('ritem', array(), 'array');
		$task    = $this->getTask();
		$data    = $app->getUserState('com_reditem.items.copy_move', array());

		if (empty($data))
		{
			$data = array (
				'task'       => '',
				'categories' => array(),
				'items'      => array(),
				'message'    => ''
			);
		}

		// Get item Categories id
		if (isset($rItem['catIds']))
		{
			$catIds = $rItem['catIds'];
		}

		// Get item items id
		if (isset($rItem['itemIds']))
		{
			$itemIds = $rItem['itemIds'];
		}

		// Count item and categories
		$total = count($catIds) + count($itemIds);

		if ($task == 'copy')
		{
			if ($total > 1)
			{
				$message = JText::sprintf('COM_REDITEM_EXPLORE_CONTROLER_COPY_ITEMS_CATEGORIES', $total);
			}
			else
			{
				$message = JText::_('COM_REDITEM_EXPLORE_CONTROLER_COPY_ITEM_CATEGORY');
			}
		}
		else
		{
			if ($total > 1)
			{
				$message = JText::sprintf('COM_REDITEM_EXPLORE_CONTROLER_MOVE_ITEMS_CATEGORIES', $total);
			}
			else
			{
				$message = JText::_('COM_REDITEM_EXPLORE_CONTROLER_MOVE_ITEM_CATEGORY');
			}
		}

		$data['task']       = $task;
		$data['categories'] = array_unique(array_merge($data['categories'], $catIds));
		$data['items']      = array_unique(array_merge($data['items'], $itemIds));
		$data['message']    = $message;

		// Set category to system
		$app->setUserState('com_reditem.items.copy_move', $data);

		echo json_encode($data);

		// Close the application
		$app->close();
	}

	/**
	 * This method paste items or categories in the same type
	 *
	 * @return void
	 */
	public function paste()
	{
		// Process save item copy to global variable
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$limit  = $this->getLimit();
		$typeId = $input->getInt('type_id', 0);
		$parent = $input->getInt('parent_id', 0);
		$data   = JFactory::getApplication()->getUserState('com_reditem.items.copy_move');
		$pCatId = ($parent > 0) ? $parent : 1;
		$model  = $this->getModel('Explore', 'ReditemModel');

		// Check if there is no data to copy or move
		if (!is_array($data) || (!isset($data['items']) && !isset($data['categories']))
			|| (count($data['items']) + count($data['categories'])) == 0)
		{
			$msg  = JText::_('COM_REDITEM_EXPLORE_CONTROLER_PASTE_ITEMS_CATEGORIES_NO_ITEMS');
			$type = 'warning';
		}
		else
		{
			$task = $data['task'];

			switch ($task)
			{
				case 'copy':
					// Clone categories in list
					$catIds = $data['categories'];
					$model->copyCategoriesProcess($catIds, $pCatId);

					// Clone items in list
					$itemIds = $data['items'];
					$model->copyItemsProcess($itemIds, $pCatId);

					break;
				case 'move':
					// Clone categories in list
					$catIds = $data['categories'];
					$model->copyCategoriesProcess($catIds, $pCatId, true);

					// Clone items in list
					$itemIds = $data['items'];
					$model->copyItemsProcess($itemIds, $pCatId, true);

					break;
				default:
					$catIds  = array();
					$itemIds = array();

					break;
			}

			// Redirect back to explore view
			$countItems = count($catIds) + count($itemIds);
			$msg        = JText::_('COM_REDITEM_EXPLORE_CONTROLER_PASTE_ITEM_COMPLETED');
			$type       = 'message';

			if ($countItems > 1)
			{
				$msg = JText::sprintf('COM_REDITEM_EXPLORE_CONTROLER_PASTE_ITEMS_COMPLETED', $countItems);
			}
		}

		// After past action completed, clear session to make sure it will be clear
		JFactory::getApplication()->setUserState('com_reditem.items.copy_move', array());

		if ($parent != 0)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_reditem&view=explore&limit=' . $limit . '&typeId=' . $typeId . '&parent_id=' . $parent, false),
				$msg,
				$type
			);
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=explore&limit=' . $limit . '&typeId=' . $typeId, false), $msg, $type);
		}
	}

	/**
	 * Controller function for preforming items/categories convert operation.
	 * It converts items/categories from one type to another, migrating fields
	 * in that process.
	 *
	 * @return void Redirect page to list view.
	 */
	public function convert()
	{
		$input      = JFactory::getApplication()->input;
		$itemIds    = array();
		$selections = $input->get('ritem', array(), 'array');
		$convert    = $input->get('convert', array(), 'array');
		$model      = $this->getModel('Items');
		$limit      = $this->getLimit();
		$parentId   = $input->getInt('parent_id', 0);
		$typeId     = $input->getInt('type_id', 0);

		// Get item items id
		if (isset($selections['itemIds']))
		{
			$itemIds = $selections['itemIds'];
		}

		if (!empty($itemIds))
		{
			if ($model->convert(
				$itemIds, json_decode(html_entity_decode($convert['typesFrom'])), $convert['type'],
				$convert['template'], $convert['fields'], $convert['categories'], $convert['keeporg']
			))
			{
				$msg     = JText::_('COM_REDITEM_ITEMS_CONVERT_SUCCESS');
				$msgType = 'message';
			}
			else
			{
				$msg     = JText::_('COM_REDITEM_ITEMS_CONVERT_FAILURE');
				$msgType = 'warning';
			}
		}
		else
		{
			$msg     = JText::_('COM_REDITEM_ITEMS_CONVERT_ONLY_ITEMS');
			$msgType = 'warning';
		}

		$this->setRedirect(
			JRoute::_(
				'index.php?option=com_reditem&view=explore&limit=' . $limit . '&typeId=' . $typeId . '&parent_id=' . $parentId,
				false
			),
			$msg,
			$msgType
		);
	}
}
