# Add INDEX for reditem_items table
ALTER TABLE `#__reditem_items` ADD INDEX(`access`);
ALTER TABLE `#__reditem_items` ADD INDEX(`type_id`);
ALTER TABLE `#__reditem_items` ADD INDEX(`template_id`);
ALTER TABLE `#__reditem_items` ADD INDEX(`published`);
ALTER TABLE `#__reditem_items` ADD INDEX(`publish_down`);
ALTER TABLE `#__reditem_items` ADD INDEX(`publish_up`);
ALTER TABLE `#__reditem_items` ADD INDEX(`featured`);

# Add INDEX for reditem_categories table
ALTER TABLE `#__reditem_categories` ADD INDEX(`access`);
ALTER TABLE `#__reditem_categories` ADD INDEX(`type_id`);
ALTER TABLE `#__reditem_categories` ADD INDEX(`template_id`);
ALTER TABLE `#__reditem_categories` ADD INDEX(`published`);
ALTER TABLE `#__reditem_categories` ADD INDEX(`featured`);

# Add INDEX for reditem_fields table
ALTER TABLE `#__reditem_fields` ADD INDEX(`type_id`);
ALTER TABLE `#__reditem_fields` ADD INDEX(`type`);
ALTER TABLE `#__reditem_fields` ADD INDEX(`published`);
