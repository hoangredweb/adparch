/* Add params column for items */
ALTER TABLE `#__reditem_fields` ADD `params` VARCHAR( 2048 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

/* Add default params value for exist "number" field */
UPDATE `#__reditem_fields`
SET `params` = '{"number_decimal_sepatator":".","number_thousand_separator":",","number_number_decimals":"2"}'
WHERE `type` = 'number';