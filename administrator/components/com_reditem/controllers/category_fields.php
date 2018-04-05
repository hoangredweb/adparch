<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

/**
 * The fields controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Fields
 * @since       2.0
 */
class ReditemControllerCategory_Fields extends RControllerAdmin
{
	/**
	 * Method to assign fields to category/categories.
	 *
	 * @return  void
	 */
	public function assign()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$modal = $this->input->post->get('modal', array(), 'array');
		$cats  = $modal['categories'];
		$model = $this->getModel('Category_Fields');

		if ($model->assign($cats, $pks))
		{
			$msg  = JText::_('COM_REDITEM_CATEGORY_FIELDS_ASSIGN_SUCCESS');
			$type = 'message';
		}
		else
		{
			$msg  = JText::_('COM_REDITEM_CATEGORY_FIELDS_ASSIGN_FAILURE');
			$type = 'error';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=category_fields', false), $msg, $type);
	}
}
