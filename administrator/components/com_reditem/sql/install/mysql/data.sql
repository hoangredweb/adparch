SET FOREIGN_KEY_CHECKS=0;

LOCK TABLES `#__reditem_categories` WRITE;
ALTER TABLE `#__reditem_categories` DISABLE KEYS;
INSERT INTO `#__reditem_categories` VALUES (1, 0, 0, 0, 1, 0, 'ROOT', 'root', 0, '', '', '', '', 0, 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '');
ALTER TABLE `#__reditem_categories` ENABLE KEYS;
UNLOCK TABLES;

INSERT INTO `#__content_types` (`type_id`, `type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
(NULL, 'redITEM Field', 'com_reditem.field', '{"special":{"dbtable":"#__reditem_fields","key":"id","type":"Field","prefix":"ReditemTable"}}', '', '', '', '{"formFile":"administrator\\/components\\/com_reditem\\/models\\/forms\\/field.xml", "hideFields":["checked_out", "checked_out_time"], "ignoreChanges":["version"], "convertToInt": ["checked_out_time"], "displayLookup":[{"sourceColumn":"type_id","targetTable":"#__reditem_types","targetColumn":"id","displayColumn":"title"}]}'),
(NULL, 'redITEM Item', 'com_reditem.item', '{"special":{"dbtable":"#__reditem_items","key":"id","type":"Item","prefix":"ReditemTable"}}', '', '', '', '{"formFile":"administrator\\/components\\/com_reditem\\/models\\/forms\\/item.xml","hideFields":["checked_out","checked_out_time","modified_time","modified_user_id","id","asset_id","params"],"ignoreChanges":["version"],"convertToInt":["publish_up","publish_down","checked_out_time","created_time","modified_time","blocked"],"displayLookup":[{"sourceColumn":"type_id","targetTable":"#__reditem_types","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"template_id","targetTable":"#__reditem_templates","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}');

SET FOREIGN_KEY_CHECKS=1;
