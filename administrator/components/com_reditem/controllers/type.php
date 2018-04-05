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
 * The type edit controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Type
 * @since       2.0
 */
class ReditemControllerType extends RControllerForm
{
	/**
	 * [description]
	 *
	 * @return void
	 */
	public function add()
	{
		$app = JFactory::getApplication();
		$app->setUserState('com_reditem.global.tid', '0');

		return parent::add();
	}

	/**
	 * [description]
	 *
	 * @param   int     $key     [description]
	 * @param   string  $urlVar  [description]
	 *
	 * @return void
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id', 0);
		$app->setUserState('com_reditem.global.tid', $id);

		return parent::edit($key, $urlVar);
	}
}
