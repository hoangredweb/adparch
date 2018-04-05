ALTER TABLE `#__reditem_fields` ADD `searchable_in_backend` BOOLEAN NOT NULL DEFAULT FALSE AFTER `checked_out_time`;
ALTER TABLE `#__reditem_fields` ADD `searchable_in_frontend` BOOLEAN NOT NULL DEFAULT FALSE AFTER `checked_out_time`;
ALTER TABLE `#__reditem_fields` ADD INDEX(`searchable_in_backend`);
ALTER TABLE `#__reditem_fields` ADD INDEX(`searchable_in_frontend`);