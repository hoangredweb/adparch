<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

/**
 * Template table
 *
 * @package     RedITEM.Backend
 * @subpackage  Table
 * @since       0.9.1
 */
class ReditemTableTemplate extends RTable
{
	/**
	 * The name of the table with category
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableName = 'reditem_templates';

	/**
	 * The primary key of the table
	 *
	 * @var string
	 * @since 0.9.1
	 */
	protected $_tableKey = 'id';

	/**
	 * Field name to publish/unpublish table registers. Ex: state
	 *
	 * @var  string
	 */
	protected $_tableFieldState = 'published';

	/**
	 * Editor content for php template content.
	 *
	 * @var  string
	 */
	protected $content = '';

	/**
	 * @var  string
	 */
	public $name;

	/**
	 * @var  string
	 */
	public $filename;

	/**
	 * @var  string
	 */
	public $description;

	/**
	 * @var  string
	 */
	public $typecode;

	/**
	 * @var  int
	 */
	public $published;

	/**
	 * @var  int
	 */
	public $type_id;

	/**
	 * @var  int
	 */
	public $ordering;

	/**
	 * @var  int
	 */
	public $checked_out = 0;

	/**
	 * @var  string
	 */
	public $checked_out_time = '0000-00-00 00:00:00';

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		$input      = RFactory::getApplication()->input;
		$dispatcher = RFactory::getDispatcher();
		$isNew      = false;
		$db         = $this->getDbo();
		$date       = ReditemHelperSystem::getDateWithTimezone();
		$user       = ReditemHelperSystem::getUser();

		JPluginHelper::importPlugin('reditem');

		if (!$this->id)
		{
			$isNew = true;
		}

		// Run event 'onBeforeTemplateSave'
		$dispatcher->trigger('onBeforeTemplateSave', array($this, $input));
		$db->transactionStart();

		if (!empty($this->type_id))
		{
			$this->type_id = (int) $this->type_id;
		}
		else
		{
			$this->type_id = null;
		}

		if ($isNew)
		{
			$this->checked_out = null;
		}

		if (!parent::store($updateNulls))
		{
			$this->setError($this->getError());
			$db->transactionRollback();

			return false;
		}

		if ($isNew)
		{
			$fileName = $this->id . '_' . strtolower(JFile::makeSafe($this->name)) . '.php';
			$fileName = str_replace(' ', '_', $fileName);
		}
		else
		{
			$fileName = JFile::getName($this->filename);
		}

		$folder = $this->typecode;

		if (!JFolder::exists(JPATH_REDITEM_TEMPLATES . $folder))
		{
			if (!JFolder::create(JPATH_REDITEM_TEMPLATES . $folder))
			{
				$this->setError(JText::_('COM_REDITEM_TEMPLATE_CANT_WRITE'));
				$db->transactionRollback();

				return false;
			}
		}

		$folder .= '/';

		if (JFile::write(JPATH_REDITEM_TEMPLATES . $folder . $fileName, $this->content))
		{
			if ($isNew)
			{
				$query = $db->getQuery(true)
					->update($db->qn('#__reditem_templates'))
					->set($db->qn('filename') . ' = ' . $db->q($folder . $fileName))
					->where($db->qn('id') . ' = ' . $this->id);
				$db->setQuery($query);

				if (!$db->execute())
				{
					$db->transactionRollback();

					return false;
				}
			}
		}
		else
		{
			$this->setError(JText::_('COM_REDITEM_TEMPLATE_CANT_WRITE'));
			$db->transactionRollback();

			return false;
		}

		$db->transactionCommit();

		// Run event 'onAfterTemplateSave'
		$dispatcher->trigger('onAfterTemplateSave', array($this, $isNew));

		return true;
	}

	/**
	 * Deletes this row in database (or if provided, the row of key $pk)
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null)
	{
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem');

		// Run event 'onBeforeTemplateDelete'
		$dispatcher->trigger('onBeforeTemplateDelete', array($this));

		// Delete template file
		$this->load($pk);

		if (!empty($this->filename) && JFile::exists(JPATH_REDITEM_TEMPLATES . $this->filename)
			&& !JFile::delete(JPATH_REDITEM_TEMPLATES . $this->filename))
		{
			$this->setError(JText::_('COM_REDITEM_TEMPLATE_ERROR_DELETING_FILE'));
		}

		if (!parent::delete($pk))
		{
			return false;
		}

		// Run event 'onAfterTemplateDelete'
		$dispatcher->trigger('onAfterTemplateDelete', array($this));

		return true;
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	public function load($keys = null, $reset = true)
	{
		$load = parent::load($keys, $reset);

		if ($load && JFile::exists(JPATH_REDITEM_TEMPLATES . $this->filename))
		{
			$content = JFile::read(JPATH_REDITEM_TEMPLATES . $this->filename);

			if ($content === false)
			{
				$this->setError(JText::_('COM_REDITEM_TEMPLATE_ERROR_FILE_READING'));

				return false;
			}
			else
			{
				$this->content = $content;
			}
		}

		return $load;
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  InvalidArgumentException
	 */
	public function bind($src, $ignore = array())
	{
		if (isset($src['content']))
		{
			$this->content = $src['content'];
		}

		return parent::bind($src, $ignore);
	}
}
