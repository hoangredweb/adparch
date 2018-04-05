<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Welcome Controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class ReditemControllerWelcome extends JControllerLegacy
{
	/**
	 * Redirect to the panel.
	 *
	 * @return  void
	 */
	public function toPanel()
	{
		$this->setRedirect('index.php?option=com_reditem');
	}
}
