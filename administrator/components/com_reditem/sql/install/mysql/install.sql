SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS `#__reditem_types`
(
  `id`          INT(11)                NOT NULL AUTO_INCREMENT,
  `asset_id`    INT(10)       UNSIGNED NULL,
  `title`       VARCHAR(255)           NOT NULL,
  `description` TEXT                   NOT NULL DEFAULT '',
  `ordering`    INT(11)                NOT NULL DEFAULT '0',
  `table_name`  VARCHAR(200)           NOT NULL,
  `params`      VARCHAR(2048)          NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT  = 1;

CREATE TABLE IF NOT EXISTS `#__reditem_templates`
(
  `id`               INT(11)      NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(255) NOT NULL,
  `filename`         VARCHAR(255) NOT NULL DEFAULT '',
  `type_id`          INT(11)      NULL     DEFAULT NULL,
  `description`      VARCHAR(255) NOT NULL,
  `typecode`         VARCHAR(255) NOT NULL,
  `published`        TINYINT(1)   NOT NULL,
  `ordering`         INT(11)      NOT NULL DEFAULT '0',
  `checked_out`      INT(11)      NULL     DEFAULT NULL,
  `checked_out_time` DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  CONSTRAINT `#__ri_templates_fk1`
  FOREIGN KEY (`type_id`) REFERENCES `#__reditem_types` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT  = 1;

CREATE TABLE IF NOT EXISTS `#__reditem_categories`
(
  `id`               INT(11)                NOT NULL AUTO_INCREMENT,
  `asset_id`         INT(10)       UNSIGNED NULL,
  `parent_id`        INT(11)                NULL,
  `lft`              INT(11)                NOT NULL DEFAULT '0',
  `rgt`              INT(11)                NOT NULL DEFAULT '0',
  `level`            INT(10)       UNSIGNED NOT NULL DEFAULT '0',
  `title`            VARCHAR(255)           NOT NULL,
  `alias`            VARCHAR(255)           NOT NULL DEFAULT '',
  `access`           TINYINT(1)    UNSIGNED NOT NULL DEFAULT '0',
  `path`             VARCHAR(255)           NOT NULL DEFAULT '',
  `category_image`   VARCHAR(255)           NOT NULL DEFAULT '',
  `introtext`        MEDIUMTEXT             NOT NULL,
  `fulltext`         TEXT                   NOT NULL,
  `template_id`      INT(11)                NULL,
  `featured`         TINYINT(1)             NOT NULL DEFAULT '0',
  `ordering`         INT(11)                NOT NULL DEFAULT '0',
  `published`        TINYINT(1)             NOT NULL DEFAULT '1',
  `publish_up`       DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down`     DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out`      INT(11)                NULL     DEFAULT NULL,
  `checked_out_time` DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id`  INT(11)                NULL     DEFAULT NULL,
  `created_time`     DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` INT(11)                NULL     DEFAULT NULL,
  `modified_time`    DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params`           VARCHAR(2048)          NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_left_right` (`lft`, `rgt`),
  KEY `access` (`access`),
  KEY `template_id` (`template_id`),
  KEY `published` (`published`),
  KEY `publish_down` (`publish_down`),
  KEY `featured` (`featured`),
  CONSTRAINT `#__ri_categories_fk1`
  FOREIGN KEY (`created_user_id`) REFERENCES `#__users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_categories_fk2`
  FOREIGN KEY (`modified_user_id`) REFERENCES `#__users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_categories_fk3`
  FOREIGN KEY (`template_id`) REFERENCES `#__reditem_templates` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_categories_fk4`
  FOREIGN KEY (`parent_id`) REFERENCES `#__reditem_categories` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT  = 1;

CREATE TABLE IF NOT EXISTS `#__reditem_fields`
(
  `id`                     INT(10)       NOT NULL AUTO_INCREMENT,
  `type_id`                INT(11)       NOT NULL DEFAULT '0',
  `type`                   VARCHAR(255)  NOT NULL,
  `default`                MEDIUMTEXT    NOT NULL DEFAULT '',
  `ordering`               INT(11)       NOT NULL DEFAULT '0',
  `published`              TINYINT(1)    NOT NULL,
  `name`                   VARCHAR(255)  NOT NULL,
  `options`                TEXT          NOT NULL DEFAULT '',
  `fieldcode`              VARCHAR(255)  NOT NULL,
  `checked_out`            INT(11)       NULL     DEFAULT NULL,
  `checked_out_time`       DATETIME      NOT NULL,
  `searchable_in_frontend` TINYINT(1)    NOT NULL DEFAULT '0',
  `searchable_in_backend`  TINYINT(1)    NOT NULL DEFAULT '0',
  `backend_filter`         TINYINT(1)    NOT NULL DEFAULT '0',
  `params`                 VARCHAR(2048) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `published` (`published`),
  KEY `searchable_in_frontend` (`searchable_in_frontend`),
  KEY `searchable_in_backend` (`searchable_in_backend`),
  KEY `backend_filter` (`backend_filter`),
  CONSTRAINT `#__ri_fields_fk1`
  FOREIGN KEY (`type_id`) REFERENCES `#__reditem_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT  = 1;

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

CREATE TABLE IF NOT EXISTS `#__reditem_items`
(
  `id`               INT(11)                NOT NULL AUTO_INCREMENT,
  `asset_id`         INT(10)       UNSIGNED NULL,
  `title`            VARCHAR(255)           NOT NULL,
  `alias`            VARCHAR(255)           NOT NULL DEFAULT '',
  `ordering`         INT(11)                NOT NULL DEFAULT '0',
  `access`           TINYINT(3)    UNSIGNED NOT NULL DEFAULT '0',
  `blocked`          TINYINT(1)             NOT NULL DEFAULT '0',
  `published`        TINYINT(1)             NOT NULL DEFAULT '1',
  `publish_up`       DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down`     DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `featured`         TINYINT(1)             NOT NULL DEFAULT '0',
  `type_id`          INT(11)                NOT NULL,
  `template_id`      INT(11)                NULL     DEFAULT NULL,
  `checked_out`      INT(11)                NULL     DEFAULT NULL,
  `checked_out_time` DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id`  INT(11)                NULL     DEFAULT NULL,
  `created_time`     DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` INT(11)                NULL     DEFAULT NULL,
  `modified_time`    DATETIME               NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params`           VARCHAR(2048)          NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `access` (`access`),
  KEY `blocked` (`blocked`),
  KEY `published` (`published`),
  KEY `publish_down` (`publish_down`),
  KEY `publish_up` (`publish_up`),
  KEY `featured` (`featured`),
  KEY `type_id` (`type_id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `#__ri_items_fk1`
  FOREIGN KEY (`type_id`) REFERENCES `#__reditem_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_items_fk2`
  FOREIGN KEY (`template_id`) REFERENCES `#__reditem_templates` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_items_fk3`
  FOREIGN KEY (`created_user_id`) REFERENCES `#__users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_items_fk4`
  FOREIGN KEY (`modified_user_id`) REFERENCES `#__users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT  = 1;

CREATE TABLE IF NOT EXISTS `#__reditem_item_preview`
(
  `id`   VARCHAR(100) NOT NULL,
  `data` TEXT         NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__reditem_item_category_xref`
(
  `item_id`     INT(11) NOT NULL,
  `category_id` INT(11) NOT NULL,
  PRIMARY KEY (`item_id`, `category_id`),
  CONSTRAINT `#__ri_item_cat_xref_fk1`
  FOREIGN KEY (`item_id`) REFERENCES `#__reditem_items` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_item_cat_xref_fk2`
  FOREIGN KEY (`category_id`) REFERENCES `#__reditem_categories` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__reditem_category_related`
(
  `related_id` INT(11) NOT NULL,
  `parent_id`  INT(11) NOT NULL,
  PRIMARY KEY (`related_id`, `parent_id`),
  CONSTRAINT `#__ri_cat_related_fk1`
  FOREIGN KEY (`related_id`) REFERENCES `#__reditem_categories` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_cat_related_fk2`
  FOREIGN KEY (`parent_id`) REFERENCES `#__reditem_categories` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT         = 'redITEM Related Categories';

CREATE TABLE IF NOT EXISTS `#__reditem_search_engine`
(
  `id`          INT(11)    NOT NULL AUTO_INCREMENT,
  `user_id`     INT(11)    NOT NULL             COMMENT 'ID of user',
  `type_id`     INT(11)    NOT NULL             COMMENT 'ID of type',
  `send_mail`   TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1 => Send mail when new item match this search criteria',
  `search_data` TEXT       NOT NULL             COMMENT 'Search data, in JSON format',
  PRIMARY KEY (`id`),
  KEY `send_mail` (`send_mail`),
  CONSTRAINT `#__ri_search_engine_fk1`
  FOREIGN KEY (`user_id`) REFERENCES `#__users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__ri_search_engine_fk2`
  FOREIGN KEY (`type_id`) REFERENCES `#__reditem_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE          = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT         = 'redITEM Search Engine for users';

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;