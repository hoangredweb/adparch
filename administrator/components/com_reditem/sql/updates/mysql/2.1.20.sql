SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `#__reditem_categories` DROP INDEX `type_id`;
ALTER TABLE `#__reditem_categories` DROP COLUMN `type_id`;

SET FOREIGN_KEY_CHECKS=1;