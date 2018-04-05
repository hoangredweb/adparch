CREATE TABLE IF NOT EXISTS `#__reditem_reporters` (
    `user_id`       int(11)     NOT NULL,
    `point`         float       NOT NULL DEFAULT '0',
    PRIMARY KEY (`user_id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    AUTO_INCREMENT=1
    COMMENT="redITEM Reporters";