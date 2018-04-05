<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedITEM CustomFields Helper
 *
 * @package     RedITEM.Component
 * @subpackage  Helpers.CusomHelper
 * @since       2.0
 *
 */
class ReditemHelperHelper
{
	/**
	 * Get the tags of user tags
	 *
	 * @return  array List of user tags
	 */
	public static function getUserTags()
	{
		$userTags = array(
			"{user_id}"              => JText::_('COM_REDITEM_TEMPLATE_TAG_USER_ID'),
			"{user_name}"            => JText::_('COM_REDITEM_TEMPLATE_TAG_USER_NAME'),
			"{user_account}"         => JText::_('COM_REDITEM_TEMPLATE_TAG_USER_USERNAME'),
			"{user_email}"           => JText::_('COM_REDITEM_TEMPLATE_TAG_USER_EMAIL'),
			"{user_registerdate}"    => JText::_('COM_REDITEM_TEMPLATE_TAG_USER_REGISTER_DATE'),
			"{user_lastvisiteddate}" => JText::_('COM_REDITEM_TEMPLATE_TAG_USER_LAST_VISITED_DATE'),
			"{sender_name}"          => JText::_('COM_REDITEM_TEMPLATE_TAG_SENDER_NAME'),
			"{sender_email}"         => JText::_('COM_REDITEM_TEMPLATE_TAG_SENDER_EMAIL'),
		);

		return $userTags;
	}

