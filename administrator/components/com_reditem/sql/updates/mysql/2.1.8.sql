ALTER TABLE `#__reditem_item_reports` ADD `state` int(1) NOT NULL DEFAULT '1' AFTER `user_id`;
ALTER TABLE `#__reditem_comment_reports` ADD `state` int(1) NOT NULL DEFAULT '1' AFTER `user_id`;