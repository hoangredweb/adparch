ALTER TABLE  `#__reditem_items` 
	ADD  `publish_up` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' AFTER  `published` , 
	ADD  `publish_down` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' AFTER  `publish_up`;

ALTER TABLE `#__reditem_types` ADD `asset_id` INT(255) UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;

DROP TABLE IF EXISTS `#__reditem_config`;