	/**
	 * Get the tags for category.
	 *
	 * @param   object   $template       Current template object
	 * @param   boolean  $subCategories  Is subCategories or not
	 *
	 * @return  array  List of avaiable tags
	 */
	public static function getCategoryTags($template, $subCategories = false)
	{
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem_categories');
		$prefix = '';

		if ($subCategories)
		{
			$prefix = 'sub_';
		}

		// Current category tags
		$categoryTags = array(
			'{' . $prefix . 'category_id}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ID'),
			'{' . $prefix . 'category_title}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_TITLE'),
			'{' . $prefix . 'category_link}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_LINK'),
			'{' . $prefix . 'category_gmap}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_GMAP'),
			'{' . $prefix . 'category_image}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_IMAGE'),
			'{' . $prefix . 'category_image_link}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_IMAGE_LINK'),
			'{' . $prefix . 'category_image_large}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_IMAGE_THUMB_LARGE'),
			'{' . $prefix . 'category_image_medium}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_IMAGE_THUMB_MEDIUM'),
			'{' . $prefix . 'category_image_small}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_IMAGE_THUMB_SMALL'),
			'{' . $prefix . 'items_count}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_COUNT'),
			'{' . $prefix . 'category_introtext|<em>limit</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_INTROTEXT'),
			'{' . $prefix . 'category_fulltext|<em>limit</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_FULLTEXT'),
		);

		if (!$subCategories)
		{
			$categoryTags['{print_icon}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_PRINT_ICON');
			$categoryTags['{include_sub_category_items}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_INCLUDE_ITEMS_SUB_CATEGORIES');
			$categoryTags['{items_masonry|<em>itemWidth</em>}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_MASONRY_EFFECT');
			$categoryTags['{group_items|<em>CustomFieldID</em>}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_GROUP_ITEMS_BY');
			$categoryTags['{show_char}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_SHOW_ITEMS_HEAD_CHARS');
			$categoryTags['{items_sort|<em>Name</em>|<em>Destination</em>}|<em>Table</em>}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_SORT');
			$categoryTags['{items_sort_tool}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_SORT_TOOL');
			$categoryTags['{if_next_category}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_IF_NEXT_CATEGORY');
			$categoryTags['{next_category_link}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_NEXT_CATEGORY_LINK');
			$categoryTags['{next_category_title}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_NEXT_CATEGORY_TITLE');
			$categoryTags['{end_if_next_category}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_END_IF_NEXT_CATEGORY');
			$categoryTags['{if_prev_category}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_IF_PREV_CATEGORY');
			$categoryTags['{prev_category_link}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_PREVIOUS_CATEGORY_LINK');
			$categoryTags['{prev_category_title}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_PREVIOUS_CATEGORY_TITLE');
			$categoryTags['{end_if_prev_category}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_END_IF_PREV_CATEGORY');

			// Items tags
			$categoryTags['{items_loop_start}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_LOOP_START');
			$categoryTags[] = self::getItemTags();
			$categoryTags['{items_loop_end}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_LOOP_END');
			$categoryTags['{items_pagination|<em>limit</em>}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_PAGINATION');

			$subCategoryTags = self::getCategoryTags($template, true);

			// Sub-Categories tags
			$categoryTags['{sub_category_start}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_SUB_LOOP_START');
			$categoryTags['{sub_category_tags}'] = $subCategoryTags;
			$categoryTags['{sub_category_end}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_SUB_LOOP_END');

			$categoryTags['{sub_category_pagination|<em>limit</em>}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_SUB_PAGINATION');

			// Sub-Categories tags
			$categoryTags['{sub_featured_category_start}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_FEATURED_SUB_LOOP_START');
			$categoryTags['{sub_featured_category_tags}'] = $subCategoryTags;
			$categoryTags['{sub_featured_category_end}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_FEATURED_SUB_LOOP_END');

			// Sub-Categories featured tags
			$categoryTags['{related_category_start}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_RELATED_LOOP_START');
			$categoryTags['{related_category_tags}'] = $subCategoryTags;
			$categoryTags['{related_category_end}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_RELATED_LOOP_END');
		}

		$dispatcher->trigger('prepareCategoryTemplateTag', array(&$categoryTags, $template, $prefix));

		return $categoryTags;
	}

	/**
	 * Get the tags for item.
	 *
	 * @param   int     $typeId   Id of type
	 * @param   string  $context  Context to let user know where use it. (Default is item)
	 *
	 * @return  array  List of avaiable tags
	 */
	public static function getItemTags($typeId = null, $context = 'item')
	{
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem_item_tag');

		$itemTags = array(
			'{print_icon}' => JText::_('COM_REDITEM_TEMPLATE_TAG_PRINT_ICON'),
			'{email_icon|<em>mailto</em>|<em>subject</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_EMAIL_ICON'),
			'{item_id}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_ID'),
			'{item_title}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_TITLE'),
			'{item_link}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_LINK'),
			'{item_created|<em>dateFormat</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_CREATED_DATE'),
			'{item_modified|<em>dateFormat</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_MODIFIED_DATE'),
			'{item_author}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_AUTHOR'),
			'{item_publishing_start|<em>format</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_PUBLISHING_START'),
			'{item_publishing_end|<em>format</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_PUBLISHING_END'),
			'{item_edit_link|<em>text</em>|<em>icon</em>|<em>class</em>|<em>Html</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_LINK'),
			'{item_first_cat_link|<em>Itemid</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_FIRST_CATEGORY_LINK'),
			'{item_first_cat_name|<em>limit</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_FIRST_CATEGORY_NAME'),
			'{item_first_cat_id}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_FIRST_CATEGORY_ID'),
			'{item_sharing}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_SHARING'),
			'{item_files_info|<em>fieldcode</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_FILES_INFO')
		);

		// If type ID is null return this tag list
		if (!isset($typeId))
		{
			return $itemTags;
		}

		$customFieldTags = self::getItemCustomFieldsTag($typeId);

		if (empty($customFieldTags))
		{
			return $itemTags;
		}

		$itemTags = array_merge($itemTags, $customFieldTags);

		$dispatcher->trigger('prepareItemTemplateTag', array(&$itemTags, $typeId, $context));

		return $itemTags;
	}

	/**
	 * Method for get custom fields tags base on Type ID
	 *
	 * @param   int  $typeId  ID of type
	 *
	 * @return  array/boolean    Array if success. False otherwise.
	 */
	public static function getItemCustomFieldsTag($typeId)
	{
		$typeId = (int) $typeId;

		if (!$typeId)
		{
			return false;
		}

		$type = ReditemHelperType::getType($typeId);

		// If type object is not available
		if (!$type)
		{
			return false;
		}

		$tags          = array();
		$typeParams    = new JRegistry($type->params);
		$itemGmapField = (boolean) $typeParams->get('item_gmap_field');

		if ($itemGmapField)
		{
			$tags['{item_location}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_LOCATION_GMAP');
			$tags['{item_location_address}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_LOCATION_ADDRESS');
		}

		$customFieldTags = ReditemHelperItem::getCustomFieldTags($type->id);

		// If custom field tags is empty
		if (empty($customFieldTags))
		{
			return $tags;
		}

		foreach ($customFieldTags as $cfTag)
		{
			$tag = '{' . $cfTag->fieldcode . '_text}';
			$tagDesc = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_TITLE', $cfTag->name);
			$tags[$tag] = $tagDesc;

			if (in_array($cfTag->type, ['file', 'url', 'image']))
			{
				$tagLink = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_LINK');
				$tag = '{' . $cfTag->fieldcode . '_link}';
				$tagDesc = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_LINK_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;

				$tagParams = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_PARAMS');
				$tag = '{' . $cfTag->fieldcode . '_value' . $tagParams . '}';
				$tagDesc = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;
			}
			elseif ($cfTag->type == 'daterange')
			{
				$tagParams = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_START_PARAMS');
				$tag = '{' . $cfTag->fieldcode . '_start' . $tagParams . '}';
				$tagDesc = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_START_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;

				$tagParams = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_END_PARAMS');
				$tag = '{' . $cfTag->fieldcode . '_end' . $tagParams . '}';
				$tagDesc = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_END_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;
			}
			else
			{
				$tagParams = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_PARAMS');
				$tag = '{' . $cfTag->fieldcode . '_value' . $tagParams . '}';
				$tagDesc = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;
			}
		}

		return $tags;
	}

	/**
	 * Get the filter tags for items.
	 *
	 * @return  array  List of avaiable tags
	 */
	public static function getFilterTags()
	{
		$filterTags = array(
			'{filter_category|<em>id</em>|<em>type</em>|<em>featured</em>}'
				=> JText::_('COM_REDITEM_TEMPLATE_TAG_FILTER_BY_CATEGORY'),
			'{filter_title|<em>autocomplete</em>|<em>hint</em>}'
				=> JText::_('COM_REDITEM_TEMPLATE_TAG_FILTER_BY_TITLE'),
			'{filter_searchinfrontend|<em>hint</em>}'
				=> JText::_('COM_REDITEM_TEMPLATE_TAG_FILTER_SEARCH_IN_FRONTEND'),
			'{filter_alphabet|<em>displayChars</em>|<em>displayNumber</em>}'
				=> JText::_('COM_REDITEM_TEMPLATE_TAG_FILTER_BY_TITLE_ALPHABET'),
			'{filter_relatedcategory|<em>id</em>}'
				=> JText::_('COM_REDITEM_TEMPLATE_TAG_FILTER_BY_RELATEDCATEGORY'),
			'{filter_customfield|<em>cfId</em>|<em>cfType</em>}'
				=> JText::_('COM_REDITEM_TEMPLATE_TAG_FILTER_BY_CUSTOMFIELDDATA'),
			'{filter_ranges|<em>cfId</em>|<em>rangeCount;minVal:maxVal;rangeText</em>|<em>default</em>}'
				=> JText::_('COM_REDITEM_TEMPLATE_TAG_FILTER_RANGES')
		);

		return $filterTags;
	}

	/**
	 * Get the tags for search view.
	 *
	 * @param   object  $template  Current template object
	 *
	 * @return  array  List of avaiable tags
	 */
	public static function getSearchTags($template)
	{
		if (!$template)
		{
			return false;
		}

		$searchTags = array();

		$searchTags['{items_masonry|<em>itemWidth</em>}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_MASONRY_EFFECT');
		$searchTags['{items_pagination|<em>limit</em>}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_PAGINATION');
		$searchTags['{items_loop_start}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_LOOP_START');
		$searchTags[] = self::getItemTags($template->type_id, 'search');
		$searchTags['{items_loop_end}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_ITEMS_LOOP_END');

		return $searchTags;
	}

	/**
	 * Get the filter tags for sub-catgories.
	 *
	 * @param   object  $template  Current template object
	 *
	 * @return  array  List of avaiable tags
	 */
	public static function getCategoryFilterTags($template)
	{
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem_categories');
		$filterTags = array(
			'{filter_subcatitemsavaiable}' => JText::_('COM_REDITEM_TEMPLATE_TAG_SUB_CATEGORY_ITEMS_AVAIABLE_DATA'),
			'{filter_subcat_title|<em>autocomplete</em>|<em>hint</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_FILTER_SUB_CATEGORY_TITLE')
		);

		$dispatcher->trigger('prepareCategoryFilterExtraTag', array(&$filterTags, $template));

		return $filterTags;
	}

	/**
	 * Replace special character in filename.
	 *
	 * @param   string  $name  Name of file
	 *
	 * @return  string
	 */
	public static function replaceSpecial($name)
	{
		$filetype = JFile::getExt($name);
		$filename = JFile::stripExt($name);
		$value = preg_replace("/[&'#]/", "", $filename);
		$value = JFilterOutput::stringURLSafe($value) . '.' . $filetype;

		return $value;
	}

	/**
	 * Method for get extension
	 *
	 * @param   string  $element  Element name of extension (ex: com_reditem)
	 * @param   string  $type     Type of extension (component, plugin, module)
	 *
	 * @return  boolean/object  Extension of object. False otherwise.
	 */
	public static function getExtension($element, $type = 'component')
	{
		if (empty($element))
		{
			return false;
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn(array('e.extension_id', 'e.name', 'e.enabled')))
			->from($db->qn('#__extensions', 'e'))
			->where($db->qn('e.type') . ' = ' . $db->quote($type))
			->where($db->qn('e.element') . ' = ' . $db->quote($element));
		$db->setQuery($query);

		$extension = $db->loadObject();

		if (!$extension)
		{
			return false;
		}

		return $extension;
	}

	/**
	 * Get list of Item's Id which belong to category have $text in title
	 *
	 * @param   string  $text  Text for filter in Category title
	 *
	 * @return  array   Result list of Item's id
	 */
	public static function searchItemsBaseCategoryTitle($text)
	{
		$db = JFactory::getDbo();

		if (empty($text))
		{
			return array();
		}

		// Get all category's Id which has $text in title
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__reditem_categories'))
			->where($db->qn('title') . ' LIKE ' . $text);
		$db->setQuery($query);

		$categoryIds = $db->loadColumn();

		if (empty($categoryIds))
		{
			return array();
		}

		// Get all items belong to result categories
		$query->clear()
			->select('DISTINCT(' . $db->qn('item_id') . ') as ' . $db->qn('id'))
			->from($db->qn('#__reditem_item_category_xref'))
			->where($db->qn('category_id') . ' IN (' . implode(',', $categoryIds) . ')');
		$db->setQuery($query);
		$result = $db->loadColumn();

		return $result;
	}

	/**
	 * Get the tags for category on Gmap view.
	 *
	 * @return  array  List of avaiable tags
	 */
	public static function getCategoryGmapTags()
	{
		$tags = array(
			'{setting_distance|<em>location</em>|<em>distance</em>}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_GMAP_SET_BY_DISTANCE')
		);

		return $tags;
	}

	/**
	 * Get the filter tags for category on Gmap view.
	 *
	 * @return  array  List of avaiable tags
	 */
	public static function getCategoryGmapFilterTags()
	{
		$tags = array(
			'{filter_distance}' => JText::_('COM_REDITEM_TEMPLATE_TAG_CATEGORY_GMAP_FILTER_BY_DISTANCE')
		);

		return $tags;
	}

	/**
	 * Method for get related items tag
	 *
	 * @param   int  $typeId  Type ID
	 *
	 * @return  array  Array of available tags
	 */
	public static function getRelatedItemsTag($typeId)
	{
		$typeId = (int) $typeId;

		if (!$typeId)
		{
			return false;
		}

		$tags = array(
			'{if_related_items}'         => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEMS_IF_RELATED_ITEMS_AVAILABLE'),
			'{related_items_loop_start}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEMS_RELATED_ITEMS_START_LOOP'),
			'{related_items}'            => self::getItemTags($typeId, 'related'),
			'{related_items_loop_end}'   => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEMS_RELATED_ITEMS_END_START_LOOP'),
			'{end_if_related_items}'     => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEMS_END_IF_RELATED_ITEMS_AVAILABLE')
		);

		return $tags;
	}

	/**
	 * Get the tags for item edit view.
	 *
	 * @param   int  $typeId  Id of type
	 *
	 * @return  array  List of avaiable tags
	 */
	public static function getItemEditTags($typeId = null)
	{
		$itemTags = array(
			'{item_alias_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_ALIAS_LABEL'),
			'{item_alias}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_ALIAS'),
			'{item_category_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_CATEGORY_LABEL'),
			'{item_category}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_CATEGORY'),
			'{item_access_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_ACCESS_LABEL'),
			'{item_access}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_ACCESS'),
			'{item_featured_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_FEATURED_LABEL'),
			'{item_featured}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_FEATURED'),
			'{item_link_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_DIRECT_LINK_LABEL'),
			'{item_link}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_DIRECT_LINK'),
			'{item_related_items_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_RELATED_ITEMS_LABEL'),
			'{item_related_items}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_RELATED_ITEMS'),
			'{item_meta_description_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_META_DESC_LABEL'),
			'{item_meta_description}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_META_DESC'),
			'{item_meta_keywords_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_META_KEYS_LABEL'),
			'{item_meta_keywords}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_META_KEYS'),
		);

		// If type ID is null return this tag list
		if (!isset($typeId))
		{
			return $itemTags;
		}

		$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
		$type = $typeModel->getItem($typeId);

		// If type object is not available
		if (empty($type))
		{
			return $itemTags;
		}

		$typeParams = new JRegistry($type->params);
		$itemGmapField = (boolean) $typeParams->get('item_gmap_field');

		if ($itemGmapField)
		{
			$itemTags['{item_location}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_LOCATION_GMAP');
			$itemTags['{item_location_address}'] = JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_LOCATION_ADDRESS');
		}

		$customFieldTags = ReditemHelperItem::getCustomFieldTags($type->id);

		// If custom field tags is empty
		if (empty($customFieldTags))
		{
			return $itemTags;
		}

		foreach ($customFieldTags as $cfTag)
		{
			$tag = '{' . $cfTag->fieldcode . '_text}';
			$tagDesc = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_TITLE', $cfTag->name);
			$itemTags[$tag] = $tagDesc;

			$tagParams = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_PARAMS');
			$tag = '{' . $cfTag->fieldcode . '_value' . $tagParams . '}';
			$tagDesc = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_VALUE', $cfTag->name);
			$itemTags[$tag] = $tagDesc;

			if (($cfTag->type == 'file') || ($cfTag->type == 'url'))
			{
				$tagLink = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_LINK');
				$tag = '{' . $cfTag->fieldcode . '_link}';
				$tagDesc = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_LINK_VALUE', $cfTag->name);
				$itemTags[$tag] = $tagDesc;
			}
		}

		return $itemTags;
	}

	/**
	 * Get the required tags for item edit view.
	 *
	 * @return  array  List of avaiable tags
	 */
	public static function getItemEditRequiredTags()
	{
		$itemTags = array(
			'{item_title_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_TITLE_LABEL'),
			'{item_title}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_TITLE'),
			'{item_template_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_TEMPLATE_LABEL'),
			'{item_template}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_TEMPLATE'),
			'{item_published_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_PUBLISHED_LABEL'),
			'{item_published}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_PUBLISHED'),
			'{item_start_publishing_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_START_PUBLISHING_LABEL'),
			'{item_start_publishing}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_START_PUBLISHING'),
			'{item_finish_publishing_label}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_FINISH_PUBLISHING_LABEL'),
			'{item_finish_publishing}' => JText::_('COM_REDITEM_TEMPLATE_TAG_ITEM_EDIT_FINISH_PUBLISHING'),
		);

		return $itemTags;
	}

	/**
	 * Method for get list of enabled templates in Front-end
	 *
	 * @return  array  List of template object
	 */
	public static function getFrontEndTemplate()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__extensions'))
			->where($db->qn('type') . ' = ' . $db->quote('template'))
			->where($db->qn('client_id') . ' = 0')
			->where($db->qn('enabled') . ' = 1');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * This method will get categories parent from child categories id
	 *
	 * @param   int  $childId  this is id of current category
	 *
	 * @return array
	 */
	public static function getParentCategories($childId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('parent.*')
			->from($db->qn('#__reditem_categories', 'node'))
			->leftJoin(
				$db->qn('#__reditem_categories', 'parent')
				. ' ON '
				. $db->qn('node.lft')
				. ' BETWEEN '
				. $db->qn('parent.lft')
				. ' AND '
				. $db->qn('parent.rgt')
			)
			->where($db->qn('node.id') . ' = ' . (int) $childId)
			->where($db->qn('parent.level') . ' > 0')
			->order($db->qn('parent.lft'));
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method for get custom fields tags base category id.
	 *
	 * @return  array/boolean    Array if success. False otherwise.
	 */
	public static function getCategoryCustomFieldsTag()
	{
		$tags        = array();
		$fieldsmodel = RModel::getAdminInstance('Category_Fields', array('ignore_request' => true), 'com_reditem');
		$fieldsmodel->setState('filter.published', 1);
		$customFieldTags = $fieldsmodel->getItems();

		// If custom field tags is empty
		if (empty($customFieldTags))
		{
			return $tags;
		}

		foreach ($customFieldTags as $cfTag)
		{
			$tag        = '{' . $cfTag->fieldcode . '_text}';
			$tagDesc    = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_TITLE', $cfTag->name);
			$tags[$tag] = $tagDesc;

			if (($cfTag->type == 'file') || ($cfTag->type == 'url'))
			{
				$tag        = '{' . $cfTag->fieldcode . '_link}';
				$tagDesc    = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_LINK_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;

				$tagParams  = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_PARAMS');
				$tag        = '{' . $cfTag->fieldcode . '_value' . $tagParams . '}';
				$tagDesc    = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;
			}
			elseif ($cfTag->type == 'daterange')
			{
				$tagParams  = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_START_PARAMS');
				$tag        = '{' . $cfTag->fieldcode . '_start' . $tagParams . '}';
				$tagDesc    = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_START_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;

				$tagParams  = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_END_PARAMS');
				$tag        = '{' . $cfTag->fieldcode . '_end' . $tagParams . '}';
				$tagDesc    = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_END_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;
			}
			else
			{
				$tagParams  = JText::_('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_PARAMS');
				$tag        = '{' . $cfTag->fieldcode . '_value' . $tagParams . '}';
				$tagDesc    = JText::sprintf('COM_REDITEM_TEMPLATE_TAG_FIELD_' . strtoupper($cfTag->type) . '_VALUE', $cfTag->name);
				$tags[$tag] = $tagDesc;
			}
		}

		return $tags;
	}
}
