-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jun 27, 2016 at 10:01 AM
-- Server version: 5.6.26-cll-lve
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `moreplan_dev`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `bullshit`()
    READS SQL DATA
begin
DECLARE counter1 INT DEFAULT 0;

SET counter1 = (SELECT COUNT(*) FROM account WHERE entered BETWEEN NOW() AND NOW());

end$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `chapter_sections_copy`(IN in_plan_id INT, IN old_chapter_id INT,IN new_chapter_id INT)
BEGIN

    DECLARE num_rows INT DEFAULT 0;
    DECLARE loop_count INT DEFAULT 0;
	DECLARE section_id INT DEFAULT 0;
	DECLARE new_section_id INT DEFAULT 0;
	DECLARE section_position SMALLINT DEFAULT 0;
	DECLARE section_title varchar(255);
	DECLARE section_content TEXT;
	DECLARE section_instructions TEXT;
	DECLARE section_example TEXT;
	
	DECLARE cur_sections CURSOR FOR SELECT section_id, title, content, `position`, instructions, example FROM section WHERE chapter_id = old_chapter_id;
	
	OPEN cur_sections;
	SELECT FOUND_ROWS() INTO num_rows;
	
    SET loop_count=0;
    
	 the_section_loop: LOOP
		
        SET loop_count=loop_count+1;
        IF loop_count > num_rows THEN
			LEAVE the_section_loop;
		END IF;
        
		FETCH  cur_sections INTO section_id, section_title, section_content, section_position,section_instructions,section_example;
	 
		INSERT INTO  `section` (`plan_id` ,`chapter_id` , `title` ,`content`, `position`, `instructions`, `example`  ,`entered` ,`updated`)
		VALUES ( in_plan_id, new_chapter_id , section_title , section_content ,section_position, section_instructions,section_example, NOW() , NOW());
	 
		SET new_section_id = LAST_INSERT_ID();
	 
		call sections_subsections_copy( in_plan_id, section_id,new_section_id);
	 
	END LOOP the_section_loop;

	CLOSE cur_sections; 
 
 
 
 
 
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graphs_load`(IN `in_organ_id` INT(11), IN `in_user_id` INT(11))
    READS SQL DATA
Select 
				graph.*,
                graph_kpi.kpi_id as kpi_id,
				users.first_name,
				users.last_name,
				graph_types.name as graph_type,
                kpi.name as kpi_name
				from graph 
                	INNER JOIN graph_kpi on graph_kpi.graph_id = graph.graph_id
					inner join kpi on graph_kpi.kpi_id = kpi.kpi_id
					inner join organisation on organisation.organ_id = kpi.organ_id
					inner join users on users.user_id = graph.entered_by 
					inner join graph_types on graph_types.graph_type_id = graph.graph_type_id
                    inner JOIN kpi_users on kpi.kpi_id = kpi_users.kpi_id
					where organisation.organ_id = in_organ_id and kpi_users.user_id = in_user_id$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_add`(IN `in_graph_name` VARCHAR(45), IN `in_description` TEXT, IN `in_graph_type_id` INT(11), IN `in_kpi_id` INT(11), IN `in_entered_by` INT(11), IN `in_entered` DATETIME, OUT `graph_id` INT(11))
BEGIN
 
 DECLARE tmp_graph_id INT DEFAULT 0;
  
 
   INSERT INTO  `moreplan_dev`.`graph` (
`graph_name`, `description` ,`graph_type_id`  ,`entered_by`, `entered`)
VALUES (in_graph_name,  in_description, in_graph_type_id,   in_entered_by, in_entered);
 
 
 SET tmp_graph_id = LAST_INSERT_ID();

  INSERT INTO  `moreplan_dev`.`graph_kpi` (
`graph_id`, `kpi_id`)
VALUES (tmp_graph_id,  in_kpi_id);
 
 INSERT INTO  `moreplan_dev`.`graph_users` (
`graph_id`, `user_id`)
VALUES (tmp_graph_id,  in_entered_by);

 INSERT INTO  `moreplan_dev`.`graph_settings` (
`graph_id`, `kpi_id`, `display_option`)
VALUES (tmp_graph_id,  in_kpi_id, 1);

 SET graph_id = tmp_graph_id;
 
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_delete`(IN `in_graph_id` INT)
    NO SQL
BEGIN 
DELETE FROM graph WHERE graph_id = in_graph_id;
DELETE FROM graph_kpi WHERE graph_id = in_graph_id;
DELETE FROM graph_users where graph_id = in_graph_id;
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_kpi_add`(IN `in_graph_id` INT, IN `in_kpi_id` INT, IN `graph_kpi_id` INT)
BEGIN
 
   INSERT INTO  `moreplan_dev`.`graph_kpi` (
`graph_id`, `kpi_id`)
VALUES (in_graph_id,  in_kpi_id);
 
 SET graph_kpi_id = LAST_INSERT_ID();
 
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_settings_add`(IN `in_graph_id` INT(11), IN `in_kpi_id` INT(11), OUT `graph_setting_id` INT(11))
INSERT INTO  `moreplan_dev`.`graph_settings` (
`graph_id`, `kpi_id`, `display_option`)
VALUES (in_graph_id,  in_kpi_id, 1)$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_settings_load`(IN `in_graph_id` INT(11))
    READS SQL DATA
Select * FROM graph_settings where graph_id = in_graph_id$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_specific_load`(IN `in_organ_id` INT(11), IN `in_user_id` INT(11), IN `in_graph_id` INT(11))
    READS SQL DATA
Select 
				graph.*,
                graph_kpi.kpi_id as kpi_id,
				users.first_name,
				users.last_name,
				graph_types.name as graph_type,
                kpi.name as kpi_name
				from graph 
                	INNER JOIN graph_kpi on graph_kpi.graph_id = graph.graph_id
					inner join kpi on graph_kpi.kpi_id = kpi.kpi_id
					inner join organisation on organisation.organ_id = kpi.organ_id
					inner join users on users.user_id = graph.entered_by 
                    INNER JOIN kpi_users on kpi.kpi_id = kpi_users.kpi_id
					inner join graph_types on graph_types.graph_type_id = graph.graph_type_id
					where organisation.organ_id = in_organ_id
                    and kpi_users.user_id = in_user_id
                    and graph.graph_id = in_graph_id$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_users_delete`(IN `in_graph_id` INT(11))
    NO SQL
BEGIN 
DELETE FROM graph_users WHERE graph_users.graph_id = in_graph_id;
END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_users_load`(IN `in_graph_id` INT(11))
    READS SQL DATA
Select graph_users.*, users.first_name, users.last_name					from graph_users inner join users on (users.user_id = graph_users.user_id) where graph_users.graph_id = in_graph_id$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_user_add`(IN `in_graph_id` INT(11), IN `in_user_id` INT(11), OUT `graph_user_id` INT)
INSERT INTO  `moreplan_dev`.`graph_users` (
`graph_id`, `user_id` )
VALUES (in_graph_id,  in_user_id)$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `graph_user_specific_load`(IN `in_graph_id` INT(11), IN `in_user_id` INT(11))
    READS SQL DATA
Select graph_users.*, users.first_name, users.last_name					from graph_users inner join users on (users.user_id = graph_users.user_id) where graph_users.graph_id = in_graph_id and graph_users.user_id = in_user_id$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpis_count`(IN `in_user_id` INT(11), IN `in_organ_id` INT(11), IN `in_plan_id` INT(11))
    READS SQL DATA
SELECT count(kpi.kpi_id) as count, frequency FROM `kpi` 
	INNER JOIN `kpi_users` on kpi_users.kpi_id = kpi.kpi_id
		where 
			kpi_users.user_id = in_user_id 
			and kpi.organ_id = in_organ_id
			and kpi.plan_id = in_plan_id	
		GROUP by frequency$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpis_load`(IN `in_organ_id` INT(11), IN `in_plan_id` INT(11), IN `in_owner_id` INT(11))
    READS SQL DATA
SELECT 
				kpi.kpi_id as kpi_id, kpi.organ_id, kpi.plan_id, kpi.icon, kpi.name, kpi.description, kpi.frequency,
				kpi_formats.name as format, kpi.kpi_format_id, kpi.best_direction, kpi.target, kpi.rag_1, kpi.rag_2,
				kpi.rag_3, kpi.rag_4, kpi.agg_type, kpi.current_trend, kpi.rollup_to_parent, kpi.parent_kpi_id, kpi.islocked
				FROM kpi, kpi_formats, kpi_users
				WHERE 
					kpi.kpi_format_id = kpi_formats.kpi_format_id
					AND kpi.organ_id = in_organ_id
					AND kpi.plan_id = in_plan_id
					AND kpi.owner_id = in_owner_id
					AND kpi.kpi_id = kpi_users.kpi_id
					AND kpi_users.user_id = in_owner_id
				ORDER BY name$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpis_load_as_admin`(IN `in_organ_id` INT(11), IN `in_plan_id` INT(11), IN `in_owner_id` INT(11))
    READS SQL DATA
SELECT 
				kpi.kpi_id as kpi_id, kpi.organ_id, kpi.plan_id, kpi.icon, kpi.name, kpi.description, kpi.frequency,
				kpi_formats.name as format, kpi.kpi_format_id, kpi.best_direction, kpi.target, kpi.rag_1, kpi.rag_2,
				kpi.rag_3, kpi.rag_4, kpi.agg_type, kpi.current_trend, kpi.rollup_to_parent, kpi.parent_kpi_id, kpi.islocked,
                count(kpi_users.user_id) as assignedUsers
				FROM kpi
                inner JOIN kpi_formats USING(kpi_format_id)
                inner JOIN kpi_users USING(kpi_id)
				WHERE 
					kpi.organ_id = in_organ_id
					AND kpi.plan_id = in_plan_id
					AND kpi.owner_id = in_owner_id
				GROUP BY kpi.kpi_id
				ORDER BY name asc$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpis_load_as_member`(IN `in_organ_id` INT(11), IN `in_plan_id` INT(11), IN `in_user_id` INT(11))
    READS SQL DATA
SELECT 
				kpi.kpi_id as kpi_id, kpi.organ_id, kpi.plan_id, kpi.icon, kpi.name, kpi.description, kpi.frequency,
				kpi_formats.name as format, kpi.kpi_format_id, kpi.best_direction, kpi.target, kpi.rag_1, kpi.rag_2,
				kpi.rag_3, kpi.rag_4, kpi.agg_type, kpi.current_trend, kpi.rollup_to_parent, kpi.parent_kpi_id, kpi.islocked
				FROM kpi
                inner JOIN kpi_formats USING(kpi_format_id)
                inner JOIN kpi_users USING(kpi_id)
				WHERE 
					kpi.organ_id = in_organ_id
					AND kpi.plan_id = in_plan_id
					AND kpi_users.user_id = in_user_id
				
				ORDER BY name asc$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_add`(IN `organ_id` INT(11), IN `plan_id` INT(11), IN `owner_id` INT(11), IN `name` VARCHAR(45), IN `icon` VARCHAR(45), IN `description` VARCHAR(45), IN `frequency` VARCHAR(45), IN `kpi_format_id` INT(11), IN `best_direction` VARCHAR(45), IN `target` VARCHAR(45), IN `rag_1` DECIMAL(10,0), IN `rag_2` DECIMAL(10,0), IN `rag_3` DECIMAL(10,0), IN `rag_4` DECIMAL(10,0), IN `agg_type` VARCHAR(45), IN `current_trend` VARCHAR(45), IN `rollup_to_parent` TINYINT(1), IN `parent_kpi_id` INT(11), OUT `kpi_id` INT(11))
BEGIN
 
  SET kpi_id = -999;
 
   INSERT INTO  `moreplan_dev`.`kpi` (
`kpi_id` ,`organ_id` ,`plan_id` ,`owner_id` ,`name`, `icon` ,`description` ,`frequency` ,`kpi_format_id` ,`best_direction` ,`target`, `rag_1`, `rag_2`, `rag_3`, `rag_4`, `agg_type`, `rollup_to_parent`, `parent_kpi_id`, `islocked`)
VALUES (NULL ,  organ_id,  plan_id,  owner_id, name, icon , description ,  frequency,  kpi_format_id,  best_direction,  target, rag_1, rag_2, rag_3, rag_4, agg_type, rollup_to_parent, parent_kpi_id, 0);
 
 
 SET kpi_id = LAST_INSERT_ID();
 
   INSERT INTO `kpi_users` (`kpi_id`, `user_id`) 
 VALUES (kpi_id, owner_id);
 
 
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_delete`(IN `in_user_id` INT(11), IN `in_kpi_id` INT(11), IN `in_organ_id` INT(11), IN `in_plan_id` INT(11))
    NO SQL
BEGIN DELETE FROM kpi WHERE kpi_id = in_kpi_id;
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_members_load`(IN `in_organ_id` INT, IN `in_kpi_id` INT, IN `in_owner_id` INT)
SELECT 
ku.user_id,
u.first_name,
u.last_name
FROM 
kpi_users ku 
inner join users u on  u.user_id = ku.user_id
inner join kpi k on k.kpi_id = ku.kpi_id
inner join organisation o on k.organ_id = o.organ_id
WHERE 
ku.kpi_id = in_kpi_id
AND 
k.owner_id = in_owner_id
AND
k.organ_id = in_organ_id
AND 
ku.user_id != in_owner_id
AND
u.deleted = 0$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_owner`(IN `in_organ_id` INT(11), IN `in_kpi_id` INT(11), IN `in_owner_id` INT(11))
    READS SQL DATA
SELECT * FROM kpi
WHERE 
kpi_id = in_kpi_id
AND 
owner_id = in_owner_id
AND
organ_id = in_organ_id$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_specific_load`(IN `in_organ_id` INT(11), IN `in_plan_id` INT(11), IN `in_owner_id` INT(11), IN `in_kpi_id` INT(11))
    READS SQL DATA
SELECT 
				kpi.kpi_id as kpi_id, kpi.organ_id, kpi.plan_id, kpi.icon, kpi.name, kpi.description, kpi.frequency,
				kpi_formats.name as format, kpi_formats.prefix as format_prefix, kpi_formats.suffix as format_suffix, kpi.kpi_format_id, kpi.best_direction, kpi.target, kpi.rag_1, kpi.rag_2,
				kpi.rag_3, kpi.rag_4, kpi.agg_type, kpi.current_trend, kpi.rollup_to_parent, kpi.parent_kpi_id, kpi.islocked
				FROM kpi, kpi_formats
				WHERE 
					kpi.kpi_format_id = kpi_formats.kpi_format_id
					AND kpi.organ_id = in_organ_id
					AND kpi.plan_id = in_plan_id
					AND kpi.owner_id = in_owner_id
					AND kpi.kpi_id = in_kpi_id
				ORDER BY name$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_specific_load_as_member`(IN `in_organ_id` INT, IN `in_plan_id` INT, IN `in_user_id` INT, IN `in_kpi_id` INT)
    READS SQL DATA
SELECT 
				kpi.*,
				kpi_formats.name as format,
kpi_formats.prefix as format_prefix, kpi_formats.suffix as format_suffix,
kpi_users.user_id
FROM kpi
inner JOIN kpi_formats on kpi.kpi_format_id = kpi_formats.kpi_format_id
INNER JOIN kpi_users on kpi.kpi_id = kpi_users.kpi_id
				WHERE 
					kpi.kpi_format_id = kpi_formats.kpi_format_id
					AND kpi.organ_id = in_organ_id
					AND kpi.plan_id = in_plan_id
					AND kpi_users.user_id = in_user_id
					AND kpi.kpi_id = in_kpi_id
				ORDER BY name$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_users_load`(IN `in_organ_id` INT, IN `in_kpi_id` INT, IN `in_owner_id` INT)
    READS SQL DATA
SELECT 
ku.user_id,
u.first_name,
u.last_name
FROM 
kpi_users ku 
inner join users u on  u.user_id = ku.user_id
inner join kpi k on k.kpi_id = ku.kpi_id
inner join organisation o on k.organ_id = o.organ_id
WHERE 
ku.kpi_id = in_kpi_id
AND 
k.owner_id = in_owner_id
AND
k.organ_id = in_organ_id
AND
u.deleted = 0$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_user_add`(IN `in_kpi_id` INT, IN `in_user_id` INT, OUT `kpi_user_id` INT)
BEGIN
 
 
   INSERT INTO  `moreplan_dev`.`kpi_users` (
`kpi_id`, `user_id`) VALUES (in_kpi_id,  in_user_id);
 
 
 SET kpi_user_id = LAST_INSERT_ID();

  
 
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_user_delete`(IN `in_kpi_id` INT, IN `in_user_id` INT)
    NO SQL
BEGIN DELETE FROM kpi_users WHERE kpi_id = in_kpi_id and user_id = in_user_id;
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `kpi_user_exists`(IN `in_user_id` INT, IN `in_kpi_id` INT)
    READS SQL DATA
SELECT COUNT(*) as count FROM kpi_users WHERE kpi_id = in_kpi_id and user_id = in_user_id$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `milestones_load_for_dash`(IN `in_user_id` INT, IN `in_plan_id` INT)
    READS SQL DATA
SELECT 
m.id,
m.owner_id, 
u.first_name,
u.last_name,
m.`name`, 
m.status,
m.startDate, 
m.duedate,
m.bShowOnDash
FROM 
milestones m inner join users u on  u.user_id = m.owner_id
WHERE 
m.bShowOnDash = 1 AND
plan_id = in_plan_id
ORDER BY m.duedate ASC$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_add`(IN `user_id` INT, IN `organ_name` VARCHAR(45), OUT `organ_id` INT, OUT `plan_id` INT)
BEGIN
 
	DECLARE tmp_organ_id INT DEFAULT 0;
	DECLARE var_plan_id INT DEFAULT 0;
	DECLARE var_account_id INT DEFAULT 0;
	DECLARE var_base_plan_id INT DEFAULT 0;   
    
	-- use the crunchers template for the plan for now
	SET var_base_plan_id = 2;

	-- for error
	SET organ_id = -999;

	-- get the account_id
	SELECT master_account_id INTO var_account_id FROM users WHERE users.user_id = user_id;

	-- create the organisation 
	INSERT INTO  `organisation` (
	`organ_id` ,`name` ,`account_id` ,`owner_id` ,`employees` ,`post_code` ,`entered_by` ,`entered` ,`updated_by` ,`updated`)
	VALUES (NULL ,  organ_name,  var_account_id,  user_id, NULL , NULL ,  user_id,  NOW(),  user_id,  NOW());

	SET tmp_organ_id = LAST_INSERT_ID();

	-- add the user to the organisation 
	INSERT INTO `organisation_users` (`organ_user_id`, `organ_id`, `user_id`, `organ_user_type_id`, `entered`, `updated`, `last_logged_in`) 
	VALUES (NULL, tmp_organ_id, user_id, '1', NOW(), NOW(), NOW());

	-- create the plan
	INSERT INTO `moreplan_dev`.`plan` (`plan_id`, `account_id`, `is_template`, `company_name`, `logo`, `owner_id`, `entered`, `created_by_user_id`, `updated`, `updated_by_user_id`, `organ_id`) 
	VALUES 
	(NULL, var_account_id, NULL, organ_name, NULL, user_id, NOW(), user_id, NOW(), user_id, tmp_organ_id);

	SET var_plan_id = LAST_INSERT_ID();
 
	-- return the new id if successful
	SET organ_id = tmp_organ_id;
	SET plan_id = var_plan_id;
 
 
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_delete`(IN `in_user_id` INT, IN `in_organ_id` INT)
    NO SQL
BEGIN

DECLARE var_plan_id INT DEFAULT 0;

DELETE FROM organisation WHERE organ_id = in_organ_id;

SELECT plan_id INTO var_plan_id FROM plan WHERE organ_id = in_organ_id;

CALL plan_delete ( var_plan_id );

END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_login`(IN `user_id` INT, IN `organ_id` INT)
BEGIN

UPDATE organisation_users SET last_logged_in = NOW() WHERE 
organisation_users.organ_id = organ_id
AND
organisation_users.user_id = user_id;

UPDATE organisation SET updated=NOW() , updated_by = user_id WHERE organisation.organ_id = organ_id;

SELECT organisation_users.organ_id, plan.plan_id, organisation_users.organ_user_type_id
FROM organisation_users, plan
WHERE 
organisation_users.organ_id = plan.organ_id  
AND
organisation_users.organ_id = organ_id
AND
organisation_users.user_id = user_id;

END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_member_exists`(IN `in_user_id` INT, IN `in_organ_id` INT)
    READS SQL DATA
SELECT COUNT(*) as count FROM organisation_users WHERE organ_id = in_organ_id and user_id = in_user_id$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_teams_load`(IN `in_user_id` INT, IN `in_organ_id` INT)
    READS SQL DATA
SELECT 
t.*,
u.first_name,
u.last_name
FROM
team t INNER JOIN users u ON u.user_id = t.manager_id
WHERE 
t.organ_id = in_organ_id
ORDER BY name$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_users_load`(IN `in_user_id` INT, IN `in_organ_id` INT)
    READS SQL DATA
SELECT 
ou.user_id,
u.first_name,
u.last_name,
u.email,
u.job_title,
u.tel_number,
u.company,
ou.organ_user_id,
ou.organ_user_type_id,
ou.last_logged_in
FROM
organisation_users ou inner join users u on  u.user_id = ou.user_id
WHERE 
ou.organ_id = in_organ_id
ORDER BY u.first_name,
u.last_name$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_user_add`(IN `in_user_id` INT, IN `in_new_user_id` INT, IN `in_organ_id` INT, OUT `organ_user_id` INT)
    MODIFIES SQL DATA
BEGIN

INSERT INTO `organisation_users` (`organ_user_id`, `organ_id`, `user_id`, `organ_user_type_id`, `entered`, `updated`, `last_logged_in`) 
 VALUES (NULL, in_organ_id, in_new_user_id, '1', NOW(), NOW(), NOW());

SET organ_user_id = LAST_INSERT_ID();

END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_user_delete`(IN `in_current_user_id` INT, IN `in_user_id_to_delete` INT, IN `in_organ_id` INT)
    MODIFIES SQL DATA
BEGIN

DELETE FROM organisation_users
WHERE `user_id` = in_user_id_to_delete AND `organ_id` = in_organ_id;

END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_user_exists`(IN `in_user_id` INT, IN `in_user_email` VARCHAR(255), IN `in_organ_id` INT)
    READS SQL DATA
