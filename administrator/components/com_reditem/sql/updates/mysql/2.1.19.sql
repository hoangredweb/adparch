 ALTER TABLE `#__reditem_fields` ADD `backend_filter` BOOLEAN NOT NULL DEFAULT FALSE AFTER `searchable_in_backend`;
 ALTER TABLE `#__reditem_fields` ADD INDEX(`backend_filter`);