ALTER TABLE `#__reditem_templates`
  DROP COLUMN `content`,
  ADD COLUMN `filename` VARCHAR (255) NOT NULL DEFAULT '' AFTER `name`;