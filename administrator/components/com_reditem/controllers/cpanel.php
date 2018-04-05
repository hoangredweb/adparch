<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Control panel view for RedITEM.
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller
 * @since       2.0
 */
class ReditemControllerCpanel extends RControllerAdmin
{
	/**
	 * function install sample content
	 * 
	 * @return void
	 */
	public function demoContentInsert()
	{
		// Install the demo content
		$model = $this->getModel('cpanel');
		$model->insertDemo();

		// Redirect to control panel
		$this->setRedirect('index.php?option=com_reditem&view=cpanel', JText::_('COM_REDITEM_CPANEL_DEMO_CONTENT_SUCCESS'), 'message');
	}
}
