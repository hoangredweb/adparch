ALTER TABLE  `#__reditem_categories` 
	ADD  `publish_up` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' AFTER  `published` , 
	ADD  `publish_down` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' AFTER  `publish_up`,
	ADD INDEX (`publish_up`),
	ADD INDEX (`publish_down`);