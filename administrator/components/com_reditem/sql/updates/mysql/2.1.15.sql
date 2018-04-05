CREATE TABLE IF NOT EXISTS `#__reditem_search_engine`
(
    `id`            int(11)     NOT NULL AUTO_INCREMENT,
    `user_id`       int(11)     NOT NULL DEFAULT '0'       COMMENT 'ID of user',
    `type_id`       int(11)     NOT NULL DEFAULT '0'       COMMENT 'ID of type',
    `send_mail`     tinyint(1)  NOT NULL DEFAULT '0'       COMMENT '1 => Send mail when new item match this search criteria',
    `search_data`   text        NOT NULL                   COMMENT 'Search data, in JSON format',
    PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COMMENT='redITEM Search Engine for users';