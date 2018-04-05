CREATE TABLE IF NOT EXISTS `#__reditem_category_related`
(
    `related_id`           int(11)         NOT NULL,
    `parent_id`           int(11)         NOT NULL
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COMMENT="redITEM Related Categories";

/* Add "Params" column for categories */
ALTER TABLE `#__reditem_categories` ADD `params` VARCHAR( 2048 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

/* Add "Access" column for items */
ALTER TABLE `#__reditem_items` ADD `access` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `published`;

/* Add default access value for items */
UPDATE `#__reditem_items` SET `access` = '1';