SELECT COUNT(*) as count FROM users WHERE users.email = in_user_email AND user_id 
IN (SELECT user_id FROM organisation_users WHERE organ_id = in_organ_id)$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_user_exists_any`(IN `in_user_id` INT, IN `in_user_email` VARCHAR(255), IN `in_organ_id` INT)
    READS SQL DATA
SELECT COUNT(u.user_id) as count, u.user_id, u.first_name, u.last_name, u.email FROM users u, organisation_users ou WHERE u.user_id = ou.user_id AND u.email = in_user_email$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `organisation_user_member_add`(IN `in_master_account_id` INT, IN `in_first_name` VARCHAR(255), IN `in_last_name` VARCHAR(255), IN `in_username` VARCHAR(60), IN `in_email` VARCHAR(255), IN `in_hash` TEXT, IN `in_organ_id` INT, OUT `user_id` INT, OUT `organ_user_id` INT)
BEGIN

INSERT INTO `users` (
    `user_id`,
    `master_account_id`, 
    `user_type`, 
    `is_active`,
    `deleted`,
    `is_confirmed`, 
    `first_name`,
    `last_name`,
    `username`,
    `email`,
    `company`,
    `hash`,
    `ip_address`,
    `job_title`,
    `tel_number`,
    `utc_timezoneoffset`,
    `entered`,
    `updated`,
    `last_logged_in`,
    `about_me`,
    `profile_pic`) 
 VALUES (
     null,
     in_master_account_id,
     'member',
     '0',
     '0',
     '1',
     in_first_name, 
     in_last_name,
     in_username,
     in_email,
     '', 
     in_hash,
     '', 
     '',
     '',
     'UTC',
     NOW(),
     NOW(),
     NOW(),
     '', 
     '');

SET user_id = LAST_INSERT_ID();

INSERT INTO `organisation_users` (`organ_id`, `user_id`, `organ_user_type_id`, `entered`, `updated`, `last_logged_in`) 
 VALUES (in_organ_id, user_id, '1', NOW(), NOW(), NOW());

 SET organ_user_id = LAST_INSERT_ID();
 
END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `plan_delete`(IN `in_plan_id` INT)
    NO SQL
BEGIN

DELETE FROM chapter WHERE plan_id = in_plan_id;
DELETE FROM section WHERE plan_id = in_plan_id;
DELETE FROM subsection WHERE plan_id = in_plan_id;

END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `run_stats_v1`()
    MODIFIES SQL DATA
BEGIN
 
	DECLARE dtEndDate DATETIME;
	DECLARE dtYestDate DATETIME;
	DECLARE dtLastWeekDate DATETIME;
	DECLARE dtLastMonthDate DATETIME;
	DECLARE dtLastQuarterDate DATETIME;
	DECLARE statscounter INT;
	DECLARE counter1 INT;
	DECLARE counter2 INT;
	DECLARE counter3 INT;
	DECLARE counter4 INT;
	DECLARE counter5 INT;

	
	
	SET dtEndDate = NOW();
	SET dtYestDate = DATE_ADD(NOW(), INTERVAL -1 DAY);
	SET dtLastWeekDate = DATE_ADD(NOW(),INTERVAL -1 WEEK);
	SET dtLastMonthDate = DATE_ADD(NOW(),INTERVAL-1  MONTH);
	SET dtLastQuarterDate = DATE_ADD(NOW(),INTERVAL -3 MONTH);
   
	SET counter1 = (SELECT COUNT(*) FROM account) ;
	SET counter2 = (SELECT COUNT(*) FROM account WHERE entered BETWEEN dtYestDate AND dtEndDate);
	SET counter3 = (SELECT COUNT(*) FROM account WHERE entered BETWEEN dtLastWeekDate AND dtEndDate);
	SET counter4 = (SELECT COUNT(*) FROM account WHERE entered BETWEEN dtLastMonthDate AND dtEndDate);
	SET counter5 = (SELECT COUNT(*) FROM account WHERE entered BETWEEN dtLastQuarterDate AND dtEndDate);

	INSERT INTO stats (description,value1,value2,value3,value4,value5)
	VALUES ('Accounts' , counter1, counter2, counter3,counter4,counter5);

	SET counter1 = (SELECT COUNT(*) FROM organisation) ;
	SET counter2 = (SELECT COUNT(*) FROM organisation WHERE entered BETWEEN dtYestDate AND dtEndDate);
	SET counter3 = (SELECT COUNT(*) FROM organisation WHERE entered BETWEEN dtLastWeekDate AND dtEndDate);
	SET counter4 = (SELECT COUNT(*) FROM organisation WHERE entered BETWEEN dtLastMonthDate AND dtEndDate);
	SET counter5 = (SELECT COUNT(*) FROM organisation WHERE entered BETWEEN dtLastQuarterDate AND dtEndDate);

	INSERT INTO stats (description,value1,value2,value3,value4,value5)
	VALUES ('Organisations' , counter1, counter2, counter3,counter4,counter5);

	SET counter1 = (SELECT COUNT(*) FROM milestones) ;
	SET counter2 = (SELECT COUNT(*) FROM milestones WHERE entered_on BETWEEN dtYestDate AND dtEndDate);
	SET counter3 = (SELECT COUNT(*) FROM milestones WHERE entered_on BETWEEN dtLastWeekDate AND dtEndDate);
	SET counter4 = (SELECT COUNT(*) FROM milestones WHERE entered_on BETWEEN dtLastMonthDate AND dtEndDate);
	SET counter5 = (SELECT COUNT(*) FROM milestones WHERE entered_on BETWEEN dtLastQuarterDate AND dtEndDate);

	INSERT INTO stats (description,value1,value2,value3,value4,value5)
	VALUES ('Milestones' , counter1, counter2, counter3,counter4,counter5);

END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `sections_subsections_copy`(IN `in_plan_id` INT, IN `old_section_id` INT, IN `new_section_id` INT)
BEGIN

	DECLARE var_base_plan_id INT DEFAULT 0;
    DECLARE num_rows INT DEFAULT 0;
    DECLARE loop_count INT DEFAULT 0;
	DECLARE section_id INT DEFAULT 0;
	DECLARE new_section_id INT DEFAULT 0;
	DECLARE subsection_position SMALLINT DEFAULT 0;
	DECLARE subsection_title varchar(255);
	DECLARE subsection_description longtext;
	DECLARE subsection_data BLOB;
	DECLARE subsection_icon varchar(45);
	DECLARE subsection_type varchar(15);
	DECLARE subsection_chart_type INT DEFAULT 0;
	DECLARE subsection_instructions TEXT;
	DECLARE subsection_example TEXT;
	
	DECLARE cur_subsections CURSOR FOR SELECT `position`, 'title', 'description', 'data', 'icon', 'type','instructions','example', 'chart_type' FROM subsection WHERE section_id = old_section_id;
	
	OPEN cur_subsections;
	SELECT FOUND_ROWS() INTO num_rows;
	
    SET loop_count=0;
    
	 the_loop: LOOP
		
        SET loop_count=loop_count+1;
        IF loop_count > num_rows THEN
			LEAVE the_loop;
		END IF;
        
		FETCH  cur_subsections INTO subsection_position, subsection_title, subsection_description, subsection_data,subsection_icon,subsection_type,subsection_instructions,subsection_example, subsection_chart_type;
	 
		INSERT INTO  `subsection` (`plan_id` ,`section_id` , `position`,`title` ,`description`,`data`,`icon`, `type`, `instructions`, `example`  ,`entered` ,`updated`,`chart_type`)
		VALUES ( in_plan_id, new_section_id , subsection_position , subsection_title ,subsection_description, subsection_data,subsection_icon,subsection_type,subsection_instructions,subsection_example , NOW() , NOW(),subsection_chart_type);
	 
	END LOOP the_loop;

	CLOSE cur_subsections; 
 
 
 
 
 
 END$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `stats_load`()
    READS SQL DATA
SELECT 
id,
description,
value1,
value2,
value3,
value4,
value5
FROM
stats$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `team_manager_list_load`(IN `in_user_id` INT, IN `in_organ_id` INT)
    READS SQL DATA
SELECT DISTINCT 
u.user_id, u.first_name, u.last_name, u.email 
FROM users u INNER JOIN organisation_users ou ON u.user_id = ou.user_id
WHERE 
ou.organ_id = in_organ_id 
AND u.deleted = 0 ORDER BY u.first_name, u.last_name$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `team_members_load`(IN `in_user_id` INT, IN `in_team_id` INT)
    READS SQL DATA
SELECT 
u.user_id, 
u.first_name, 
u.last_name ,
u.email 
FROM 
users u INNER JOIN team_users tu ON u.user_id=tu.user_id 
WHERE 
tu.team_id = in_team_id AND u.deleted = 0 
ORDER BY u.first_name, u.last_name$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `team_non_members_load`(IN `in_user_id` INT, IN `in_team_id` INT, IN `in_organ_id` INT)
    READS SQL DATA
SELECT 
u.user_id, u.first_name, u.last_name, u.email 
FROM users u INNER JOIN organisation_users ou ON u.user_id = ou.user_id 
WHERE 
ou.organ_id = in_organ_id 
AND 
u.user_id NOT IN (SELECT user_id FROM team_users  WHERE team_id =in_team_id ) AND u.deleted =0 ORDER BY u.first_name, u.last_name$$

CREATE DEFINER=`moreplan`@`localhost` PROCEDURE `user_does_email_exist`(IN `in_user_id` INT, IN `in_user_email` VARCHAR(255), IN `in_organ_id` INT)
    READS SQL DATA
SELECT COUNT(*) FROM users WHERE users.email = in_user_email$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE IF NOT EXISTS `account` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `timezone` varchar(10) NOT NULL,
  `billing_date` datetime DEFAULT NULL,
  `entered` varchar(45) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `account_owner_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`account_id`, `name`, `timezone`, `billing_date`, `entered`, `updated`, `updated_by`, `account_owner_id`) VALUES
(2, 'Tim''s Account', 'UTC', NULL, '2015-07-10 10:43:41', NULL, NULL, 5),
(3, '', '', NULL, '2016-02-10 14:42:01', NULL, NULL, 11),
(4, '', '', NULL, '2016-04-21 12:24:15', NULL, NULL, 17),
(5, '', '', NULL, '2016-05-09 13:04:04', NULL, NULL, 31),
(6, '', '', NULL, '2016-05-11 13:31:00', NULL, NULL, 32),
(7, '', '', NULL, '2016-05-11 13:35:51', NULL, NULL, 33),
(8, '', '', NULL, '2016-05-11 23:42:03', NULL, NULL, 34),
(9, '', '', NULL, '2016-05-12 23:44:14', NULL, NULL, 35),
(10, '', '', NULL, '2016-06-22 12:02:36', NULL, NULL, 51),
(11, '', '', NULL, '2016-06-22 12:58:46', NULL, NULL, 52),
(12, '', '', NULL, '2016-06-24 14:57:44', NULL, NULL, 55);

-- --------------------------------------------------------

--
-- Table structure for table `chapter`
--

