DELIMITER //

-- Add/Update user keys
DROP PROCEDURE IF EXISTS Keys_KeysAddUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Keys_KeysAddUpdate(IN `resource` VARCHAR(255), IN `countryName` VARCHAR(64), IN `stateOrProvinceName` VARCHAR(64), IN `localityName` VARCHAR(64), IN `organizationName` VARCHAR(64), IN `commonName` VARCHAR(64), IN `emailAddress` VARCHAR(64), IN `pass` LONGTEXT, IN `pub` LONGTEXT, IN `private` LONGTEXT, IN `certificate` LONGTEXT, IN `latitude` VARCHAR(64), IN `longitude` VARCHAR(64))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update user keys'
BEGIN
 INSERT INTO `keys` (`resource`, `countryName`, `stateOrProvinceName`, `localityName`, `organizationName`, `organizationalUnitName`, `commonName`, `emailAddress`, `pass`, `pub`, `private`, `latitude`, `longitude`) VALUES (resource, countryName, stateOrProvinceName, localityName, organizationalName, organizationalUnitName, commonName, emailAddress, pass, pub, private, certificate, latitude, longitude) ON DUPLICATE KEY UPDATE `resource`=resource, `countryName`=countryName, `stateOrProvinceName`=stateOrProvinceName, `localityName`=localityName, `organizationName`=organizationName, `organizationalUnitName`=organizationalUnitName, `commonName`=commonName, `emailAddress`=emailAddress, `pass`=pass, `pub`=pub, `private`=private, `certificate`=certificate, `latitude`=latitude, `longitude`=longitude;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete user keys
DROP PROCEDURE IF EXISTS Keys_KeysDelete//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Keys_KeysDelete(IN `email` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete user keys'
BEGIN
 DELETE FROM `keys` WHERE `emailAddress`=@email LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Get public key for authenticated user
DROP PROCEDURE IF EXISTS Keys_KeysGet//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Keys_KeysGet(IN `email` VARCHAR(128), IN `ipv4` VARCHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Get public key for client by email, ip or use configured applications'
BEGIN
 SET @x = 0;
 SET @y = 0;
 SELECT COUNT(*) INTO @x FROM `keys` WHERE `email`=@email;
 SELECT COUNT(*) INTO @y FROM `keys` WHERE `ip`=@ipv4;
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

DELIMITER ;
