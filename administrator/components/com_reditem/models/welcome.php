<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Reditem welcome model
 *
 * @package     RedITEM.Backend
 * @subpackage  Model.welcome
 * @since       2.0
 */
class RedItemModelWelcome extends RModelAdmin
{
	/**
	 * Get the current redITEM version
	 *
	 * @return  string  The redITEM version
	 *
	 * @since   0.9.1
	 */
	public function getVersion()
	{
		$xmlfile = JPATH_SITE . '/administrator/components/com_reditem/reditem.xml';
		$version = JText::_('COM_REDITEM_FILE_NOT_FOUND');

		if (file_exists($xmlfile))
		{
			$data = JApplicationHelper::parseXMLInstallFile($xmlfile);
			$version = $data['version'];
		}

		return $version;
	}
}
