DELIMITER //

-- Test authentication
DROP PROCEDURE IF EXISTS Auth_Authenticate//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_Authenticate(IN `email` VARCHAR(128), IN `password` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Authentication check'
BEGIN
 SELECT * FROM `authentication` WHERE `email`=email AND `password`=password;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add/Update user
DROP PROCEDURE IF EXISTS Auth_UserAddUpdate//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_UserAddUpdate(IN `resource` VARCHAR(255), IN `email` VARCHAR(128), IN `password` VARCHAR(255), IN `level` VARCHAR(40), IN `ggroup` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update user authentication'
BEGIN
 INSERT INTO `authentication` (`resource`, `emailAddress`, `password`, `level`, `group`) VALUES (resource, emailAddress, password, level, ggroup) ON DUPLICATE KEY UPDATE `resource`=resource, `emailAddress`=email, `password`=password, `level`=level, `group`=ggroup;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete user
DROP PROCEDURE IF EXISTS Auth_UserDelete//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_UserDelete(IN `email` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete user authentication'
BEGIN
 DELETE FROM `authentication` WHERE `email`=@email LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add/Update group
DROP PROCEDURE IF EXISTS Auth_GroupAddUpdate//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_GroupAddUpdate(IN `resource` VARCHAR(255), IN `group` VARCHAR(128), IN `manager` VARCHAR(128), IN `description` VARCHAR(255), IN `owner` VARCHAR(128), OUT `x` INT)
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
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_GroupDelete(IN `group` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete group'
BEGIN
 DELETE FROM `authentication_groups` WHERE `group`=@group LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Search licensing
DROP PROCEDURE IF EXISTS License_Search//
CREATE DEFINER='licensing'@'localhost' PROCEDURE License_Search(IN `name` VARCHAR(128), IN `expiration` VARCHAR(20), IN `amount` INT(4), IN `price` DECIMAL(20,2), IN `purchased` VARCHAR(20), IN `type` CHAR(5), IN `maintenance` INT(1), IN `notes` LONGTEXT, IN `serial` VARCHAR(255))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Search licenses'
BEGIN
 SELECT * FROM `license` WHERE `name` LIKE name OR `expiration` LIKE expiration OR `amount` LIKE amount OR `price` LIKE price OR `purchased` LIKE purchased OR `type` LIKE type OR `maintenance` LIKE maintenance OR `notes` LIKE notes OR `serial` LIKE serial;
END//

-- Add/Update licenses
DROP PROCEDURE IF EXISTS License_AddUpdate//
CREATE DEFINER='licensing'@'localhost' PROCEDURE License_AddUpdate(IN `name` VARCHAR(128), IN `expiration` VARCHAR(20), IN `amount` INT(4), IN `price` DECIMAL(20,2), IN `purchased` VARCHAR(20), IN `type` CHAR(5), IN `maintenance` INT(1), IN `notes` LONGTEXT, IN `serial` VARCHAR(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update licenses'
BEGIN
 SET x=0;
 SELECT COUNT(*) INTO x FROM `license` WHERE `name`=name;
 IF x>0 THEN
  UPDATE `license` SET `name`=name, `expiration`=expiration, `amount`=amount, `price`=price, `purchased`=purchased, `type`=type, `maintenance`=maintenance, `notes`=notes, `serial`=serial WHERE `name`=name LIMIT 1;
 ELSE
  SELECT * FROM `license` WHERE `name` LIKE name OR `expiration` LIKE expiration OR `amount` LIKE amount OR `price` LIKE price OR `purchased` LIKE purchased OR `type` LIKE type OR `maintenance` LIKE maintenance OR `notes` LIKE notes OR `serial` LIKE serial;
 END IF;
 SET x = ROW_COUNT();
 SELECT x;
END//

-- Delete license
DROP PROCEDURE IF EXISTS License_Delete//
CREATE DEFINER='licensing'@'localhost' PROCEDURE License_Delete(IN `name` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete license'
BEGIN
 DELETE FROM `license` WHERE `name`=name LIMIT 1;
END//

-- Retrieve current session
DROP PROCEDURE IF EXISTS Session_Search//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Search(IN `session_id` VARCHAR(128), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieve current session'
BEGIN
 SELECT `session_id`,AES_DECRYPT(BINARY(UNHEX(session_data)), SHA1(sKey)) AS session_data,`session_expire`,`session_agent`,`session_ip`,`session_referer` FROM `sessions` WHERE `session_id`=session_id;
END//

DELIMITER //
DROP PROCEDURE IF EXISTS Session_Add//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Add(IN `session_id` VARCHAR(64), `session_data` LONGTEXT, `session_expire` INT(10), `session_agent` VARCHAR(64), `session_ip` VARCHAR(64), `session_referer` VARCHAR(64), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or update existing session id & data'
BEGIN
 INSERT INTO `sessions` (`session_id`,`session_data`,`session_expire`,`session_agent`,`session_ip`,`session_referer`) VALUES (session_id, HEX(AES_ENCRYPT(session_data, SHA1(sKey))), session_expire, session_agent, session_ip, session_referer) ON DUPLICATE KEY UPDATE `session_id`=session_id, `session_data`=HEX(AES_ENCRYPT(session_data, SHA1(sKey))), `session_expire`=session_expire;
END//

DROP PROCEDURE IF EXISTS Session_Destroy//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Destroy(IN `session_id` VARCHAR(64))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete users sessions id'
BEGIN
 DELETE FROM `sessions` WHERE `session_id`=session_id LIMIT 1;
END//

DROP PROCEDURE IF EXISTS Session_Timeout//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Timeout(IN `session_expire` INT(10))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Expire session based on timeout option'
BEGIN
 DELETE FROM `sessions` WHERE `session_expire`>session_expire LIMIT 1;
END//

DROP PROCEDURE IF EXISTS Logs_Add//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Logs_Add(IN `guid` VARCHAR(64), `adate` VARCHAR(64), `ip` VARCHAR(10), `hostname` VARCHAR(80), `agent` VARCHAR(128), `query` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or update logs'
BEGIN
 INSERT INTO `logs` (`guid`,`adate`,`ip`,`hostname`,`agent`,`query`) VALUES (guid, adate, ip, hostname, agent, query);
END//

DROP PROCEDURE IF EXISTS Configuration_def_add//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_def_add(IN `title` VARCHAR(128), IN `templates` VARCHAR(255), IN `cache` VARCHAR(255), IN `private` INT(1), IN `email` VARCHAR(64), IN `timeout` INT(10), IN `privateKey` LONGTEXT, IN `publicKey` LONGTEXT, IN `secret` LONGTEXT, IN `countryName` VARCHAR(64), IN `stateOrProvinceName` VARCHAR(64), IN `localityName` VARCHAR(64), IN `organizationName` VARCHAR(64), IN `organizationalUnitName` VARCHAR(64), IN `commonName` VARCHAR(64), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates configuration'
BEGIN
 INSERT INTO `configuration` (`title`, `templates`, `cache`, `private`, `email`, `timeout`, `privateKey`, `publicKey`, `sKey`, `countryName`, `stateOrProvinceName`, `localityName`, `organizationName`, `organizationalUnitName`, `commonName`) VALUES (title, templates, cache, private, email, timeout, HEX(AES_ENCRYPT(privateKey, SHA1(sKey))), HEX(AES_ENCRYPT(publicKey, SHA1(sKey))), HEX(AES_ENCRYPT(secret, SHA1(sKey))), countryName, stateOrProvinceName, localityName, organizationName, organizationalUnitName, commonName) ON DUPLICATE KEY UPDATE `title`=title, `templates`=templates, `cache`=cache, `private`=private, `email`=email, `timeout`=timeout, `privateKey`=HEX(AES_ENCRYPT(privateKey, SHA1(sKey))), `publicKey`=HEX(AES_ENCRYPT(publicKey, SHA1(sKey))), `sKey`=HEX(AES_ENCRYPT(secret, SHA1(sKey))), `countryName`=countryName, `stateOrProvinceName`=stateOrProvinceName, `localityName`=localityName, `organizationName`=organizationName, `organizationalUnitName`=organizationalUnitName, `commonName`=commonName;
END//

DROP PROCEDURE IF EXISTS Configuration_def_get//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_def_get(IN `skey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieves configuration'
BEGIN
 SELECT `title`, `templates`, `cache`, `private`, `email` AS `emailAddress`, `timeout`, AES_DECRYPT(BINARY(UNHEX(privateKey)), SHA1(skey)) AS privateKey, AES_DECRYPT(BINARY(UNHEX(publicKey)), SHA1(skey)) AS publicKey, AES_DECRYPT(BINARY(UNHEX(sKey)), SHA1(skey)) AS password, `countryName`, `stateOrProvinceName`, `localityName`, `organizationName`, `organizationalUnitName`, `commonName` FROM `configuration`;
END//

DROP PROCEDURE IF EXISTS Configuration_cnf_add//
CREATE DEFINER='licesning'@'localhost' PROCEDURE Configuration_cnf_add(IN `config` VARCHAR(64), IN `encrypt_key` INT(1), IN `private_key_type` VARCHAR(64), IN `digest_algorithm` VARCHAR(64), IN `private_key_bits` INT(4), IN `x509_extensions` VARCHAR(32), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates OpenSSL configuration'
BEGIN
 SET x=0;
 SELECT COUNT(*) INTO x FROM `configuration_openssl_cnf`;
 IF x>0 THEN
  INSERT INTO `configuration_openssl_cnf` (`config`, `encrypt_key`, `private_key_type`, `digest_algorithm`, `private_key_bits`, `x509_extensions`) VALUES (config, encrypt_key, private_key_type, digest_algorithm, private_key_bits, x509_extensions) ON DUPLICATE KEY UPDATE `config`=config, `encrypt_key`=encrypt_key, `private_key_type`=private_key_type, `digest_algorithm`=digest_algorithm, `x509_extennsions`=x509_extensions;
 ELSE
  UPDATE `configuration_openssl_cnf` SET `config`=config, `encrypt_key`=encrypt_key, `private_key_type`=private_key_type, `digest_algorithm`=digest_algorithm, `x509_extennsions`=x509_extensions;
 END IF;
 SET x=ROW_COUNT();
 SELECT x;
END//

DROP PROCEDURE IF EXISTS Configuration_cnf_get//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_cnf_get()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieves OpenSSL configuration'
BEGIN
 SELECT `config`,`encrypt_key`,`private_key_type`,`digest_algorithm`,`private_key_bits`,`x509_extensions`,`encrypt_key_cipher` FROM `configuration_openssl_cnf`;
END//

DROP PROCEDURE IF EXISTS Configuration_get_dn//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_get_dn()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieves OpenSSL DN configuration'
BEGIN
 SELECT `countryName`,`stateOrProvinceName`,`localityName`,`organizationName`,`organizationalUnitName`,`commonName`,`email` AS emailAddress FROM `configuration`;
END//

DROP PROCEDURE IF EXISTS Configuration_keys_add//
CREATE DEFINER='licesning'@'localhost' PROCEDURE Configuration_keys_add(IN `countryName` VARCHAR(64), IN `stateOrProvinceName` VARCHAR(64), IN `localityName` VARCHAR(64), IN `organizationalName` VARCHAR(64), IN `organizationalUnitName` VARCHAR(64), IN `commonName` VARCHAR(64), IN `emailAddress` VARCHAR(64), IN `privateKey` LONGTEXT, IN `publicKey` LONGTEXT, IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates users key pair'
BEGIN
 INSERT INTO `configuration_openssl_keys` (`countryName`, `stateOrProvinceName`, `localityName`, `organizationalName`, `organizationalUnitName`, `commonName`, `emailAddress`, `privateKey`, `publicKey`) VALUES (countryName, stateOrProvinceName, localityName, organizationalName, organizationalUnitName, commonName, emailAddress, HEX(AES_ENCRYPT(privateKey, SHA1(sKey))), HEX(AES_ENCRYPT(publicKey, SHA1(sKey)))) ON DUPLICATE KEY UPDATE `countryName`=countryName, `stateOrProvinceName`=stateOrProvinceName, `localityName`=localityName, `organizationalName`=organizationalName, `organizationalUnitName`=organizationalUnitName, `commonName`=commonName, `emailAddress`=emailAddress, `privateKey`=HEX(AES_ENCRYPT(privateKey, SHA1(sKey))), `publicKey`=HEX(AES_ENCRYPT(publicKey, SHA1(sKey)));
END//

DROP PROCEDURE IF EXISTS Configuration_keys_get//
CREATE DEFINER='licesning'@'localhost' PROCEDURE Configuration_keys_get(IN `emailAddress` VARCHAR(64), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieves OpenSSL keypair by email address'
BEGIN
 SELECT `countryName`, `stateOrProvinceName`, `localityName`, `organizationalName`, `organizationalUnitName`, `commonName`, `emailAddress`, AES_DECRYPT(BINARY(UNHEX(privateKey)), SHA1(sKey)) AS privateKey, AES_DECRYPT(BINARY(UNHEX(publicKey)), SHA1(sKey)) AS publicKey FROM `configuration_openssl_keys` WHERE `emailAddress`=emailAddress;
END//

DELIMITER ;
