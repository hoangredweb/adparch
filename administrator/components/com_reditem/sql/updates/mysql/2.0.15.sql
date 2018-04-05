CREATE TABLE IF NOT EXISTS `#__reditem_config`
(
    `id`        int(11)     NOT NULL AUTO_INCREMENT,
    `params`    text        NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8;

INSERT INTO `#__reditem_config` (`id`, `params`) VALUES (1, '{"seo_title_config":""}');