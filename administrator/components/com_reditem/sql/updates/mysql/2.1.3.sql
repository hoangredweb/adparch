ALTER TABLE `#__reditem_categories` ADD `asset_id` INT(255) UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;

ALTER TABLE `#__reditem_items` ADD `asset_id` INT(255) UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;