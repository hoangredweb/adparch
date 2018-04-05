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
 * Xref table between an item and category.
 *
 * @package     RedITEM.Backend
 * @subpackage  Table
 * @since       2.5.0
 */
class ReditemTableItem_Category_Xref extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 */
	protected $_tableName = 'reditem_item_category_xref';

	/**
	 * The primary key of the table
	 *
	 * @var string
	 */
	protected $_tableKey = array('item_id', 'category_id');

	/**
	 * Item id.
	 *
	 * @var  int
	 */
	public $item_id;

	/**
	 * Category id.
	 *
	 * @var int
	 */
	public $category_id;

	/**
	 * Method to reset class properties to the defaults set in the class
	 * definition. It will ignore the primary key as well as any private class
	 * properties (except $_errors).
	 *
	 * @return  void
	 *
	 * @link    https://docs.joomla.org/JTable/reset
	 * @since   11.1
	 */
	public function reset()
	{
		$this->category_id = null;
		$this->item        = null;

		// Reset table errors
		$this->_errors = array();
	}
}
