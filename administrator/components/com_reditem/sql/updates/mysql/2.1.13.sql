CREATE TABLE IF NOT EXISTS `#__reditem_log_useractivity` (
    `id`          int(11)               NOT NULL AUTO_INCREMENT,
    `user_id`     int(11)               NOT NULL DEFAULT '0'                    COMMENT 'ID of user',
    `type`        varchar(255)          NOT NULL DEFAULT ''                     COMMENT 'Type of log (item.*, template.*, type.*, comment.*, field.*)',
    `target_id`   int(11)               NOT NULL DEFAULT '0'                    COMMENT 'ID of target (Base on type of log: item, template, type, comment, field)',
    `data`        text                  NOT NULL                                COMMENT 'JSON data of log',
    `created`     datetime              NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT 'Created time of log',
    PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COMMENT='redITEM Log User Activity'
    AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__reditem_watch_xref`
(
    `item_id`     int(11)   NOT NULL DEFAULT '0'    COMMENT 'ID of item',
    `user_id`     int(11)   NOT NULL DEFAULT '0'    COMMENT 'ID of user',
    PRIMARY KEY (`item_id`,`user_id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COMMENT='redITEM Watch feature reference';