CREATE TABLE IF NOT EXISTS `chapter` (
  `chapter_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `position` smallint(6) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `updated` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`chapter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=151 ;

--
-- Dumping data for table `chapter`
--

INSERT INTO `chapter` (`chapter_id`, `plan_id`, `title`, `description`, `position`, `entered`, `updated`) VALUES
(3, 2, 'Company', NULL, 2, '2015-07-22 08:54:18', '2015-07-22 08:54:18'),
(5, 2, 'Opportunity', NULL, 1, '2015-07-22 08:55:31', '2015-07-22 08:55:31'),
(6, 2, 'Execution', NULL, 3, '2015-07-22 08:55:53', '2015-07-22 08:55:53'),
(9, 2, 'Financial Plan', NULL, 4, '2015-07-22 08:57:22', '2015-07-22 08:57:22'),
(10, 2, 'Appendix', NULL, 5, '2015-07-22 08:57:30', '2015-07-22 08:57:30'),
(14, 2, 'Executive Summary', NULL, 0, '2016-02-05 10:36:36', '2016-02-05 10:36:36'),
(15, 8, 'Company', NULL, 3, '2016-05-05 10:52:52', '2016-05-05 10:52:52'),
(16, 9, 'Company', NULL, 3, '2016-05-05 10:52:52', '2016-05-05 10:52:52'),
(17, 10, 'Company', NULL, 3, '2016-05-05 10:52:52', '2016-05-05 10:52:52'),
(18, 11, 'Company', NULL, 3, '2016-05-05 11:10:08', '2016-05-05 11:10:08'),
(19, 11, 'Opportunity', NULL, 1, '2016-05-05 11:10:08', '2016-05-05 11:10:08'),
(20, 11, 'Execution', NULL, 2, '2016-05-05 11:10:11', '2016-05-05 11:10:11'),
(21, 11, 'Financial Plan', NULL, 5, '2016-05-05 11:10:36', '2016-05-05 11:10:36'),
(22, 11, 'Appendix', NULL, 7, '2016-05-05 11:13:06', '2016-05-05 11:13:06'),
(23, 12, 'Company', NULL, 3, '2016-05-05 13:33:25', '2016-05-05 13:33:25'),
(24, 13, 'Company', NULL, 3, '2016-05-05 13:33:25', '2016-05-05 13:33:25'),
(55, 22, 'Company', '', 3, '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(56, 22, 'Opportunity', '', 1, '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(57, 22, 'Execution', '', 2, '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(58, 22, 'Financial Plan', '', 5, '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(59, 22, 'Appendix', '', 7, '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(60, 22, 'Executive Summary', '', 0, '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(61, 23, 'Company', '', 3, '2016-05-11 23:42:03', '2016-05-11 23:42:03'),
(62, 23, 'Opportunity', '', 1, '2016-05-11 23:42:03', '2016-05-11 23:42:03'),
(63, 23, 'Execution', '', 2, '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(64, 23, 'Financial Plan', '', 5, '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(65, 23, 'Appendix', '', 7, '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(66, 23, 'Executive Summary', '', 0, '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(67, 24, 'Company', '', 3, '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(68, 24, 'Opportunity', '', 1, '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(69, 24, 'Execution', '', 2, '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(70, 24, 'Financial Plan', '', 5, '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(71, 24, 'Appendix', '', 7, '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(72, 24, 'Executive Summary', '', 0, '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(73, 25, 'Company', '', 3, '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(74, 25, 'Opportunity', '', 1, '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(75, 25, 'Execution', '', 2, '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(76, 25, 'Financial Plan', '', 5, '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(77, 25, 'Appendix', '', 7, '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(78, 25, 'Executive Summary', '', 0, '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(79, 26, 'Company', '', 3, '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(80, 26, 'Opportunity', '', 1, '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(81, 26, 'Execution', '', 2, '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(82, 26, 'Financial Plan', '', 5, '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(83, 26, 'Appendix', '', 7, '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(84, 26, 'Executive Summary', '', 0, '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(91, 28, 'Company', '', 3, '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(92, 28, 'Opportunity', '', 1, '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(93, 28, 'Execution', '', 2, '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(94, 28, 'Financial Plan', '', 5, '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(95, 28, 'Appendix', '', 7, '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(96, 28, 'Executive Summary', '', 0, '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(97, 29, 'Company', '', 3, '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(98, 29, 'Opportunity', '', 1, '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(99, 29, 'Execution', '', 2, '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(100, 29, 'Financial Plan', '', 5, '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(101, 29, 'Appendix', '', 7, '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(102, 29, 'Executive Summary', '', 0, '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(103, 30, 'Company', '', 2, '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(104, 30, 'Opportunity', '', 1, '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(105, 30, 'Execution', '', 3, '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(106, 30, 'Financial Plan', '', 4, '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(107, 30, 'Appendix', '', 5, '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(108, 30, 'Executive Summary', '', 0, '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(127, 34, 'Company', '', 2, '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(128, 34, 'Opportunity', '', 1, '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(129, 34, 'Execution', '', 3, '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(130, 34, 'Financial Plan', '', 4, '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(131, 34, 'Appendix', '', 5, '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(132, 34, 'Executive Summary', '', 0, '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(133, 36, 'Company', '', 2, '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(134, 36, 'Opportunity', '', 1, '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(135, 36, 'Execution', '', 3, '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(136, 36, 'Financial Plan', '', 4, '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(137, 36, 'Appendix', '', 5, '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(138, 36, 'Executive Summary', '', 0, '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(139, 37, 'Company', '', 2, '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(140, 37, 'Opportunity', '', 1, '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(141, 37, 'Execution', '', 3, '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(142, 37, 'Financial Plan', '', 4, '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(143, 37, 'Appendix', '', 5, '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(144, 37, 'Executive Summary', '', 0, '2016-06-22 12:58:47', '2016-06-22 12:58:47'),
(145, 38, 'Company', '', 2, '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(146, 38, 'Opportunity', '', 1, '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(147, 38, 'Execution', '', 3, '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(148, 38, 'Financial Plan', '', 4, '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(149, 38, 'Appendix', '', 5, '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(150, 38, 'Executive Summary', '', 0, '2016-06-24 14:57:44', '2016-06-24 14:57:44');

-- --------------------------------------------------------

--
-- Table structure for table `chart_type`
--

CREATE TABLE IF NOT EXISTS `chart_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `chart_type`
--

INSERT INTO `chart_type` (`id`, `name`) VALUES
(1, 'Revenue'),
(2, 'Expenses'),
(3, 'Net ProfitNet Profit'),
(4, 'Financial Highlights (Year one)'),
(5, 'Financial Highlights (All years)'),
(6, 'Revenue by Month (Classic)'),
(7, 'Revenue by Year (Classic)'),
(8, 'Expenses by Month (Classic)'),
(9, 'Expenses by Year (Classic)'),
(10, 'Gross Margin by Month (Classic)'),
(11, 'Gross Margin by Year (Classic)'),
(12, 'Net Profit by Month (Classic)'),
(13, 'Net Profit by Year (Classic)'),
(14, 'Cash Flow by Month (Classic)'),
(15, 'Cash Flow by Year (Classic)');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE IF NOT EXISTS `feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `organ_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL COMMENT '1 = Green , 2 = Amber , 3 = Red',
  `comments` varchar(255) DEFAULT NULL,
  `enteredon` datetime DEFAULT NULL,
  `URL` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `organ_id`, `plan_id`, `status_id`, `comments`, `enteredon`, `URL`) VALUES
(1, 1, 2, 3, 3, 'this is a comment', '2016-06-27 01:05:12', '0');

-- --------------------------------------------------------

--
-- Table structure for table `forecast`
--

CREATE TABLE IF NOT EXISTS `forecast` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `organ_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `url` text,
  `file` text,
  `file_url` varchar(255) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `forecast`
--

INSERT INTO `forecast` (`id`, `plan_id`, `organ_id`, `owner_id`, `url`, `file`, `file_url`, `entered`) VALUES
(2, 2, 2, 5, 'bda', 'Test_Paper.docx', 'http://dev.moreplanner.com/uploads/docs/Test_Paper.docx', '2016-05-12 12:49:26'),
(4, 19, 19, 31, 'https://docs.google.com/document/d/1XXnIMzINk84F-u6QBiRjCIF3C2EgUJ7UsUwIkbrt7Q0/edit?ts=56f158f9', NULL, NULL, '2016-05-12 23:06:04'),
(5, 19, 19, 31, NULL, 'Demo_Company.pdf', 'http://dev.moreplanner.com/uploads/docs/Demo_Company.pdf', '2016-05-12 23:06:58'),
(6, 22, 22, 5, NULL, 'Maenporth_Electrical-Method-Statement.docx', 'http://dev.moreplanner.com/uploads/docs/Maenporth_Electrical-Method-Statement.docx', '2016-06-09 10:45:25');

-- --------------------------------------------------------

--
-- Table structure for table `graph`
--

CREATE TABLE IF NOT EXISTS `graph` (
  `graph_id` int(11) NOT NULL AUTO_INCREMENT,
  `graph_name` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `graph_type_id` int(11) NOT NULL,
  `entered_by` int(11) NOT NULL,
  `entered` datetime NOT NULL,
  PRIMARY KEY (`graph_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `graph`
--

INSERT INTO `graph` (`graph_id`, `graph_name`, `description`, `graph_type_id`, `entered_by`, `entered`) VALUES
(6, 'test 3', 'aa', 1, 14, '2016-05-02 23:29:52'),
(7, 'test final 1', 'david1', 2, 14, '2016-05-02 23:30:05'),
(21, 'Graph 1', '', 1, 17, '2016-05-16 08:05:53'),
(24, 'day', '', 3, 17, '2016-05-25 09:27:45'),
(25, 'Production', '', 1, 5, '2016-05-25 14:53:56'),
(26, 'Sales', '', 3, 5, '2016-05-25 14:56:11'),
(29, 'pie day', '', 2, 17, '2016-06-03 09:18:34'),
(30, 'bar day', '', 3, 17, '2016-06-03 09:18:48'),
(31, 'line day', '', 1, 17, '2016-06-03 09:19:02'),
(32, 'Calls', '', 1, 36, '2016-06-03 10:10:11'),
(33, 'Test 32', '', 1, 5, '2016-06-08 23:11:56'),
(36, 'test 1 week', '', 3, 5, '2016-06-09 09:31:16'),
(37, 't2', '', 1, 5, '2016-06-09 09:41:24'),
(38, 'test 1', '', 1, 17, '2016-06-09 15:35:05'),
(39, 'test 43', '', 1, 5, '2016-06-10 08:52:30'),
(40, 'test 5', '', 1, 5, '2016-06-10 10:11:19'),
(41, 'Gym', '', 3, 5, '2016-06-10 12:01:23'),
(42, 'test 6', '', 3, 5, '2016-06-14 10:07:56'),
(43, 'Pressups', '', 1, 5, '2016-06-14 15:48:11');

-- --------------------------------------------------------

--
-- Table structure for table `graph_kpi`
--

CREATE TABLE IF NOT EXISTS `graph_kpi` (
  `graph_kpi_id` int(11) NOT NULL AUTO_INCREMENT,
  `graph_id` int(11) NOT NULL,
  `kpi_id` int(11) NOT NULL,
  PRIMARY KEY (`graph_kpi_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `graph_kpi`
--

INSERT INTO `graph_kpi` (`graph_kpi_id`, `graph_id`, `kpi_id`) VALUES
(1, 6, 2),
(2, 7, 2),
(14, 21, 21),
(17, 24, 20),
(18, 25, 24),
(19, 26, 27),
(22, 29, 16),
(23, 30, 16),
(24, 31, 16),
(25, 32, 30),
(26, 33, 29),
(29, 36, 27),
(30, 37, 29),
(31, 38, 25),
(32, 39, 27),
(33, 40, 25),
(34, 41, 31),
(35, 42, 33),
(36, 43, 32);

-- --------------------------------------------------------

--
-- Table structure for table `graph_settings`
--

CREATE TABLE IF NOT EXISTS `graph_settings` (
  `graph_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `graph_id` int(11) NOT NULL,
  `kpi_id` int(11) NOT NULL,
  `display_option` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`graph_setting_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `graph_settings`
--

INSERT INTO `graph_settings` (`graph_setting_id`, `graph_id`, `kpi_id`, `display_option`) VALUES
(1, 26, 26, 1),
(2, 25, 24, 0),
(3, 21, 21, 1),
(4, 23, 18, 1),
(5, 33, 29, 1),
(6, 34, 30, 0),
(7, 35, 25, 1),
(8, 36, 25, 1),
(9, 37, 29, 1),
(10, 38, 25, 1),
(11, 39, 25, 1),
(12, 40, 30, 1),
(13, 41, 31, 0),
(14, 31, 16, 1),
(15, 29, 16, 1),
(16, 30, 16, 1),
(17, 42, 26, 1),
(18, 43, 32, 1);

-- --------------------------------------------------------

--
-- Table structure for table `graph_types`
--

CREATE TABLE IF NOT EXISTS `graph_types` (
  `graph_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`graph_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `graph_types`
--

INSERT INTO `graph_types` (`graph_type_id`, `name`, `disabled`) VALUES
(1, 'Line', 0),
(2, 'Pie - single kpi', 0),
(3, 'Bar', 0),
(4, 'Pie - multiple kpi', 1);

-- --------------------------------------------------------

--
-- Table structure for table `graph_users`
--

CREATE TABLE IF NOT EXISTS `graph_users` (
  `graph_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `graph_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`graph_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=92 ;

--
-- Dumping data for table `graph_users`
--

INSERT INTO `graph_users` (`graph_user_id`, `graph_id`, `user_id`) VALUES
(50, 25, 5),
(52, 26, 5),
(60, 38, 17),
(63, 39, 5),
(72, 36, 5),
(77, 33, 5),
(78, 37, 5),
(79, 40, 5),
(80, 21, 17),
(83, 42, 5),
(86, 41, 5),
(87, 43, 5),
(88, 43, 39),
(89, 31, 5),
(90, 29, 5),
(91, 30, 5);

-- --------------------------------------------------------

--
-- Table structure for table `kpi`
--

CREATE TABLE IF NOT EXISTS `kpi` (
  `kpi_id` int(11) NOT NULL AUTO_INCREMENT,
  `organ_id` int(11) DEFAULT NULL,
  `plan_id` varchar(45) DEFAULT NULL,
  `owner_id` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `icon` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `frequency` varchar(45) DEFAULT NULL,
  `kpi_format_id` int(11) DEFAULT NULL,
  `best_direction` varchar(45) DEFAULT NULL,
  `target` decimal(10,2) DEFAULT NULL,
  `rag_1` decimal(10,0) DEFAULT NULL,
  `rag_2` decimal(10,0) DEFAULT NULL,
  `rag_3` decimal(10,0) DEFAULT NULL,
  `rag_4` decimal(10,0) DEFAULT NULL,
  `agg_type` varchar(45) DEFAULT NULL,
  `current_trend` varchar(45) DEFAULT NULL,
  `rollup_to_parent` tinyint(1) DEFAULT NULL,
  `parent_kpi_id` int(11) DEFAULT NULL,
  `islocked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`kpi_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `kpi`
--

INSERT INTO `kpi` (`kpi_id`, `organ_id`, `plan_id`, `owner_id`, `name`, `icon`, `description`, `frequency`, `kpi_format_id`, `best_direction`, `target`, `rag_1`, `rag_2`, `rag_3`, `rag_4`, `agg_type`, `current_trend`, `rollup_to_parent`, `parent_kpi_id`, `islocked`) VALUES
(15, 7, '7', '17', 'da', '', '', 'monthly', 3, 'up', '213.00', '3', '1', '0', '3', 'sum_total', NULL, 0, 0, 0),
(16, 2, '2', '5', 'Telemarketing Calls', '', '', 'daily', 1, 'up', '100.00', '25', '50', '75', '100', 'average', NULL, 0, 0, 1),
(17, 2, '2', '5', 'Web Stats', '', '', 'weekly', 1, 'up', '1000.00', '250', '500', '750', '1000', 'sum_total', NULL, 0, 0, 1),
(18, 2, '2', '5', 'Quarterly Something', '', '', 'quarterly', 1, 'up', '1000.00', '250', '500', '750', '1000', 'sum_total', NULL, 0, 0, 1),
(21, 28, '28', '17', 'Branch 1', '', '', 'daily', 1, 'up', '1000.00', '0', '0', '0', '0', 'sum_total', NULL, 0, 0, 0),
(24, 29, '29', '5', 'No of widgets produced', '', '', 'weekly', 1, 'up', '7500.00', '0', '2500', '5000', '7500', 'sum_total', NULL, 0, 0, 1),
(25, 29, '29', '5', 'No of faulty Widgets', '', '', 'weekly', 1, 'down', '1.00', '100', '75', '50', '25', 'sum_total', NULL, 0, 0, 1),
(26, 29, '29', '5', 'Sales - No of widgets sold', '', '', 'monthly', 1, 'up', '30000.00', '20000', '25000', '27500', '30000', 'sum_total', NULL, 0, 0, 1),
(27, 29, '29', '5', 'Sales - Debtor Days', '', '', 'monthly', 1, 'down', '30.00', '120', '90', '60', '30', 'sum_total', NULL, 0, 0, 1),
(28, 2, '2', '5', 'test', '', '', 'monthly', 1, 'down', NULL, '0', '0', '0', '0', 'sum_total', NULL, 0, 0, 0),
(29, 2, '2', '5', 'test 32', '', '', 'daily', 1, 'up', '312.00', '0', '0', '0', '0', 'sum_total', NULL, 0, 0, 1),
(30, 29, '29', '5', 'Daily Test', '', '', 'daily', 1, 'up', '100.00', '25', '50', '75', '100', 'average', NULL, 0, 0, 1),
(31, 34, '34', '5', 'Gym Sessions', '', '', 'weekly', 1, 'up', '4.00', '1', '2', '3', '4', 'sum_total', NULL, 0, 0, 1),
(32, 34, '34', '5', 'Pressups', '', '', 'weekly', 1, 'up', '100.00', '0', '0', '0', '0', 'average', NULL, 0, 0, 1),
(33, 29, '29', '5', 'test', '', '', 'daily', 7, 'up', '0.00', '0', '0', '0', '0', 'sum_total', NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `kpi_data`
--

CREATE TABLE IF NOT EXISTS `kpi_data` (
  `kpi_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `kpi_id` int(11) DEFAULT NULL,
  `organ_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `actual` decimal(10,2) DEFAULT NULL,
  `target` decimal(10,2) DEFAULT NULL,
  `difference` decimal(10,0) DEFAULT NULL,
  `trend` decimal(10,0) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `kpi_datacol1` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`kpi_data_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=143 ;

--
-- Dumping data for table `kpi_data`
--

INSERT INTO `kpi_data` (`kpi_data_id`, `plan_id`, `kpi_id`, `organ_id`, `user_id`, `actual`, `target`, `difference`, `trend`, `notes`, `date`, `entered`, `kpi_datacol1`) VALUES
(12, 5, 10, 5, 5, '25.00', '100.00', '0', '0', NULL, '2016-04-15', NULL, NULL),
(13, 5, 12, 5, 5, '1.00', '5.00', '0', '0', NULL, '2016-04-15', NULL, NULL),
(14, 5, 13, 5, 5, '50.00', '100.00', '0', '0', NULL, '2016-04-17', NULL, NULL),
(15, 2, 14, 2, 5, '75.00', '100.00', '0', '0', NULL, '2016-04-26', NULL, NULL),
(16, 2, 14, 2, 5, '75.00', '100.00', '0', '0', NULL, '2016-04-27', NULL, NULL),
(17, 2, 14, 2, 5, '75.00', '100.00', '0', '0', NULL, '2016-04-28', NULL, NULL),
(18, 2, 14, 2, 5, '75.00', '100.00', '0', '0', NULL, '2016-04-29', NULL, NULL),
(19, 2, 14, 2, 5, '75.00', '100.00', '0', '0', NULL, '2016-04-30', NULL, NULL),
(20, 2, 14, 2, 5, '50.00', '100.00', '0', '0', NULL, '2016-04-18', NULL, NULL),
(21, 2, 14, 2, 5, '50.00', '100.00', '0', '0', NULL, '2016-04-19', NULL, NULL),
(22, 2, 14, 2, 5, '50.00', '100.00', '0', '0', NULL, '2016-04-20', NULL, NULL),
(23, 2, 16, 2, 5, '15.00', '100.00', '0', '0', NULL, '2016-04-30', NULL, NULL),
(24, 2, 16, 2, 5, '10.00', '100.00', '0', '0', NULL, '2016-05-01', NULL, NULL),
(25, 2, 16, 2, 5, '25.00', '100.00', '0', '0', NULL, '2016-05-02', NULL, NULL),
(26, 2, 17, 2, 5, '750.00', NULL, '0', '0', NULL, '2016-04-14', NULL, NULL),
(27, 2, 17, 2, 5, '1000.00', '31.00', '0', '0', NULL, '2016-04-21', NULL, NULL),
(28, 2, 14, 2, 5, '90.00', '100.00', '0', '0', NULL, '2016-05-03', NULL, NULL),
(29, 2, 14, 2, 5, '80.00', '100.00', '0', '0', NULL, '2016-05-04', NULL, NULL),
(30, 2, 14, 2, 5, '105.00', '100.00', '0', '0', NULL, '2016-05-05', NULL, NULL),
(31, 2, 14, 2, 5, '110.00', '100.00', '0', '0', NULL, '2016-05-06', NULL, NULL),
(32, 2, 18, 2, 5, '900.00', '1000.00', '0', '0', NULL, '2015-07-10', NULL, NULL),
(33, 2, 18, 2, 5, '950.00', '1000.00', '0', '0', NULL, '2015-10-10', NULL, NULL),
(34, 2, 18, 2, 5, '1300.00', '1000.00', '0', '0', NULL, '2016-01-10', NULL, NULL),
(35, 2, 18, 2, 5, '700.00', '1000.00', '0', '0', NULL, '2016-04-10', NULL, NULL),
(36, 22, 19, 22, 14, '32.00', '50.00', '0', '0', NULL, '2016-05-08', NULL, NULL),
(37, 22, 19, 22, 14, '44.00', '50.00', '0', '0', NULL, '2016-05-09', NULL, NULL),
(38, 22, 19, 22, 14, '31.00', '50.00', '0', '0', NULL, '2016-05-10', NULL, NULL),
(39, 22, 19, 22, 14, '15.00', '50.00', '0', '0', NULL, '2016-05-11', NULL, NULL),
(40, 2, 20, 2, 5, '345.00', NULL, '0', '0', NULL, '2016-05-10', NULL, NULL),
(41, 2, 17, 2, 5, '3.00', '1000.00', '0', '0', NULL, '2016-04-28', NULL, NULL),
(42, 2, 17, 2, 17, '999.00', '1000.00', '0', '0', NULL, '2016-05-05', NULL, NULL),
(43, 2, 20, 2, 5, '31.00', '1.00', '0', '0', NULL, '2016-05-15', NULL, NULL),
(44, 2, 17, 2, 5, '123.00', NULL, '0', '0', NULL, '2016-05-12', NULL, NULL),
(45, 2, 17, 2, 5, '1250.00', '100012.00', '0', '0', NULL, '2016-05-19', NULL, NULL),
(46, 2, 14, 2, 5, '1.00', '100.00', '0', '0', NULL, '2016-05-14', NULL, NULL),
(47, 2, 14, 2, 5, '2.00', '100.00', '0', '0', NULL, '2016-05-15', NULL, NULL),
(48, 2, 14, 2, 5, '3.00', '100.00', '0', '0', NULL, '2016-05-16', NULL, NULL),
(49, 2, 17, 2, 5, NULL, '1000.00', '0', '0', NULL, '2016-05-26', NULL, NULL),
(50, 2, 16, 2, 5, '1.00', '100.00', '0', '0', NULL, '2016-05-15', NULL, NULL),
(51, 2, 16, 2, 5, '2.00', '100.00', '0', '0', NULL, '2016-05-16', NULL, NULL),
(52, 2, 16, 2, 5, '3.00', '100.00', '0', '0', NULL, '2016-05-17', NULL, NULL),
(53, 2, 16, 2, 5, '4.00', '100.00', '0', '0', NULL, '2016-05-18', NULL, NULL),
(54, 2, 16, 2, 5, '5.00', NULL, '0', '0', NULL, '2016-05-19', NULL, NULL),
(55, 2, 20, 2, 17, '23.00', '0.00', '0', '0', NULL, '2016-05-16', NULL, NULL),
(56, 2, 20, 2, 17, NULL, '123.00', '0', '0', NULL, '2016-05-17', NULL, NULL),
(57, 2, 18, 2, 5, '1500.00', '1000.00', '0', '0', NULL, '2016-07-10', NULL, NULL),
(58, 2, 20, 2, 5, '8.00', '0.00', '0', '0', NULL, '2016-05-17', NULL, NULL),
(59, 2, 20, 2, 5, NULL, '9.00', '0', '0', NULL, '2016-05-18', NULL, NULL),
(60, 2, 20, 2, 5, NULL, NULL, '0', '0', NULL, '2016-05-19', NULL, NULL),
(61, 2, 16, 2, 5, '89.00', '100.00', '0', '0', NULL, '2016-05-20', NULL, NULL),
(62, 2, 17, 2, 5, '8.00', '1000.00', '0', '0', NULL, '2016-05-05', NULL, NULL),
(63, 2, 20, 2, 17, '100.00', '90.00', '0', '0', NULL, '2016-05-22', NULL, NULL),
(64, 2, 20, 2, 17, NULL, '21.00', '0', '0', NULL, '2016-05-23', NULL, NULL),
(65, 2, 20, 2, 17, '50.00', NULL, '0', '0', NULL, '2016-05-24', NULL, NULL),
(66, 2, 20, 2, 17, '12.00', '100.00', '0', '0', NULL, '2016-05-25', NULL, NULL),
(67, 2, 20, 2, 17, '68.00', '0.00', '0', '0', NULL, '2016-05-26', NULL, NULL),
(68, 2, 22, 2, 17, '100.00', '0.00', '0', '0', NULL, '2016-05-22', NULL, NULL),
(69, 29, 25, 29, 5, '10.00', '1.00', '0', '0', NULL, '2016-05-05', NULL, NULL),
(70, 29, 25, 29, 5, '25.00', '1.00', '0', '0', NULL, '2016-05-12', NULL, NULL),
(71, 29, 25, 29, 5, '12.00', '1.00', '0', '0', NULL, '2016-05-19', NULL, NULL),
(72, 29, 25, 29, 5, '13.00', '1.00', '0', '0', NULL, '2016-05-26', NULL, NULL),
(73, 29, 25, 29, 5, '20.00', '1.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(74, 29, 24, 29, 5, '6478.00', '7500.00', '0', '0', NULL, '2016-05-05', NULL, NULL),
(75, 29, 24, 29, 5, '6852.00', '7500.00', '0', '0', NULL, '2016-05-12', NULL, NULL),
(76, 29, 24, 29, 5, '5928.00', '7500.00', '0', '0', NULL, '2016-05-19', NULL, NULL),
(77, 29, 24, 29, 5, '6003.00', '6000.00', '0', '0', NULL, '2016-05-26', NULL, NULL),
(78, 29, 24, 29, 5, '6234.00', '7500.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(79, 29, 26, 29, 5, '22000.00', '30000.00', '0', '0', NULL, '2016-02-05', NULL, NULL),
(80, 29, 26, 29, 5, '21000.00', '30000.00', '0', '0', NULL, '2016-03-05', NULL, NULL),
(81, 29, 26, 29, 5, '35000.00', '30000.00', '0', '0', NULL, '2016-04-05', NULL, NULL),
(82, 29, 26, 29, 5, '24000.00', '30000.00', '0', '0', NULL, '2016-05-05', NULL, NULL),
(83, 29, 26, 29, 5, '19500.00', '30000.00', '0', '0', NULL, '2016-06-05', NULL, NULL),
(84, 2, 16, 2, 5, NULL, '100.00', '0', '0', NULL, '2016-05-31', NULL, NULL),
(85, 2, 29, 2, 5, '3.00', '312.00', '0', '0', NULL, '2016-06-01', NULL, NULL),
(86, 2, 29, 2, 5, '2.00', '312.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(87, 2, 16, 2, 5, '1.00', '100.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(88, 2, 29, 2, 5, '2.00', NULL, '0', '0', NULL, '2016-05-31', NULL, NULL),
(89, 2, 16, 2, 5, NULL, NULL, '0', '0', NULL, '2016-06-03', NULL, NULL),
(90, 2, 16, 2, 17, '32.00', '100.00', '0', '0', NULL, '2016-05-24', NULL, NULL),
(91, 29, 30, 29, 5, '50.00', '100.00', '0', '0', NULL, '2016-05-26', NULL, NULL),
(92, 29, 30, 29, 5, '75.00', '100.00', '0', '0', NULL, '2016-05-27', NULL, NULL),
(93, 29, 30, 29, 5, '88.00', '100.00', '0', '0', NULL, '2016-05-28', NULL, NULL),
(94, 29, 30, 29, 5, '105.00', '100.00', '0', '0', NULL, '2016-05-29', NULL, NULL),
(95, 29, 30, 29, 5, '26.00', '100.00', '0', '0', NULL, '2016-05-30', NULL, NULL),
(96, 29, 30, 29, 5, '70.00', '100.00', '0', '0', NULL, '2016-05-31', NULL, NULL),
(97, 29, 30, 29, 5, '71.00', '100.00', '0', '0', NULL, '2016-06-01', NULL, NULL),
(98, 29, 30, 29, 5, '75.00', '100.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(99, 2, 16, 2, 17, '100.00', '100.00', '0', '0', NULL, '2016-05-31', NULL, NULL),
(100, 2, 16, 2, 17, '90.00', '100.00', '0', '0', NULL, '2016-06-01', NULL, NULL),
(101, 2, 16, 2, 17, '70.00', '100.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(102, 2, 16, 2, 17, '110.00', '100.00', '0', '0', NULL, '2016-06-03', NULL, NULL),
(103, 29, 30, 29, 36, '75.00', '100.00', '0', '0', NULL, '2016-05-31', NULL, NULL),
(104, 29, 30, 29, 36, '80.00', '100.00', '0', '0', NULL, '2016-06-01', NULL, NULL),
(105, 29, 30, 29, 36, '80.00', '100.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(106, 29, 30, 29, 36, '120.00', '100.00', '0', '0', NULL, '2016-06-03', NULL, NULL),
(107, 29, 30, 29, 36, '85.00', '100.00', '0', '0', NULL, '2016-06-04', NULL, NULL),
(108, 2, 29, 2, 5, '27.00', '312.00', '0', '0', NULL, '2016-05-30', NULL, NULL),
(109, 2, 29, 2, 5, '50.00', '312.00', '0', '0', NULL, '2016-05-29', NULL, NULL),
(110, 29, 25, 29, 17, '3.00', '1.00', '0', '0', NULL, '2016-05-19', NULL, NULL),
(111, 29, 25, 29, 17, '1.00', '1.00', '0', '0', NULL, '2016-05-26', NULL, NULL),
(112, 29, 25, 29, 17, '2.00', '1.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(113, 29, 25, 29, 17, '3.00', '1.00', '0', '0', NULL, '2016-06-09', NULL, NULL),
(114, 29, 30, 29, 5, '90.00', '85.00', '0', '0', NULL, '2016-06-07', NULL, NULL),
(115, 29, 30, 29, 5, '33.00', '110.00', '0', '0', NULL, '2016-06-08', NULL, NULL),
(116, 29, 30, 29, 5, '50.00', '100.00', '0', '0', NULL, '2016-06-09', NULL, NULL),
(117, 29, 30, 29, 5, '110.00', '90.00', '0', '0', NULL, '2016-06-10', NULL, NULL),
(118, 29, 30, 29, 5, '120.00', '100.00', '0', '0', NULL, '2016-06-11', NULL, NULL),
(119, 29, 30, 29, 5, '120.00', '100.00', '0', '0', NULL, '2016-06-14', NULL, NULL),
(120, 29, 30, 29, 5, '120.00', '100.00', '0', '0', NULL, '2016-06-12', NULL, NULL),
(121, 29, 30, 29, 5, '120.00', '120.00', '0', '0', NULL, '2016-06-13', NULL, NULL),
(122, 34, 31, 34, 5, '4.00', '4.00', '0', '0', NULL, '2016-05-19', NULL, NULL),
(123, 34, 31, 34, 5, '2.00', '4.00', '0', '0', NULL, '2016-05-26', NULL, NULL),
(124, 34, 31, 34, 5, '3.00', '4.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(125, 34, 31, 34, 5, '5.00', '4.00', '0', '0', NULL, '2016-06-09', NULL, NULL),
(126, 34, 31, 34, 5, '4.00', '4.00', '0', '0', NULL, '2016-06-16', NULL, NULL),
(127, 34, 31, 34, 5, '6.00', '4.00', '0', '0', NULL, '2016-05-12', NULL, NULL),
(128, 34, 31, 34, 5, '12.00', '4.00', '0', '0', NULL, '2016-06-23', NULL, NULL),
(129, 34, 31, 34, 5, '6.00', '4.00', '0', '0', NULL, '2016-06-30', NULL, NULL),
(130, 29, 27, 29, 5, '3.00', '30.00', '0', '0', NULL, '2016-04-05', NULL, NULL),
(131, 29, 27, 29, 5, '42.00', '30.00', '0', '0', NULL, '2016-05-05', NULL, NULL),
(132, 29, 27, 29, 5, '12.00', '30.00', '0', '0', NULL, '2016-06-05', NULL, NULL),
(133, 29, 27, 29, 5, '3.00', '30.00', '0', '0', NULL, '2016-03-05', NULL, NULL),
(134, 34, 32, 34, 5, '909.00', '100.00', '0', '0', NULL, '2016-05-26', NULL, NULL),
(135, 34, 32, 34, 5, '95.00', '100.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(136, 34, 32, 34, 5, '50.00', '100.00', '0', '0', NULL, '2016-06-09', NULL, NULL),
(137, 34, 32, 34, 5, '75.00', '100.00', '0', '0', NULL, '2016-06-16', NULL, NULL),
(138, 34, 32, 34, 5, '80.00', '100.00', '0', '0', NULL, '2016-06-23', NULL, NULL),
(139, 34, 32, 34, 39, '50.00', '100.00', '0', '0', NULL, '2016-06-02', NULL, NULL),
(140, 34, 32, 34, 39, '75.00', '100.00', '0', '0', NULL, '2016-06-09', NULL, NULL),
(141, 34, 32, 34, 39, '100.00', '100.00', '0', '0', NULL, '2016-06-16', NULL, NULL),
(142, 34, 32, 34, 39, '99.00', '100.00', '0', '0', NULL, '2016-06-23', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kpi_formats`
--

CREATE TABLE IF NOT EXISTS `kpi_formats` (
  `kpi_format_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `prefix` varchar(45) DEFAULT NULL,
  `suffix` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`kpi_format_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `kpi_formats`
--

INSERT INTO `kpi_formats` (`kpi_format_id`, `name`, `prefix`, `suffix`) VALUES
(1, '1,234', '', ''),
(2, '1,234.56', '', ''),
(3, '12%', '', '%'),
(4, '12.34%', '', '%'),
(5, '&#8369;1,234.56', '&#8369;', ''),
(6, '&#36;1,234.56', '&#36;', ''),
(7, '&#163;1,234.56', '&#163;', ''),
(8, '&#165;1,234.56', '&#165;', ''),
(9, '12 secs', '', ' secs'),
(10, '12 mins', '', ' mins'),
(11, '12 hrs', '', ' hrs'),
(12, '12 days', '', ' days'),
(13, '12 wks', '', ' wks'),
(14, '12 mths', '', ' mths'),
(15, '12 qtrs', '', ' qtrs'),
(16, '12 yrs', '', ' yrs');

-- --------------------------------------------------------

--
-- Table structure for table `kpi_users`
--

CREATE TABLE IF NOT EXISTS `kpi_users` (
  `kpi_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `kpi_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`kpi_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `kpi_users`
--

INSERT INTO `kpi_users` (`kpi_user_id`, `kpi_id`, `user_id`) VALUES
(1, 10, 5),
(2, 11, 5),
(3, 12, 5),
(4, 13, 5),
(5, 14, 5),
(6, 15, 17),
(7, 16, 5),
(8, 17, 5),
(9, 18, 5),
(10, 19, 14),
(11, 20, 5),
(12, 21, 17),
(14, 17, 17),
(15, 20, 15),
(16, 20, 17),
(17, 20, 16),
(18, 22, 5),
(19, 22, 17),
(20, 23, 5),
(21, 23, 36),
(22, 16, 14),
(23, 16, 15),
(24, 16, 17),
(25, 16, 16),
(26, 24, 5),
(27, 24, 36),
(28, 25, 5),
(29, 25, 36),
(30, 26, 5),
(31, 26, 36),
(32, 27, 5),
(34, 28, 5),
(35, 29, 5),
(36, 30, 5),
(37, 30, 36),
(38, 25, 17),
(39, 29, 15),
(40, 29, 17),
(41, 29, 26),
(42, 31, 5),
(43, 32, 5),
(44, 33, 5),
(45, 32, 39),
(46, 31, 39);

-- --------------------------------------------------------

--
-- Table structure for table `meeting`
--

CREATE TABLE IF NOT EXISTS `meeting` (
  `meeting_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `organ_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `meeting_title` varchar(150) NOT NULL,
  `meeting_tags` varchar(150) DEFAULT NULL,
  `meeting_participants` varchar(250) NOT NULL,
  `nonuser_participants` varchar(512) DEFAULT NULL,
  `meeting_optional` varchar(150) DEFAULT NULL,
  `meeting_cc` varchar(150) DEFAULT NULL,
  `when_from_date` varchar(50) NOT NULL,
  `when_to_date` varchar(50) NOT NULL,
  `formatted_when_from_date` date NOT NULL,
  `meeting_location` varchar(150) NOT NULL,
  PRIMARY KEY (`meeting_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `meeting`
--

INSERT INTO `meeting` (`meeting_id`, `user_id`, `organ_id`, `plan_id`, `meeting_title`, `meeting_tags`, `meeting_participants`, `nonuser_participants`, `meeting_optional`, `meeting_cc`, `when_from_date`, `when_to_date`, `formatted_when_from_date`, `meeting_location`) VALUES
(8, 5, 22, 22, 'test meeting', 's:0:"";', 'a:1:{i:0;s:1:"5";}', 's:0:"";', 'NA', 'NA', '08/06/2016 12:00 AM', '16/06/2016 12:00 AM', '2016-06-08', 'location'),
(9, 5, 29, 29, 'Test', 's:4:"test";', 'a:1:{i:0;s:1:"5";}', 's:0:"";', 'NA', 'NA', '10/06/2016 12:00 AM', '29/06/2016 12:00 AM', '2016-06-10', 'Test'),
(10, 5, 29, 29, 'Test', 's:4:"test";', 'a:1:{i:0;s:1:"5";}', 's:0:"";', 'NA', 'NA', '10/06/2016 12:00 AM', '29/06/2016 12:00 AM', '2016-06-10', 'Test'),
(11, 5, 34, 34, 'Test 1', 's:0:"";', 'a:1:{i:0;s:1:"5";}', 's:0:"";', 'NA', 'NA', '23/06/2016 12:00 PM', '16/06/2016 1:00 PM', '2016-06-23', 'Office');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_attendees`
--

CREATE TABLE IF NOT EXISTS `meeting_attendees` (
  `meeting_attendee_id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_id` int(11) DEFAULT NULL,
  `attended` tinyint(1) DEFAULT NULL,
  `invite_sent` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `acceptance_status` int(11) DEFAULT '2' COMMENT '0=decline, 1=accepted, 2=pending',
  `email` varchar(150) NOT NULL,
  PRIMARY KEY (`meeting_attendee_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `meeting_attendees`
--

INSERT INTO `meeting_attendees` (`meeting_attendee_id`, `meeting_id`, `attended`, `invite_sent`, `acceptance_status`, `email`) VALUES
(1, 3, 1, '2016-06-02 09:07:42', 1, 'tim@crunchersaccountants.co.uk'),
(2, 3, 0, '2016-06-02 10:21:27', 0, 'tim.pointon@crunchersaccountants.co.uk'),
(3, 5, 1, '2016-06-02 10:27:58', 1, 'timpointon@hotmail.com'),
(4, 6, 1, '2016-06-02 10:48:11', 1, 'tim.pointon@crunchersaccountants.co.uk'),
(5, 6, 1, '2016-06-02 10:48:19', 1, 'ted@smartstart.us'),
(6, 7, 1, '2016-06-02 10:48:40', 1, 'timpointon@hotmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_note`
--

CREATE TABLE IF NOT EXISTS `meeting_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_id` int(11) DEFAULT NULL,
  `meeting_topic_id` int(11) DEFAULT NULL,
  `meeting_subtopic_id` int(11) DEFAULT NULL,
  `entered_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `entered_by` int(11) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL COMMENT '1=note, 2=task, 3=decision',
  `text` varchar(150) DEFAULT NULL,
  `assigned_user` varchar(150) DEFAULT NULL,
  `due_date` varchar(150) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT NULL,
  `position` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `meeting_note`
--

INSERT INTO `meeting_note` (`id`, `meeting_id`, `meeting_topic_id`, `meeting_subtopic_id`, `entered_on`, `entered_by`, `updated_on`, `updated_by`, `type`, `text`, `assigned_user`, `due_date`, `completed`, `position`) VALUES
(1, 1, 1, NULL, '2016-06-01 12:31:58', 5, NULL, NULL, 3, 'decided to leave', NULL, NULL, NULL, 1),
(2, 8, 7, NULL, '2016-06-08 18:25:50', 5, NULL, NULL, 1, 'test npte', NULL, NULL, NULL, 2),
(3, 8, 7, NULL, '2016-06-08 18:26:14', 5, NULL, NULL, 1, 'test w2', NULL, NULL, NULL, 3),
(4, 8, 7, NULL, '2016-06-08 18:28:15', 5, NULL, NULL, 1, 'this is a two lines\r\nand it seems\r\nto work fine for time\r\n', NULL, NULL, NULL, 4),
(5, 8, 7, NULL, '2016-06-08 21:27:25', 5, NULL, NULL, 1, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500', NULL, NULL, NULL, 5),
(6, 11, 8, NULL, '2016-06-10 07:37:12', 5, NULL, NULL, 1, 'This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.This is where some instructional ', NULL, NULL, NULL, 6),
(7, 11, 8, NULL, '2016-06-14 08:35:08', 5, NULL, NULL, 1, 'gbsfgbsfgbgfsb', NULL, NULL, NULL, 7),
(8, 11, 8, NULL, '2016-06-14 08:35:13', 5, NULL, NULL, 1, 'fg bfgs g sfgb f fgs fsg wdsahgfmbj,n.', NULL, NULL, NULL, 8);

-- --------------------------------------------------------

--
-- Table structure for table `meeting_notes`
--

CREATE TABLE IF NOT EXISTS `meeting_notes` (
  `meeting_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` varchar(45) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`meeting_note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_parkinglot`
--

CREATE TABLE IF NOT EXISTS `meeting_parkinglot` (
  `parkinglot_id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `moved_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`parkinglot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `meeting_parkinglot`
--

INSERT INTO `meeting_parkinglot` (`parkinglot_id`, `meeting_id`, `topic_id`, `moved_on`) VALUES
(2, 2, 3, '2016-06-02 09:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_subtopics`
--

CREATE TABLE IF NOT EXISTS `meeting_subtopics` (
  `subtopic_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL COMMENT 'base on meeting topics',
  `subtopic_title` varchar(150) NOT NULL,
  `position` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`subtopic_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `meeting_subtopics`
--

INSERT INTO `meeting_subtopics` (`subtopic_id`, `topic_id`, `subtopic_title`, `position`) VALUES
(1, 1, 'subtopic 1', 1),
(2, 1, 'Sub topic 2', 2),
(3, 4, 'subtopic 1', 3),
(4, 4, 'Sub topic 2', 4);

-- --------------------------------------------------------

--
-- Table structure for table `meeting_templates`
--

CREATE TABLE IF NOT EXISTS `meeting_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `organ_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `from_meeting_id` int(11) NOT NULL,
  `template_name` varchar(150) NOT NULL,
  `date_saved` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `meeting_templates`
--

INSERT INTO `meeting_templates` (`template_id`, `user_id`, `organ_id`, `plan_id`, `from_meeting_id`, `template_name`, `date_saved`) VALUES
(1, 5, 2, 2, 1, 'test', '2016-06-02 10:04:00'),
(2, 5, 2, 2, 2, 'test 2 sub topics', '2016-06-02 10:05:26');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_topics`
--

CREATE TABLE IF NOT EXISTS `meeting_topics` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_id` int(11) NOT NULL COMMENT 'base on meeting table',
  `topic_title` varchar(150) NOT NULL,
  `presenter` int(11) DEFAULT NULL COMMENT 'base on user id',
  `time` varchar(150) DEFAULT NULL,
  `position` smallint(6) DEFAULT NULL,
  `moved_to_parkinglot` int(11) NOT NULL DEFAULT '0' COMMENT '0=no,1=moved',
  PRIMARY KEY (`topic_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `meeting_topics`
--

INSERT INTO `meeting_topics` (`topic_id`, `meeting_id`, `topic_title`, `presenter`, `time`, `position`, `moved_to_parkinglot`) VALUES
(1, 2, 'update', 26, '15m', 1, 0),
(2, 4, 'update', 26, '15m', 1, 0),
(3, 2, 'update', 26, '15m', 2, 1),
(4, 3, 'update', 26, '15m', 3, 0),
(5, 3, 'update', 26, '15m', 4, 1),
(6, 1, 'update', 26, '15m', 1, 0),
(7, 8, 'Topic 1', 5, '15m', 5, 0),
(8, 11, 'Topic 1 ', 5, '15m', 6, 0),
(9, 11, 'test', 39, 'NA', 7, 0);

-- --------------------------------------------------------

--
-- Table structure for table `milestones`
--

CREATE TABLE IF NOT EXISTS `milestones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organ_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `dueDate` datetime DEFAULT NULL,
  `startDate` datetime DEFAULT NULL,
  `bShowOnDash` int(11) NOT NULL,
  `entered_on` datetime DEFAULT NULL,
  `entered_by` int(11) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=108 ;

--
-- Dumping data for table `milestones`
--

INSERT INTO `milestones` (`id`, `organ_id`, `plan_id`, `owner_id`, `status`, `name`, `description`, `dueDate`, `startDate`, `bShowOnDash`, `entered_on`, `entered_by`, `updated_on`, `updated_by`) VALUES
(77, NULL, 29, 36, NULL, 'Website', '', '2016-06-30 00:00:00', NULL, 0, '2016-05-25 12:22:49', 36, NULL, NULL),
(78, NULL, 29, 36, NULL, 'Website', '', '2016-06-30 00:00:00', NULL, 0, '2016-05-25 12:22:53', 36, NULL, NULL),
(79, NULL, 29, 36, NULL, 'Website', '', '2016-06-30 00:00:00', NULL, 0, '2016-05-25 12:22:55', 36, NULL, NULL),
(80, NULL, 29, 36, NULL, 'Website', '', '2016-06-30 00:00:00', NULL, 0, '2016-05-25 12:22:55', 36, NULL, NULL),
(81, NULL, 29, 36, NULL, 'Website', '', '2016-06-30 00:00:00', NULL, 0, '2016-05-25 12:22:55', 36, NULL, NULL),
(88, NULL, 29, 36, NULL, 'New Widget Machine', '', '2016-06-11 00:00:00', NULL, 0, '2016-05-25 14:30:36', 36, NULL, NULL),
(89, NULL, 29, 36, NULL, 'New Widget Machine', '', '2016-06-11 00:00:00', NULL, 0, '2016-05-25 14:30:40', 36, NULL, NULL),
(90, NULL, 29, 36, NULL, 'New Widget Machine', '', '2016-06-11 00:00:00', NULL, 0, '2016-05-25 14:30:40', 36, NULL, NULL),
(91, NULL, 29, 36, NULL, 'New Widget Machine', '', '2016-06-11 00:00:00', NULL, 0, '2016-05-25 14:30:40', 36, NULL, NULL),
(92, NULL, 29, 36, NULL, 'New Widget Machine', '', '2016-06-11 00:00:00', NULL, 0, '2016-05-25 14:30:41', 36, NULL, NULL),
(93, NULL, 29, 36, NULL, 'New Widget Machine', '', '2016-06-11 00:00:00', NULL, 0, '2016-05-25 14:30:41', 36, NULL, NULL),
(96, 3, 3, 5, NULL, 'Test II', 'Test', '2016-05-27 00:00:00', NULL, 0, '2016-05-25 15:00:20', 5, NULL, NULL),
(98, 29, 29, 5, NULL, 'New Website', '', '2016-07-31 00:00:00', '2016-06-17 00:00:00', 0, '2016-05-31 20:06:06', 5, '2016-06-13 21:06:19', 5),
(100, 30, 30, 5, NULL, 'MS 1', '', '2016-06-30 00:00:00', NULL, 1, '2016-06-07 12:54:35', 5, NULL, NULL),
(101, 2, 2, 5, NULL, 'MS 1', '', '2016-06-15 00:00:00', NULL, 0, '2016-06-08 23:55:25', 5, NULL, NULL),
(102, 29, 29, 5, NULL, 'MS 2', '', '2016-06-30 00:00:00', NULL, 0, '2016-06-09 14:59:08', 5, NULL, NULL),
(103, 34, 34, 5, 8, 'Version 1 MVP', '', '2016-06-30 00:00:00', '2016-01-06 00:00:00', 1, '2016-06-09 15:29:31', 5, '2016-06-15 08:56:44', 5),
(104, 34, 34, 5, 6, 'Rollout Beta', '', '2016-06-30 00:00:00', '0000-00-00 00:00:00', 1, '2016-06-09 15:30:25', 5, '2016-06-15 08:56:54', 5),
(107, 34, 34, 5, 2, 'Implement Goal Driver', '', '2016-06-30 00:00:00', '2016-06-21 00:00:00', 0, '2016-06-10 08:38:51', 5, '2016-06-21 22:09:57', 5);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `organ_id` int(20) NOT NULL,
  `notification_type_id` int(20) NOT NULL,
  `text` varchar(100) NOT NULL,
  `link_value` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `enteredon` datetime NOT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=167 ;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `organ_id`, `notification_type_id`, `text`, `link_value`, `status`, `enteredon`) VALUES
(150, 5, 30, 5, 'You have created MS 1 Milestone ', 'Schedule', '1', '2016-06-07 12:54:35'),
(151, 5, 22, 3, 'You are requested to a meeting', 'meeting', '1', '2016-06-08 19:25:16'),
(152, 5, 2, 5, 'You have created MS 1 Milestone ', 'Schedule', '1', '2016-06-08 23:55:25'),
(153, 5, 29, 5, 'You have created MS 2 Milestone ', 'Schedule', '1', '2016-06-09 14:59:08'),
(154, 5, 34, 5, 'You have created MS 1 Milestone ', 'Schedule', '1', '2016-06-09 15:29:31'),
(155, 5, 34, 5, 'You have created MS1 Milestone ', 'Schedule', '1', '2016-06-09 15:30:25'),
(156, 5, 34, 5, 'You have created MS 3 Milestone ', 'Schedule', '1', '2016-06-09 18:03:16'),
(157, 5, 34, 4, 'You have a task MS3 Region 1 in Milestone ', 'Schedule/update_task/90', '1', '2016-06-09 18:04:26'),
(158, 5, 29, 3, 'You are requested to a meeting', 'meeting', '1', '2016-06-09 18:31:47'),
(159, 5, 34, 5, 'You have created MS 4 Milestone ', 'Schedule', '1', '2016-06-09 18:43:30'),
(160, 5, 34, 3, 'You are requested to a meeting', 'meeting', '1', '2016-06-10 08:36:47'),
(161, 5, 34, 5, 'You have created ms5 Milestone ', 'Schedule', '1', '2016-06-10 08:38:51'),
(162, 5, 34, 4, 'You have a task test in Milestone ', 'Schedule/update_task/95', '0', '2016-06-14 14:38:33'),
(163, 5, 34, 4, 'You have a task MS3 012 in Milestone ', 'Schedule/update_task/97', '0', '2016-06-14 16:07:19'),
(164, 51, 35, 1, 'Welcome to Business Planner', 'account/', '0', '2016-06-22 12:02:36'),
(165, 52, 36, 1, 'Welcome to Business Planner', 'account/', '0', '2016-06-22 12:58:47'),
(166, 55, 37, 1, 'Welcome to Business Planner', 'account/', '0', '2016-06-24 14:57:44');

-- --------------------------------------------------------

--
-- Table structure for table `organisation`
--

CREATE TABLE IF NOT EXISTS `organisation` (
  `organ_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `employees` varchar(45) DEFAULT NULL,
  `post_code` varchar(45) DEFAULT NULL,
  `entered_by` int(11) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`organ_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `organisation`
--

INSERT INTO `organisation` (`organ_id`, `name`, `account_id`, `owner_id`, `employees`, `post_code`, `entered_by`, `entered`, `updated_by`, `updated`) VALUES
(2, 'Base Template', 2, 5, '', '', 5, '2016-03-23 00:00:00', 5, '2016-06-24 14:22:04'),
(7, 'My Organisation', 4, 17, NULL, NULL, 17, '2016-04-21 12:24:15', 5, '2016-06-10 12:33:52'),
(8, 'tim test er', 0, 2, NULL, NULL, 2, '2016-05-05 10:52:52', 5, '2016-06-10 12:33:52'),
(9, 'tim test er', 0, 2, NULL, NULL, 2, '2016-05-05 10:52:52', 5, '2016-06-10 12:33:52'),
(10, 'tim test er', 0, 2, NULL, NULL, 2, '2016-05-05 10:52:52', 5, '2016-06-10 12:33:52'),
(11, 'tim test', 0, 2, NULL, NULL, 2, '2016-05-05 11:10:08', 5, '2016-06-10 12:33:52'),
(12, 'tim test', 0, 2, NULL, NULL, 2, '2016-05-05 13:33:25', 5, '2016-06-10 12:33:52'),
(13, 'tim test', 0, 2, NULL, NULL, 2, '2016-05-05 13:33:25', 5, '2016-06-10 12:33:52'),
(19, 'My Organisation', 5, 31, NULL, NULL, 31, '2016-05-09 13:04:04', 5, '2016-06-10 12:33:52'),
(20, 'My Organisation', 6, 32, NULL, NULL, 32, '2016-05-11 13:31:00', 5, '2016-06-10 12:33:52'),
(21, 'My Organisation', 7, 33, NULL, NULL, 33, '2016-05-11 13:35:51', 5, '2016-06-10 12:33:52'),
(22, 'More Number Limited', 2, 5, NULL, NULL, 5, '2016-05-11 13:36:36', 5, '2016-06-24 14:23:01'),
(23, 'My Organisation', 8, 34, NULL, NULL, 34, '2016-05-11 23:42:03', 5, '2016-06-10 12:33:52'),
(24, 'Erie', 8, 34, NULL, NULL, 34, '2016-05-11 23:56:48', 5, '2016-06-10 12:33:52'),
(25, 'My Organisation', 9, 35, NULL, NULL, 35, '2016-05-12 23:44:14', 5, '2016-06-10 12:33:52'),
(26, 'James Moto Shop', 9, 35, NULL, NULL, 35, '2016-05-12 23:44:49', 5, '2016-06-10 12:33:52'),
(28, 'David Org', 4, 17, NULL, NULL, 17, '2016-05-16 08:00:50', 17, '2016-06-14 08:02:02'),
(29, 'Demo Company', 2, 5, NULL, NULL, 5, '2016-05-24 22:28:54', 5, '2016-06-24 14:48:27'),
(30, 'Demo - Accountancy Practice', 2, 5, NULL, NULL, 5, '2016-06-02 10:43:49', 5, '2016-06-24 14:22:35'),
(34, 'Tims test DNFW', 2, 5, NULL, NULL, 5, '2016-06-09 15:29:07', 5, '2016-06-27 09:26:19'),
(35, 'My Organisation', 10, 51, NULL, NULL, 51, '2016-06-22 12:02:36', 51, '2016-06-22 21:24:29'),
(36, 'Organisation 1', 11, 52, NULL, NULL, 52, '2016-06-22 12:58:46', 52, '2016-06-22 13:16:32'),
(37, 'My Organisation', 12, 55, NULL, NULL, 55, '2016-06-24 14:57:44', 55, '2016-06-24 14:58:07');

-- --------------------------------------------------------

--
-- Table structure for table `organisation_users`
--

CREATE TABLE IF NOT EXISTS `organisation_users` (
  `organ_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `organ_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `organ_user_type_id` int(11) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `last_logged_in` datetime DEFAULT NULL,
  PRIMARY KEY (`organ_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=151 ;

--
-- Dumping data for table `organisation_users`
--

INSERT INTO `organisation_users` (`organ_user_id`, `organ_id`, `user_id`, `organ_user_type_id`, `entered`, `updated`, `last_logged_in`) VALUES
(9, 2, 5, 1, '2016-03-23 00:00:00', '2016-03-23 00:00:00', '2016-06-24 14:22:04'),
(30, 2, 10, 1, NULL, NULL, NULL),
(31, 2, 8, 1, NULL, NULL, NULL),
(34, 2, 13, 1, '2016-03-31 19:11:56', '2016-03-31 19:11:56', NULL),
(35, 2, 14, 1, '2016-04-06 21:49:15', '2016-04-06 21:49:15', '2016-05-11 15:08:27'),
(36, 2, 15, 1, '2016-04-06 21:52:50', '2016-04-06 21:52:50', '2016-06-01 14:09:07'),
(37, 3, 5, 1, '2016-04-07 09:46:55', '2016-04-07 09:46:55', '2016-06-02 10:38:51'),
(38, 2, 16, 1, '2016-04-07 11:16:36', '2016-04-07 11:16:36', NULL),
(39, 4, 5, 1, '2016-04-13 09:22:02', '2016-04-13 09:22:02', '2016-05-03 11:56:18'),
(40, 5, 5, 1, '2016-04-13 09:22:11', '2016-04-13 09:22:11', '2016-04-26 09:15:58'),
(41, 6, 5, 1, '2016-04-13 09:58:27', '2016-04-13 09:58:27', '2016-05-03 12:20:16'),
(42, 7, 17, 1, '2016-04-21 12:24:15', '2016-04-21 12:24:15', '2016-04-23 14:23:47'),
(61, 2, 26, 1, '2016-04-26 09:14:42', '2016-04-26 09:14:42', '2016-05-20 09:48:09'),
(62, 5, 26, 1, '2016-04-26 09:16:11', '2016-04-26 09:16:11', '2016-04-26 09:16:11'),
(70, 2, 17, 1, '2016-04-26 14:22:23', '2016-04-26 14:22:23', '2016-06-10 18:10:51'),
(73, 2, 29, 1, '2016-04-26 14:41:57', '2016-04-26 14:41:57', '2016-04-26 14:41:57'),
(74, 8, 2, 1, '2016-05-05 10:52:52', '2016-05-05 10:52:52', '2016-05-05 10:52:52'),
(75, 9, 2, 1, '2016-05-05 10:52:52', '2016-05-05 10:52:52', '2016-05-05 10:52:52'),
(76, 10, 2, 1, '2016-05-05 10:52:52', '2016-05-05 10:52:52', '2016-05-05 10:52:52'),
(77, 11, 2, 1, '2016-05-05 11:10:08', '2016-05-05 11:10:08', '2016-05-05 11:10:08'),
(78, 12, 2, 1, '2016-05-05 13:33:25', '2016-05-05 13:33:25', '2016-05-05 13:33:25'),
(79, 13, 2, 1, '2016-05-05 13:33:25', '2016-05-05 13:33:25', '2016-05-05 13:33:25'),
(80, 14, 5, 1, '2016-05-05 19:09:12', '2016-05-05 19:09:12', '2016-05-31 13:52:05'),
(81, 15, 5, 1, '2016-05-06 09:59:40', '2016-05-06 09:59:40', '2016-05-06 09:59:44'),
(82, 16, 5, 1, '2016-05-06 10:11:46', '2016-05-06 10:11:46', '2016-05-06 10:11:49'),
(83, 17, 5, 1, '2016-05-06 10:12:41', '2016-05-06 10:12:41', '2016-05-06 10:12:43'),
(84, 14, 26, 1, '2016-05-06 13:25:27', '2016-05-06 13:25:27', '2016-05-06 13:25:27'),
(85, 14, 30, 1, '2016-05-06 13:26:12', '2016-05-06 13:26:12', '2016-05-06 13:26:12'),
(86, 18, 5, 1, '2016-05-06 13:42:51', '2016-05-06 13:42:51', '2016-05-06 13:42:51'),
(87, 19, 31, 1, '2016-05-09 13:04:04', '2016-05-09 13:04:04', '2016-05-12 23:05:45'),
(88, 20, 32, 1, '2016-05-11 13:31:00', '2016-05-11 13:31:00', '2016-05-11 13:31:00'),
(89, 21, 33, 1, '2016-05-11 13:35:51', '2016-05-11 13:35:51', '2016-05-11 13:35:51'),
(90, 22, 5, 1, '2016-05-11 13:36:36', '2016-05-11 13:36:36', '2016-06-24 14:23:01'),
(91, 22, 14, 1, '2016-05-11 13:37:22', '2016-05-11 13:37:22', '2016-05-19 12:42:32'),
(92, 23, 34, 1, '2016-05-11 23:42:03', '2016-05-11 23:42:03', '2016-05-11 23:42:21'),
(93, 24, 34, 1, '2016-05-11 23:56:48', '2016-05-11 23:56:48', '2016-05-11 23:56:51'),
(94, 25, 35, 1, '2016-05-12 23:44:14', '2016-05-12 23:44:14', '2016-05-12 23:46:29'),
(95, 26, 35, 1, '2016-05-12 23:44:49', '2016-05-12 23:44:49', '2016-05-12 23:44:52'),
(96, 27, 17, 1, '2016-05-16 08:00:12', '2016-05-16 08:00:12', '2016-05-16 08:00:12'),
(97, 28, 17, 1, '2016-05-16 08:00:50', '2016-05-16 08:00:50', '2016-06-14 08:02:02'),
(98, 29, 5, 1, '2016-05-24 22:28:54', '2016-05-24 22:28:54', '2016-06-24 14:48:27'),
(99, 29, 36, 1, '2016-05-25 12:09:54', '2016-05-25 12:09:54', '2016-06-03 09:56:12'),
(100, 29, 15, 1, '2016-06-01 13:48:52', '2016-06-01 13:48:52', '2016-06-01 14:06:02'),
(101, 30, 5, 1, '2016-06-02 10:43:49', '2016-06-02 10:43:49', '2016-06-24 14:22:35'),
(102, 31, 5, 1, '2016-06-02 13:35:09', '2016-06-02 13:35:09', '2016-06-02 13:35:09'),
(103, 32, 5, 1, '2016-06-02 13:35:24', '2016-06-02 13:35:24', '2016-06-02 13:35:24'),
(104, 33, 5, 1, '2016-06-02 13:35:38', '2016-06-02 13:35:38', '2016-06-02 13:35:38'),
(105, 29, 37, 1, '2016-06-02 17:22:45', '2016-06-02 17:22:45', '2016-06-13 05:25:30'),
(106, 30, 15, 1, '2016-06-07 12:42:01', '2016-06-07 12:42:01', '2016-06-07 12:42:01'),
(107, 29, 17, 1, '2016-06-08 16:20:37', '2016-06-08 16:20:37', '2016-06-20 13:06:49'),
(108, 29, 26, 1, '2016-06-09 12:36:21', '2016-06-09 12:36:21', '2016-06-09 12:36:21'),
(109, 34, 5, 1, '2016-06-09 15:29:07', '2016-06-09 15:29:07', '2016-06-27 09:26:19'),
(110, 34, 39, 1, '2016-06-20 10:36:37', '2016-06-20 10:36:37', '2016-06-20 14:32:21'),
(111, 34, 40, 1, '2016-06-20 10:50:30', '2016-06-20 10:50:30', '2016-06-20 10:50:30'),
(114, 34, 43, 1, '2016-06-21 11:46:04', '2016-06-21 11:46:04', '2016-06-21 11:46:04'),
(115, 34, 36, 1, '2016-06-22 09:05:28', '2016-06-22 09:05:28', '2016-06-22 09:05:28'),
(116, 2, 37, 1, '2016-06-22 09:11:07', '2016-06-22 09:11:07', '2016-06-22 09:11:07'),
(117, 30, 37, 1, '2016-06-22 09:20:26', '2016-06-22 09:20:26', '2016-06-22 09:20:26'),
(118, 22, 37, 1, '2016-06-22 09:26:13', '2016-06-22 09:26:13', '2016-06-22 09:26:13'),
(119, 34, 37, 1, '2016-06-22 09:32:40', '2016-06-22 09:32:40', '2016-06-22 09:32:40'),
(124, 34, 46, 1, '2016-06-22 10:03:11', '2016-06-22 10:03:11', '2016-06-22 10:03:11'),
(125, 22, 46, 1, '2016-06-22 10:03:46', '2016-06-22 10:03:46', '2016-06-22 10:03:46'),
(126, 2, 46, 1, '2016-06-22 10:04:24', '2016-06-22 10:04:24', '2016-06-22 10:04:24'),
(137, 34, 50, 1, '2016-06-22 11:54:47', '2016-06-22 11:54:47', '2016-06-22 11:54:47'),
(138, 2, 50, 1, '2016-06-22 11:55:32', '2016-06-22 11:55:32', '2016-06-22 11:55:32'),
(139, 35, 51, 1, '2016-06-22 12:02:36', '2016-06-22 12:02:36', '2016-06-22 21:24:29'),
(140, 35, 50, 1, '2016-06-22 12:03:48', '2016-06-22 12:03:48', '2016-06-22 12:03:48'),
(141, 36, 52, 1, '2016-06-22 12:58:46', '2016-06-22 12:58:46', '2016-06-22 13:16:32'),
(142, 34, 53, 1, '2016-06-22 13:00:47', '2016-06-22 13:00:47', '2016-06-22 13:00:47'),
(143, 36, 53, 1, '2016-06-22 13:01:49', '2016-06-22 13:01:49', '2016-06-22 13:01:49'),
(144, 34, 51, 1, '2016-06-22 21:26:32', '2016-06-22 21:26:32', '2016-06-22 21:26:32'),
(146, 34, 54, 1, '2016-06-24 14:24:23', '2016-06-24 14:24:23', '2016-06-24 14:24:23'),
(147, 29, 54, 1, '2016-06-24 14:24:58', '2016-06-24 14:24:58', '2016-06-24 14:24:58'),
(148, 37, 55, 1, '2016-06-24 14:57:44', '2016-06-24 14:57:44', '2016-06-24 14:58:07'),
(149, 37, 54, 1, '2016-06-24 14:58:28', '2016-06-24 14:58:28', '2016-06-24 14:58:28'),
(150, 37, 56, 1, '2016-06-24 14:59:37', '2016-06-24 14:59:37', '2016-06-24 14:59:37');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `organ_id` int(11) DEFAULT NULL COMMENT 'Linked to organisation table',
  `tab_id` int(11) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `readonly` tinyint(1) NOT NULL COMMENT '1=Has rights, 0= No rights',
  `readwrite` tinyint(1) NOT NULL COMMENT '1=Has rights, 0= No rights',
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=168 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `user_id`, `organ_id`, `tab_id`, `hidden`, `readonly`, `readwrite`) VALUES
(8, 36, 29, 1, 0, 0, 1),
(9, 36, 29, 2, 0, 0, 1),
(10, 36, 29, 3, 0, 0, 1),
(11, 36, 29, 4, 0, 1, 0),
(12, 36, 29, 5, 0, 1, 0),
(13, 36, 29, 6, 0, 1, 0),
(14, 36, 29, 7, 0, 1, 0),
(15, 26, 2, 1, 0, 1, 1),
(16, 26, 2, 2, 0, 1, 1),
(17, 26, 2, 3, 0, 1, 1),
(18, 26, 2, 4, 0, 1, 1),
(19, 26, 2, 5, 0, 1, 1),
(20, 26, 2, 6, 0, 1, 1),
(21, 26, 2, 7, 0, 1, 1),
(22, 26, 29, 1, 0, 1, 1),
(23, 26, 29, 2, 0, 1, 1),
(24, 26, 29, 3, 0, 1, 1),
(25, 26, 29, 4, 0, 1, 1),
(26, 26, 29, 5, 0, 1, 1),
(27, 26, 29, 6, 0, 1, 1),
(28, 26, 29, 7, 0, 1, 1),
(36, 5, 29, 1, 0, 0, 1),
(37, 5, 29, 2, 0, 0, 1),
(38, 5, 29, 3, 0, 0, 1),
(39, 5, 29, 4, 0, 0, 1),
(40, 5, 29, 5, 0, 0, 1),
(41, 5, 29, 6, 0, 0, 1),
(42, 5, 29, 7, 0, 0, 1),
(43, 37, 29, 1, 1, 0, 0),
(44, 37, 29, 2, 1, 0, 0),
(45, 37, 29, 3, 1, 0, 0),
(46, 37, 29, 4, 1, 0, 0),
(47, 37, 29, 5, 1, 0, 0),
(48, 37, 29, 6, 0, 1, 0),
(49, 37, 29, 7, 0, 1, 0),
(56, 39, 34, 1, 1, 0, 0),
(57, 39, 34, 2, 1, 0, 0),
(58, 39, 34, 3, 1, 0, 0),
(59, 39, 34, 4, 1, 0, 0),
(60, 39, 34, 5, 0, 0, 1),
(61, 39, 34, 6, 1, 0, 0),
(62, 39, 34, 7, 1, 0, 0),
(63, 41, 29, 1, 1, 0, 0),
(64, 41, 29, 2, 1, 0, 0),
(65, 41, 29, 3, 0, 1, 0),
(66, 41, 29, 4, 0, 1, 0),
(67, 41, 29, 5, 0, 1, 0),
(68, 41, 29, 6, 1, 0, 0),
(69, 41, 29, 7, 0, 1, 0),
(77, 42, 29, 1, 0, 1, 0),
(78, 42, 29, 2, 1, 0, 0),
(79, 42, 29, 3, 1, 0, 0),
(80, 42, 29, 4, 1, 0, 0),
(81, 42, 29, 5, 0, 1, 0),
(82, 42, 29, 6, 1, 0, 0),
(83, 42, 29, 7, 0, 1, 0),
(91, 17, 29, 1, 0, 0, 1),
(92, 17, 29, 2, 0, 0, 1),
(93, 17, 29, 3, 0, 0, 1),
(94, 17, 29, 4, 0, 0, 1),
(95, 17, 29, 5, 0, 0, 1),
(96, 17, 29, 6, 0, 0, 1),
(97, 17, 29, 7, 0, 0, 1),
(98, 43, 34, 1, 0, 0, 1),
(99, 43, 34, 2, 0, 0, 1),
(100, 43, 34, 3, 0, 0, 1),
(101, 43, 34, 4, 0, 0, 1),
(102, 43, 34, 5, 0, 0, 1),
(103, 43, 34, 6, 0, 0, 1),
(104, 43, 34, 7, 0, 0, 1),
(105, 45, 34, 1, 0, 0, 1),
(106, 45, 34, 2, 0, 0, 1),
(107, 45, 34, 3, 0, 0, 1),
(108, 45, 34, 4, 0, 0, 1),
(109, 45, 34, 5, 0, 0, 1),
(110, 45, 34, 6, 0, 0, 1),
(111, 45, 34, 7, 0, 0, 1),
(119, 50, 2, 1, 0, 0, 1),
(120, 50, 2, 2, 0, 0, 1),
(121, 50, 2, 3, 0, 0, 1),
(122, 50, 2, 4, 0, 0, 1),
(123, 50, 2, 5, 0, 0, 1),
(124, 50, 2, 6, 0, 0, 1),
(125, 50, 2, 7, 0, 0, 1),
(126, 50, 34, 1, 0, 0, 1),
(127, 50, 34, 2, 0, 0, 1),
(128, 50, 34, 3, 0, 0, 1),
(129, 50, 34, 4, 0, 0, 1),
(130, 50, 34, 5, 0, 0, 1),
(131, 50, 34, 6, 0, 0, 1),
(132, 50, 34, 7, 0, 0, 1),
(133, 52, 34, 1, 0, 0, 1),
(134, 52, 34, 2, 0, 0, 1),
(135, 52, 34, 3, 0, 0, 1),
(136, 52, 34, 4, 0, 0, 1),
(137, 52, 34, 5, 0, 0, 1),
(138, 52, 34, 6, 0, 0, 1),
(139, 52, 34, 7, 0, 0, 1),
(147, 54, 29, 1, 0, 0, 1),
(148, 54, 29, 2, 0, 0, 1),
(149, 54, 29, 3, 0, 0, 1),
(150, 54, 29, 4, 0, 0, 1),
(151, 54, 29, 5, 0, 0, 1),
(152, 54, 29, 6, 0, 0, 1),
(153, 54, 29, 7, 0, 0, 1),
(154, 54, 34, 1, 0, 0, 1),
(155, 54, 34, 2, 0, 0, 1),
(156, 54, 34, 3, 0, 0, 1),
(157, 54, 34, 4, 0, 0, 1),
(158, 54, 34, 5, 0, 0, 1),
(159, 54, 34, 6, 0, 0, 1),
(160, 54, 34, 7, 0, 0, 1),
(161, 54, 37, 1, 0, 0, 1),
(162, 54, 37, 2, 0, 0, 1),
(163, 54, 37, 3, 0, 0, 1),
(164, 54, 37, 4, 0, 0, 1),
(165, 54, 37, 5, 0, 0, 1),
(166, 54, 37, 6, 0, 0, 1),
(167, 54, 37, 7, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_company`
--

CREATE TABLE IF NOT EXISTS `pitch_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `pitch_company`
--

INSERT INTO `pitch_company` (`id`, `plan_id`, `name`, `logo`, `updated_by`, `updated`, `hide`) VALUES
(2, 2, 'Crunchers Network', 'e9cba36b524ec1989f59cc2571d638f5.png', 5, '2016-04-19 19:49:42', 0),
(3, 29, 'Name', '6e3cf3428d70876cb1b29fa57bff29c1.png', 5, '2016-06-07 13:28:45', 0),
(4, 30, '', '1618924bd85283a1d2e993fb3730d06c.png', 5, '2016-06-14 08:41:16', 0),
(5, 34, 'Name is here', NULL, 5, '2016-06-23 13:23:48', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_competition`
--

CREATE TABLE IF NOT EXISTS `pitch_competition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `advantage` varchar(255) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `entered` timestamp NULL DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `pitch_competition`
--

INSERT INTO `pitch_competition` (`id`, `plan_id`, `name`, `advantage`, `updated_by`, `updated`, `entered`, `hide`) VALUES
(7, 2, 'People who know stuff', 'we know more', 5, '2016-03-21 22:52:50', '2016-03-21 22:52:50', 0),
(8, 2, 'People who know more stuff', 'we know more', 5, '2016-03-21 22:53:16', '2016-03-21 22:53:16', 0),
(9, 29, 'ABC Widgets', 'Better widgets', 36, '2016-05-25 13:11:08', '2016-05-25 13:09:53', 0),
(10, 29, 'ACME Widgets', 'Better quality', 36, '2016-05-25 13:11:43', '2016-05-25 13:10:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_funding`
--

CREATE TABLE IF NOT EXISTS `pitch_funding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `amount` decimal(10,0) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `pitch_funding`
--

INSERT INTO `pitch_funding` (`id`, `plan_id`, `amount`, `text`, `updated_by`, `updated`, `hide`) VALUES
(2, 2, '24000', 'Complete the core product and promotion.', 5, '2016-06-02 21:37:35', 0),
(3, 29, '250000', 'To but new machinery to increase widget output by 200% / year', 36, NULL, 0),
(4, 30, '300000', 'New Office', 5, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_headline`
--

CREATE TABLE IF NOT EXISTS `pitch_headline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `value` text,
  `updated_by` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `pitch_headline`
--

INSERT INTO `pitch_headline` (`id`, `plan_id`, `value`, `updated_by`, `updated`, `hide`) VALUES
(2, 2, '&lt;p&gt;&lt;strong&gt;&lt;em&gt;We have created a brand, service and software to enable accountants to add value.&lt;/em&gt;&lt;/strong&gt;&lt;/p&gt;', 5, '2016-06-17 16:33:12', 0),
(3, 29, '&lt;p&gt;&lt;strong&gt;&lt;em&gt;We are here to be the best demo company around.&lt;/em&gt;&lt;/strong&gt;&lt;/p&gt;', 36, '2016-05-25 11:37:21', 0),
(4, 30, '&lt;h1&gt;&lt;em&gt;Headline goes here init&lt;/em&gt;&lt;/h1&gt;', 5, '2016-06-14 08:41:09', 0),
(5, 34, '&lt;p&gt;Vision test label&lt;/p&gt;', 5, '2016-06-24 13:54:27', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_marketing`
--

CREATE TABLE IF NOT EXISTS `pitch_marketing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `text_value` text,
  `list_value` text,
  `updated_by` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `pitch_marketing`
--

INSERT INTO `pitch_marketing` (`id`, `plan_id`, `type`, `text_value`, `list_value`, `updated_by`, `updated`, `hide`) VALUES
(2, 2, 'list', '', 'a:5:{i:0;s:13:"Telemarketing";i:1;s:11:"Direct mail";i:2;s:8:"LinkedIn";i:3;s:8:"Referals";i:4;s:0:"";}', 5, '2016-04-19 20:50:05', 0),
(3, 30, 'desc', '', 'a:5:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";i:4;s:0:"";}', 5, NULL, 0),
(4, 34, 'desc', '', 'a:5:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";i:4;s:0:"";}', 5, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_milestone`
--

CREATE TABLE IF NOT EXISTS `pitch_milestone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pitch_milestone`
--

INSERT INTO `pitch_milestone` (`id`, `plan_id`, `updated_by`, `hide`, `updated`) VALUES
(1, 34, 5, 0, '2016-06-17 17:30:24');

-- --------------------------------------------------------

--
-- Table structure for table `pitch_partners`
--

CREATE TABLE IF NOT EXISTS `pitch_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `logo` text,
  `updated_by` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `pitch_partners`
--

INSERT INTO `pitch_partners` (`id`, `plan_id`, `name`, `type`, `description`, `logo`, `updated_by`, `updated`, `hide`) VALUES
(8, 2, 'Test resource entry', NULL, 'Description of the entry', '3283d8608e06d035cfc2dbfed2abd9af.jpg', 5, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_problems`
--

CREATE TABLE IF NOT EXISTS `pitch_problems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `text_value` varchar(255) DEFAULT NULL,
  `list_value` text,
  `updated_by` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `pitch_problems`
--

INSERT INTO `pitch_problems` (`id`, `plan_id`, `type`, `text_value`, `list_value`, `updated_by`, `updated`, `hide`) VALUES
(2, 2, 'desc', '&lt;p&gt;Accountants need to add value but can''t within the clients budget&lt;/p&gt;', 'a:5:{i:0;s:18:"Lack of experience";i:1;s:14:"lack of monies";i:2;s:0:"";i:3;s:0:"";i:4;s:0:"";}', 5, '2016-04-19 19:49:48', 0),
(3, 29, 'desc', '&lt;p&gt;Accountants need to add value but can''t within the clients budget&lt;/p&gt;', NULL, 36, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_published`
--

CREATE TABLE IF NOT EXISTS `pitch_published` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `code` varchar(8) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `pitch_published`
--

INSERT INTO `pitch_published` (`id`, `plan_id`, `user_id`, `code`, `entered`) VALUES
(7, 2, 5, '48UXKSxW', '2016-03-29 14:50:13'),
(8, 29, 5, 'gKx4B5rz', '2016-05-31 20:20:27'),
(9, 30, 5, 'Zod1fbOR', '2016-06-15 11:54:53'),
(10, 34, 5, '3HxsgpFw', '2016-06-24 14:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `pitch_sales`
--

CREATE TABLE IF NOT EXISTS `pitch_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `text_value` text,
  `list_value` text,
  `updated_by` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pitch_sales`
--

INSERT INTO `pitch_sales` (`id`, `plan_id`, `type`, `text_value`, `list_value`, `updated_by`, `updated`, `hide`) VALUES
(2, 2, 'list', '', 'a:5:{i:0;s:12:"Remote sales";i:1;s:30:"Drive people to our whitepaper";i:2;s:22:"Build a plan with them";i:3;s:0:"";i:4;s:0:"";}', 5, '2016-04-19 19:50:03', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_solution`
--

CREATE TABLE IF NOT EXISTS `pitch_solution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `text_value` varchar(255) DEFAULT NULL,
  `list_value` text,
  `updated_by` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `pitch_solution`
--

INSERT INTO `pitch_solution` (`id`, `plan_id`, `type`, `text_value`, `list_value`, `updated_by`, `updated`, `hide`) VALUES
(2, 2, 'desc', '&lt;p&gt;We have created a brand, service and software to offer lost cost added value.&lt;/p&gt;', 'a:5:{i:0;s:16:"more experience ";i:1;s:11:"more monies";i:2;s:0:"";i:3;s:0:"";i:4;s:0:"";}', 5, '2016-04-19 19:49:52', 0),
(3, 29, 'desc', '&lt;p&gt;We have created a brand, service and software to offer lost cost added value&lt;/p&gt;', NULL, 36, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_target_market`
--

CREATE TABLE IF NOT EXISTS `pitch_target_market` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `data` text,
  `updated_by` int(11) DEFAULT NULL,
  `entered` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `pitch_target_market`
--

INSERT INTO `pitch_target_market` (`id`, `plan_id`, `data`, `updated_by`, `entered`, `updated`, `hide`) VALUES
(9, 2, 'a:4:{s:12:"name_segment";s:16:"Less than £100k";s:16:"prospect_segment";s:5:"21000";s:15:"annual_prospect";s:4:"2000";s:2:"id";s:1:"9";}', 5, '2016-03-21 22:52:01', '2016-04-06 20:39:04', 0),
(10, 2, 'a:4:{s:12:"name_segment";s:11:"Over £100k";s:16:"prospect_segment";s:5:"14000";s:15:"annual_prospect";s:4:"2000";s:2:"id";s:2:"10";}', 5, '2016-03-21 22:52:19', '2016-04-06 20:39:38', 0),
(11, 29, 'a:4:{s:12:"name_segment";s:17:"Low costs widgets";s:16:"prospect_segment";s:5:"20000";s:15:"annual_prospect";s:4:"1000";s:2:"id";s:2:"11";}', 36, '2016-05-25 13:08:48', '2016-05-25 13:13:01', 0),
(12, 29, 'a:4:{s:12:"name_segment";s:17:"High cost widgets";s:16:"prospect_segment";s:5:"14000";s:15:"annual_prospect";s:4:"2000";s:2:"id";s:2:"12";}', 36, '2016-05-25 13:09:21', '2016-05-25 13:13:22', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pitch_teamkey`
--

CREATE TABLE IF NOT EXISTS `pitch_teamkey` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pitch_teamkey`
--

INSERT INTO `pitch_teamkey` (`id`, `plan_id`, `updated`, `updated_by`, `hide`) VALUES
(1, 2, '2016-06-20 09:30:35', 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `plan`
--

CREATE TABLE IF NOT EXISTS `plan` (
  `plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` varchar(45) DEFAULT NULL,
  `is_template` varchar(45) DEFAULT NULL,
  `company_name` varchar(45) DEFAULT NULL,
  `logo` blob,
  `owner_id` smallint(6) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `created_by_user_id` smallint(6) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by_user_id` tinyint(4) DEFAULT NULL,
  `organ_id` int(11) DEFAULT NULL,
  `is_coverpage` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `plan`
--

INSERT INTO `plan` (`plan_id`, `account_id`, `is_template`, `company_name`, `logo`, `owner_id`, `entered`, `created_by_user_id`, `updated`, `updated_by_user_id`, `organ_id`, `is_coverpage`) VALUES
(2, '2', '', 'My Company', '', 5, '2015-07-10 10:43:41', 0, '2015-07-10 10:43:41', 0, 2, 1),
(3, '2', NULL, 'ABC Widgets', NULL, 5, '2016-04-07 09:46:55', 5, '2016-04-07 09:46:55', 5, 3, 0),
(4, '2', NULL, 'organisation test 3', NULL, 5, '2016-04-13 09:22:02', 5, '2016-04-13 09:22:02', 5, 4, 0),
(5, '2', NULL, 'organisation test 4', NULL, 5, '2016-04-13 09:22:11', 5, '2016-04-13 09:22:11', 5, 5, 0),
(6, '2', NULL, 'error test', NULL, 5, '2016-04-13 09:58:27', 5, '2016-04-13 09:58:27', 5, 6, 0),
(7, '4', NULL, 'My Organisation', NULL, 17, '2016-04-21 12:24:15', 17, '2016-04-21 12:24:15', 17, 7, 0),
(8, '0', NULL, 'tim test er', NULL, 2, '2016-05-05 10:52:52', 2, '2016-05-05 10:52:52', 2, 8, 0),
(9, '0', NULL, 'tim test er', NULL, 2, '2016-05-05 10:52:52', 2, '2016-05-05 10:52:52', 2, 9, 0),
(10, '0', NULL, 'tim test er', NULL, 2, '2016-05-05 10:52:52', 2, '2016-05-05 10:52:52', 2, 10, 0),
(11, '0', NULL, 'tim test', NULL, 2, '2016-05-05 11:10:08', 2, '2016-05-05 11:10:08', 2, 11, 0),
(12, '0', NULL, 'tim test', NULL, 2, '2016-05-05 13:33:25', 2, '2016-05-05 13:33:25', 2, 12, 0),
(13, '0', NULL, 'tim test', NULL, 2, '2016-05-05 13:33:25', 2, '2016-05-05 13:33:25', 2, 13, 0),
(14, '2', NULL, 'New cruncher', NULL, 5, '2016-05-05 19:09:12', 5, '2016-05-05 19:09:12', 5, 14, 0),
(15, '2', NULL, 'Tim test', NULL, 5, '2016-05-06 09:59:40', 5, '2016-05-06 09:59:40', 5, 15, 0),
(16, '2', NULL, 'Crunch', NULL, 5, '2016-05-06 10:11:46', 5, '2016-05-06 10:11:46', 5, 16, 0),
(17, '2', NULL, 'cruncherssssss', NULL, 5, '2016-05-06 10:12:41', 5, '2016-05-06 10:12:41', 5, 17, 0),
(18, '2', NULL, 'data test', NULL, 5, '2016-05-06 13:42:51', 5, '2016-05-06 13:42:51', 5, 18, 0),
(19, '5', NULL, 'My Organisation', NULL, 31, '2016-05-09 13:04:04', 31, '2016-05-09 13:04:04', 31, 19, 0),
(20, '6', NULL, 'My Organisation', NULL, 32, '2016-05-11 13:31:00', 32, '2016-05-11 13:31:00', 32, 20, 0),
(21, '7', NULL, 'My Organisation', NULL, 33, '2016-05-11 13:35:51', 33, '2016-05-11 13:35:51', 33, 21, 0),
(22, '2', NULL, 'More Number Limited', NULL, 5, '2016-05-11 13:36:36', 5, '2016-05-11 13:36:36', 5, 22, 0),
(23, '8', NULL, 'My Organisation', NULL, 34, '2016-05-11 23:42:03', 34, '2016-05-11 23:42:03', 34, 23, 0),
(24, '8', NULL, 'Erie', NULL, 34, '2016-05-11 23:56:48', 34, '2016-05-11 23:56:48', 34, 24, 0),
(25, '9', NULL, 'My Organisation', NULL, 35, '2016-05-12 23:44:14', 35, '2016-05-12 23:44:14', 35, 25, 0),
(26, '9', NULL, 'James Moto Shop', NULL, 35, '2016-05-12 23:44:49', 35, '2016-05-12 23:44:49', 35, 26, 0),
(27, '4', NULL, 'David Org', NULL, 17, '2016-05-16 08:00:12', 17, '2016-05-16 08:00:12', 17, 27, 0),
(28, '4', NULL, 'David Org', NULL, 17, '2016-05-16 08:00:50', 17, '2016-05-16 08:00:50', 17, 28, 0),
(29, '2', NULL, 'Demo Company', NULL, 5, '2016-05-24 22:28:54', 5, '2016-05-24 22:28:54', 5, 29, 1),
(30, '2', NULL, 'Demo - Accountancy Practice', NULL, 5, '2016-06-02 10:43:49', 5, '2016-06-02 10:43:49', 5, 30, 0),
(31, '2', NULL, 'test', NULL, 5, '2016-06-02 13:35:09', 5, '2016-06-02 13:35:09', 5, 31, 0),
(32, '2', NULL, 'test 2', NULL, 5, '2016-06-02 13:35:24', 5, '2016-06-02 13:35:24', 5, 32, 0),
(33, '2', NULL, 'test 3', NULL, 5, '2016-06-02 13:35:38', 5, '2016-06-02 13:35:38', 5, 33, 0),
(34, '2', NULL, 'Tims test DNFW', NULL, 5, '2016-06-09 15:29:07', 5, '2016-06-09 15:29:07', 5, 34, 0),
(35, '2', NULL, 'Live Test', NULL, 5, '2016-06-22 08:50:03', 5, '2016-06-22 08:50:03', 5, 42, 0),
(36, '10', NULL, 'My Organisation', NULL, 51, '2016-06-22 12:02:36', 51, '2016-06-22 12:02:36', 51, 35, 0),
(37, '11', NULL, 'My Organisation', NULL, 52, '2016-06-22 12:58:46', 52, '2016-06-22 12:58:46', 52, 36, 0),
(38, '12', NULL, 'My Organisation', NULL, 55, '2016-06-24 14:57:44', 55, '2016-06-24 14:57:44', 55, 37, 0);

-- --------------------------------------------------------

--
-- Table structure for table `plan_coverpage`
--

CREATE TABLE IF NOT EXISTS `plan_coverpage` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_logo` text,
  `slogan` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT '',
  `state` varchar(255) DEFAULT NULL,
  `postal` varchar(10) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `confidentiality_message` text,
  `print_options` text NOT NULL,
  `entered` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `plan_coverpage`
--

INSERT INTO `plan_coverpage` (`id`, `plan_id`, `company_name`, `company_logo`, `slogan`, `street_address`, `state`, `postal`, `city`, `country`, `contact_name`, `contact_email`, `contact_phone`, `company_website`, `confidentiality_message`, `print_options`, `entered`, `updated`) VALUES
(1, 2, 'More Numbers Limited', '0578b71e9a8c748667f31029180b25f8.png', 'We have created a brand, service and software to enable accountants to add value.', 'Tremough Innovation Centre', 'Cornwall', 'TR10 9TA', '', 'United Kingdom', 'Tim Pointon', 'tim.pointon@crunchersaccountants.co.uk', '0333 320 4550', '', 'CONFIDENTIAL', 'a:8:{s:10:"paper_size";s:2:"a4";s:7:"spacing";s:1:"1";s:13:"is_plan_title";s:1:"1";s:9:"is_paging";s:1:"1";s:4:"page";s:4:"1-10";s:19:"is_confidential_msg";s:1:"1";s:19:"confidentiality_msg";s:12:"CONFIDENTIAL";s:6:"is_toc";s:1:"1";}', '2016-04-05 20:42:49', '2016-04-05 20:42:49');

-- --------------------------------------------------------

--
-- Table structure for table `plan_users`
--

CREATE TABLE IF NOT EXISTS `plan_users` (
  `plan_users` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` smallint(6) DEFAULT NULL,
  `plan_id` smallint(6) DEFAULT NULL,
  `plan_user_type` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`plan_users`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE IF NOT EXISTS `section` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` varchar(45) DEFAULT NULL,
  `chapter_id` bigint(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `position` int(11) DEFAULT NULL,
  `instructions` text NOT NULL,
  `example` text NOT NULL,
  `entered` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=420 ;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`section_id`, `plan_id`, `chapter_id`, `title`, `content`, `position`, `instructions`, `example`, `entered`, `updated`) VALUES
(5, '1', 11, 'Who we are', '&lt;p&gt;&lt;br data-mce-bogus=&quot;1&quot;&gt;&lt;/p&gt;', 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2015-07-23 08:19:27', '2015-07-23 08:19:27'),
(6, '1', 11, 'What we sell', '', 3, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2015-07-23 08:19:38', '2015-07-23 08:19:38'),
(7, '1', 11, 'Core values', '&lt;p&gt;Our core values 123&lt;/p&gt;', 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2015-07-23 08:19:49', '2015-07-23 08:19:49'),
(9, '2', 10, 'Section', '&lt;p&gt;Add some text.&lt;/p&gt;', 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2015-07-23 10:39:08', '2015-07-23 10:39:08'),
(10, '2', 10, 'Section 2', '', 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2015-07-23 10:39:18', '2015-07-23 10:39:18'),
(12, '2', 10, 'Section 3', '&lt;p&gt;Add some text.&lt;/p&gt;', 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2015-08-20 08:56:47', '2015-08-20 08:56:47'),
(14, '1', 11, 'Contact Us', '&lt;p&gt;&lt;br data-mce-bogus=&quot;1&quot;&gt;&lt;/p&gt;', 5, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-01-13 08:24:23', '2016-01-13 08:24:23'),
(18, '2', 8, 'SWOT Analysis', '&lt;p&gt;&lt;span style=&quot;color: #000000; font-size: 13px; line-height: 20px;&quot; data-mce-style=&quot;color: #000000; font-size: 13px; line-height: 20px;&quot;&gt;Don''t be intimidated by the technical-sounding term. Preparing a SWOT analysis is actually very simple. The acronym just stands for &quot;Strengths, Weaknesses, Opportunities, and Threats.&quot; The idea behind this exercise is to describe your company''s strategic position in those four areas. What are the greatest strengths and weaknesses of your company? Where do you see your most promising opportunities? What competitive threats do you need to avoid or overcome to take advantage of those opportunities?&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', 9, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-02-05 10:52:34', '2016-02-05 10:52:34'),
(20, '2', 14, 'Opportunity', '&lt;p style=&quot;font-size: 12.6667px; line-height: 18.0952px;&quot; data-mce-style=&quot;font-size: 12.6667px; line-height: 18.0952px;&quot;&gt;&lt;br&gt;&lt;/p&gt;', 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-02-10 12:01:41', '2016-02-10 12:01:41'),
(22, NULL, NULL, NULL, NULL, NULL, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', NULL, NULL),
(38, '2', 5, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:24:01', '2016-04-06 22:24:01'),
(39, '2', 5, 'Target Market', '&lt;p&gt;Add some text.&lt;/p&gt;', 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:24:20', '2016-04-06 22:24:20'),
(40, '2', 5, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:24:35', '2016-04-06 22:24:35'),
(41, '2', 6, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:25:29', '2016-04-06 22:25:29'),
(42, '2', 6, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:25:49', '2016-04-06 22:25:49'),
(43, '2', 6, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:26:17', '2016-04-06 22:26:17'),
(44, '2', 3, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:26:35', '2016-04-06 22:26:35'),
(45, '2', 3, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:26:45', '2016-04-06 22:26:45'),
(46, '2', 9, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:27:01', '2016-04-06 22:27:01'),
(47, '2', 9, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:27:16', '2016-04-06 22:27:16'),
(48, '2', 9, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:27:31', '2016-04-06 22:27:31'),
(49, '2', 14, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-04-06 22:28:03', '2016-04-06 22:28:03'),
(50, '8', 15, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 10:52:52', '2016-05-05 10:52:52'),
(51, '9', 16, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 10:52:52', '2016-05-05 10:52:52'),
(52, '10', 17, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 10:52:52', '2016-05-05 10:52:52'),
(53, '11', 18, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:10:08', '2016-05-05 11:10:08'),
(54, '11', 18, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:10:08', '2016-05-05 11:10:08'),
(55, '11', 19, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:10:08', '2016-05-05 11:10:08'),
(56, '11', 19, 'Target Market', '&lt;p&gt;Add some text.&lt;/p&gt;', 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:10:08', '2016-05-05 11:10:08'),
(57, '11', 19, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:10:09', '2016-05-05 11:10:09'),
(58, '11', 20, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:10:11', '2016-05-05 11:10:11'),
(59, '11', 20, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:10:15', '2016-05-05 11:10:15'),
(60, '11', 20, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:10:23', '2016-05-05 11:10:23'),
(61, '11', 21, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:10:36', '2016-05-05 11:10:36'),
(62, '11', 21, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:11:03', '2016-05-05 11:11:03'),
(63, '11', 21, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:11:47', '2016-05-05 11:11:47'),
(64, '11', 22, 'Section', '&lt;p&gt;Add some text.&lt;/p&gt;', 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:13:06', '2016-05-05 11:13:06'),
(65, '11', 22, 'Section 2', '', 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:16:31', '2016-05-05 11:16:31'),
(66, '11', 22, 'Section 3', '&lt;p&gt;Add some text.&lt;/p&gt;', 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:21:42', '2016-05-05 11:21:42'),
(67, '11', 22, 'Section', '&lt;p&gt;Add some text.&lt;/p&gt;', 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:31:54', '2016-05-05 11:31:54'),
(68, '11', 22, 'Section 2', '&lt;p&gt;Add some text.&lt;/p&gt;', 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 11:51:53', '2016-05-05 11:51:53'),
(69, '11', 22, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 12:33:48', '2016-05-05 12:33:48'),
(70, '12', 23, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 13:33:25', '2016-05-05 13:33:25'),
(71, '13', 24, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-05 13:33:25', '2016-05-05 13:33:25'),
(164, '22', 55, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(165, '22', 55, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(166, '22', 56, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(167, '22', 56, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(168, '22', 56, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(169, '22', 57, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(170, '22', 57, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(171, '22', 57, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(172, '22', 58, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(173, '22', 58, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(174, '22', 58, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(175, '22', 59, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(176, '22', 59, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(177, '22', 59, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(178, '22', 60, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(179, '22', 60, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 13:36:36', '2016-05-11 13:36:36'),
(180, '23', 61, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:03', '2016-05-11 23:42:03'),
(181, '23', 61, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:03', '2016-05-11 23:42:03'),
(182, '23', 62, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(183, '23', 62, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(184, '23', 62, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(185, '23', 63, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(186, '23', 63, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(187, '23', 63, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(188, '23', 64, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(189, '23', 64, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(190, '23', 64, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(191, '23', 65, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(192, '23', 65, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(193, '23', 65, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(194, '23', 66, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(195, '23', 66, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:42:04', '2016-05-11 23:42:04'),
(196, '24', 67, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(197, '24', 67, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(198, '24', 68, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(199, '24', 68, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(200, '24', 68, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(201, '24', 69, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(202, '24', 69, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(203, '24', 69, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(204, '24', 70, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(205, '24', 70, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(206, '24', 70, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(207, '24', 71, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(208, '24', 71, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(209, '24', 71, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(210, '24', 72, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(211, '24', 72, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-11 23:56:48', '2016-05-11 23:56:48'),
(212, '25', 73, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(213, '25', 73, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(214, '25', 74, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(215, '25', 74, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(216, '25', 74, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(217, '25', 75, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(218, '25', 75, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(219, '25', 75, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(220, '25', 76, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(221, '25', 76, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(222, '25', 76, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(223, '25', 77, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(224, '25', 77, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(225, '25', 77, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(226, '25', 78, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(227, '25', 78, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:14', '2016-05-12 23:44:14'),
(228, '26', 79, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(229, '26', 79, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(230, '26', 80, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(231, '26', 80, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(232, '26', 80, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(233, '26', 81, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(234, '26', 81, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(235, '26', 81, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(236, '26', 82, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(237, '26', 82, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(238, '26', 82, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(239, '26', 83, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(240, '26', 83, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(241, '26', 83, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(242, '26', 84, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(243, '26', 84, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-12 23:44:49', '2016-05-12 23:44:49'),
(260, '28', 91, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(261, '28', 91, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(262, '28', 92, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(263, '28', 92, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(264, '28', 92, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(265, '28', 93, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(266, '28', 93, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(267, '28', 93, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(268, '28', 94, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(269, '28', 94, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(270, '28', 94, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(271, '28', 95, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(272, '28', 95, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(273, '28', 95, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(274, '28', 96, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50'),
(275, '28', 96, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-16 08:00:50', '2016-05-16 08:00:50');
INSERT INTO `section` (`section_id`, `plan_id`, `chapter_id`, `title`, `content`, `position`, `instructions`, `example`, `entered`, `updated`) VALUES
(276, '29', 97, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(277, '29', 97, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(278, '29', 98, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(279, '29', 98, 'Target Market', '&lt;p&gt;Add some text.&lt;/p&gt;', 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(280, '29', 98, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(281, '29', 99, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(282, '29', 99, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(283, '29', 99, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(284, '29', 100, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(285, '29', 100, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(286, '29', 100, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(287, '29', 101, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(288, '29', 101, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(289, '29', 101, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(290, '29', 102, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(291, '29', 102, 'Expectations', '&lt;p&gt;&lt;br&gt;&lt;/p&gt;', 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-05-24 22:28:54', '2016-05-24 22:28:54'),
(292, '30', 103, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(293, '30', 103, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(294, '30', 104, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(295, '30', 104, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(296, '30', 104, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(297, '30', 105, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(298, '30', 105, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(299, '30', 105, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(300, '30', 106, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(301, '30', 106, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(302, '30', 106, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(303, '30', 107, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(304, '30', 107, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(305, '30', 107, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(306, '30', 108, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(307, '30', 108, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-02 10:43:49', '2016-06-02 10:43:49'),
(356, '34', 127, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(357, '34', 127, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(358, '34', 128, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(359, '34', 128, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(360, '34', 128, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(361, '34', 129, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(362, '34', 129, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(363, '34', 129, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(364, '34', 130, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(365, '34', 130, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(366, '34', 130, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(367, '34', 131, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(368, '34', 131, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(369, '34', 131, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(370, '34', 132, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(371, '34', 132, 'Expectations', '&lt;p&gt;&lt;span style=&quot;background-color: #ffffff;&quot; data-mce-style=&quot;background-color: #ffffff;&quot;&gt;This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.&lt;/span&gt;&lt;span style=&quot;line-height: 1.42857; background-color: #ffffff;&quot; data-mce-style=&quot;line-height: 1.42857; background-color: #ffffff;&quot;&gt;This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.&lt;/span&gt;&lt;span style=&quot;line-height: 1.42857; background-color: #ffffff;&quot; data-mce-style=&quot;line-height: 1.42857; background-color: #ffffff;&quot;&gt;This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.&lt;/span&gt;&lt;span style=&quot;line-height: 1.42857; background-color: #ffffff;&quot; data-mce-style=&quot;line-height: 1.42857; background-color: #ffffff;&quot;&gt;This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-09 15:29:07', '2016-06-09 15:29:07'),
(372, '36', 133, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(373, '36', 133, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(374, '36', 134, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(375, '36', 134, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(376, '36', 134, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(377, '36', 135, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(378, '36', 135, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(379, '36', 135, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(380, '36', 136, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(381, '36', 136, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(382, '36', 136, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(383, '36', 137, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(384, '36', 137, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(385, '36', 137, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(386, '36', 138, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(387, '36', 138, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:02:36', '2016-06-22 12:02:36'),
(388, '37', 139, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(389, '37', 139, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(390, '37', 140, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(391, '37', 140, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(392, '37', 140, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(393, '37', 141, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(394, '37', 141, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(395, '37', 141, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(396, '37', 142, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(397, '37', 142, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(398, '37', 142, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(399, '37', 143, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:46', '2016-06-22 12:58:46'),
(400, '37', 143, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:47', '2016-06-22 12:58:47'),
(401, '37', 143, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:47', '2016-06-22 12:58:47'),
(402, '37', 144, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:47', '2016-06-22 12:58:47'),
(403, '37', 144, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-22 12:58:47', '2016-06-22 12:58:47'),
(404, '38', 145, 'Overview', NULL, 19, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(405, '38', 145, 'Team', NULL, 20, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(406, '38', 146, 'Problem & Solution', NULL, 13, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(407, '38', 146, 'Target Market', NULL, 14, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(408, '38', 146, 'Competition', NULL, 15, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(409, '38', 147, 'Marketing & Sales', NULL, 16, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(410, '38', 147, 'Operations', NULL, 17, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(411, '38', 147, 'Milestones & Metrics', NULL, 18, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(412, '38', 148, 'Forecast', NULL, 21, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(413, '38', 148, 'Financing', NULL, 22, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(414, '38', 148, 'Statements', NULL, 23, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(415, '38', 149, 'Section', NULL, 0, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(416, '38', 149, 'Section 2', NULL, 1, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(417, '38', 149, 'Section 3', NULL, 2, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(418, '38', 150, 'Opportunity', NULL, 11, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44'),
(419, '38', 150, 'Expectations', NULL, 24, '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', '2016-06-24 14:57:44', '2016-06-24 14:57:44');

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE IF NOT EXISTS `stats` (
  `ID` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `value1` int(11) DEFAULT NULL,
  `value2` int(11) DEFAULT NULL,
  `value3` int(11) DEFAULT NULL,
  `value4` int(11) DEFAULT NULL,
  `value5` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`ID`, `description`, `value1`, `value2`, `value3`, `value4`, `value5`) VALUES
(0, 'New account', 0, NULL, NULL, NULL, NULL),
(0, 'Accounts', 8, 0, 0, 0, 6),
(0, 'Organisations', 20, 0, 0, 3, 20),
(0, 'Milestones', 19, 0, 0, 19, 19);

-- --------------------------------------------------------

--
-- Table structure for table `subsection`
--

CREATE TABLE IF NOT EXISTS `subsection` (
  `subsection_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `title` varchar(80) DEFAULT NULL,
  `description` longtext,
  `data` blob,
  `icon` varchar(45) DEFAULT NULL,
  `type` varchar(15) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `instructions` text NOT NULL,
  `example` text NOT NULL,
  `chart_type` int(11) NOT NULL,
  PRIMARY KEY (`subsection_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3286309 ;

--
-- Dumping data for table `subsection`
--

INSERT INTO `subsection` (`subsection_id`, `plan_id`, `section_id`, `position`, `title`, `description`, `data`, `icon`, `type`, `entered`, `updated`, `instructions`, `example`, `chart_type`) VALUES
(4, 2, 13, NULL, 'Test Item', NULL, 0x266c743b702667743b266c743b7370616e207374796c653d2671756f743b636f6c6f723a20233030303030303b20666f6e742d66616d696c793a20417269616c2c2048656c7665746963612c2073616e733b20666f6e742d73697a653a20313170783b206c696e652d6865696768743a20313470783b20746578742d616c69676e3a206a7573746966793b2671756f743b20646174612d6d63652d7374796c653d2671756f743b636f6c6f723a20233030303030303b20666f6e742d66616d696c793a20417269616c2c2048656c7665746963612c2073616e733b20666f6e742d73697a653a20313170783b206c696e652d6865696768743a20313470783b20746578742d616c69676e3a206a7573746966793b2671756f743b2667743b2671756f743b5365642075742070657273706963696174697320756e6465206f6d6e69732069737465206e61747573206572726f722073697420766f6c7570746174656d206163637573616e7469756d20646f6c6f72656d717565206c617564616e7469756d2c20746f74616d2072656d206170657269616d2c2065617175652069707361207175616520616220696c6c6f20696e76656e746f726520766572697461746973206574207175617369206172636869746563746f206265617461652076697461652064696374612073756e74206578706c696361626f2e204e656d6f20656e696d20697073616d20766f6c7570746174656d207175696120766f6c7570746173207369742061737065726e6174757220617574206f646974206175742066756769742c20736564207175696120636f6e73657175756e747572206d61676e6920646f6c6f72657320656f732071756920726174696f6e6520766f6c7570746174656d207365717569206e65736369756e742e204e6571756520706f72726f20717569737175616d206573742c2071756920646f6c6f72656d266c743b2f7370616e2667743b266c743b62722667743b266c743b2f702667743b, NULL, 'text', '2016-01-13 15:03:58', '2016-01-13 15:03:58', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(9, 2, 18, NULL, 'Strengths', NULL, 0x266c743b702667743b54686520737472656e67746873206f66206f757220627573696e65737320696e636c7564652074686520666f6c6c6f77696e673a266c743b2f702667743b266c743b756c2667743b266c743b6c692667743b5468652068696768207175616c697479206f66206f7572207765627369746573266c743b2f6c692667743b266c743b6c692667743b537472617465676963206d61726b6574207365676d656e746174696f6e20616e6420696d706c656d656e746174696f6e2073747261746567696573266c743b2f6c692667743b266c743b6c692667743b4469766572736966696564206d61726b6574207365676d656e74732c20656e737572696e6720746865206c61636b206f6620646570656e64656e6379206f6e206f6e6520706172746963756c6172206d61726b6574266c743b2f6c692667743b266c743b6c692667743b5374726f6e67206272616e64696e6720616e6420706f736974696f6e696e67266c743b2f6c692667743b266c743b6c692667743b416e206167677265737369766520616e6420666f6375736564206d61726b6574696e672063616d706169676e207769746820636c65617220676f616c7320616e642073747261746567696573266c743b2f6c692667743b266c743b6c692667743b4f7572206b6e6f776c6564676520616e6420736b696c6c73266c743b2f6c692667743b266c743b6c692667743b536f667477617265266c743b2f6c692667743b266c743b6c692667743b52657075746174696f6e266c743b2f6c692667743b266c743b6c692667743b4f66666963657320616e6420747261696e696e6720726f6f6d732e266c743b2f6c692667743b266c743b2f756c2667743b, NULL, 'text', '2016-02-05 10:56:44', '2016-02-05 10:56:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(10, 2, 18, NULL, 'Weeknesses', NULL, 0x266c743b756c2667743b266c743b6c692667743b4c61636b206f66204d61726b6574696e6720696e766573746d656e74266c743b2f6c692667743b266c743b6c692667743b506f6f7220617420666f6c6c6f77696e67207570206f6e2053616c6573206c65616473266c743b2f6c692667743b266c743b6c692667743b4f7665722064656c69766572206f66207365727669636573266c743b2f6c692667743b266c743b6c692667743b4e65656420746f20696d70726f7665206f757220696e7465726e616c20747261696e696e672e266c743b2f6c692667743b266c743b2f756c2667743b, NULL, 'text', '2016-02-05 10:57:19', '2016-02-05 10:57:19', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(12, 2, 18, NULL, 'Oportunities', NULL, 0x266c743b70207374796c653d2671756f743b6d617267696e2d626f74746f6d3a20312e34656d3b2070616464696e673a203070783b20626f726465723a203070783b206f75746c696e653a203070783b20666f6e742d73697a653a20313370783b206c696e652d6865696768743a20312e34656d3b20636f6c6f723a20233366343834623b2671756f743b20646174612d6d63652d7374796c653d2671756f743b6d617267696e2d626f74746f6d3a20312e34656d3b2070616464696e673a203070783b20626f726465723a203070783b206f75746c696e653a203070783b20666f6e742d73697a653a20313370783b206c696e652d6865696768743a20312e34656d3b20636f6c6f723a20233366343834623b2671756f743b2667743b26616d703b6e6273703b266c743b62722667743b266c743b2f702667743b266c743b756c2667743b266c743b6c692667743b546f20696d70726f7665206f757220757365206f662046616365426f6f6b266c743b2f6c692667743b266c743b6c692667743b4672616e63686973696e67206f662074686520627573696e657373266c743b2f6c692667743b266c743b6c692667743b546f20637265617465206d6f726520726573696475616c20696e636f6d65266c743b2f6c692667743b266c743b6c692667743b496e63726561736520746865206e756d626572206f66202641636972633b26706f756e643b322c3030302b20706572206d6f6e746820636c69656e7473266c743b2f6c692667743b266c743b6c692667743b557365206f6620505043204164766572746973696e67266c743b2f6c692667743b266c743b6c692667743b557365206f662072652d746172676574696e67266c743b2f6c692667743b266c743b6c692667743b526563727569746d656e74206f6620612073616c657320706572736f6e2e266c743b2f6c692667743b266c743b2f756c2667743b, NULL, 'text', '2016-02-05 11:17:36', '2016-02-05 11:17:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(13, 2, 18, NULL, 'Threats', NULL, 0x266c743b756c2667743b266c743b6c692667743b46696e616e6369616c20726573747261696e7473266c743b2f6c692667743b266c743b6c692667743b456d657267656e6365206f6620636f6d70657469746f7273266c743b2f6c692667743b266c743b6c692667743b4e6f74206d616b696e6720656e6f7567682070726f666974266c743b2f6c692667743b266c743b6c692667743b4368616e67657320696e20746865206d61726b6574696e6720776f726c642e266c743b2f6c692667743b266c743b2f756c2667743b, NULL, 'text', '2016-02-05 11:17:54', '2016-02-05 11:17:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(15, 2, 15, NULL, 'Who are we', NULL, 0x266c743b702667743b576520617265206120736d616c6c207465616d206f66206d61726b6574696e672061647669736f72732c20747261696e65727320616e6420737570706f72742070656f706c652e2026616d703b6e6273703b2661636972633b809c57652068656c7020627573696e657373657320746f20696e6372656173652073616c65732062792034302520746f2031303025207573696e6720612073797374656d2077652063616c6c202661636972633b809c50727564656e74204d61726b6574696e672661636972633b809d2e26616d703b6e6273703b20576520747261696e2c2061647669736520616e64206f666665722061202661636972633b80984d61726b6574696e6720537570706f72742661636972633b8099207365727669636520666f7220627573696e657373657320696e2074686520554b20616e64206162726f61642e266c743b2f702667743b, NULL, 'text', '2016-02-10 12:07:18', '2016-02-10 12:07:18', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(17, 2, 16, NULL, 'What we sell', NULL, 0x266c743b702667743b7a6762676273676662676673626667266c743b62722667743b266c743b2f702667743b, NULL, 'text', '2016-02-11 11:11:16', '2016-02-11 11:11:16', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(18, 2, 21, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-02-11 19:36:13', '2016-02-11 19:36:13', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(21, 2, 20, NULL, 'Problem', NULL, 0x266c743b702667743b4564697420746869732073656374696f6e266c743b2f702667743b, NULL, 'text', '2016-04-06 22:18:02', '2016-04-06 22:18:02', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(22, 2, 20, NULL, 'Solution', NULL, 0x266c743b702667743b266c743b62722667743b266c743b2f702667743b, NULL, 'text', '2016-04-06 22:20:11', '2016-04-06 22:20:11', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(23, 2, 20, NULL, 'Market', NULL, 0x266c743b702667743b4564697420746869732073656374696f6e266c743b2f702667743b, NULL, 'text', '2016-04-06 22:20:47', '2016-04-06 22:20:47', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(24, 2, 20, NULL, 'Competition', NULL, 0x266c743b702667743b4564697420746869732073656374696f6e266c743b2f702667743b, NULL, 'text', '2016-04-06 22:20:55', '2016-04-06 22:20:55', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(25, 2, 20, NULL, 'Why Us?', NULL, 0x266c743b702667743b4564697420746869732073656374696f6e266c743b2f702667743b, NULL, 'text', '2016-04-06 22:21:07', '2016-04-06 22:21:07', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(26, 2, 38, NULL, 'Problem Worth Solving', NULL, 0x266c743b702667743b4564697420746869732073656374696f6e266c743b2f702667743b, NULL, 'text', '2016-04-06 23:13:55', '2016-04-06 23:13:55', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(27, 2, 38, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-04-06 23:14:09', '2016-04-06 23:14:09', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(28, 2, 39, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-04-06 23:21:14', '2016-04-06 23:21:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(29, 2, 40, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-04-06 23:21:42', '2016-04-06 23:21:42', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(30, 2, 40, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-04-06 23:21:52', '2016-04-06 23:21:52', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(31, 2, 41, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-04-06 23:22:30', '2016-04-06 23:22:30', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(32, 2, 41, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-04-06 23:22:41', '2016-04-06 23:22:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(33, 2, 42, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-04-06 23:24:04', '2016-04-06 23:24:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(34, 2, 42, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-04-06 23:24:20', '2016-04-06 23:24:20', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(35, 2, 42, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-04-06 23:24:35', '2016-04-06 23:24:35', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(39, 2, 46, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-04-18 14:53:57', '2016-04-18 14:53:57', '', '', 6),
(40, 2, 46, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-04-18 14:55:02', '2016-04-18 14:55:02', '', '', 6),
(41, 2, 46, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-04-18 14:55:10', '2016-04-18 14:55:10', '', '', 14),
(3285914, 15, 95, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-06 09:59:40', '2016-05-06 09:59:40', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285915, 15, 95, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-06 09:59:40', '2016-05-06 09:59:40', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285916, 15, 109, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-06 09:59:40', '2016-05-06 09:59:40', '', '', 4),
(3285917, 15, 109, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-06 09:59:40', '2016-05-06 09:59:40', '', '', 0),
(3285918, 16, 112, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285919, 16, 112, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285920, 16, 113, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285921, 16, 114, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285922, 16, 114, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285923, 16, 115, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285924, 16, 115, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285925, 16, 116, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285926, 16, 116, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285927, 16, 116, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3285928, 16, 118, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '', '', 6),
(3285929, 16, 118, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '', '', 6),
(3285930, 16, 118, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '', '', 14),
(3285931, 16, 127, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285932, 16, 127, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285933, 16, 127, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285934, 16, 127, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285935, 16, 127, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285936, 16, 127, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '', '', 5),
(3285937, 16, 128, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '', '', 4),
(3285938, 16, 128, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-06 10:11:46', '2016-05-06 10:11:46', '', '', 0),
(3285939, 17, 131, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285940, 17, 131, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285941, 17, 132, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285942, 17, 133, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285943, 17, 133, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285944, 17, 134, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285945, 17, 134, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285946, 17, 135, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285947, 17, 135, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285948, 17, 135, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3285949, 17, 137, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '', '', 6),
(3285950, 17, 137, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '', '', 6),
(3285951, 17, 137, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '', '', 14),
(3285952, 17, 146, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285953, 17, 146, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285954, 17, 146, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285955, 17, 146, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285956, 17, 146, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285957, 17, 146, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '', '', 5),
(3285958, 17, 147, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '', '', 4),
(3285959, 17, 147, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-06 10:12:41', '2016-05-06 10:12:41', '', '', 0),
(3285981, 22, 166, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285982, 22, 166, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285983, 22, 167, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285984, 22, 168, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285985, 22, 168, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285986, 22, 169, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285987, 22, 169, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285988, 22, 170, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285989, 22, 170, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285990, 22, 170, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3285991, 22, 172, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '', '', 6),
(3285992, 22, 172, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '', '', 6),
(3285993, 22, 172, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '', '', 14),
(3285994, 22, 178, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285995, 22, 178, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285996, 22, 178, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285997, 22, 178, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285998, 22, 178, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3285999, 22, 178, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '', '', 5),
(3286000, 22, 179, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '', '', 4),
(3286001, 22, 179, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-11 13:36:36', '2016-05-11 13:36:36', '', '', 0),
(3286002, 23, 182, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286003, 23, 182, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286004, 23, 183, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286005, 23, 184, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286006, 23, 184, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286007, 23, 185, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286008, 23, 185, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286009, 23, 186, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286010, 23, 186, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286011, 23, 186, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286012, 23, 188, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '', '', 6),
(3286013, 23, 188, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '', '', 6),
(3286014, 23, 188, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '', '', 14),
(3286015, 23, 194, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286016, 23, 194, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286017, 23, 194, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286018, 23, 194, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286019, 23, 194, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286020, 23, 194, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '', '', 5),
(3286021, 23, 195, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '', '', 4),
(3286022, 23, 195, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-11 23:42:04', '2016-05-11 23:42:04', '', '', 0),
(3286023, 24, 198, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286024, 24, 198, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286025, 24, 199, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286026, 24, 200, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286027, 24, 200, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286028, 24, 201, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286029, 24, 201, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286030, 24, 202, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286031, 24, 202, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286032, 24, 202, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286033, 24, 204, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '', '', 6),
(3286034, 24, 204, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '', '', 6),
(3286035, 24, 204, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '', '', 14),
(3286036, 24, 210, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286037, 24, 210, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286038, 24, 210, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286039, 24, 210, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286040, 24, 210, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286041, 24, 210, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '', '', 5),
(3286042, 24, 211, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '', '', 4),
(3286043, 24, 211, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-11 23:56:48', '2016-05-11 23:56:48', '', '', 0),
(3286044, 25, 214, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286045, 25, 214, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286046, 25, 215, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0);
INSERT INTO `subsection` (`subsection_id`, `plan_id`, `section_id`, `position`, `title`, `description`, `data`, `icon`, `type`, `entered`, `updated`, `instructions`, `example`, `chart_type`) VALUES
(3286047, 25, 216, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286048, 25, 216, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286049, 25, 217, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286050, 25, 217, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286051, 25, 218, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286052, 25, 218, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286053, 25, 218, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286054, 25, 220, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '', '', 6),
(3286055, 25, 220, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '', '', 6),
(3286056, 25, 220, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '', '', 14),
(3286057, 25, 226, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286058, 25, 226, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286059, 25, 226, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286060, 25, 226, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286061, 25, 226, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286062, 25, 226, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '', '', 5),
(3286063, 25, 227, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '', '', 4),
(3286064, 25, 227, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:14', '2016-05-12 23:44:14', '', '', 0),
(3286065, 26, 230, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286066, 26, 230, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286067, 26, 231, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286068, 26, 232, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286069, 26, 232, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286070, 26, 233, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286071, 26, 233, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286072, 26, 234, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286073, 26, 234, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286074, 26, 234, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286075, 26, 236, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '', '', 6),
(3286076, 26, 236, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '', '', 6),
(3286077, 26, 236, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '', '', 14),
(3286078, 26, 242, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286079, 26, 242, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286080, 26, 242, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286081, 26, 242, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286082, 26, 242, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286083, 26, 242, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '', '', 5),
(3286084, 26, 243, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '', '', 4),
(3286085, 26, 243, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-12 23:44:49', '2016-05-12 23:44:49', '', '', 0),
(3286107, 28, 262, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286108, 28, 262, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286109, 28, 263, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286110, 28, 264, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286111, 28, 264, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286112, 28, 265, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286113, 28, 265, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286114, 28, 266, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286115, 28, 266, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286116, 28, 266, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286117, 28, 268, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '', '', 6),
(3286118, 28, 268, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '', '', 6),
(3286119, 28, 268, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '', '', 14),
(3286120, 28, 274, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286121, 28, 274, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286122, 28, 274, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286123, 28, 274, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286124, 28, 274, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286125, 28, 274, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '', '', 5),
(3286126, 28, 275, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '', '', 4),
(3286127, 28, 275, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-16 08:00:50', '2016-05-16 08:00:50', '', '', 0),
(3286128, 29, 278, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286129, 29, 278, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286130, 29, 279, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286131, 29, 280, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286132, 29, 280, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286133, 29, 281, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286134, 29, 281, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286135, 29, 282, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286136, 29, 282, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286137, 29, 282, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286138, 29, 284, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '', '', 6),
(3286139, 29, 284, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '', '', 6),
(3286140, 29, 284, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '', '', 14),
(3286141, 29, 290, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286142, 29, 290, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286143, 29, 290, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286144, 29, 290, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286145, 29, 290, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286146, 29, 290, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '', '', 5),
(3286147, 29, 291, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '', '', 4),
(3286148, 29, 291, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-05-24 22:28:54', '2016-05-24 22:28:54', '', '', 0),
(3286149, 30, 294, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286150, 30, 294, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286151, 30, 295, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286152, 30, 296, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286153, 30, 296, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286154, 30, 297, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286155, 30, 297, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286156, 30, 298, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286157, 30, 298, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286158, 30, 298, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286159, 30, 300, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '', '', 6),
(3286160, 30, 300, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '', '', 6),
(3286161, 30, 300, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '', '', 14),
(3286162, 30, 306, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286163, 30, 306, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286164, 30, 306, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286165, 30, 306, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286166, 30, 306, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286167, 30, 306, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '', '', 5),
(3286168, 30, 307, NULL, 'Revenue', NULL, NULL, NULL, 'chart', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '', '', 4),
(3286169, 30, 307, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-06-02 10:43:49', '2016-06-02 10:43:49', '', '', 0),
(3286170, 29, 290, NULL, 'New Chart', NULL, NULL, NULL, 'chart', '2016-06-02 11:22:55', '2016-06-02 11:22:55', '', '', 0),
(3286236, 34, 358, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286237, 34, 358, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286238, 34, 359, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286239, 34, 360, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286240, 34, 360, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286241, 34, 361, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286242, 34, 361, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286243, 34, 362, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286244, 34, 362, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286245, 34, 362, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286246, 34, 364, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '', '', 6),
(3286247, 34, 364, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '', '', 6),
(3286248, 34, 364, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '', '', 14),
(3286249, 34, 370, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286250, 34, 370, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286251, 34, 370, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286252, 34, 370, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286253, 34, 370, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-06-09 15:29:07', '2016-06-09 15:29:07', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286254, 34, 371, NULL, 'Test Subsection', NULL, NULL, NULL, 'text', '2016-06-10 08:35:05', '2016-06-10 08:35:05', '', '', 0),
(3286255, 36, 374, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286256, 36, 374, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286257, 36, 375, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286258, 36, 376, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286259, 36, 376, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286260, 36, 377, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286261, 36, 377, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286262, 36, 378, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286263, 36, 378, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286264, 36, 378, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286265, 36, 380, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '', '', 6),
(3286266, 36, 380, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '', '', 6),
(3286267, 36, 380, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '', '', 14),
(3286268, 36, 386, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286269, 36, 386, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286270, 36, 386, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286271, 36, 386, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286272, 36, 386, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-06-22 12:02:36', '2016-06-22 12:02:36', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286273, 37, 390, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286274, 37, 390, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286275, 37, 391, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286276, 37, 392, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286277, 37, 392, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286278, 37, 393, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286279, 37, 393, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286280, 37, 394, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286281, 37, 394, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286282, 37, 394, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286283, 37, 396, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '', '', 6),
(3286284, 37, 396, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '', '', 6),
(3286285, 37, 396, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-06-22 12:58:46', '2016-06-22 12:58:46', '', '', 14),
(3286286, 37, 402, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-06-22 12:58:47', '2016-06-22 12:58:47', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286287, 37, 402, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-06-22 12:58:47', '2016-06-22 12:58:47', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286288, 37, 402, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-06-22 12:58:47', '2016-06-22 12:58:47', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286289, 37, 402, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-06-22 12:58:47', '2016-06-22 12:58:47', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286290, 37, 402, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-06-22 12:58:47', '2016-06-22 12:58:47', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0);
INSERT INTO `subsection` (`subsection_id`, `plan_id`, `section_id`, `position`, `title`, `description`, `data`, `icon`, `type`, `entered`, `updated`, `instructions`, `example`, `chart_type`) VALUES
(3286291, 38, 406, NULL, 'Problem Worth Solving', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286292, 38, 406, NULL, 'Our Solution', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286293, 38, 407, NULL, 'Market Size & Segments', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286294, 38, 408, NULL, 'Current Alternatives', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286295, 38, 408, NULL, 'Our Advantages', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286296, 38, 409, NULL, 'Marketing Plan', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286297, 38, 409, NULL, 'Sales Plan', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286298, 38, 410, NULL, 'Locations & Facilities', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286299, 38, 410, NULL, 'Technology', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286300, 38, 410, NULL, 'Equipment & Tools', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '', 0),
(3286301, 38, 412, NULL, 'Sales forcast', NULL, NULL, NULL, 'chart', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '', '', 6),
(3286302, 38, 412, NULL, 'Monthly Revenue', NULL, NULL, NULL, 'chart', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '', '', 6),
(3286303, 38, 412, NULL, 'Cash Flow', NULL, NULL, NULL, 'chart', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '', '', 14),
(3286304, 38, 418, NULL, 'Problem', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286305, 38, 418, NULL, 'Solution', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286306, 38, 418, NULL, 'Market', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286307, 38, 418, NULL, 'Competition', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0),
(3286308, 38, 418, NULL, 'Why Us?', NULL, NULL, NULL, 'text', '2016-06-24 14:57:44', '2016-06-24 14:57:44', '<p>Summarize what you wrote in the Company chapter. What is it about your company &mdash; your skills, experience, subject-matter expertise, business acumen, team, innovations, industry connections, key advisors, and so on &mdash; that makes you the right ones to take advantage of this market opportunity?</p>', '<p>An example of what is required will be displayed here for the user to get some idea of what they should be thinking about.</p>', 0);

-- --------------------------------------------------------

--
-- Table structure for table `subsection_comment`
--

CREATE TABLE IF NOT EXISTS `subsection_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subsection_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text,
  `entered` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

--
-- Dumping data for table `subsection_comment`
--

INSERT INTO `subsection_comment` (`id`, `subsection_id`, `user_id`, `comment`, `entered`) VALUES
(27, 3286254, 5, 'This is where some instructional notes on what to do will be displayed. This can be customised for each organisation. afdfbdgbgfb', '2016-06-10 08:35:26'),
(28, 3286249, 5, 'test comment', '2016-06-24 11:34:22'),
(29, 3286249, 5, 'testc omment 2', '2016-06-24 11:34:31'),
(30, 3286249, 5, 'This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.', '2016-06-24 11:35:28'),
(31, 3286249, 5, 'This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.This is where some instructional notes on what to do will be displayed. This can be customised for each organisation.', '2016-06-24 11:35:34'),
(32, 3286237, 5, 'test', '2016-06-24 13:21:15'),
(33, 3286237, 5, 'test', '2016-06-24 13:22:05'),
(34, 3286249, 5, 'tgwtgrtgbrtb rthgwthsrtpboiuadorgkjaeiurh qp9sae haosirhfaiosdhv;kladjhvnjae rv9padsr fvopqierh voqeirhvo[iqeru voiearu goiaerj gpiadjgposdjg seh]0', '2016-06-24 14:53:47');

-- --------------------------------------------------------

--
-- Table structure for table `system_tabs`
--

CREATE TABLE IF NOT EXISTS `system_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tab_name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `system_tabs`
--

INSERT INTO `system_tabs` (`id`, `tab_name`) VALUES
(1, 'dashboard'),
(2, 'pitch'),
(3, 'plan'),
(4, 'schedule'),
(5, 'goals'),
(6, 'meetings'),
(7, 'teams');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `organ_id` int(11) DEFAULT NULL,
  `participant_id` text,
  `owner_id` int(11) DEFAULT NULL,
  `task_name` varchar(150) DEFAULT NULL,
  `task_description` varchar(255) DEFAULT NULL,
  `task_dueDate` datetime DEFAULT NULL,
  `task_startDate` datetime NOT NULL,
  `entered_on` datetime DEFAULT NULL,
  `entered_by` int(11) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `date_completed` date DEFAULT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=99 ;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `organ_id`, `participant_id`, `owner_id`, `task_name`, `task_description`, `task_dueDate`, `task_startDate`, `entered_on`, `entered_by`, `updated_on`, `updated_by`, `status`, `priority`, `plan_id`, `milestone_id`, `user_id`, `date_completed`) VALUES
(87, 29, 'a:0:{}', 5, 'Homepage Design', '', '2016-06-08 00:00:00', '2016-06-02 00:00:00', '2016-06-08 11:53:27', 5, '2016-06-10 09:32:48', 5, 1, 2, 29, 98, 5, NULL),
(88, 29, 'a:0:{}', 5, 'Form Layouts', '', '2016-06-16 00:00:00', '2016-06-09 00:00:00', '2016-06-09 13:01:14', 5, '2016-06-10 09:33:30', 5, 3, 4, 29, 98, 5, NULL),
(89, 34, 'a:0:{}', 5, 'task 1', 'v avd', '2016-05-30 00:00:00', '2016-05-30 00:00:00', '2016-06-09 15:31:06', 5, '2016-06-24 11:32:56', 5, 5, 2, 34, 104, 5, NULL),
(90, 34, 'a:0:{}', 5, 'MS3 Region 1', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2016-06-09 18:04:26', 5, '2016-06-10 13:46:44', 5, 0, 1, 34, 103, 5, NULL),
(91, 34, 'a:0:{}', 5, 'test task 2', '', '2016-06-21 00:00:00', '2016-06-14 00:00:00', '2016-06-10 08:39:28', 5, '2016-06-14 15:49:04', 5, 5, 3, 34, 107, 5, NULL),
(92, 34, 'a:0:{}', 5, 'Test 5', 'fvqerfv', '2016-06-13 00:00:00', '2016-06-13 00:00:00', '2016-06-14 09:38:38', 5, '2016-06-27 09:27:21', 5, 4, 1, 34, 106, 5, NULL),
(93, 34, 'a:0:{}', 5, 'Test', '', '2016-06-16 00:00:00', '2016-06-14 00:00:00', '2016-06-14 14:37:03', 5, NULL, NULL, 1, 4, 34, 104, 5, NULL),
(94, 34, 'a:0:{}', 5, 'zvbsgbvsgfb', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2016-06-14 14:37:46', 5, NULL, NULL, 0, 1, 34, 106, 5, NULL),
(95, 34, 'a:1:{i:0;s:1:"5";}', 5, 'test', 'test', '2016-06-14 00:00:00', '2016-06-14 00:00:00', '2016-06-14 14:38:33', 5, NULL, NULL, 0, 1, 34, 107, 5, NULL),
(96, 34, 'a:0:{}', 5, 'test', '', '2016-06-22 00:00:00', '2016-06-21 00:00:00', '2016-06-14 15:49:44', 5, '2016-06-21 22:07:46', 5, 0, 1, 34, 107, 5, NULL),
(97, 34, 'a:1:{i:0;s:1:"5";}', 5, 'MS3 012', '', '2016-06-15 00:00:00', '2016-06-14 00:00:00', '2016-06-14 16:07:19', 5, '2016-06-14 16:07:43', 5, 3, 2, 34, 105, 5, NULL),
(98, 34, 'a:0:{}', 5, 'test 5', '', '2016-06-22 00:00:00', '2016-06-14 00:00:00', '2016-06-14 16:09:00', 5, '2016-06-21 22:10:29', 5, 0, 1, 34, 107, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tasks_progress`
--

CREATE TABLE IF NOT EXISTS `tasks_progress` (
  `task_progress_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `date_post` datetime NOT NULL,
  `organ_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  PRIMARY KEY (`task_progress_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `tasks_progress`
--

INSERT INTO `tasks_progress` (`task_progress_id`, `comment`, `user_id`, `task_id`, `date_post`, `organ_id`, `plan_id`) VALUES
(5, 'Test Comment', 5, 87, '2016-06-08 11:57:33', 29, 29),
(6, 'This is the second comment', 5, 87, '2016-06-08 11:57:45', 29, 29),
(7, 'Test COmment edited', 5, 88, '2016-06-09 13:01:38', 29, 29),
(8, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book', 5, 87, '2016-06-09 13:05:49', 29, 29),
(9, 'test comment for this task', 5, 90, '2016-06-10 09:44:17', 34, 34),
(10, 'wtyhwreyh5e3', 5, 91, '2016-06-13 16:34:07', 34, 34),
(11, 'Test', 5, 89, '2016-06-24 11:34:21', 34, 34);

-- --------------------------------------------------------

--
-- Table structure for table `tasks_subtask`
--

CREATE TABLE IF NOT EXISTS `tasks_subtask` (
  `subtask_id` int(11) NOT NULL AUTO_INCREMENT,
  `subtask` text,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `date_post` datetime NOT NULL,
  `organ_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  PRIMARY KEY (`subtask_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_shared`
--

CREATE TABLE IF NOT EXISTS `task_shared` (
  `task_shared_id` int(11) NOT NULL AUTO_INCREMENT,
  `shared_to` text NOT NULL,
  `shared_from` int(11) NOT NULL,
  `shared_date` datetime NOT NULL,
  PRIMARY KEY (`task_shared_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_users`
--

CREATE TABLE IF NOT EXISTS `task_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `entered_on` datetime DEFAULT NULL,
  `entered_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `task_users`
--

INSERT INTO `task_users` (`id`, `task_id`, `user_id`, `entered_on`, `entered_by`) VALUES
(7, 87, 5, '2016-06-08 11:53:28', 5),
(8, 88, 5, '2016-06-09 13:01:14', 5),
(9, 89, 5, '2016-06-09 15:31:06', 5),
(10, 90, 5, '2016-06-09 18:04:26', 5),
(11, 91, 5, '2016-06-10 08:39:28', 5),
(12, 92, 5, '2016-06-14 09:38:38', 5),
(13, 93, 5, '2016-06-14 14:37:03', 5),
(14, 94, 5, '2016-06-14 14:37:46', 5),
(15, 95, 5, '2016-06-14 14:38:33', 5),
(16, 96, 5, '2016-06-14 15:49:44', 5),
(17, 97, 5, '2016-06-14 16:07:19', 5),
(18, 98, 5, '2016-06-14 16:09:00', 5);

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE IF NOT EXISTS `team` (
  `team_id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` text,
  `organ_id` int(11) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `member_count` int(11) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by_user_id` int(11) DEFAULT NULL,
  `entered_by_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`team_id`, `deleted`, `name`, `description`, `organ_id`, `manager_id`, `member_count`, `entered`, `updated`, `updated_by_user_id`, `entered_by_user_id`) VALUES
(8, 0, 'Management', '', 2, 14, 2, '2016-04-06 21:54:47', '2016-04-07 11:17:48', NULL, 5),
(9, 0, 'Development', '', 2, 5, 1, '2016-04-06 21:55:12', '2016-04-06 21:55:20', NULL, 5),
(10, 0, 'Sales', '', 2, 15, 2, '2016-04-07 11:17:26', '2016-04-07 11:17:34', NULL, 5),
(11, 0, 'd', NULL, 6, NULL, 0, '2016-04-26 10:34:00', '2016-04-26 10:34:00', NULL, 5),
(12, 0, 'managers', '', 22, 14, 1, '2016-05-11 13:43:10', '2016-05-11 13:43:19', NULL, 14);

-- --------------------------------------------------------

--
-- Table structure for table `team_users`
--

CREATE TABLE IF NOT EXISTS `team_users` (
  `team_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_by_user_id` int(11) DEFAULT NULL,
  `entered_by_user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`team_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `team_users`
--

INSERT INTO `team_users` (`team_user_id`, `team_id`, `user_id`, `entered`, `updated`, `updated_by_user_id`, `entered_by_user_id`, `role_id`) VALUES
(43, 8, 5, '2016-04-06 21:54:57', '2016-04-06 21:54:57', NULL, NULL, NULL),
(44, 8, 15, '2016-04-06 21:54:58', '2016-04-06 21:54:58', NULL, NULL, NULL),
(45, 9, 5, '2016-04-06 21:55:18', '2016-04-06 21:55:18', NULL, NULL, NULL),
(46, 10, 14, '2016-04-07 11:17:31', '2016-04-07 11:17:31', NULL, NULL, NULL),
(47, 10, 5, '2016-04-07 11:17:32', '2016-04-07 11:17:32', NULL, NULL, NULL),
(49, 12, 14, '2016-05-11 13:43:13', '2016-05-11 13:43:13', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `master_account_id` int(11) DEFAULT NULL,
  `user_type` varchar(15) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `is_confirmed` tinyint(1) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `username` varchar(60) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `hash` text,
  `ip_address` varchar(16) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `tel_number` varchar(45) DEFAULT NULL,
  `utc_timezoneoffset` varchar(10) DEFAULT NULL,
  `entered` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `last_logged_in` datetime DEFAULT NULL,
  `about_me` text,
  `profile_pic` text,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=57 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `master_account_id`, `user_type`, `is_active`, `deleted`, `is_confirmed`, `first_name`, `last_name`, `username`, `email`, `company`, `hash`, `ip_address`, `job_title`, `tel_number`, `utc_timezoneoffset`, `entered`, `updated`, `last_logged_in`, `about_me`, `profile_pic`) VALUES
(5, 2, 'admin', 0, 0, 1, 'Tim', 'Pointon', 'timmyp666', 'tim.pointon@crunchersaccountants.co.uk', 'Crunchers', 'PBqu3qBjKZLvOpjIw90ukgiUcrsaTt+7TOeCNyzpNsB6sAhsgJ2mAmoaQEOH/n61VV9AvDQK5+KyUj+b9PvclA==', '141.163.217.173', 'Technical Director', '07721314666 ', 'UTC', '2015-07-10 10:43:41', '2016-06-27 09:25:52', '2016-06-27 09:25:52', '', 'ef5f27ca156f993d5a20ec841abd7333.png'),
(14, 5, 'member', 0, 1, 1, 'Barry', 'Slaughter', 'barry0594', 'barry.slaughter@crunchersaccountants.co.uk', 'Crunchers', 'wWp2WgFndwIfL6AKEslBOmBca5EKFLCuchiK4BHJmMmlyFab5jj6s9toP/MIHXyAFBDuncRipj3RhkIj1IMWIw==', NULL, 'Implementations', '0333 320 4550', NULL, '2016-04-06 21:49:15', '2016-05-19 12:42:26', '2016-05-19 12:42:26', NULL, NULL),
(15, 2, 'member', 0, 0, 1, 'Bob', 'Harper', 'bob0186', 'bob.harper@crunchersaccountants.co.uk', 'Crunchers', '7YiymaH3PyWoDwxss0YlR4oshpYMeNgvDyDLODTEWrfeDsdMbev1H0nLVK12rtDAecqyU02O9/aRwUgH/qXFkA==', NULL, 'Sales & Marketing Director', '0333 320 4550', NULL, '2016-04-06 21:52:50', '2016-06-01 13:47:45', '2016-06-01 13:47:45', NULL, NULL),
(16, 5, 'member', 0, 1, 0, 'Malcolm', 'Sackman', 'malcolm5298', 'malcolm@sackmans.co.uk', 'Crunchers', 'TKKvwIFUgmDJQc1Zp+JyxLsn3RZuiSpsHhf2O9YArO/LAYASzqUb0CdyZs5J0kfFNVmhHYBz4nduDFCOlBFRlA==', NULL, '', '0800 088 7367', NULL, '2016-04-07 11:16:36', '2016-04-07 11:17:00', NULL, NULL, NULL),
(17, 4, 'admin', 0, 0, 1, 'John', 'Sandig', 'john_sandig', 'johndavidelijahsandig@gmail.com', 'JD', 'Ls/nBTCIB3QcKUnKcuwSA4pMtsGUDGwYUNTy7t9Da42aRKWX46haR7jsi++oiN7ykVaFaIP2PDdypgVquSbYIQ==', '180.191.119.176', 'Developers', '0812312', 'UTC', '2016-04-21 12:24:15', '2016-06-24 14:35:14', '2016-06-24 14:35:14', NULL, NULL),
(26, 5, 'member', 0, 1, 1, 'tim', 'pointonn', 'timpointon9130', 'tim@crunchersaccountants.co.uk', '', 'UFbpW2SvFZz4ULXnXT+jwdhH2715mPsNAOKRGE3dFVeewloCmM+Gr8bQHzMZHJPlVW1AICJqy5It/12s1mjwhQ==', '', '', '', 'UTC', '2016-04-26 09:14:42', '2016-05-20 09:48:06', '2016-05-20 09:48:06', '', ''),
(29, 5, 'member', 0, 1, 1, 'wsss', 'david', 'wsssdavid1458', 'johndesandig.wspi@gmail.com', '', 'khcmiCq1vvAb3eHwXR2x7PERDlyXlYTjovubbP5X/G4IvYQvJKsQ3jW52AanIGvIxcB/8rA7hJ4/jb78v/kX+w==', '', '', '', 'UTC', '2016-04-26 14:41:57', '2016-04-26 14:41:57', '2016-04-26 14:41:57', '', ''),
(30, 5, 'member', 0, 0, 1, 't', 'hotmail', 'thotmail3142', 'timpointon@hotmaill.com', '', 'oKrR+tCNPgRnSfkJOOlFv3P8JeR9Vi5W4MeWDqEm6AnpTbLMrg25yP/94KSm+SfKJT0UYd7yn1VTVoK2/ufr3A==', '', '', '', 'UTC', '2016-05-06 13:26:12', '2016-05-06 13:26:12', '2016-05-06 13:26:12', '', ''),
(31, 5, 'admin', 0, 0, 1, 'James', 'Erie', 'jameserie', 'jameserie@yahoo.com', 'Cruncher', 'BRDGugMmoF0xJLWIK2uD30+dOdpOLnHpxMQNFr1jBa+YI8DUXlCpupn5Q/xAy6hJBaacVZ1z9YVTh7Dvcj8/bg==', '112.198.102.56', 'Web Developer', '+639192857748', 'UP8', '2016-05-09 13:04:04', '2016-05-12 23:05:38', '2016-05-12 23:05:38', NULL, NULL),
(32, 6, 'admin', 0, 0, 1, 'Tim', 'Pointon', 'timmyp66666', 'timmypointonnn@gmail.com', 'More Numbers Limited', 'VkRAWrJkbClYfRMRccbKgTZjt7isnGSaiFypVPSQGl/Ne93xRC6aLXiG91LygRlgHYFlLAo+1UuHW+9kHak7dw==', '141.163.217.173', 'Director', 'na', 'UTC', '2016-05-11 13:31:00', '2016-05-11 13:31:00', NULL, NULL, NULL),
(33, 7, 'admin', 0, 0, 1, 'tgqtg', 'tgtrg', 'tgbtr', 'tim@maenporthelectcrical.co.uk', 'Maenporth Electrical', '2NKNpeVosOFCSypOrpjqhxCzOd/JZ4Deu5Pbi6nXEpdjHX5d8PduJYwHCo7HpqY05JAPC/j/851ePFGKD7Z21g==', '141.163.217.173', 'trtg', '+441326250297', 'UTC', '2016-05-11 13:35:51', '2016-05-11 13:35:51', NULL, NULL, NULL),
(34, 8, 'admin', 0, 0, 1, 'James', 'Erie', 'eiresemaj', 'jameserie81188@gmail.com', 'Cruncher', '1/lPzP4FNBgCL8dX8sNX1EJMqCapDwPv2D7mDG9TeG/+ZYHUF0G1bG6UXMA+pUunSak3p2aXtCq47gmOZwJabA==', '112.198.98.222', 'Web Developer', '+639192857748', 'UTC', '2016-05-11 23:42:03', '2016-05-11 23:42:17', '2016-05-11 23:42:17', NULL, NULL),
(35, 9, 'admin', 0, 0, 1, 'Russell', 'Erie', 'russelljoy', 'russelljoy72291@yahoo.com', 'James Erie Studio', 'b/xOjIGVxosa3t8hvMv7XwIn6e1jlXwbJiIuIvKh9kdzTnnGHEMG6HqaOD/x42fQFkkTPGthcQN0drop7gN3TQ==', '112.198.102.29', 'Cashier', '+639192857748', 'UTC', '2016-05-12 23:44:14', '2016-05-12 23:44:35', '2016-05-12 23:44:35', NULL, NULL),
(36, 5, 'member', 0, 1, 1, 'Joe', 'Bloggs', 'joebloggs8791', 'timpointon@hotmail.comm', '', '1LgyGZJ95I87sQpNmaadVK5cpDK88q4n0oYB4zkccx3T4c1eemm4qv2t69Ru2ncXzmnH/PwwbZrK/qM+VbfCjQ==', '', 'IT Director', '0800 9154225', 'UTC', '2016-05-25 12:09:54', '2016-06-03 09:53:42', '2016-06-03 09:53:42', '', '1714ba9c015d55138b12597e1c8bdc57.png'),
(38, 0, 'superadmin', 1, 0, 1, 'Tim', 'Pointon', 'tim', 'tim@moresoftware.biz', 'Cruncher', 'nfr0el19IZkZFlEGc1IrHykfL4damHR3mvAzirYZRIef6eevMbrwO16zB7SKN9q55/Ax/eYCSRZ4uKI5tZso/Q==', NULL, NULL, NULL, NULL, NULL, '2016-06-22 11:45:13', '2016-06-22 11:45:13', NULL, NULL),
(40, 2, 'member', 0, 1, 1, 'James', 'Bond', 'jamesbond0391', 'jameserie89@yahoo.com', '', 'CmaAmIxZevblrZEi+atLFO60W1mAheT9Jg5fW5860dUjEMxjJjxDwuoWtsp839GZOWDKd4z5XtaraQ/e+Ohwpw==', '', '', '', 'UTC', '2016-06-20 10:50:30', '2016-06-20 10:50:30', '2016-06-20 10:50:30', '', ''),
(43, 2, 'member', 0, 1, 1, 'billy', 'he fish', 'billyhe fish2570', 'billythefish@crunchersaccountants.co.uk', '', 'AT5ujMaYZiunwgHIDjwRCGr1GOwI1gSrXJGnQJgeUtjuKEo3PQfVoH2gUNmZ0LPS2m4BGYGmLZT7J3XyryJGfg==', '', '', '', 'UTC', '2016-06-21 11:46:04', '2016-06-21 11:46:04', '2016-06-21 11:46:04', '', ''),
(44, 2, 'member', 0, 0, 1, 'Jose', 'Rizal', 'joserizal2465', 'joserizal@yahoo.com', '', 'd6ZYNCsZFbf4V933l6udMnyy/XvOMsjUh9nfUko146XFGMPvDRtbTeypx7K2X6NEHLl6kj3U7mcF8HMtqYbzug==', '', '', '', 'UTC', '2016-06-22 09:58:18', '2016-06-22 09:58:18', '2016-06-22 09:58:18', '', ''),
(45, 2, 'member', 0, 1, 1, 'rtgrg', 'tgtrg', 'rtgrgtgtrg1394', 'timmyp@crunchersaccountants.co', '', 'dfZFjgomttqAVvr9RtgNBe3Iu0Fjtmx42yKL3XtbvUsGaPN4QrQ/EyEoCqGFb0/L1dEGBdtu5TMPK+CtP+p+Bg==', '', '', '', 'UTC', '2016-06-22 09:59:42', '2016-06-22 09:59:42', '2016-06-22 09:59:42', '', ''),
(47, 2, 'member', 0, 0, 1, 'Michael', 'Sean', 'michaelsean9407', 'michael.sean@gmail.com', '', 'PvKB8Ctpr7jp0TfrJf4h+QQCzGoF5kmosTqyLRGyFrF/Zw/UwmyCqQAbvpnoeZhVjbhjfwHJ5HBeAAIopv/Q5Q==', '', '', '', 'UTC', '2016-06-22 11:12:02', '2016-06-22 11:12:02', '2016-06-22 11:12:02', '', ''),
(48, 2, 'member', 0, 0, 1, 'Jose', 'Rizal', 'joserizal9365', 'joserizal@yahoo.com', '', 'VLb7J33NHDJvdFl2l7ttEP35CEGUHWbUXyZ3Qz63spAadi3r1oVLkD9uDZAZok89kUt46edVUx2UX/JfxKmg4Q==', '', '', '', 'UTC', '2016-06-22 11:31:44', '2016-06-22 11:31:44', '2016-06-22 11:31:44', '', ''),
(50, 2, 'member', 0, 0, 1, 'tim', 'test', 'timtest0674', 'tim@crunchers.co', '', 'M/rAWwaFiQnAxE7534PwLfMLQLRdmuHC8BMpRcO6aTsDsUETGhryVglWw3utPCgWbGxYTpQ+89jvWuS033lt/g==', '', '', '', 'UTC', '2016-06-22 11:54:47', '2016-06-22 11:54:47', '2016-06-22 11:54:47', '', ''),
(51, 10, 'admin', 0, 0, 1, 'Toby', 'Pointon', 'tobyp', 'timpointon@hotmail.com', 'goal drivers', 'oCUKZlvXXZomKZdwDPVcv9uDsoLvrEFa9xyN4H63kZ46CAy2oMkRye4sXO7AgGYSQowvuA+Fa84pm6m8jwYuRw==', '86.174.192.43', 'Boss', '0800999999', 'UTC', '2016-06-22 12:02:36', '2016-06-22 21:24:23', '2016-06-22 21:24:23', NULL, NULL),
(53, 2, 'member', 0, 0, 1, 'Jose', 'Rizal', 'joserizal9463', 'joserizal@yahoo.com', '', 'Hir8EU96Ll07xvGusHxRPaAf1aK0sVMxi5qvhh1ol3DrKU4axN3q4NUC9qqn/bTjmO8AVG9qcK6/FmLtEdSfow==', '', '', '', 'UTC', '2016-06-22 13:00:47', '2016-06-22 13:00:47', '2016-06-22 13:00:47', '', ''),
(54, 2, 'member', 0, 0, 1, 'Ted', 'Saavedra', 'tedsaavedra6517', 'saavedra.ted@gmail.com', '', 'tSKvvsE/E952ruRrQUguQE2tFuS2SLhf2kHVctgBFFtP+wxoqL/4SDnDVMdvBcd+fLAK4EESMECy6hooVSpOng==', '', '', '', 'UTC', '2016-06-24 14:24:23', '2016-06-24 14:24:23', '2016-06-24 14:24:23', '', ''),
(55, 12, 'admin', 0, 0, 1, 'Teddy', 'Saavedra', 'NA', 'ted@smartstart.us', 'Ted', 'gIVdNZBsw9IiJlCyRoTxraeUbfSsjkNmwpCX0zxBka4cBQNinDCO0cD5cgGFvhoRI2xUrYB2wPbd9gY+OsLSJA==', '112.198.72.220', 'Ted', '1234', 'UTC', '2016-06-24 14:57:44', '2016-06-24 14:58:00', '2016-06-24 14:58:00', NULL, NULL),
(56, 12, 'member', 0, 0, 1, 'Dets', 'Saavedra', 'detssaavedras3784', 'lychael1@gmail.com', '', 'Roaltv2+Fr6L19jz1nIn8WWyepaFgH9afcgCQ3gdm/JFnTrBJTskqCS5JbpE4QCiPBbfdX3b3TE36SMT7MtvNw==', '', '', '', 'UTC', '2016-06-24 14:59:37', '2016-06-24 15:01:18', '2016-06-24 14:59:37', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
