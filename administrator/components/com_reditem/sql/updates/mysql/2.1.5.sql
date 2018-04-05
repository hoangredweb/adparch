DROP TABLE IF EXISTS `#__reditem_reporters`;

CREATE TABLE IF NOT EXISTS `#__reditem_reporter_point` (
    `id`        int(11)     NOT NULL AUTO_INCREMENT,
    `user_id`   int(11)     NOT NULL DEFAULT '0',
    `type`      varchar(10) NOT NULL DEFAULT '',
    `report_id` int(11)     NOT NULL DEFAULT '0',
    `point`     float       NOT NULL DEFAULT '5',
    PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    AUTO_INCREMENT=1
    COMMENT="redITEM Reporter Point";

CREATE TABLE IF NOT EXISTS `#__reditem_mail` (
  `id`                   int(11)      NOT NULL AUTO_INCREMENT,
  `name`                 varchar(255) NOT NULL,
  `subject`              varchar(255) NOT NULL,
  `description`          varchar(255) NOT NULL,
  `section`              varchar(255) NOT NULL,
  `content`              longtext     NOT NULL,
  `published`            tinyint(4)   NOT NULL,
  `ordering`             int(11)      NOT NULL DEFAULT '0',
  `checked_out`          int(11)      NOT NULL DEFAULT '0',
  `checked_out_time`     datetime     NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    AUTO_INCREMENT=1
    COMMENT="redITEM Mail Center";
