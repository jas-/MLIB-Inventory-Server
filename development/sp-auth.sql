DELIMITER //

-- Test authentication
DROP PROCEDURE IF EXISTS Auth_GetPassword//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Auth_GetPassword(IN `email` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Auth_ check'
BEGIN
 SELECT `password` FROM `authentication` WHERE `email`=email AND `password`=password;
END//

-- Add/Update user
DROP PROCEDURE IF EXISTS Auth_UserAddUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Auth_UserAddUpdate(IN `resource` VARCHAR(255), IN `email` VARCHAR(128), IN `password` VARCHAR(255), IN `level` VARCHAR(40), IN `ggroup` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update user authentication'
BEGIN
 INSERT INTO `authentication` (`resource`, `email`, `password`, `level`, `group`) VALUES (resource, email, password, level, ggroup) ON DUPLICATE KEY UPDATE `resource`=resource, `email`=email, `password`=password, `level`=level, `group`=ggroup;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete user
DROP PROCEDURE IF EXISTS Auth_UserDelete//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Auth_UserDelete(IN `email` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete user authentication'
BEGIN
 DELETE FROM `authentication` WHERE `email`=@email LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add/Update user keys
DROP PROCEDURE IF EXISTS Auth_KeysAddUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Auth_KeysAddUpdate(IN `resource` VARCHAR(255), IN `countryName` VARCHAR(64), IN `stateOrProvinceName` VARCHAR(64), IN `localityName` VARCHAR(64), IN `organizationName` VARCHAR(64), IN `commonName` VARCHAR(64), IN `emailAddress` VARCHAR(64), IN `pass` LONGTEXT, IN `pub` LONGTEXT, IN `private` LONGTEXT, IN `certificate` LONGTEXT, IN `latitude` VARCHAR(64), IN `longitude` VARCHAR(64))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update user keys'
BEGIN
 INSERT INTO `authentication_keys` (`resource`, `countryName`, `stateOrProvinceName`, `localityName`, `organizationName`, `organizationalUnitName`, `commonName`, `emailAddress`, `pass`, `pub`, `private`, `latitude`, `longitude`) VALUES (resource, countryName, stateOrProvinceName, localityName, organizationalName, organizationalUnitName, commonName, emailAddress, pass, pub, private, certificate, latitude, longitude) ON DUPLICATE KEY UPDATE `resource`=resource, `countryName`=countryName, `stateOrProvinceName`=stateOrProvinceName, `localityName`=localityName, `organizationName`=organizationName, `organizationalUnitName`=organizationalUnitName, `commonName`=commonName, `emailAddress`=emailAddress, `pass`=pass, `pub`=pub, `private`=private, `certificate`=certificate, `latitude`=latitude, `longitude`=longitude;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete user keys
DROP PROCEDURE IF EXISTS Auth_KeysDelete//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Auth_KeysDelete(IN `email` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete user keys'
BEGIN
 DELETE FROM `authentication_keys` WHERE `emailAddress`=@email LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Get public key for authenticated user
DROP PROCEDURE IF EXISTS Auth_KeysGet//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Auth_KeysGet(IN `email` VARCHAR(128), IN `ipv4` VARCHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Get public key for client by email, ip or use configured applications'
BEGIN
 SET @x = 0;
 SET @y = 0;
 SELECT COUNT(*) INTO @x FROM `authentication_keys` WHERE `email`=@email;
 SELECT COUNT(*) INTO @y FROM `authentication_keys` WHERE `ip`=@ipv4;
 IF @x > 0 THEN
  SELECT * FROM `authentication_keys` WHERE `email`=@email LIMIT 1;
 ELSE
  IF @y > 0 THEN
   SELECT * FROM `authentication_keys` WHERE `ip`=@ipv4 LIMIT 1;
  ELSE
   SELECT * FROM `configuration`;
  END IF;
 END IF;
END//

-- Add/Update group
DROP PROCEDURE IF EXISTS Auth_GroupAddUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Auth_GroupAddUpdate(IN `resource` VARCHAR(255), IN `group` VARCHAR(128), IN `manager` VARCHAR(128), IN `description` VARCHAR(255), IN `owner` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update group'
BEGIN
 INSERT INTO `authentication_groups` (`resource`, `group`, `manager`, `description`, `owner`) VALUES (@resource, @group, @manager, @description, @owner) ON DUPLICATE KEY UPDATE `resource`=@resource, `group`=@group, `manager`=@manager, `description`=@description, `owner`=@owner;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete group
DROP PROCEDURE IF EXISTS Auth_GroupDelete//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Auth_GroupDelete(IN `group` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete group'
BEGIN
 DELETE FROM `authentication_groups` WHERE `group`=@group LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

DELIMITER ;
