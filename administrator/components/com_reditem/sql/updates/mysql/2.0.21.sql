CREATE TABLE IF NOT EXISTS `#__reditem_items_preview`
(
    `id`      int(11)     NOT NULL,
    `data`    text        NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
)
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8;