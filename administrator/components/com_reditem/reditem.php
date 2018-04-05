<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Entry point
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$app   = JFactory::getApplication();
$user  = JFactory::getUser();
$input = $app->input;

// Access check.
if (!$user->authorise('core.manage', 'com_reditem'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Load redITEM Library
JLoader::import('reditem.library');

// Register component prefix
JLoader::registerPrefix('Reditem', __DIR__);

$controller = $input->getCmd('view', 'cpanel');

// Set the controller page
if (!file_exists(JPATH_COMPONENT . '/controllers/' . $controller . '.php'))
{
	$controller = 'reditem';
	$input->set('view', 'cpanel');
}

RHelperAsset::load('reditem.min.js', 'com_reditem');
RHelperAsset::load('reditem.backend.min.css', 'com_reditem');

$controller = JControllerLegacy::getInstance('Reditem');
$controller->execute($input->getCmd('task', ''));
$controller->redirect();
