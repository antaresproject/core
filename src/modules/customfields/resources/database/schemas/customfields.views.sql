DROP VIEW IF EXISTS `view_fields`;

CREATE
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `view_fields` AS
    SELECT 
        `tf`.`id` AS `id`,
        `tf`.`brand_id` AS `brand_id`,
        `tf`.`group_id` AS `group_id`,
        `tf`.`type_id` AS `type_id`,
        `tf`.`name` AS `name`,
        `tf`.`label` AS `label`,
        `tf`.`description` AS `description`,
        `tf`.`placeholder` AS `placeholder`,
        `tf`.`value` AS `value`,
        `tf`.`force_display` AS `force_display`,
        `tf`.`additional_attributes` AS `additional_attributes`,
        `tf`.`imported` AS `imported`,
        GROUP_CONCAT(DISTINCT CONCAT(`tfto`.`id`, ':', `tfto`.`value`)
            ORDER BY `tfto`.`id` DESC
            SEPARATOR ';') AS `options`,
        GROUP_CONCAT(DISTINCT CONCAT(`tfto`.`value`, ':', `tfto`.`label`) ORDER BY `tfto`.`label` DESC SEPARATOR ';') AS `option_values`,    
        `tfc`.`name` AS `category_name`,
        `tfg`.`name` AS `group_name`,
        GROUP_CONCAT(DISTINCT CONCAT(`tfc`.`name`, '.', `tfg`.`name`)
            ORDER BY `tfv`.`id` DESC
            SEPARATOR ';') AS `namespace`,
        `tft`.`name` AS `type_name`,
        `tft`.`type` AS `type`,
        GROUP_CONCAT(DISTINCT CONCAT(`tfv`.`id`, ':', `tfv`.`name`)
            ORDER BY `tfv`.`id` DESC
            SEPARATOR ';') AS `validators`
    FROM
        ((((((`tbl_fields` `tf`
        LEFT JOIN `tbl_fields_groups` `tfg` ON ((`tf`.`group_id` = `tfg`.`id`)))
        LEFT JOIN `tbl_fields_categories` `tfc` ON ((`tfg`.`category_id` = `tfc`.`id`)))
        LEFT JOIN `tbl_fields_types` `tft` ON ((`tf`.`type_id` = `tft`.`id`)))
        LEFT JOIN `tbl_fields_types_options` `tfto` ON ((`tf`.`id` = `tfto`.`field_id`)))
        LEFT JOIN `tbl_fields_validators_config` `tfvc` ON ((`tf`.`id` = `tfvc`.`field_id`)))
        LEFT JOIN `tbl_fields_validators` `tfv` ON ((`tfvc`.`validator_id` = `tfv`.`id`)))
    GROUP BY `tf`.`id`