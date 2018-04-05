CREATE TABLE IF NOT EXISTS `#__reditem_mail_settings` (
  `user_id`              int(11)             NOT NULL DEFAULT '0',
  `state`                int(1)              NOT NULL DEFAULT '1'  COMMENT '0: unsubscribed, 1: subscribed',
  `type`                 int(1)              NOT NULL DEFAULT '0'  COMMENT '0: send now, 1: send daily, 2: send weekly',
  `params`               varchar(2048)       NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8
  COMMENT='redITEM Notification Settings';