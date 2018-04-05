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
 * RedITEM type Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Type
 * @since       0.9.1
 *
 */
class ReditemModelType extends RModelAdmin
{
	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = parent::getForm($data, $loadData);
		$user = ReditemHelperSystem::getUser();

		if (!$user->authorise('core.admin', 'com_reditem'))
		{
			foreach ($form->getGroup('params') as $field)
			{
				$fieldName	= $field->getAttribute('name');
				$fieldClass	= $field->class . ' disabled';

				$form->setFieldAttribute($fieldName, 'readonly', true, 'params');
				$form->setFieldAttribute($fieldName, 'class', $fieldClass, 'params');
			}
		}

		return $form;
	}
}
