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

-- Config_ exists?
DROP PROCEDURE IF EXISTS Config_Check//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_Check(OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Config_ settings exist?'
BEGIN
 SELECT COUNT(`id`) INTO @x FROM `configuration`;
 SELECT @x;
END//

-- Use compression?
DROP PROCEDURE IF EXISTS USECompression//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE USECompression(OUT n INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Is compress enabled?'
BEGIN
 SELECT `compression` FROM `configuration`;
END//

-- Use encryption?
DROP PROCEDURE IF EXISTS USEEncryption//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE USEEncryption(OUT n INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Is encryption enabled?'
BEGIN
 SELECT `encryption` FROM `configuration`;
END//

-- Update configuration
DROP PROCEDURE IF EXISTS Config_Update//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_Update(IN `title` VARCHAR(128), IN `templates` VARCHAR(255), IN `cache` VARCHAR(255), IN `private` BOOLEAN, IN `email` VARCHAR(45), IN `timeout` INT(10), IN `pkey` LONGTEXT, IN `pvkey` LONGTEXT, IN `pass` VARCHAR(64))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update configuration'
BEGIN
 DECLARE x INT;
 SET x = 0;
 SELECT COUNT(*) INTO x FROM `configuration`;
 IF x > 0 THEN
  UPDATE `configuration` SET `title`=title, `templates`=templates, `cache`=cache, `private`=private, `email`=email, `timeout`=timeout, `pkey`=pkey, `pvkey`=pvkey, `pass`=pass WHERE `id`=1;
 ELSE
  INSERT INTO `configuration` (`title`, `templates`, `cache`, `private`, `email`, `timeout`, `pkey`, `pvkey`, `pass`) VALUES (title, templates, cache, private, email, timeout, pkey, pvkey, pass);
 END IF;
 SET x = ROW_COUNT();
 SELECT x;
END//

-- Get configuration options
DROP PROCEDURE IF EXISTS Config_Select//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_Select()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Select configuration data'
BEGIN
 SELECT * FROM `configuration`;
END//

