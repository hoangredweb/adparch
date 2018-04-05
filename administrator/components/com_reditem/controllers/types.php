<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_reditem/helpers/helper.php';

/**
 * The types controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Types
 * @since       2.0
 */
class ReditemControllerTypes extends RControllerAdmin
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
		$jInput->set('view', 'type');
		$jInput->set('layout', 'default');
		$jInput->set('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * Clone types function
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

			foreach ($cids as $cid)
			{
				$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
				$type = $typeModel->getItem($cid);

				$type->id = null;
				$type->alias = '';
				$type->title = JString::increment($type->title);

				$typeTable = RTable::getAdminInstance('Type', array('ignore_request' => true));
				$typeTable->bind((array) $type);

				if (!$typeTable->check())
				{
				}

				if (!$typeTable->store(true))
				{
				}
				else
				{
					$i++;
				}
			}

			JFactory::getApplication()->enqueueMessage($i . " " . JText::_('COM_REDITEM_ITEMS_COPY_SUCCESSFUL'));
		};

		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=types', false));
	}

	/**
	 * Method for load view to choose Site template for copy override layout files
	 *
	 * @return  void
	 */
	public function copyOverrideTemplate()
	{
		$app   = JFactory::getApplication();
		$type  = $app->input->getRaw('typeIds', '');
		$types = array();

		if (empty($type))
		{
			echo JText::_('COM_REDITEM_COPY_OVERRIDE_TEMPLATE_ERROR_EMPTY_TYPE');

			$app->close();
		}

		// Get Site template list
		$templates = ReditemHelperHelper::getFrontEndTemplate();

		// Get types object
		$typeIds   = explode(',', $type);
		$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');

		foreach ($typeIds as $typeId)
		{
			if ($typeId)
			{
				$types[] = $typeModel->getItem($typeId);
			}
		}

		$layoutData = array('types' => $types, 'typeIds' => $type, 'templates' => $templates);
		echo RLayoutHelper::render('copyoverridetemplate', $layoutData, null, array('component' => 'com_reditem'));
	}

	/**
	 * Method for copy layout files to override folder base on type
	 *
	 * @return  void
	 */
	public function ajaxCopyOverrideTemplate()
	{
		$app     = JFactory::getApplication();
		$folder  = $app->input->get('folder', '');
		$typeIds = $app->input->get('typeIds', '');
		$typeIds = explode(',', $typeIds);

		if (empty($folder))
		{
			echo '0';
			$app->close();
		}

		$sourceFolder   = JPATH_ROOT . '/components/com_reditem/layouts';
		$templateFolder = JPATH_ROOT . '/templates/' . $folder . '/html/layouts/com_reditem/';
		$typeModel      = RModel::getAdminInstance('Type', array('ignore_request' => true));

		foreach ($typeIds as $typeId)
		{
			$type     = $typeModel->getItem($typeId);
			$folder = $templateFolder . 'type_' . $type->table_name;

			if (!JFolder::exists($folder))
			{
				JFolder::create($folder);
			}

			JFolder::copy($sourceFolder, $folder, '', true);
		}

		echo '1';
		$app->close();
	}

	/**
	 * Method for rebuild permission of list types given
	 *
	 * @return  void
	 */
	public function rebuildPermission()
	{
		$user    = ReditemHelperSystem::getUser();
		$pks     = $this->input->post->get('cid', array(), 'array');

		$this->setRedirect($this->getRedirectToListRoute());

		if (!$user->authorise('core.edit.state', 'com_reditem'))
		{
			$this->setMessage(JText::_('COM_REDITEM_EDIT_PERMISSION_NOT_GRANTED'), 'error');

			return;
		}

		try
		{
			ReditemHelperAssets::rebuildTypes($pks);
		}
		catch (RuntimeException $e)
		{
			$this->setMessage(JText::sprintf('COM_REDITEM_TYPES_ERROR_ASSET_REBUILD', $e->getMessage()), 'error');

			return;
		}

		$this->setMessage(JText::_('COM_REDITEM_TYPES_REBUILD_PERMISSION_SUCCESS'));
	}
}
