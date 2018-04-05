<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Welcome View
 *
 * @package     RedITEM.Backend
 * @subpackage  Views
 * @since       1.0
 */
class ReditemViewWelcome extends ReditemViewAdmin
{
	/**
	 * Do we have to display a sidebar ?
	 *
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display the welcome page
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   2.0
	 */
	public function display($tpl = null)
	{
		$this->reditemversion = $this->get('Version');

		$this->installationType = JFactory::getApplication()->input->getString('type', '');

		parent::display($tpl);
	}
}
