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
 * The templates controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Templates
 * @since       2.0
 */
class ReditemControllerTemplates extends RControllerAdmin
{
	/**
	 * constructor (registers additional tasks to methods)
	 */
	public function __construct()
	{
		parent::__construct();

		// Write this to make two tasks use the same method (in this example the add method uses the edit method)
		$this->registerTask('add', 'edit');
	}

	/**
	 * display the add and the edit form
	 *
	 * @return void
	 */
	public function edit()
	{
		$jInput = JFactory::getApplication()->input;
		$jInput->set('view', 'template');
		$jInput->set('layout', 'default');
		$jInput->set('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * Logic to copy template
	 *
	 * @return void
	 */
	public function copy()
	{
		$input = JFactory::getApplication()->input;
		$cids = $input->get('cid', array(), 'array');

		if (count($cids))
		{
			$i = 0;

			foreach ($cids as $id)
			{
				$model = RModel::getAdminInstance('Template', array('ignore_request' => true), 'com_reditem');
				$template = $model->getItem($id);

				$template->id = null;
				$template->name = JString::increment($template->name);

				$table = RTable::getAdminInstance('Template', array('ignore_request' => true), 'com_reditem');
				$table->bind((array) $template);

				if (!$table->check())
				{
					continue;
				}

				if ($table->store(false))
				{
					$i++;
				}
			}

			JFactory::getApplication()->enqueueMessage($i . " " . JText::_('COM_REDITEM_TEMPLATES_COPIED_SUCCESSFUL'));
		};

		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=templates', false));
	}
}
