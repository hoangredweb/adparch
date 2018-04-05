CREATE TABLE IF NOT EXISTS `#__reditem_comments` (
  `id`              int(11)     NOT NULL AUTO_INCREMENT,
  `parent_id`       int(11)     NOT NULL DEFAULT '0',
  `item_id`         int(11)     NOT NULL DEFAULT '0',
  `user_id`         int(11)     NOT NULL DEFAULT '0',
  `reply_user_id`   int(11)     NOT NULL DEFAULT '0',
  `private`         tinyint(1)  NOT NULL DEFAULT '0',
  `state`           tinyint(1)  NOT NULL DEFAULT '1',
  `trash`           tinyint(1)  NOT NULL DEFAULT '0',
  `comment`         mediumtext  NOT NULL DEFAULT '',
  `created`         datetime    NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    AUTO_INCREMENT=1
    COMMENT="redITEM Comments";

CREATE TABLE IF NOT EXISTS `#__reditem_item_rating` (
    `id`            int(11)     NOT NULL AUTO_INCREMENT,
    `item_id`       int(11)     NOT NULL DEFAULT '0',
    `rating`        float       NOT NULL DEFAULT '0',
    `user_id`       int(11)     NOT NULL DEFAULT '0',
    `rating_date`   datetime    NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    KEY `item_id` (`item_id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    AUTO_INCREMENT=1
    COMMENT="redITEM Item Ratings";

CREATE TABLE IF NOT EXISTS `#__reditem_item_reports` (
  `id`        int(11)     NOT NULL AUTO_INCREMENT,
  `item_id`   int(11)     NOT NULL DEFAULT '0',
  `user_id`   int(11)     NOT NULL DEFAULT '0',
  `reason`    mediumtext  NOT NULL DEFAULT '',
  `created`   datetime    NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    AUTO_INCREMENT=1
    COMMENT="redITEM Items Reports";

CREATE TABLE IF NOT EXISTS `#__reditem_comment_reports` (
    `id`            int(11)     NOT NULL AUTO_INCREMENT,
    `comment_id`    int(11)     NOT NULL DEFAULT '0',
    `user_id`       int(11)     NOT NULL DEFAULT '0',
    `reason`        mediumtext  NOT NULL DEFAULT '',
    `created`       datetime    NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    AUTO_INCREMENT=1
    COMMENT="redITEM Comments Reports";

CREATE TABLE IF NOT EXISTS `#__reditem_item_preview`
(
    `id`      varchar(100)     NOT NULL,
    `data`    text             NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__reditem_items_preview`;

ALTER TABLE `#__reditem_items` ADD `blocked` BOOLEAN NOT NULL DEFAULT FALSE AFTER `access`;
ALTER TABLE `#__reditem_items` ADD INDEX(`blocked`);