DELIMITER //

-- Add resource permissions
DROP PROCEDURE IF EXISTS Perm_AddNew//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Perm_AddNew(IN `resource` VARCHAR(255), IN `common_name` VARCHAR(128), IN `owner` VARCHAR(128), IN `group` VARCHAR(128), IN `gread` INT(1), IN `gwrite` INT(1), IN `user` VARCHAR(128), IN `uread` INT(1), IN `uwrite` INT(1), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add resource permissions'
BEGIN
 INSERT INTO `resources` (`resource`, `common_name`, `owner`) VALUES (@resource, @common_name, @owner) ON DUPLICATE KEY UPDATE `owner`=@owner;
 INSERT INTO `resources_groups` (`resource`, `group`, `read`, `write`, `owner`) VALUES (@resource, @group, @gread, @gwrite, @owner) ON DUPLICATE KEY UPDATE `group`=@group, `read`=@gread, `write`=@write, `owner`=@owner;
 INSERT INTO `resources_users` (`resource`, `user`, `read`, `write`, `owner`) VALUES (@resource, @user, @uread, @uwrite, @owner) ON DUPLICATE KEY UPDATE `user`=@user, `read`=@uread, `write`=@write, `owner`=@owner;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add group to resource permissions
DROP PROCEDURE IF EXISTS Perm_AddGroup//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Perm_AddGroup(IN `resource` VARCHAR(255), IN `group` VARCHAR(128), IN `gread` INT(1), IN `gwrite` INT(1), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add group to resource permissions'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `resources_groups` WHERE `resource`=@resource AND `group`=@group;
 IF @x > 0 THEN
  UPDATE `resources_groups` SET `read`=@gread, `write`=@gwrite WHERE `resource`=@resource AND `group`=@group;
 ELSE
  INSERT INTO `resources_groups` (`resource`, `group`, `read`, `write`) VALUES (@resource, @group, @gread, @gwrite);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add user to resource permissions
DROP PROCEDURE IF EXISTS Perm_AddUser//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Perm_AddUser(IN `resource` VARCHAR(255), IN `user` VARCHAR(128), IN `uread` INT(1), IN `uwrite` INT(1), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add user to resource permissions'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `resources_users` WHERE `resource`=@resource AND `user`=@user;
 IF @x > 0 THEN
  UPDATE `resources_users` SET `read`=@uread, `write`=@uwrite WHERE `resource`=@resource AND `user`=@user;
 ELSE
  INSERT INTO `resources_users` (`resource`, `user`, `read`, `write`) VALUES (@resource, @user, @uread, @uwrite);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Edit resource permissions
DROP PROCEDURE IF EXISTS Perm_EditAll//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Perm_EditAll(IN `resource` VARCHAR(255), IN `common_name` VARCHAR(128), IN `owner` VARCHAR(128), IN `group` VARCHAR(128), IN `gread` INT(1), IN `gwrite` INT(1), IN `user` VARCHAR(128), IN `uread` INT(1), IN `uwrite` INT(1), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Edit resource permissions'
BEGIN
 UPDATE `resources` SET `resource`=@resource, `common_name`=@common_name, `owner`=@owner WHERE `resource`=@resource AND `owner`=@owner;
 UPDATE `resources_groups` SET `group`=@group, `read`=@gread, `write`=@gwrite, `owner`=@owner WHERE `resource`=@resource AND `owner`=@owner;
 UPDATE `resources_users` SET `user`=@user, `read`=@uread, `write`=@uwrite, `owner`=@owner WHERE `resource`=@resource AND `owner`=@owner;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Edit group resource permissions
DROP PROCEDURE IF EXISTS Perm_EditGroup//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Perm_EditGroup(IN `resource` VARCHAR(255), IN `group` VARCHAR(128), IN `gread` INT(1), IN `gwrite` INT(1), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Edit group resource permissions'
BEGIN
 UPDATE `resources_groups` SET `read`=@gread, `write`=@gwrite WHERE `group`=@group AND `resource`=@resource LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Edit user resource permissions
DROP PROCEDURE IF EXISTS Perm_EditUser//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Perm_EditUser(IN `resource` VARCHAR(255), IN `user` VARCHAR(128), IN `uread` INT(1), IN `uwrite` INT(1), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Edit user resource permissions'
BEGIN
 UPDATE `resources_users` SET `read`=@uread, `write`=@uwrite WHERE `user`=@user AND `resource`=@resource LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete resource permissions
DROP PROCEDURE IF EXISTS Perm_DeleteAll//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Perm_DeleteAll(IN `resource` VARCHAR(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete all resource permissions'
BEGIN
 DELETE FROM `resources` WHERE `resource`=@resource;
 DELETE FROM `resources_groups` WHERE `resource`=@resource;
 DELETE FROM `resources_users` WHERE `resource`=@resource;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete group resource permissions
DROP PROCEDURE IF EXISTS Perm_DeleteGroup//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Perm_DeleteGroup(IN `resource` VARCHAR(255), `group` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete group from group resource permissions'
BEGIN
 DELETE FROM `resources_groups` WHERE `resource`=@resource AND `group`=@group;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete user resource permissions
DROP PROCEDURE IF EXISTS Perm_DeleteUser//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Perm_DeleteUser(IN `resource` VARCHAR(255), `user` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete user from user resource permissions'
BEGIN
 DELETE FROM `resources_users` WHERE `resource`=@resource AND `user`=@user;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

DELIMITER ;
