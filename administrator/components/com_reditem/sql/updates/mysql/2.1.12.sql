CREATE TABLE IF NOT EXISTS `#__reditem_mail_queue` (
  `id`          int(11)             NOT NULL AUTO_INCREMENT,
  `state`       int(1)              NOT NULL DEFAULT '0'    COMMENT '0: unsend, 1: sent',
  `section`     varchar(255)        NOT NULL DEFAULT ''     COMMENT 'Mail section',
  `subject`     varchar(255)        NOT NULL DEFAULT ''     COMMENT 'Mail subject',
  `body`        text                NOT NULL                COMMENT 'Mail body',
  `recipient`   int(11)             NOT NULL DEFAULT '0'    COMMENT 'Mail recipient (User ID)',
  `created`     datetime            NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COMMENT='redITEM Mail Queue'
    AUTO_INCREMENT=1;