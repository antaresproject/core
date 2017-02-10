CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `permissions` AS
    SELECT 
        `tc`.`id` AS `id`,
        `tp`.`brand_id` AS `brand_id`,
        `tc`.`name` AS `name`,
        `tc`.`full_name` AS `full_name`,
        `tc`.`status` AS `status`,
        `tc`.`path` AS `path`,
        `tc`.`description` AS `description`,
        `tc`.`author` AS `author`,
        `tc`.`url` AS `url`,
        `tc`.`version` AS `version`,
        `tc`.`options` AS `options`,
        `tcc`.`handles` AS `handles`,
        `tcc`.`autoload` AS `autoload`,
        `tcc`.`provides` AS `provides`,
        GROUP_CONCAT(DISTINCT `ta`.`id`
            ORDER BY `ta`.`id` DESC
            SEPARATOR ';') AS `actions`,
        GROUP_CONCAT(DISTINCT CONCAT(`tp`.`role_id`,
                    ':',
                    `tp`.`action_id`,
                    '=',
                    `tp`.`allowed`)
            ORDER BY `tp`.`id` ASC
            SEPARATOR ';') AS `permissions`
    FROM
        (((`tbl_components` `tc`
        LEFT JOIN `tbl_component_config` `tcc` ON ((`tc`.`id` = `tcc`.`component_id`)))
        LEFT JOIN `tbl_actions` `ta` ON ((`tc`.`id` = `ta`.`component_id`)))
        LEFT JOIN `tbl_permissions` `tp` ON (((`tc`.`id` = `tp`.`component_id`)
            AND (`ta`.`id` = `tp`.`action_id`))))
    GROUP BY `tp`.`brand_id` , `tc`.`id`
    ORDER BY `tc`.`order`