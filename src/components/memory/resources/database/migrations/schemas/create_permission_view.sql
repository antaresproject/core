DROP VIEW IF EXISTS `permissions`;
CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `permissions` AS
    SELECT 
        `tc`.`id` AS `id`,
        `tp`.`brand_id` AS `brand_id`,
        `tc`.`vendor` AS `vendor`,
        `tc`.`name` AS `name`,
        `tc`.`status` AS `status`,
        `tc`.`options` AS `options`,
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
        (((`tbl_components` `tc`)
        LEFT JOIN `tbl_actions` `ta` ON ((`tc`.`id` = `ta`.`component_id`)))
        LEFT JOIN `tbl_permissions` `tp` ON (((`tc`.`id` = `tp`.`component_id`)
            AND (`ta`.`id` = `tp`.`action_id`))))
    GROUP BY `tp`.`brand_id` , `tc`.`id`
    ORDER BY `tc`.`id`