SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `#__reditem_categories`;
DROP TABLE IF EXISTS `#__reditem_fields`;
DROP TABLE IF EXISTS `#__reditem_items`;
DROP TABLE IF EXISTS `#__reditem_item_category_xref`;
DROP TABLE IF EXISTS `#__reditem_templates`;
DROP TABLE IF EXISTS `#__reditem_types`;
DROP TABLE IF EXISTS `#__reditem_category_related`;
DROP TABLE IF EXISTS `#__reditem_comments`;
DROP TABLE IF EXISTS `#__reditem_item_preview`;
DROP TABLE IF EXISTS `#__reditem_search_engine`;
DROP TABLE IF EXISTS `#__reditem_category_category_field_xref`;
DROP TABLE IF EXISTS `#__reditem_category_fields`;

/* Delete version feature */
DELETE FROM `#__content_types` WHERE `type_alias` LIKE 'com_reditem.%';

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