-- Add/Update OpenSSL CNF options
DROP PROCEDURE IF EXISTS Config_OpenSSLCNFUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_OpenSSLCNFUpdate(IN `config` VARCHAR(64), IN `encrypt_key` VARCHAR(64), IN `private_key_type` VARCHAR(64), IN `digest_algorithm` VARCHAR(64), IN `private_key_bits` INT(4), IN `x509_extensions` VARCHAR(32), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update OpenSSL CNF options'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `configuration_openssl_cnf`;
 IF @x > 0 THEN
  UPDATE `configuration_openssl_cnf` SET `config`=config, `encrypt_key`=encrypt_key, `private_key_type`=private_key_type, `digest_algorithm`=digest_algorithm, `private_key_bits`=private_key_bits, `x509_extensions`=x509_extensions WHERE `id`=1;
 ELSE
  INSERT INTO `configuration_openssl_cnf` (`config`, `encrypt_key`, `private_key_type`, `digest_algorithm`, `private_key_bits`, `x509_extensions`) VALUES (config, encrypt_key, private_key_type, digest_algorithm, private_key_bits, x509_extensions);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Get OpenSSL CNF configuration options
DROP PROCEDURE IF EXISTS Config_OpenSSLCNFSelect//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_OpenSSLCNFSelect()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Select OpenSSL CNF configuration data'
BEGIN
 SELECT `config`, `encrypt_key`, `private_key_type`, `digest_algorithm`, `private_key_bits`, `x509_extensions` FROM `configuration_openssl_cnf`;
END//

-- Add/Update OpenSSL DN options
DROP PROCEDURE IF EXISTS Config_OpenSSLDNUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_OpenSSLDNUpdate(IN `countryName` VARCHAR(64), IN `stateOrProvinceName` VARCHAR(64), IN `localityName` VARCHAR(64), IN `organizationName` VARCHAR(64), IN `organizationalUnitName` VARCHAR(64), IN `commonName` VARCHAR(64), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update OpenSSL DN options'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `configuration_openssl_dn`;
 IF @x > 0 THEN
  UPDATE `configuration_openssl_dn` SET `countryName`=countryName, `stateOrProvinceName`=stateOrProvinceName, `localityName`=localityName, `organizationName`=organizationName, `organizationalUnitName`=organizationalUnitName, `commonName`=commonName WHERE `id`=1;
 ELSE
  INSERT INTO `configuration_openssl_dn` (`countryName`, `stateOrProvinceName`, `localityName`, `organizationName`, `organizationalUnitName`, `commonName`) VALUES (countryName, stateOrProvinceName, localityName, organizationName, organizationalUnitName, commonName);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Get OpenSSL DN configuration options
DROP PROCEDURE IF EXISTS Config_OpenSSLDNSelect//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_OpenSSLDNSelect()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Select OpenSSL DN configuration data'
BEGIN
 SELECT `countryName`, `stateOrProvinceName`, `localityName`, `organizationName`, `organizationalUnitName`, `commonName` FROM `configuration_openssl_dn`;
END//

-- Update LDAP configuration
DROP PROCEDURE IF EXISTS Config_LDAPUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_LDAPUpdate(IN `key` VARCHAR(255), IN `domain` VARCHAR(255), IN `servers` VARCHAR(255), IN `port` INT(5), IN `username` VARCHAR(20), IN `password` VARCHAR(255), IN `bind_dn` VARCHAR(255), IN `base_dn` VARCHAR(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update LDAP configuration'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `configuration_ldap`;
 IF @x > 0 THEN
  UPDATE `configuration_ldap` SET `key`=@key, `domain`=@domain, `servers`=@servers, `port`=@port, `username`=@username, `password`=@password, `bind_dn`=@bind_dn, `base_dn`=@base_dn WHERE `id`=1;
 ELSE
  INSERT INTO `configuration_ldap` (`key`, `domain`, `servers`, `port`, `username`, `password`, `bind_dn`, `base_dn`) VALUES (@key, @salt, @domain, @servers, @port, @username, @password, @bind_dn, @base_dn);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Update Memcache configuration
DROP PROCEDURE IF EXISTS Config_MemcacheUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_MemcacheUpdate(IN `server` VARCHAR(128), IN `port` INT(8), IN `timeout` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update Memcache configuration'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `configuration_memcache`;
 IF @x > 0 THEN
  UPDATE `configuration_memcache` SET `server`=@server, `port`=@port, `timeout`=@timeout WHERE `id`=1;
 ELSE
  INSERT INTO `configuration_memcache` (`server`, `port`, `timeout`) VALUES (@server, @port, @timeout);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add/Update computer
DROP PROCEDURE IF EXISTS Inv_ComputerAddUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Inv_ComputerAddUpdate(IN `resource` VARCHAR(255), IN `model` VARCHAR(128), IN `sku` VARCHAR(128), IN `uuic` VARCHAR(128), IN `serial` VARCHAR(128), IN `hostname` VARCHAR(128), IN `location` VARCHAR(128), IN `eowd` VARCHAR(32), IN `opd` VARCHAR(32), IN `notes` LONGTEXT, OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update computer'
BEGIN
 INSERT INTO `inventory_computer` (`resource`, `model`, `sku`, `uuic`, `serial`, `hostname`, `location`, `eowd`, `opd`, `notes`) VALUES (@resource, @model, @sku, @uuic, @serial, @hostname, @location, @eowd, @opd, @notes) ON DUPLICATE KEY UPDATE `resource`=@resource, `model`=@model, `sku`=@sku, `uuic`=@uuic, `serial`=@serial, `hostname`=@hostname, `location`=@location, `eowd`=@eowd, `opd`=@opd, `notes`=@notes;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete computer
DROP PROCEDURE IF EXISTS Inv_ComputerDelete//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Inv_ComputerDelete(IN `id` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete computer'
BEGIN
 DELETE FROM `inventory_computer` WHERE `id`=@id;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add/Update model
DROP PROCEDURE IF EXISTS Inv_ModelAddUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Inv_ModelAddUpdate(IN `resource` VARCHAR(255), IN `model` VARCHAR(128), IN `type` VARCHAR(128), IN `description` VARCHAR(128), IN `notes` LONGTEXT, OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update model'
BEGIN
 INSERT INTO `inventory_model` (`resource`, `model`, `type`, `description`, `notes`) VALUES (@resource, @model, @type, @description, @notes) ON DUPLICATE KEY UPDATE `resource`=@resource, `model`=@model, `type`=@type, `description`=@description, `notes`=@notes;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete model
DROP PROCEDURE IF EXISTS Inv_ModelDelete//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Inv_ModelDelete(IN `id` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete model'
BEGIN
 DELETE FROM `inventory_model` WHERE `id`=@id;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add/Update monitors
DROP PROCEDURE IF EXISTS Inv_MonitorAddUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Inv_MonitorAddUpdate(IN `resource` VARCHAR(255), IN `sku` VARCHAR(128), IN `serial` VARCHAR(128), IN `hostname` VARCHAR(128), IN `model` VARCHAR(128), IN `mmodel` VARCHAR(128), IN `location` VARCHAR(128), IN `eowd` VARCHAR(128), IN `order` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update monitor'
BEGIN
 INSERT INTO `inventory_monitors` (`resource`, `sku`, `serial`, `hostname`, `model`, `mmodel`, `location`, `eowd`, `order`) VALUES (@resource, @sku, @serial, @hostname, @model, @mmodel, @location, @eowd, @order) ON DUPLICATE KEY UPDATE `resource`=@resource, `sku`=@sku, `serial`=@serial, `hostname`=@hostname, `model`=@model, `mmodel`=@mmodel, `location`=@location, `eowd`=@eowd, `order`=@order;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete monitor
DROP PROCEDURE IF EXISTS Inv_MonitorDelete//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Inv_MonitorDelete(IN `id` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete monitor'
BEGIN
 DELETE FROM `inventory_monitors` WHERE `id`=@id;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Validate record
DROP PROCEDURE IF EXISTS ValidateRecord//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE ValidateRecord(IN `hostname` VARCHAR(128), IN `sku` VARCHAR(128), IN `uuic` VARCHAR(128), IN `serial` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Validate computer/monitor record'
BEGIN
 SELECT COUNT(*) INTO @x FROM `inventory_computer` WHERE `sku`=@sku XOR `uuic`=@uuic XOR `serial`=@serial AND `hostname`=@hostname;
 SELECT @x;
END//

-- Add/Update rma
DROP PROCEDURE IF EXISTS Inv_RMAAddUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Inv_RMAAddUpdate(IN `resource` VARCHAR(255), IN `date` VARCHAR(12), IN `hostname` VARCHAR(128), IN `sku` VARCHAR(128), IN `uuic` VARCHAR(128), IN `serial` VARCHAR(128), IN `model` VARCHAR(128), IN `incorrect` INT(1), IN `part` VARCHAR(128), IN `notes` LONGTEXT, OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update rma'
BEGIN
 INSERT INTO `inventory_rma` (`resource`, `date`, `hostname`, `sku`, `uuic`, `serial`, `model`, `incorrect`, `part`, `notes`) VALUES (@resource, @sku, @serial, @hostname, @model, @mmodel, @location, @eowd, @order) ON DUPLICATE KEY UPDATE `resource`=@resource, `sku`=@sku, `serial`=@serial, `hostname`=@hostname, `model`=@model, `mmodel`=@mmodel, `location`=@location, `eowd`=@eowd, `order`=@order;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete monitor
DROP PROCEDURE IF EXISTS Inv_RMADelete//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Inv_RMADelete(IN `id` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete rma'
BEGIN
 DELETE FROM `inventory_rma` WHERE `id`=@id LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add resource permissions
DROP PROCEDURE IF EXISTS Res_AddNew//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Res_AddNew(IN `resource` VARCHAR(255), IN `common_name` VARCHAR(128), IN `owner` VARCHAR(128), IN `group` VARCHAR(128), IN `gread` INT(1), IN `gwrite` INT(1), IN `user` VARCHAR(128), IN `uread` INT(1), IN `uwrite` INT(1), OUT `x` INT)
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
DROP PROCEDURE IF EXISTS Res_AddGroup//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Res_AddGroup(IN `resource` VARCHAR(255), IN `group` VARCHAR(128), IN `gread` INT(1), IN `gwrite` INT(1), OUT `x` INT)
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
DROP PROCEDURE IF EXISTS Res_AddUser//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Res_AddUser(IN `resource` VARCHAR(255), IN `user` VARCHAR(128), IN `uread` INT(1), IN `uwrite` INT(1), OUT `x` INT)
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
DROP PROCEDURE IF EXISTS Res_EditAll//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Res_EditAll(IN `resource` VARCHAR(255), IN `common_name` VARCHAR(128), IN `owner` VARCHAR(128), IN `group` VARCHAR(128), IN `gread` INT(1), IN `gwrite` INT(1), IN `user` VARCHAR(128), IN `uread` INT(1), IN `uwrite` INT(1), OUT `x` INT)
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
DROP PROCEDURE IF EXISTS Res_EditGroup//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Res_EditGroup(IN `resource` VARCHAR(255), IN `group` VARCHAR(128), IN `gread` INT(1), IN `gwrite` INT(1), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Edit group resource permissions'
BEGIN
 UPDATE `resources_groups` SET `read`=@gread, `write`=@gwrite WHERE `group`=@group AND `resource`=@resource LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Edit user resource permissions
DROP PROCEDURE IF EXISTS Res_EditUser//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Res_EditUser(IN `resource` VARCHAR(255), IN `user` VARCHAR(128), IN `uread` INT(1), IN `uwrite` INT(1), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Edit user resource permissions'
BEGIN
 UPDATE `resources_users` SET `read`=@uread, `write`=@uwrite WHERE `user`=@user AND `resource`=@resource LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete resource permissions
DROP PROCEDURE IF EXISTS Res_DeleteAll//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Res_DeleteAll(IN `resource` VARCHAR(255), OUT `x` INT)
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
DROP PROCEDURE IF EXISTS Res_DeleteGroup//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Res_DeleteGroup(IN `resource` VARCHAR(255), `group` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete group from group resource permissions'
BEGIN
 DELETE FROM `resources_groups` WHERE `resource`=@resource AND `group`=@group;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete user resource permissions
DROP PROCEDURE IF EXISTS Res_DeleteUser//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Res_DeleteUser(IN `resource` VARCHAR(255), `user` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete user from user resource permissions'
BEGIN
 DELETE FROM `resources_users` WHERE `resource`=@resource AND `user`=@user;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- CRUD_ helper to return permissions
DROP PROCEDURE IF EXISTS CRUD_Permissions//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE CRUD_Permissions(IN `resource` VARCHAR(255), IN `user` VARCHAR(128), IN `group` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns permissions object dictating allowed read/write privleges per user/group on specified resource'
BEGIN
 -- SELECT `resource`,`common_name` FROM `resources` LEFT JOIN `resources_groups`.`read`, `resources_groups`.`write` ON `resources`.`resource`=`resources_groups`.`resource` AND `group`=@group LEFT JOIN `resources_users`.`read`, `resources_users`.write` ON `resources`.`resource`==`resources_users`.`resource` AND `user`=@user WHERE `resource`=@resource;
END//

-- CRUD_ helper to add inventory records and associated permissions objects
DROP PROCEDURE IF EXISTS CRUD_AddUpdateComputer//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE CRUD_AddUpdateComputer(IN `resource` VARCHAR(255), IN `user` VARCHAR(128), IN `group` VARCHAR(128), IN `model` VARCHAR(128), IN `sku` VARCHAR(128), IN `uuic` VARCHAR(128), IN `serial` VARCHAR(128), IN `hostname` VARCHAR(128), IN `location` VARCHAR(128), IN `eowd` VARCHAR(32), IN `opd` VARCHAR(32), IN `notes` LONGTEXT, OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Public interface to add/update computer as well as assocated permissions object(s)'
BEGIN

END//

-- CRUD_ helper to delete inventory records and associated permissions objects
DROP PROCEDURE IF EXISTS CRUD_DeleteComputer//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE CRUD_DeleteComputer(IN `resource` VARCHAR(255), IN `user` VARCHAR(128), IN `group` VARCHAR(128), IN `model` VARCHAR(128), IN `sku` VARCHAR(128), IN `uuic` VARCHAR(128), IN `serial` VARCHAR(128), IN `hostname` VARCHAR(128), IN `location` VARCHAR(128), IN `eowd` VARCHAR(32), IN `opd` VARCHAR(32), IN `notes` LONGTEXT, OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Public interface to delete computer as well as assocated permissions object(s)'
BEGIN

END//

DELIMITER ;
