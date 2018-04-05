SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS `#__reditem_category_fields`
(
  `id`                     INT(11)       NOT NULL AUTO_INCREMENT,
  `type`                   VARCHAR(255)  NOT NULL,
  `default`                MEDIUMTEXT    NOT NULL DEFAULT '',
  `ordering`               INT(11)       NOT NULL DEFAULT '0',
  `state`                  TINYINT(3)    NOT NULL DEFAULT '1',
  `name`                   VARCHAR(255)  NOT NULL,
  `options`                TEXT          NOT NULL DEFAULT '',
  `fieldcode`              VARCHAR(255)  NOT NULL,
  `checked_out`            INT(11)       NULL     DEFAULT NULL,
  `checked_out_time`       DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params`                 VARCHAR(2048) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `state` (`state`),
  KEY `name` (`name`),
  KEY `fieldcode` (`fieldcode`)
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT  = 1;

CREATE TABLE IF NOT EXISTS `#__reditem_category_category_field_xref`
(
  `category_id`       INT(11)    NOT NULL,
  `category_field_id` INT(11)    NOT NULL,
  `value`             MEDIUMTEXT NOT NULL DEFAULT '',
  PRIMARY KEY (`category_id`, `category_field_id`),
  CONSTRAINT `#__ri_cat_catfield_xf_fk1`
  FOREIGN KEY (`category_id`) REFERENCES `#__reditem_categories` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_cat_catfield_xf_fk2`
  FOREIGN KEY (`category_field_id`) REFERENCES `#__reditem_category_fields` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE `#__reditem_templates`
  CHANGE `type_id` `type_id` INT(11) NULL DEFAULT NULL,
  CHANGE `checked_out` `checked_out` INT(11) NULL DEFAULT NULL,
  ADD CONSTRAINT `#__ri_templates_fk1` FOREIGN KEY (`type_id`) REFERENCES `#__reditem_types` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

ALTER TABLE `#__reditem_categories`
  CHANGE `asset_id` `asset_id` INT(10) UNSIGNED NULL,
  CHANGE `parent_id` `parent_id` INT(11) NULL,
  CHANGE `access` `access` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  CHANGE `checked_out` `checked_out` INT(11) NULL DEFAULT NULL,
  CHANGE `created_user_id` `created_user_id` INT(11) NULL DEFAULT NULL,
  CHANGE `modified_user_id` `modified_user_id` INT(11) NULL DEFAULT NULL,
  CHANGE `template_id` `template_id` INT(11) NULL,
  ADD CONSTRAINT `#__ri_categories_fk1` FOREIGN KEY (`created_user_id`) REFERENCES `#__users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  ADD  CONSTRAINT `#__ri_categories_fk2` FOREIGN KEY (`modified_user_id`) REFERENCES `#__users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  ADD CONSTRAINT `#__ri_categories_fk3` FOREIGN KEY (`template_id`) REFERENCES `#__reditem_templates` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  ADD CONSTRAINT `#__ri_categories_fk4` FOREIGN KEY (`parent_id`) REFERENCES `#__reditem_categories` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

ALTER TABLE `#__reditem_fields`
  CHANGE `checked_out` `checked_out` INT(11) NULL DEFAULT NULL,
  ADD CONSTRAINT `#__ri_fields_fk1` FOREIGN KEY (`type_id`) REFERENCES `#__reditem_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `#__reditem_items`
  CHANGE `asset_id` `asset_id` INT(10) UNSIGNED NULL,
  CHANGE `checked_out` `checked_out` INT(11) NULL DEFAULT NULL,
  CHANGE `created_user_id` `created_user_id` INT(11) NULL DEFAULT NULL,
  CHANGE `modified_user_id` `modified_user_id` INT(11) NULL DEFAULT NULL,
  CHANGE `template_id` `template_id` INT(11) NULL DEFAULT NULL,
  ADD CONSTRAINT `#__ri_items_fk1` FOREIGN KEY (`type_id`) REFERENCES `#__reditem_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  ADD CONSTRAINT `#__ri_items_fk2` FOREIGN KEY (`template_id`) REFERENCES `#__reditem_templates` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  ADD CONSTRAINT `#__ri_items_fk3` FOREIGN KEY (`created_user_id`) REFERENCES `#__users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  ADD CONSTRAINT `#__ri_items_fk4` FOREIGN KEY (`modified_user_id`) REFERENCES `#__users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

ALTER TABLE `#__reditem_item_category_xref`
  ADD CONSTRAINT `#__ri_item_cat_xref_fk1`
  FOREIGN KEY (`item_id`) REFERENCES `#__reditem_items` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  ADD CONSTRAINT `#__ri_item_cat_xref_fk2`
  FOREIGN KEY (`category_id`) REFERENCES `#__reditem_categories` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `#__reditem_category_related`
  DROP KEY `related_id`,
  ADD CONSTRAINT `#__ri_cat_related_fk1` FOREIGN KEY (`related_id`) REFERENCES `#__reditem_categories` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  ADD CONSTRAINT `#__ri_cat_related_fk2` FOREIGN KEY (`parent_id`) REFERENCES `#__reditem_categories` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `#__reditem_search_engine`
  CHANGE `user_id` `user_id` INT(11) NOT NULL,
  CHANGE `type_id` `type_id` INT(11) NOT NULL,
  ADD KEY `send_mail` (`send_mail`),
  ADD  CONSTRAINT `#__ri_search_engine_fk1` FOREIGN KEY (`user_id`) REFERENCES `#__users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  ADD CONSTRAINT `#__ri_search_engine_fk2` FOREIGN KEY (`type_id`) REFERENCES `#__reditem_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

DROP TABLE IF EXISTS `#__reditem_comments`;
DROP TABLE IF EXISTS `#__reditem_item_reports`;
DROP TABLE IF EXISTS `#__reditem_reporter_point`;
DROP TABLE IF EXISTS `#__reditem_comment_reports`;
DROP TABLE IF EXISTS `#__reditem_mail`;
DROP TABLE IF EXISTS `#__reditem_mail_settings`;
DROP TABLE IF EXISTS `#__reditem_mail_queue`;
DROP TABLE IF EXISTS `#__reditem_log_useractivity`;
DROP TABLE IF EXISTS `#__reditem_item_rating`;
DROP TABLE IF EXISTS `#__reditem_watch_xref`;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;