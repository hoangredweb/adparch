<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('helper', JPATH_COMPONENT . '/helpers');

/**
 * Class ReditemHelperCpanel
 *
 * @since  2.0
 */
class ReditemHelperCpanel extends JObject
{
	/**
	 * Some function which was in obscure reddesignhelper class.
	 *
	 * @return array
	 */
	public static function getIconArray()
	{
		// Run plugin event
		JPluginHelper::importPlugin('reditem_quickicon');
		$dispatcher = RFactory::getDispatcher();
		$icons      = $dispatcher->trigger('getSidebarIcons');

		return $icons[0];
	}
}
