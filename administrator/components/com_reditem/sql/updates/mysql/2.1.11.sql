ALTER TABLE `#__reditem_mail_settings` ADD `secret` varchar(100) NOT NULL DEFAULT '' COMMENT 'Secret hash to check when unsubscribing' AFTER `type`;