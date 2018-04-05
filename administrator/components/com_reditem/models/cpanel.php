<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedITEM CPanel Model
 *
 * @package     RedITEM.Backend
 * @subpackage  Model.CPanel
 * @since       2.0
 */
class RedItemModelCpanel extends RModelAdmin
{
	/**
	 * Demo items.
	 *
	 * @var array
	 */
	private $items = array();

	/**
	 * Demo types.
	 *
	 * @var array
	 */
	private $types = array();

	/**
	 * Demo templates.
	 *
	 * @var array
	 */
	private $templates = array();

	/**
	 * Demo categories.
	 *
	 * @var array
	 */
	private $categories = array();

	/**
	 * Demo users.
	 *
	 * @var array
	 */
	private $users = array();

	/**
	 * Demo dates.
	 *
	 * @var array
	 */
	private $dates = array();

	/**
	 * Install demo content.
	 *
	 * @return  void
	 *
	 * @since	2.5.0
	 *
	 * @throws  RuntimeException
	 */
	public function insertDemo()
	{
		$demoFile = JPATH_SITE . '/media/com_reditem/demo.data.json';

		if (!file_exists($demoFile))
		{
			throw new RuntimeException('Demo file does not exist: ' . $demoFile);
		}

		// Save data that can be used in the demo data
		$this->users[] = ReditemHelperSystem::getUser()->get('id');
		$this->dates[] = ReditemHelperSystem::getDateWithTimezone()->toSql();
		$demoData      = json_decode(file_get_contents($demoFile));

		if (property_exists($demoData, 'types'))
		{
			foreach ($demoData->types as $type)
			{
				$this->createType($type);
			}
		}

		if (property_exists($demoData, 'fields'))
		{
			foreach ($demoData->fields as $field)
			{
				$this->createField($field);
			}
		}

		if (property_exists($demoData, 'templates'))
		{
			foreach ($demoData->templates as $template)
			{
				$this->createTemplate($template);
			}
		}

		if (property_exists($demoData, 'categories'))
		{
			foreach ($demoData->categories as $category)
			{
				$this->createCategory($category);
			}
		}

		if (property_exists($demoData, 'items'))
		{
			foreach ($demoData->items as $item)
			{
				$this->createItem($item);
			}
		}
	}

	/**
	 * Create a category on the database.
	 *
	 * @param   stdClass  $data  Category data.
	 *
	 * @return  void
	 *
	 * @since	2.5.0
	 *
	 * @throws  RuntimeException
	 */
	private function createCategory($data)
	{
		$table           = RTable::getAdminInstance('Category', array(), 'com_reditem');
		$data            = $this->replaceJsonTags($data);
		$params          = new JRegistry($data->params);
		$data->params    = $params->toArray();
		$location        = !empty($data->parent_id) ? $data->parent_id : $table->getRootId();
		$data->parent_id = $location;

		$table->setLocation($location, 'first-child');

		if (!$table->save((array) $data))
		{
			throw new RuntimeException("Error creating demo category: " . $table->getError());
		}

		$this->categories[] = $table->id;
	}

	/**
	 * Create a field on the database.
	 *
	 * @param   stdClass  $data  Field data.
	 *
	 * @return  void
	 *
	 * @since	2.5.0
	 *
	 * @throws  RuntimeException
	 */
	private function createField($data)
	{
		$table        = RTable::getAdminInstance('Field', array(), 'com_reditem');
		$data         = $this->replaceJsonTags($data);
		$params       = new JRegistry($data->params);
		$data->params = $params->toArray();

		if (!$table->save((array) $data))
		{
			throw new RuntimeException("Error creating demo field: " . $table->getError());
		}
	}

	/**
	 * Create an item on the database.
	 *
	 * @param   stdClass  $data  Item data.
	 *
	 * @return  void
	 *
	 * @since	2.5.0
	 *
	 * @throws  RuntimeException
	 */
	private function createItem($data)
	{
		$table        = RTable::getAdminInstance('Item', array(), 'com_reditem');
		$data         = $this->replaceJsonTags($data);
		$params       = new JRegistry($data->params);
		$data->params = $params->toArray();
		$data->fields = JArrayHelper::fromObject($data->fields);

		if (!$table->save((array) $data))
		{
			throw new RuntimeException("Error creating demo item: " . $table->getError());
		}

		$this->items[] = $table->id;
	}

	/**
	 * Create a template on the database.
	 *
	 * @param   stdClass  $data  Template data.
	 *
	 * @return  void
	 *
	 * @since	2.5.0
	 *
	 * @throws  RuntimeException
	 */
	private function createTemplate($data)
	{
		$table = RTable::getAdminInstance('Template', array(), 'com_reditem');
		$data  = $this->replaceJsonTags($data);

		if (!$table->save((array) $data))
		{
			throw new RuntimeException("Error creating demo template: " . $table->getError());
		}

		$this->templates[] = $table->id;
	}

	/**
	 * Creates a type on the database.
	 *
	 * @param   stdClass  $data  Type data.
	 *
	 * @return  void
	 *
	 * @since	2.5.0
	 */
	private function createType($data)
	{
		$table        = RTable::getAdminInstance('Type', array(), 'com_reditem');
		$params       = new JRegistry($data->params);
		$data->params = $params->toArray();

		if (!$table->save((array) $data))
		{
			throw new RuntimeException("Error creating type: " . $table->getError());
		}

		$this->types[] = $table->id;
	}

	/**
	 * Replace dynamic tags on the data
	 *
	 * @param   stdClass  $data  Object containing the data of the entity.
	 *
	 * @return  stdClass  Object with data replaced
	 *
	 * @since	2.5.0
	 */
	private function replaceJsonTags($data)
	{
		$jsonString = json_encode($data);

		if (!preg_match_all("/\@([a-z]+[0-9]+)\@/i", $jsonString, $matches))
		{
			return $data;
		}

		foreach ($matches[1] as $position => $match)
		{
			if (!preg_match('/^([a-z]+)([0-9]+)$/i', $match, $parts) || count($parts) < 3)
			{
				continue;
			}

			$property = $parts[1];
			$key      = (int) $parts[2] - 1;

			if (!property_exists($this, $property) || !isset($this->{$property}[$key]))
			{
				$jsonString = str_replace($matches[0][$position], null, $jsonString);

				continue;
			}

			$jsonString = str_replace($matches[0][$position], $this->{$property}[$key], $jsonString);
		}

		return json_decode($jsonString);
	}
}
