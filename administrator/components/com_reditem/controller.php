<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * RedITEM Controller.
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller
 * @since       1.0
 */
class ReditemController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * @since	2.5
	 */
	protected $default_view = 'cpanel';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   12.2
	 */
	public function __construct($config = array())
	{
		// Set default framework to bootstrap 2
		RHtmlMedia::setFramework('bootstrap2');

		parent::__construct($config);
	}
}
